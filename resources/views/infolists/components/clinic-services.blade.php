<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="border-[1px] rounded-lg p-4  shadow-md">
        
        <ul>
            @php
                $totalCost = 0; // Initialize the total cost variable
            @endphp
    
            @forelse ($getRecord()->clinicServices as $service)
                @php
                    $cost = $service->cost;
                    $totalCost += $cost; // Add the cost to the total
                @endphp
                <li class="mb-2 flex justify-between">
                    <div class="">{{ $service->name }}</div>
                    <div class="">₱ {{ number_format($cost) }}</div>
                </li>
            @empty
                <li class="">No services added yet.</li>
            @endforelse
        </ul>
    
        <div class="border-t mt-4 py-2 flex justify-between">
            <div class="font-bold ">Subtotal:</div>
            <div class="font-bold ">₱ {{ number_format($totalCost) }}</div>
        </div>
        <div class="border-t py-2 flex justify-between">
            <div class="font-bold ">Total:</div>
            <div class="font-bold ">₱ {{ number_format($totalCost) }}</div>
        </div>
    </div>
</x-dynamic-component>
