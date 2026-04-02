<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Activity;
use App\Models\Tag;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

        $activities = $query->latest()->paginate(12)->appends($request->query());

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
        $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'date'                  => 'required|date',
            'registration_deadline' => 'required|date',
            'location'              => 'required|string|max:255',
            'max_participants'      => 'required|integer|min:0',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            $imagePath = null;

            if ($request->hasFile('image')) {
                $imagePath = $this->uploadActivityImage($request->file('image'));
            }

            $activity = Activity::create([
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'registration_deadline' => $request->registration_deadline,
                'location' => $request->location,
                'max_participants' => $request->max_participants,
                'image' => $imagePath,
                'status' => 'pending',
                'user_id' => auth()->id()
            ]);

            if ($request->has('tags')) {
                $activity->tags()->attach($request->tags);
            }

            return redirect('/dashboard')->with('success', 'สร้างกิจกรรมสำเร็จแล้ว');
        } catch (\Throwable $e) {
            Log::error('Create activity failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'สร้างกิจกรรมไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        }
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

        $this->deleteActivityImage($activity->image);
        $activity->delete();

        return back()->with('success', 'ลบกิจกรรมเรียบร้อย');
    }
    // แก้ไขกิจกรรม
    public function edit($id)
    {
        // ดึงกิจกรรมพร้อม tags ที่มีอยู่
        $activity = Activity::with('tags')->findOrFail($id);

        // ตรวจสอบว่าเป็นเจ้าของกิจกรรม
        if ($activity->user_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขกิจกรรมนี้');
        }

        $tags = Tag::all();
        
        return view('activities.edit', compact('activity', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $request->validate([
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'date'                  => 'required|date',
            'registration_deadline' => 'required|date',
            'location'              => 'required|string|max:255',
            'max_participants'      => 'required|integer|min:0',
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // ตรวจสอบว่าเป็นเจ้าของกิจกรรม
        if ($activity->user_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์แก้ไขกิจกรรมนี้');
        }
        
        // ดึงข้อมูลทั้งหมดที่ Staff ส่งมา
        $data = $request->only(['title', 'description', 'date', 'registration_deadline', 'location', 'max_participants']);
        
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadActivityImage($request->file('image'));
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
        $reg = \App\Models\Registration::findOrFail($id);

        // ตรวจสอบว่าเป็นเจ้าของกิจกรรมที่คนนี้สมัคร
        if ($reg->activity->user_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์จัดการผู้เข้าร่วมกิจกรรมนี้');
        }

        $reg->status = 'approved';
        $reg->save();

        return back()->with('success', 'อนุมัติผู้เข้าร่วมเรียบร้อย!');
    }

    public function rejectParticipant($id)
    {
        $reg = \App\Models\Registration::findOrFail($id);

        // ตรวจสอบว่าเป็นเจ้าของกิจกรรมที่คนนี้สมัคร
        if ($reg->activity->user_id !== auth()->id()) {
            abort(403, 'คุณไม่มีสิทธิ์จัดการผู้เข้าร่วมกิจกรรมนี้');
        }

        $reg->status = 'rejected';
        $reg->save();

        return back()->with('success', 'ปฏิเสธผู้เข้าร่วมเรียบร้อย!');
    }

    private function uploadActivityImage(UploadedFile $file): string
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        // If Cloudinary is not configured, keep legacy local behavior.
        if (!$cloudName || !$apiKey || !$apiSecret) {
            return $file->store('activities', 'public');
        }

        $timestamp = time();
        $folder = 'activity-hub/activities';
        $signature = sha1("folder={$folder}&timestamp={$timestamp}{$apiSecret}");

        $response = Http::asMultipart()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
            [
                'name' => 'file',
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ],
            ['name' => 'api_key', 'contents' => $apiKey],
            ['name' => 'timestamp', 'contents' => (string) $timestamp],
            ['name' => 'folder', 'contents' => $folder],
            ['name' => 'signature', 'contents' => $signature],
        ]);

        if (!$response->successful() || !$response->json('secure_url')) {
            throw new \RuntimeException('Cloud upload failed: '.$response->body());
        }

        return $response->json('secure_url');
    }

    private function deleteActivityImage(?string $imagePath): void
    {
        if (!$imagePath) {
            return;
        }

        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            $this->deleteCloudinaryImageByUrl($imagePath);
            return;
        }

        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    private function deleteCloudinaryImageByUrl(string $imageUrl): void
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            return;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH);
        if (!$path || !str_contains($path, '/upload/')) {
            return;
        }

        $parts = explode('/upload/', $path, 2);
        if (count($parts) !== 2) {
            return;
        }

        $publicIdPath = preg_replace('#^v\d+/#', '', $parts[1]);
        $publicId = preg_replace('/\.[^.]+$/', '', $publicIdPath ?? '');

        if (!$publicId) {
            return;
        }

        $timestamp = time();
        $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

        Http::asForm()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy", [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'api_key' => $apiKey,
            'signature' => $signature,
        ]);
    }
}