<?php

use App\Models\Activity;
use App\Models\Registration;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ═══════════════════════════════════════════════════════
// FR-02: Activity Management
// ═══════════════════════════════════════════════════════

// TC-02-1: Activity Admin สร้างกิจกรรมใหม่ → status = pending
test('TC-02-1: activity admin can create activity and status is pending', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => 'admin']);
    $tag   = Tag::factory()->create();

    $response = $this->actingAs($admin)->post('/create-activity', [
        'title'                 => 'Test Activity',
        'description'           => 'Description here',
        'date'                  => now()->addDays(10)->format('Y-m-d'),
        'registration_deadline' => now()->addDays(5)->format('Y-m-d'),
        'location'              => 'KMUTNB',
        'max_participants'      => 30,
        'tags'                  => [$tag->id],
        'image'                 => UploadedFile::fake()->image('activity.jpg'),
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertDatabaseHas('activities', [
        'title'   => 'Test Activity',
        'status'  => 'pending',
        'user_id' => $admin->id,
    ]);
});

// TC-02-2: Staff เห็นเฉพาะกิจกรรม pending
test('TC-02-2: staff admin index shows only pending activities', function () {
    $staff = User::factory()->create(['role' => 'staff']);
    Activity::factory()->create(['status' => 'pending']);
    Activity::factory()->create(['status' => 'approved']);
    Activity::factory()->create(['status' => 'rejected']);

    $response = $this->actingAs($staff)->get('/admin/activities');

    $response->assertStatus(200);
    $response->assertViewHas('activities', function ($activities) {
        return $activities->count() === 1
            && $activities->first()->status === 'pending';
    });
});

// TC-02-3: Staff Approve กิจกรรม → status = approved
test('TC-02-3: staff can approve pending activity', function () {
    $staff    = User::factory()->create(['role' => 'staff']);
    $activity = Activity::factory()->create(['status' => 'pending']);

    $this->actingAs($staff)->post("/admin/activities/{$activity->id}/approve");

    $this->assertDatabaseHas('activities', [
        'id'     => $activity->id,
        'status' => 'approved',
    ]);
});

// TC-02-4: Staff Reject กิจกรรม (ใหม่) → status = rejected
test('TC-02-4: staff can reject new pending activity', function () {
    $staff    = User::factory()->create(['role' => 'staff']);
    $activity = Activity::factory()->create([
        'status'       => 'pending',
        'edit_payload' => null,
    ]);

    $this->actingAs($staff)->post("/admin/activities/{$activity->id}/reject");

    $this->assertDatabaseHas('activities', [
        'id'     => $activity->id,
        'status' => 'rejected',
    ]);
});

// TC-02-5: Activity Admin ขอแก้ไข → edit_payload บันทึก, status = pending
test('TC-02-5: activity admin edit request saves to edit_payload with pending status', function () {
    $admin    = User::factory()->create(['role' => 'admin']);
    $activity = Activity::factory()->create([
        'status'  => 'approved',
        'user_id' => $admin->id,
    ]);

    $this->actingAs($admin)->put("/activities/{$activity->id}", [
        'title'                 => 'Updated Title',
        'description'           => 'Updated Description',
        'date'                  => now()->addDays(15)->format('Y-m-d'),
        'registration_deadline' => now()->addDays(10)->format('Y-m-d'),
        'location'              => 'New Location',
        'max_participants'      => 50,
    ]);

    $activity->refresh();
    expect($activity->status)->toBe('pending');
    expect($activity->edit_payload)->not->toBeNull();
    expect($activity->edit_payload['title'])->toBe('Updated Title');
});

// TC-02-6: Staff Approve การแก้ไข → apply edit_payload, edit_payload = null
test('TC-02-6: staff approving edit applies payload and clears it', function () {
    $staff    = User::factory()->create(['role' => 'staff']);
    $activity = Activity::factory()->create([
        'status'       => 'pending',
        'title'        => 'Old Title',
        'edit_payload' => ['title' => 'New Title', 'tags' => []],
    ]);

    $this->actingAs($staff)->post("/admin/activities/{$activity->id}/approve");

    $activity->refresh();
    expect($activity->title)->toBe('New Title');
    expect($activity->edit_payload)->toBeNull();
    expect($activity->status)->toBe('approved');
});

// TC-02-7: Staff Reject การแก้ไข → edit_payload = null, กลับเป็น approved
test('TC-02-7: staff rejecting edit clears payload and restores approved status', function () {
    $staff    = User::factory()->create(['role' => 'staff']);
    $activity = Activity::factory()->create([
        'status'       => 'pending',
        'title'        => 'Original Title',
        'edit_payload' => ['title' => 'Attempted Change', 'tags' => []],
    ]);

    $this->actingAs($staff)->post("/admin/activities/{$activity->id}/reject");

    $activity->refresh();
    expect($activity->title)->toBe('Original Title');
    expect($activity->edit_payload)->toBeNull();
    expect($activity->status)->toBe('approved');
});

// TC-02-8: Staff ลบ Activity → Registrations ถูกลบตาม CASCADE
test('TC-02-8: staff delete activity also removes related registrations', function () {
    $staff    = User::factory()->create(['role' => 'staff']);
    $activity = Activity::factory()->create(['status' => 'approved']);
    $student  = User::factory()->create(['role' => 'student']);

    Registration::create([
        'user_id'     => $student->id,
        'activity_id' => $activity->id,
        'status'      => 'approved',
    ]);

    $this->assertDatabaseCount('registrations', 1);

    $this->actingAs($staff)->delete("/admin/activities/{$activity->id}");

    $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    $this->assertDatabaseCount('registrations', 0);
});

// TC-02-9: View count เพิ่มทุกครั้งที่เปิดดูรายละเอียด
test('TC-02-9: view count increments each time activity detail is visited', function () {
    $user     = User::factory()->create(['role' => 'student']);
    $activity = Activity::factory()->create([
        'status'     => 'approved',
        'view_count' => 0,
    ]);

    $this->actingAs($user)->get("/activities/{$activity->id}");
    $this->actingAs($user)->get("/activities/{$activity->id}");

    expect($activity->fresh()->view_count)->toBe(2);
});
