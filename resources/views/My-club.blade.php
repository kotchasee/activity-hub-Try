<x-app-layout>

<h1>My club</h1>

@foreach($activities as $act)

<div style="border:1px solid black; margin:10px; padding:10px;">

    <h3>{{ $act->title }}</h3>

    <p>Status: {{ $act->status }}</p>

    @if($act->status == 'pending')
        <p style="color:orange;">รออนุมัติ</p>
    @elseif($act->status == 'approved')
        <p style="color:green;">อนุมัติแล้ว</p>
    @else
        <p style="color:red;">ถูกปฏิเสธ</p>
    @endif

</div>

@endforeach

</x-app-layout>