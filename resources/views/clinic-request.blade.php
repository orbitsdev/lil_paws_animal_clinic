<x-main-layout>
    <div class="w-full h-screen flex justify-center items-center">
        <div class="w-[1200px] p-8 bg-white rounded-lg shadow-md">
            @if (Auth::user()->clinic)
            <div class="m-8 clinic-status bg-green-50 p-8 rounded-lg mb-8">
                <p class="text-3xl font-bold mb-4 text-center text-green-700">Clinic Status</p>
                @if (Auth::user()->clinic->status == 'approved')
                <p class="text-lg text-center text-green-600">Congratulations! Your clinic has been approved.</p>
                @elseif (Auth::user()->clinic->status == 'pending')
                <p class="text-lg text-center text-orange-500">Your clinic approval is pending. Please wait for confirmation. Usually, it takes around 3 days.</p>
                @elseif (Auth::user()->clinic->status == 'rejected')
                <p class="text-lg text-center text-orange-500">Your Request has been rejected Please contact the admin</p>
                @else
                <p class="text-lg text-center text-red-500">Your clinic has not been approved yet.</p>
                @endif
            </div>
            @endif

            <form action="{{ route('logout.filament') }}" method="post" class="text-center">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-full hover:bg-red-600 focus:outline-none focus:ring focus:border-blue-300">Logout</button>
            </form>

            <!-- Additional Content -->
            <div class="mt-8">
                <h2 class="text-4xl font-bold mb-4 text-center text-blue-800">Welcome to Lil Paws Animal Clinic 🐾</h2>
                <p class="text-lg text-center text-gray-700">We're thrilled to have you on board! Your request is currently being processed.</p>
                <!-- Add more content or features as needed -->
            </div>
        </div>
    </div>
</x-main-layout>
