<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    
    <div class="text-sm border-t pt-2">
        <ul>
            @forelse ($getRecord()->services as $service)
                <li class="mb-2">{{ $service->name }} - ₱ {{number_format($service?->cost) ?? '0' }} </li>
            @empty
                <!-- Handle the empty case if needed -->
            @endforelse
        </ul>
        <div class="border-t pt-2 flex items-center justify-end">
            <p class="font-bold">
                Subtotal :
                @if(!empty($getRecord()->services))

                ₱ {{number_format($getRecord()->services->sum('cost'))}}
                @endif
            </p>
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
