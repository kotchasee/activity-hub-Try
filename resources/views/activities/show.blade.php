<x-app-layout>

<style>
    /* Icon Management (Clean HTML) */
    .icon-date::before     { content: '📅'; margin-right: 8px; }
    .icon-deadline::before { content: '⏳'; margin-right: 8px; }
    .icon-location::before { content: '📍'; margin-right: 8px; }
    .icon-people::before   { content: '👥'; margin-right: 8px; }
    
    /* Layout Styling */
    .activity-container {
        width: 95%; 
        max-width: 800px; 
        margin: 30px auto; 
        padding: 25px; 
        background: white; 
        border-radius: 12px; 
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); 
        font-family: sans-serif;
    }

    .status-badge {
        font-size: 12px; 
        padding: 5px 12px; 
        border-radius: 50px; 
        display: inline-block; 
        margin-bottom: 8px;
        font-weight: 600;
    }

    .btn {
        padding: 10px 25px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        font-weight: bold; 
        transition: all 0.2s;
    }
    
    .btn:disabled { cursor: not-allowed; opacity: 0.7; }

    /* Modal Styling */
    .modal-overlay {
        display:none; position:fixed; top:0; left:0; width:100%; height:100%;
        background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index: 100;
        backdrop-filter: blur(2px);
    }
</style>

