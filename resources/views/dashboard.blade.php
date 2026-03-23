<x-app-layout>

<x-slot name="header">
<h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200">
All Activities
</h2>
</x-slot>

<div class="py-10">

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

<!-- Activities Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

@foreach($activities as $activity)

<!-- Card -->
<div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl overflow-hidden hover:scale-105 transition">

@if($activity->image)
<img src="{{ asset('storage/'.$activity->image) }}"
class="w-full h-48 object-cover">
@endif

<div class="p-5">

<h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">
{{ $activity->title }}
</h3>

<p class="text-sm text-gray-600 dark:text-gray-300">
📅 {{ $activity->date }}
</p>

<p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
📍 {{ $activity->location }}
</p>

<a href="/activities/{{ $activity->id }}"
class="text-blue-600 font-semibold hover:underline">

View Details

</a>

</div>

</div>

@endforeach

</div>

</div>

</div>

</x-app-layout>