
    <table>
        <thead>
            <tr>
                <td colspan="5">{{now()->format('F-Y')}}</td>
            </tr>
            <tr>
                <th width="35">Source</th>
                <th width="35">Title</th>
                <th width="35">Description</th>
                <th width="35">Amount</th>
                <th width="35">Recorded</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $item)
            <tr>
                <td>
                    @if($item->patient && $item->patient->appointment)
                    From Appointment {{\Carbon\Carbon::parse($item->patient->appointment->date)->format('F d, Y')}}
                    @else
                    From Clinic {{$item->clinic ? $item->clinic->name : ''}}
                    @endif
                </td>
                <td>{{$item->title}}</td>
                <td>{{$item->description}}</td>
                <td>
                    @if(!empty($item->amount))
                    {{number_format($item->amount)}}
                    @endif
                </td>
                <td> {{$item->created_at->format('F-d-Y h:i A') }} </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4">Total</td>
                <td>{{number_format($collections->sum('amount'))}}</td>
            </tr>
        </tbody>
    </table>

