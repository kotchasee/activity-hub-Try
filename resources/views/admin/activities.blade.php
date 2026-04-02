<x-app-layout>

    <div class="py-10 bg-orange-50 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold text-orange-600 mb-6">
                Approve Activities
            </h1>

            @foreach($activities as $act)

            <div x-data="{ open: false }"
                class="bg-white rounded-2xl shadow-md p-6 mb-6 border border-orange-100">

                <!-- HEADER -->
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">
                        {{ $act->title }}
                    </h3>

                    <button @click="open = !open"
                        class="text-orange-500 font-semibold hover:underline">
                        View Details
                    </button>
                </div>

                <p class="text-gray-600 mt-1">
                    📅 {{ $act->date }}
                </p>

                <p class="mt-1 font-semibold
                    @if($act->status === 'approved') text-green-500
                    @elseif($act->status === 'rejected') text-red-500
                    @else text-yellow-500
                    @endif
                ">
                    Status: {{ $act->status }}
                </p>

                <!-- 🔽 FULL DETAILS -->
                <div x-show="open" x-transition class="mt-4 border-t pt-4 text-gray-700 space-y-2">

                    <p><strong>📝 Description:</strong> {{ $act->description ?? '-' }}</p>

                    <p><strong>📍 Location:</strong> {{ $act->location ?? '-' }}</p>

                    <p><strong>⏳ Registration Deadline:</strong> {{ $act->registration_deadline ?? '-' }}</p>

                    <p><strong>👥 Max Participants:</strong> {{ $act->max_participants ?? '-' }}</p>

                    <p><strong>👤 Created By:</strong> {{ $act->user->name ?? '-' }}</p>

                    <p><strong>📅 Created At:</strong> {{ $act->created_at ?? '-' }}</p>

                    <p><strong>🕒 Updated At:</strong> {{ $act->updated_at ?? '-' }}</p>

                    <!-- IMAGE -->
                    @if($act->image)
                        <div class="mt-3">
                            <img src="{{ str_starts_with($act->image, 'http') ? $act->image : asset('storage/'.$act->image) }}"
                                class="w-full h-56 object-cover rounded-lg">
                        </div>
                    @endif

                    <!-- TAGS -->
                    @if(isset($act->tags))
                        <div class="mt-2">
                            <strong>🏷 Tags:</strong><br>
                            @foreach($act->tags as $tag)
                                <span class="bg-orange-100 text-orange-700 text-xs px-2 py-1 rounded mr-1">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                </div>

                <!-- BUTTONS -->
                <div class="flex gap-3 mt-4">

                    <form method="POST" action="/admin/activities/{{ $act->id }}/approve">
                        @csrf
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg transition">
                            Approve
                        </button>
                    </form>

                    <form method="POST" action="/admin/activities/{{ $act->id }}/reject">
                        @csrf
                        <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition">
                            Reject
                        </button>
                    </form>

                </div>

            </div>

            @endforeach

        </div>
    </div>

</x-app-layout>