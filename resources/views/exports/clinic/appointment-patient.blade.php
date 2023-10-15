<table>
    <thead>
        <tr colspan="6">
            <td> {{now()->format('F-Y')}}</td>
        </tr>
   
        <tr>
        <th   width="25">Clinic</th>
        <th   width="25">Pet Name</th>
        <th   width="25">Pet Owner</th>
        <th   width="25">Created at</th>
        <th   width="25">Examination</th>
        <th   width="25">Prescriptions</th>

    </tr>
    </thead>
    <tbody>
       
    @foreach($collections as $item)
        <tr>
            <td   width="25">{{$item->clinic ? $item->clinic->name : ''}}</td>
            <td   width="25"> {{$item?->animal?->name}} {{$item->animal?->category?->name}}</td>
            <td   width="25">
              {{$item?->animal?->user?->first_name }} {{$item?->animal?->user?->last_name}}
            </td>

            <td   width="35">
                {{$item->created_at->format('F d, Y h:i A')}}
            </td>

            <td   width="40">
                    @if($item->examination)
                    <p></p>
                    <p>Exam Type: {{$item->examination->exam_type}} </p>
                    <p></p>
                    <p>Temperature: {{$item->examination->temperature}} </p>
                    <p></p>
                    <p>Crt: {{$item->examination->crt}} </p>
                    <p></p>
                    <p>Exam Result: {{$item->examination->exam_result}} </p>
                    <p></p>
                    <p>Diagnosis: {{$item->examination->diagnosis}} </p>
                    <p></p>

                    @else
                    <p>NONE</p>
                    @endif
            </td>
            
            <td   width="40">
                    @if($item->examination?->prescriptions)

                    @forelse($item->examination->prescriptions as $prescription)
                    <p></p>
                    <p>Drug: {{$prescription->drug}} </p>
                    <p></p>
                    <p>Dosage: {{$prescription->dosage}} </p>
                    <p></p>
                    <p>Description: {{$prescription->description}} </p>
                    <p></p>
                    @empty
                    <p>NONE</p>
                    @endforelse

                    @else
                    <p>NONE</p>


                    @endif
            </td>


        
        </tr>
    @endforeach
    </tbody>
</table>
