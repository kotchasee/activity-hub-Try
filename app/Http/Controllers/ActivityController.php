<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::all();

        return view('dashboard', compact('activities'));
    }

    public function create()
    {
        return view('activities.create');
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
            'image' => $imagePath
        ]);

        return redirect('/dashboard')->with('success', 'Activity created!');
    }
    public function show($id)
{
    $activity = Activity::findOrFail($id);

    return view('activities.show', compact('activity'));
}
}