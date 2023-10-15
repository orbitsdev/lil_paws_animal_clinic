<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="border-[1px] rounded-lg p-4  shadow-md">
        
        <ul>
            @php
                $totalCost = 0; // Initialize the total cost variable
            @endphp
    
            @forelse ($getRecord()->services as $service)
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
    
    
{{--         
    <table class="w-full">
        <thead/>
        <tr>
            <td>
                Service 
            </td>
            <td>
                Cost 
            </td>
            
        </tr>
        <thead/>
        <tr>
               @forelse ($getRecord()->services as $service)
               
            <td>
              {{ $service?->name }}
            </td>
            <td>
                {{ $service?->cost }}
            </td>
                   @empty
               @endforelse

          
        </tr>
        <tr>
            <td>
                @if(!empty($getRecord()->services))
                ₱ {{$getRecord()->services->sum('cost')}}
                @endif
            </td>
            <td></td>
        </tr>
        <tbody>

        </tbody>
    </table>     --}}
     
</x-dynamic-component>
