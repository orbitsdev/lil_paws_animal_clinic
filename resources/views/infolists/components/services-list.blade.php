<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
  
    <div class="text-sm">
        <ul>
            @forelse ($getRecord()->services as $service)
                <li class="mb-1">{{ $service->name }}</li>
            @empty
                <!-- Handle the empty case if needed -->
            @endforelse
        </ul>
    </div>
    
        
        
     
</x-dynamic-component>
