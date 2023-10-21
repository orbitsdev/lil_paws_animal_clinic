<div>

    @if($getRecord()->status == 'accepted')

        {{-- {{$getRecord()->patient->id}} --}}

        <a href="{{ route('download-medical-record', $getRecord()->patient->id) }}">Download</a>

    @else
        {{-- {{ ucfirst($getRecord()->status) }} --}}
        Unauthorized
    @endif

</div>
