<table>
    <thead>
        <tr colspan="6">
            <td> {{now()->format('F-Y')}}</td>
        </tr>
   
        <tr>
        <th width="25">Clinic</th>
        <th width="25">Veterinarian</th>
        <th width="25">Pet Owner</th>
        <th width="25">Scheduled Date</th>
        <th width="25">Scheduled Time</th>
        <th width="25">Appointment Extra Details</th>
        <th width="25">Pets</th>
    </tr>
    </thead>
    <tbody>
       
    @foreach($collections as $item)
        <tr>
            <td width="25">{{$item->clinic ? $item->clinic->name : ''}}</td>
            <td width="25">{{$item->veterinarian ? $item->veterinarian->first_name . ' ' . $item->veterinarian->last_name : ''}}</td>
            <td width="25">
                @if($item->patients)
                {{$item->patients[0]->animal->user->first_name . ' ' .$item->patients[0]->animal?->user?->last_name }}
            @endif
            </td>
            <td width="25">{{\Carbon\Carbon::parse($item->date)->format('F d, Y')}}</td>
            <td width="25">{{\Carbon\Carbon::parse($item->time)->format('h:i A')}}</td>
            <td width="25">{!! $item->extra_pet_info !!}</td>     
            <td width="25">
                <table>
                    <thead>
                        <tr>
                            <th> Pets</th>
                        </tr>

                    </thead>
                    <tbody>
                        <tr>
                            
                        <td>
                            @forelse ($item->patients as $subitem)
                            {{$subitem?->animal?->name}} {{$subitem->animal?->category?->name}} <br>
                            @empty
                            
                            @endforelse 
                         </td>
                        </tr>
                    </tbody>
                    
                </table>   
            </td>     
        
        </tr>
    @endforeach
    </tbody>
</table>
