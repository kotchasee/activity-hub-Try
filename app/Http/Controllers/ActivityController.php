<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::where('status', 'approved')->get();

        return view('dashboard', compact('activities'));
    }

    public function create()
    {
        return view('activities.create');
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

        Activity::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'image' => $imagePath,
            'status' => 'pending',
            'user_id' => auth()->id()
        ]);

        return redirect('/dashboard')->with('success', 'Activity created!');
    }

    public function show($id)
    {
        $activity = Activity::findOrFail($id);

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
            $activity->status = 'approved';
            $activity->save();
        }

        return back()->with('success', 'อนุมัติกิจกรรมเรียบร้อย!');
    }

    // ปฏิเสธกิจกรรม
    public function reject($id)
    {
        $activity = Activity::find($id);
        if ($activity) {
            $activity->status = 'rejected';
            $activity->save();
        }

        return back()->with('success', 'ปฏิเสธกิจกรรมเรียบร้อย!');
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

}