<div class="activity-container">
    {{-- Header Section --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <h1 style="font-size: 28px; font-weight: bold; color: #1f2937; margin-bottom: 15px;">
            {{ $activity->title }}
        </h1>
        <img src="{{ str_starts_with($activity->image, 'http') ? $activity->image : asset('storage/'.$activity->image) }}" 
             style="width: 100%; max-width: 500px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
    </div>

    {{-- Details Section --}}
    <div style="margin-bottom: 25px; line-height: 1.8; color: #4b5563;">
        <p><strong style="color: #111827;">รายละเอียด:</strong> {{ $activity->description }}</p>
        <p><span class="icon-date"></span><strong>วันจัดกิจกรรม:</strong> {{ $activity->date }}</p>

        @php
            $today = \Carbon\Carbon::now()->startOfDay();
            $deadline = \Carbon\Carbon::parse($activity->registration_deadline)->startOfDay();
            $isClosed = $today->gt($deadline);
            
            $currentParticipants = \App\Models\Registration::where('activity_id', $activity->id)
                ->where('status', 'approved')->count();
            $max = $activity->max_participants;
            $isFull = $max > 0 && $currentParticipants >= $max;

            $registration = \App\Models\Registration::where('user_id', auth()->id())
                ->where('activity_id', $activity->id)->first();
        @endphp

        <p style="color: {{ $isClosed ? '#ef4444' : '#16a34a' }}; font-weight: bold;">
            <span class="icon-deadline"></span><strong>ปิดรับสมัคร:</strong> {{ $activity->registration_deadline }}
        </p>

        <p><span class="icon-location"></span><strong>สถานที่:</strong> {{ $activity->location }}</p>

        <p><span class="icon-people"></span><strong>จำนวนผู้เข้าร่วม:</strong>
            <span style="color: {{ $isFull ? '#ef4444' : '#16a34a' }}; font-weight: bold;">
                {{ $currentParticipants }} / {{ $max ?: 'ไม่จำกัด' }} คน
            </span>
        </p>
    </div>

    {{-- Tags Section --}}
    <div style="margin-bottom: 30px;">
        @forelse($activity->tags as $tag)
            <span class="status-badge" style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe;">
                #{{ $tag->name }}
            </span>
        @empty
            <span style="color: #9ca3af; font-size: 14px;">(ไม่มีหมวดหมู่)</span>
        @endforelse
    </div>

    <hr style="border: 0; border-top: 1px solid #f3f4f6; margin-bottom: 25px;">

    {{-- Action Buttons --}}
    <div style="display:flex; gap:12px; align-items:center; flex-wrap: wrap; justify-content: center;">

        @if($registration)
            {{-- แสดงสถานะตาม Database (จากชุดที่ 2) --}}
            @if($registration->status == 'pending')
                <button disabled class="btn" style="background:#fbbf24; color:white;">⏳ รออนุมัติการเข้าร่วม</button>
            @elseif($registration->status == 'approved')
                <button disabled class="btn" style="background:#10b981; color:white;">✅ คุณเข้าร่วมกิจกรรมนี้แล้ว</button>
            @elseif($registration->status == 'rejected')
                <button disabled class="btn" style="background:#ef4444; color:white;">❌ การสมัครถูกปฏิเสธ</button>
            @endif

        @elseif($isClosed)
            <button disabled class="btn" style="background:#9ca3af; color:white;">❌ ปิดรับสมัครแล้ว</button>

        @else
            {{-- ปุ่มสมัครพร้อม Modal ยืนยัน (รวมร่าง Logic Full/Normal) --}}
            <form id="registerForm" method="POST" action="/activities/{{ $activity->id }}/register" style="margin: 0;">
                @csrf
                @if($isFull)
                    <button type="button" onclick="openModal()" class="btn" style="background:#f97316; color:white;">
                        ⚠️ ส่งคำขอเข้าร่วม (เต็มแล้ว)
                    </button>
                @else
                    <button type="button" onclick="openModal()" class="btn" style="background:#2563eb; color:white;">
                        สมัครกิจกรรม
                    </button>
                @endif
            </form>
        @endif

        {{-- Staff Delete Button พร้อม Modal --}}
        @if(auth()->user()->role === 'staff')
            <form id="deleteForm" method="POST" action="/admin/activities/{{ $activity->id }}" style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="button" onclick="openDeleteModal()" class="btn" style="background:#dc2626; color:white;">
                    ลบกิจกรรม (Staff)
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Registration Modal --}}
<div id="confirmModal" class="modal-overlay">
    <div onclick="event.stopPropagation()" style="background:white; padding:30px; border-radius:15px; text-align:center; width: 90%; max-width: 400px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <h3 style="font-size: 20px; font-weight: bold; margin-bottom: 10px;">ยืนยันการสมัคร</h3>
        <p style="margin-bottom: 25px; color: #6b7280;">{{ $isFull ? 'กิจกรรมนี้เต็มแล้ว คุณต้องการส่งคำขอเพื่อรอคิวสำรองหรือไม่?' : 'คุณต้องการลงทะเบียนเข้าร่วมกิจกรรมนี้ใช่หรือไม่?' }}</p>
        <button onclick="submitForm()" class="btn" style="background:#2563eb; color:white; margin-right:10px;">ยืนยัน</button>
        <button onclick="closeModal()" class="btn" style="background:white; color:#374151; border:1px solid #d1d5db;">ยกเลิก</button>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="modal-overlay">
    <div onclick="event.stopPropagation()" style="background:white; padding:30px; border-radius:15px; text-align:center; width: 90%; max-width: 400px;">
        <h3 style="color:#dc2626; font-size: 20px; font-weight: bold; margin-bottom: 10px;">⚠️ ยืนยันการลบ</h3>
        <p style="margin-bottom: 25px; color: #6b7280;">ข้อมูลทั้งหมดของกิจกรรมนี้จะหายไปและไม่สามารถกู้คืนได้</p>
        <button onclick="submitDelete()" class="btn" style="background:#dc2626; color:white; margin-right:10px;">ลบข้อมูล</button>
        <button onclick="closeDeleteModal()" class="btn" style="background:white; color:#374151; border:1px solid #d1d5db;">ยกเลิก</button>
    </div>
</div>

<script>
    function openModal(){ document.getElementById('confirmModal').style.display='flex'; }
    function closeModal(){ document.getElementById('confirmModal').style.display='none'; }
    function submitForm(){ document.getElementById('registerForm').submit(); }

    function openDeleteModal(){ document.getElementById('deleteModal').style.display='flex'; }
    function closeDeleteModal(){ document.getElementById('deleteModal').style.display='none'; }
    function submitDelete(){ document.getElementById('deleteForm').submit(); }
</script>

</x-app-layout>