<x-app-layout>
    <div style="max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); font-family: 'sans-serif';">
        
        <h1 style="font-size: 24px; font-weight: bold; color: #1f2937; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
            สร้างกิจกรรมใหม่ (Create Activity)
        </h1>

        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <strong>❌ สร้างกิจกรรมไม่สำเร็จ กรุณาตรวจสอบข้อมูล:</strong>
                <ul style="margin-top: 8px; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/create-activity" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">ชื่อกิจกรรม (Title)</label>
                <input type="text" name="title" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">รายละเอียด (Description)</label>
                <textarea name="description" rows="3" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">วันที่จัดกิจกรรม</label>
                    <input type="date" name="date" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">วันปิดรับสมัคร</label>
                    <input type="date" name="registration_deadline" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">สถานที่ (Location)</label>
                    <input type="text" name="location" required style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">จำนวนรับสมัครสูงสุด (คน)</label>
                    <input type="number" name="max_participants" value="{{ old('max_participants') }}" min="0" placeholder="0 = ไม่จำกัดจำนวน" style="width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 10px;">
                </div>
            </div>

            <div style="margin-bottom: 15px; background: #f9fafb; padding: 15px; border-radius: 10px;">
                <label style="display: block; font-weight: 600; margin-bottom: 10px; color: #374151;">เลือกหมวดหมู่ (Tags):</label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach($tags as $tag)
                    <label style="display: flex; align-items: center; background: white; padding: 5px 12px; border: 1px solid #e5e7eb; border-radius: 20px; cursor: pointer; font-size: 14px;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" style="margin-right: 8px;">
                        {{ $tag->name }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">รูปภาพหน้าปก (Cover Image)</label>
                <input type="file" name="image" style="width: 100%; font-size: 14px; color: #6b7280;">
            </div>

            @if(auth()->user()->role === 'admin_club')     
                <button type="submit" style="width: 100%; background: #065f46; color: white; padding: 12px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);">
                    สร้างกิจกรรมเลย
                </button>          
            @else     
                <div style="text-align: center; color: #ef4444; font-weight: bold; padding: 10px; border: 1px dashed #f87171; border-radius: 10px;">
                    ⚠️ เฉพาะ Admin Club เท่านั้นที่สร้างกิจกรรมได้
                </div>
            @endif

        </form>
    </div>
</x-app-layout>