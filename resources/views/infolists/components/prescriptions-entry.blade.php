<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="container mx-auto py-8">
        {{-- {{ $getState() }} --}}
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @forelse ($getState() as $item)
                <div class=" shadow-md p-4">
                    <h5 class="text-lg font-semibold mb-2 capitalize">{{ $item->drug }}</h5>
                    <p class="text-sm"><span class="font-semibold">Dosage:</span> {{ $item->dosage }}</p>
                    <p class="text-sm"><span class="font-semibold">Description:</span> {{ $item->description }}</p>
                </div>
            @empty
                <div class="col-span-3">
                    <div class="bg-blue-100 text-blue-500 border border-blue-400 rounded p-4">
                        No items found.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
    
    
    
</x-dynamic-component>
