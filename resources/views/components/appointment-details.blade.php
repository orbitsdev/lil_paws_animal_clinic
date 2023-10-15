<x-main-layout>
    <div class="bg-white dark:bg-gray-900 min-h-screen text-primary p-4">
        <div class="mx-auto max-w-lg">
            <div class="text-2xl font-semibold mb-4">Clinic Information:</div>
            <ul class="list-disc pl-6">
                <li><strong>Clinic:</strong> {{ $record->clinic->name ?? '' }}</li>
                <li><strong>Owner:</strong> {{ $record->user->name ?? '' }}</li>
                @if($record->veterinarian)
                    <li><strong>Veterinarian:</strong> {{ optional($record->veterinarian)->user->name ?? '' }}</li>
                @endif
            </ul>
    
            @if($record->date)
                <div class="text-2xl font-semibold mt-4">Patient Information:</div>
                <ul class="list-disc pl-6">
                    <li><strong>Date of Birth:</strong> {{ \Carbon\Carbon::parse($patient->animal->date_of_birth ?? '')->format('F j Y') }}</li>
                </ul>
            @endif
    
            @if($record->time)
                <div class="text-2xl font-semibold mt-4">Appointment Time:</div>
                <p>{{ \Carbon\Carbon::parse($record->time)->format('h:i A') }}</p>
            @endif
    
            <div class="text-2xl font-semibold mt-4">Additional Information:</div>
            <p>{{ $record->extra_info ?? '' }}</p>
    
            @forelse ($record->patients as $patient)
                <div class="rounded-lg shadow-lg p-6 my-4 bg-card text-card-text">
                    <div class="flex items-center">
                        @if($patient->animal->image)
                            <a href="{{Storage::disk('public')->url($patient->animal->image)}}" target="_blank">
                                <img src="{{ Storage::disk('public')->url($patient->animal->image) }}" class="max-w-sm rounded-lg shadow-2xl mr-4" />
                            </a>
                        @endif
                        <div>
                            <h1 class="text-xl font-semibold">{{ $patient->animal->name }}</h1>
                            <ul>
                                <li><strong>Breed:</strong> {{ $patient->animal->breed }}</li>
                                <li><strong>Sex:</strong> {{ $patient->animal->sex }}</li>
                                <li><strong>Weight:</strong> {{ $patient->animal->weight }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <!-- No patients found -->
            @endforelse
        </div>
    </div>
    
    
    
        
</x-main-layout>