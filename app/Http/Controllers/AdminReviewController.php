<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Tag;
use App\Models\Registration;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    // หน้า Review สถิติและข้อมูลการเข้าชม
    public function index()
    {
        // 1. สถิติแท็กที่มีกิจกรรมจำนวนมาก
        $tagStats = Tag::withCount('activities')
            ->orderBy('activities_count', 'desc')
            ->get();

        // 2. กิจกรรมที่มีการเข้าชมมากที่สุด (view_count สูงสุด)
        $topViewedActivities = Activity::where('status', 'approved')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();

        // 3. สถิติแท็กจากมุมมองของการเข้าชม (รวม view_count ของกิจกรรมทั้งหมดสำหรับแท็กนี้)
        $tagViewStats = Tag::with('activities')
            ->get()
            ->map(function ($tag) {
                return [
                    'tag_name' => $tag->name,
                    'activity_count' => $tag->activities->count(),
                    'total_views' => $tag->activities->sum('view_count'),
                    'avg_views' => $tag->activities->count() > 0 
                        ? round($tag->activities->avg('view_count'), 2) 
                        : 0,
                    'total_registrations' => Registration::whereIn('activity_id', $tag->activities->pluck('id'))->count()
                ];
            })
            ->sortByDesc('total_views')
            ->values();

        // 4. กิจกรรมที่มีจำนวนสมัครมากที่สุด
        $topRegistrationActivities = Activity::withCount('registrations')
            ->where('status', 'approved')
            ->orderBy('registrations_count', 'desc')
            ->limit(10)
            ->get();

        // 5. สรุปทั่วไป
        $stats = [
            'total_activities' => Activity::count(),
            'approved_activities' => Activity::where('status', 'approved')->count(),
            'pending_activities' => Activity::where('status', 'pending')->count(),
            'total_views' => Activity::sum('view_count'),
            'total_registrations' => Registration::count(),
            'avg_views_per_activity' => Activity::count() > 0 
                ? round(Activity::avg('view_count'), 2) 
                : 0,
        ];

        return view('admin.review', compact(
            'tagStats',
            'topViewedActivities',
            'tagViewStats',
            'topRegistrationActivities',
            'stats'
        ));
    }
}
