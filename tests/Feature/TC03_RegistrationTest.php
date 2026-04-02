<?php

use App\Models\Activity;
use App\Models\Registration;
use App\Models\User;

// ═══════════════════════════════════════════════════════
// FR-03: Registration Management
// ═══════════════════════════════════════════════════════

// TC-03-1: สมัครกิจกรรมที่ยังไม่เต็ม → approved ทันที
test('TC-03-1: registering for activity with seats available auto-approves', function () {
    $student  = User::factory()->create(['role' => 'student']);
    $activity = Activity::factory()->create([
        'status'           => 'approved',
        'max_participants' => 10,
    ]);

    $this->actingAs($student)
        ->post("/activities/{$activity->id}/register")
        ->assertSessionHas('success');

    $this->assertDatabaseHas('registrations', [
        'user_id'     => $student->id,
        'activity_id' => $activity->id,
        'status'      => 'approved',
    ]);
});

// TC-03-2: สมัครกิจกรรมที่เต็มแล้ว → status = pending
test('TC-03-2: registering for full activity sets status to pending', function () {
    $activity = Activity::factory()->create([
        'status'           => 'approved',
        'max_participants' => 1,
    ]);

    // เติมที่นั่งจนเต็ม
    $other = User::factory()->create(['role' => 'student']);
    Registration::create([
        'user_id'     => $other->id,
        'activity_id' => $activity->id,
        'status'      => 'approved',
    ]);

    $late = User::factory()->create(['role' => 'student']);
    $this->actingAs($late)->post("/activities/{$activity->id}/register");

    $this->assertDatabaseHas('registrations', [
        'user_id'     => $late->id,
        'activity_id' => $activity->id,
        'status'      => 'pending',
    ]);
});

// TC-03-3: สมัครซ้ำ → แสดง error
test('TC-03-3: registering for the same activity twice shows error', function () {
    $student  = User::factory()->create(['role' => 'student']);
    $activity = Activity::factory()->create([
        'status'           => 'approved',
        'max_participants' => 10,
    ]);

    $this->actingAs($student)->post("/activities/{$activity->id}/register");
    $this->actingAs($student)->post("/activities/{$activity->id}/register")
        ->assertSessionHas('error');

    $this->assertDatabaseCount('registrations', 1);
});

// TC-03-4: Admin Approve ผู้สมัคร pending
test('TC-03-4: admin can approve pending registration', function () {
    $admin        = User::factory()->create(['role' => 'admin']);
    $activity     = Activity::factory()->create(['status' => 'approved']);
    $registration = Registration::create([
        'user_id'     => User::factory()->create(['role' => 'student'])->id,
        'activity_id' => $activity->id,
        'status'      => 'pending',
    ]);

    $this->actingAs($admin)
        ->post("/admin/registrations/{$registration->id}/approve");

    $this->assertDatabaseHas('registrations', [
        'id'     => $registration->id,
        'status' => 'approved',
    ]);
});

// TC-03-5: Admin Reject ผู้สมัคร
test('TC-03-5: admin can reject pending registration', function () {
    $admin        = User::factory()->create(['role' => 'admin']);
    $activity     = Activity::factory()->create(['status' => 'approved']);
    $registration = Registration::create([
        'user_id'     => User::factory()->create(['role' => 'student'])->id,
        'activity_id' => $activity->id,
        'status'      => 'pending',
    ]);

    $this->actingAs($admin)
        ->post("/admin/registrations/{$registration->id}/reject");

    $this->assertDatabaseHas('registrations', [
        'id'     => $registration->id,
        'status' => 'rejected',
    ]);
});

// TC-03-6: My Activities แสดงเฉพาะของตัวเอง
test('TC-03-6: my activities page shows only the logged in users registrations', function () {
    $me      = User::factory()->create(['role' => 'student']);
    $other   = User::factory()->create(['role' => 'student']);
    $activity = Activity::factory()->create(['status' => 'approved']);

    Registration::create(['user_id' => $me->id,    'activity_id' => $activity->id, 'status' => 'approved']);
    Registration::create(['user_id' => $other->id, 'activity_id' => $activity->id, 'status' => 'approved']);

    $response = $this->actingAs($me)->get('/my-activities');

    $response->assertStatus(200);
    $response->assertViewHas('registrations', function ($registrations) use ($me) {
        return $registrations->count() === 1
            && $registrations->first()->user_id === $me->id;
    });
});
