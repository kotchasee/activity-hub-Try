<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function register($id)
    {
        Registration::create([
            'user_id' => Auth::id(),
            'activity_id' => $id,
            'status' => 'pending'
        ]);

        return back()->with('success','สมัครกิจกรรมสำเร็จ รอแอดมินอนุมัติ');
    }

    public function myActivities()
    {
        $registrations = Registration::where(
            'user_id', Auth::id()
        )->get();

        return view('my-activities', compact('registrations'));

    }
    public function store(Request $request)
    {
        Registration::create([
            'user_id' => Auth::id(),
            'activity_id' => $request->activity_id,
            'status' => 'pending'
        ]);

        return back()->with('success','สมัครกิจกรรมเรียบร้อยแล้ว');
    }
}