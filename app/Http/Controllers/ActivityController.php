<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Tag;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('tags')->where('status', 'approved');

        //  ค้นหาชื่อ
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        //  filter tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        //  filter date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $activities = $query->latest()->get();

        $tags = Tag::all(); // ส่ง tag ไป view 

        // Monthly Analytics
        $monthlyActivities = Activity::where('status', 'approved')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->orderBy('date')
            ->get();

        $hotActivities = Activity::where('status', 'approved')
            ->orderBy('view_count', 'desc')
            ->limit(3)
            ->get();

        return view('dashboard', compact('activities', 'tags', 'monthlyActivities', 'hotActivities'));
    }

    public function create()
    {
        $tags = \App\Models\Tag::all(); // ดึง Tag ทั้งหมดจากฐานข้อมูล
        return view('activities.create', compact('tags')); // ส่งตัวแปร $tags ไปที่หน้า create
    }
    public function myActivities()
    {

        $activities = Activity::where('user_id', auth()->id())->get();
        return view('My-club', compact('activities'));
    }

    public function store(Request $request)
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('activities', 'public');
        }

        $activity = Activity::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'registration_deadline' => $request->registration_deadline,
            'location' => $request->location,
            'max_participants' => $request->max_participants, // เพิ่มบรรทัดนี้เพื่อรับค่าจำนวนคน
            'image' => $imagePath,
            'status' => 'pending',
            'user_id' => auth()->id()
        ]);
        // 2. บันทึก Tag ที่เลือก 
        if ($request->has('tags')) {
            $activity->tags()->attach($request->tags); // ใช้ $activity ที่เพิ่งสร้างด้านบน และสั่ง attach Tag เข้าไป
    }

        return redirect('/dashboard')->with('success', 'Activity created!');
    }

    public function show($id)
    {
        $activity = Activity::with('tags')->findOrFail($id);
        
        // Increment view count
        $activity->increment('view_count');

        return view('activities.show', compact('activity'));
    }

    // --- ส่วนของ Admin  ---

    // ดึงเฉพาะกิจกรรมที่สถานะเป็น pending มาตรวจสอบ
    public function adminIndex()
    {
        $activities = Activity::where('status', 'pending')->get();

        return view('admin.activities', compact('activities'));
    }

    // อนุมัติกิจกรรม
    public function approve($id)
    {
        $activity = Activity::find($id);
        if ($activity) {
            // ถ้ามีข้อมูลที่พักไว้ (การแก้ไข)
            if ($activity->edit_payload) {
                $newData = $activity->edit_payload;
                
                // แยก Tags ออกมาจัดการ
                $tags = $newData['tags'] ?? [];
                unset($newData['tags']); // ลบออกจากก้อนข้อมูลหลักก่อน update ลงตาราง
                
                // เอาข้อมูลใหม่ทับข้อมูลเดิม
                $activity->update($newData);
                
                // อัปเดต Tags
                $activity->tags()->sync($tags);
                
                // ล้างข้อมูลที่พักไว้
                $activity->edit_payload = null;
            }

            $activity->status = 'approved';
            $activity->save();
        }
        return back()->with('success', 'อนุมัติการเปลี่ยนแปลงเรียบร้อย!');
    }

    // ปฏิเสธกิจกรรม
    public function reject($id)
    {
        $activity = Activity::find($id);
        if ($activity) {
            // ถ้าเป็นการปฏิเสธการแก้ไข (มีข้อมูลพักไว้)
            if ($activity->edit_payload) {
                $activity->edit_payload = null; // ล้างข้อมูลที่ขอแก้ทิ้งไป
                $activity->status = 'approved'; // กลับไปใช้สถานะอนุมัติเดิม (ข้อมูลเดิม)
            } else {
                // ถ้าเป็นการสร้างครั้งแรกแล้วโดนปฏิเสธ
                $activity->status = 'rejected';
            }
            $activity->save();
        }
        return back()->with('success', 'ปฏิเสธคำขอแก้ไข ข้อมูลเดิมยังคงอยู่');
    }
    // ลบกิจกรรม
    public function destroy($id)
    {
        $activity = \App\Models\Activity::find($id);

        if (!$activity) {
            return back()->with('error', 'ไม่พบกิจกรรม');
        }

        $activity->delete();

        return back()->with('success', 'ลบกิจกรรมเรียบร้อย');
    }
    // แก้ไขกิจกรรม
    public function edit($id)
    {
        // ดึงกิจกรรมพร้อม tags ที่มีอยู่
        $activity = Activity::with('tags')->findOrFail($id); 
        $tags = Tag::all();
        
        return view('activities.edit', compact('activity', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        
        // ดึงข้อมูลทั้งหมดที่ Staff ส่งมา
        $data = $request->only(['title', 'description', 'date', 'registration_deadline', 'location', 'max_participants']);
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        // เพิ่มข้อมูล Tags เข้าไปในก้อนข้อมูลที่จะพักไว้ด้วย
        $data['tags'] = $request->tags;

        //  แทนที่จะ update ของจริง เราเอาข้อมูลก้อนใหม่ไปพักไว้ใน edit_payload 
        $activity->edit_payload = $data; 
        $activity->status = 'pending'; // เปลี่ยนสถานะเป็นรอตรวจสอบ
        $activity->save();

        return redirect('/my-activities')->with('success', 'ส่งคำขอแก้ไขแล้ว ข้อมูลเดิมจะยังแสดงอยู่จนกว่า Admin จะอนุมัติ');
    }
    public function approveParticipant($id)
    {
        // เข้าถึง Model Registration โดยตรงเพื่อเปลี่ยนสถานะคนสมัคร
        $reg = \App\Models\Registration::findOrFail($id);
        $reg->status = 'approved';
        $reg->save();

        return back()->with('success', 'อนุมัติผู้เข้าร่วมเรียบร้อย!');
    }

    public function rejectParticipant($id)
    {
        $reg = \App\Models\Registration::findOrFail($id);
        $reg->status = 'rejected';
        $reg->save();

        return back()->with('success', 'ปฏิเสธผู้เข้าร่วมเรียบร้อย!');
    }
}