<x-app-layout>
    <div class="max-w-2xl mx-4 sm:mx-auto my-6 sm:my-10" style="padding: 20px; background: white; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); font-family: 'sans-serif';">
        
        <h1 style="font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
            แก้ไขกิจกรรม (Edit Activity)
        </h1>

        <form action="/activities/{{ $activity->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">ชื่อกิจกรรม</label>
                <input type="text" name="title" value="{{ $activity->title }}" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">รายละเอียด</label>
                <textarea name="description" rows="3" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">{{ $activity->description }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">วันที่จัดกิจกรรม</label>
                    <input type="date" name="date" value="{{ $activity->date }}" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">วันปิดรับสมัคร</label>
                    <input type="date" name="registration_deadline" value="{{ $activity->registration_deadline }}" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">สถานที่</label>
                    <input type="text" name="location" value="{{ $activity->location }}" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">จำนวนรับสูงสุด</label>
                    <input type="number" name="max_participants" value="{{ $activity->max_participants }}" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
            </div>

            <div style="margin-bottom: 15px; background: #f9fafb; padding: 15px; border-radius: 10px;">
                <label style="display: block; font-weight: 600; margin-bottom: 10px;">หมวดหมู่ (Tags):</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($tags as $tag)
                    <label style="display: flex; align-items: center; background: white; padding: 5px 12px; border: 1px solid #e5e7eb; border-radius: 20px; cursor: pointer;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                            {{ $activity->tags->contains($tag->id) ? 'checked' : '' }} style="margin-right: 8px;">
                        {{ $tag->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">เปลี่ยนรูปหน้าปก (ถ้ามี)</label>
                <input type="file" name="image" style="width: 100%; font-size: 14px;">
                <p style="font-size: 12px; color: gray; margin-top: 5px;">รูปปัจจุบัน: {{ $activity->image }}</p>
            </div>

            <button type="submit" style="width: 100%; background: #10b981; color: white; padding: 12px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer;">
                บันทึกการแก้ไข (ส่งให้ Admin ตรวจสอบใหม่)
            </button>
        </form>
    </div>
</x-app-layout>