<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    // สมัครกิจกรรม (User)
    public function register(Request $request, $id)
    {
        $exists = Registration::where('user_id', Auth::id())
            ->where('activity_id', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'คุณสมัครกิจกรรมนี้แล้ว');
        }

        Registration::create([
            'user_id' => Auth::id(),
            'activity_id' => $id,
            'status' => 'approved'
        ]);

        return back()->with('success', 'สมัครกิจกรรมสำเร็จ รอการอนุมัติ');
    }

    // หน้ากิจกรรมของฉัน (User)
    public function myActivities()
    {
        $registrations = Registration::where(
            'user_id', Auth::id()
        )->get();

        return view('my-activities', compact('registrations'));
    }

    // --- ส่วนของ Admin 

    // แสดงรายการสมัครทั้งหมด
    public function adminIndex()
    {
        $registrations = Registration::with(['user','activity'])->get();
        return view('admin.registrations', compact('registrations'));
    }

    // อนุมัติการสมัคร
    public function approve($id)
    {
        $reg = Registration::find($id);
        if ($reg) {
            $reg->status = 'approved';
            $reg->save();
        }
        return back()->with('success', 'อนุมัติเรียบร้อยแล้ว');
    }

    // ปฏิเสธการสมัคร
    public function reject($id)
    {
        $reg = Registration::find($id);
        if ($reg) {
            $reg->status = 'rejected';
            $reg->save();
        }
        return back()->with('success', 'ปฏิเสธการสมัครเรียบร้อยแล้ว');
    }

    // ยังไม่ได้ใช้
    public function store(Request $request)
    {
        Registration::create([
            'user_id' => Auth::id(),
            'activity_id' => $request->activity_id,
            'status' => 'pending'
        ]);

        return back()->with('success', 'สมัครกิจกรรมเรียบร้อยแล้ว');
    }
}