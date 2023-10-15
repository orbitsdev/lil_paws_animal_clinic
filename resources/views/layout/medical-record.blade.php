<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Medical Record Report</title>
    <style>

        html{
            margin: 0;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 0;
            margin: 0;
        }

        .container {
           
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-weight: bold;
        }

        .section-content {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f8f8f8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-style: italic;
        }
        .title-header{
            text-align: center;
            border-bottom: 5px solid ;
            padding: 16px 0px;
        }

        .title-header p{
            margin: 0;
            line-height: 1.5rem;
        }

        .title-header .p1{
            font-weight: bold;
        }
        .title-header .p3{
            font-size: 14px;
        }

        .title-h1{
            font-size: 24px;
        }
        @media print {
    body {
        font-family: Arial, sans-serif;
        padding: 0;
        margin: 0;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        page-break-after: always; /* Ensure each container starts on a new page */
    }

    /* Add page breaks as needed within sections */
    .section {
        page-break-inside: avoid; /* Avoid splitting sections across pages */
        margin-bottom: 20px;
    }

    h1 {
        text-align: center;
    }

    /* Add more specific styles for page breaks as needed */
    /* For example, you can use .section-title for titles that shouldn't split across pages */

    /* Customize other print styles as needed */
    /* ... */

    .title-admission{
        font-size: 16px;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="title-header">

            <p class="p1">LIL PAWS ANIMAL CLINIC</p>
            <p class="p2">Mindanao, Kabacan</p>
            <p class="p3">{{now()->format('F d, Y')}}</p>
        </div>
        <h1 class="title-h1">Medical Record Report</h1>

        <div class="section">
            <p class="section-title">Patient Details</p>
            <div class="section-content">
                <p><strong>Patient Name:</strong> {{$patient->animal?->name}}</p>
                <p><strong>Patient Owner:</strong> {{$patient->animal->user?->first_name}} {{$patient->animal->user?->last_name}} </p>
            </div>
        </div>

        @if($patient->examination)
        <div class="section">
            <p class="section-title">Examination Details</p>
            <div class="section-content">
                <table>
                    <tr>
                        <th>Exam Type</th>
                        <th>Examination Date</th>
                        <th>Temperature</th>
                        <th>CRT</th>
                    </tr>
                    <tr>
                        <td>
                            {{ucfirst($patient?->examination?->exam_type)}}

                        </td>
                        <td>{{Carbon\Carbon::parse($patient?->examination?->examination_date)->format('F d, Y')}}</td>
                        <td>
                            @if($patient->examination->temperature)
                            {{$patient?->examination?->temperature}}°C

                            @endif
                        
                        </td>
                        <td>{{$patient?->examination?->crt}}</td>
                    </tr>
                </table>
                <p><strong>Exam Result:</strong> {{$patient?->examination?->exam_result}}</p>
                <p><strong>Diagnosis:</strong> {{$patient?->examination?->diagnosis}}</p>
            </div>
        </div>
        @endif
        

        @if($patient->examination?->prescriptions)
        <div class="section">
            <p class="section-title">Prescriptions</p>
            <div class="section-content">
                <table>
                    <tr>
                        <th>Drug</th>
                        <th>Dosage</th>
                        <th>Description</th>
                    </tr>

                    @forelse ($patient->examination?->prescriptions as $prescription)
                    <tr>
                        <td>{{$prescription?->drug}}</td>
                        <td>{{$prescription?->dosage}}</td>
                        <td>{{$prescription?->description}}</td>
  
                    </tr>
                    @empty
                        
                    @endforelse
                 
                </table>
            </div>
        </div>
    @endif

    @if($patient->examination?->treatments)
        <div class="section">
            <p class="section-title">Treatments</p>
            <div class="section-content">
                <table>
                    <tr>
                        <th>Treatment</th>
                        <th>Treatment Price</th>
                        <th>Treatment Date</th>
                    </tr>
                    @forelse ($patient->examination?->treatments as $treatment)
                    <tr>
                        <td>{{$treatment?->treatment}}</td>
                        <td>
                            @if($treatment->treatment_price)
                            {{number_format($treatment->treatment_price)}}
                            @endif
                        
                        </td>
                        <td>
                            @if($treatment->treatment_date)
                            {{Carbon\Carbon::parse($treatment?->treatment_date)->format('F d, Y')}}    
                           
                            @endif
                        </td>
  
                    </tr>
                    @empty
                        
                    @endforelse
                </table>
            </div>
        </div>
        @endif
    @if($patient->admissions)
        <div class="section">
            <p class="section-title">Admissions Reports</p>
            <div class="section-content">
                <table>
                    <tr>
                        <th>Admission Date</th>
                        <th>Admission Time</th>
                        <th>Status</th>
                    </tr>
                    @forelse ($patient->admissions as $admission)
                    <tr>
                       
                        <td>
                            @if($admission->admission_date)
                            {{Carbon\Carbon::parse($admission?->admission_date)->format('F d, Y')}}    
                           
                            @endif
                        </td>
                        <td>
                            @if($admission->admission_time)
                            {{Carbon\Carbon::parse($treatment?->admission_time)->format('h:i A')}}    
                           
                            @endif
                        </td>
                        <td>
                           {{$admission?->status}}
                        </td>
  
                    </tr>
                    @empty
                        
                    @endforelse
                </table>
            </div>
        </div>
        @endif
        @if($patient->admissions)
        <div class="section">
            <p class="section-title">Admission Treatment Plans</p>
            <div class="section-content">
                @foreach ($patient->admissions as $admission)
                    @if ($admission->admission_date)
                        <p class="title-admission">Admission Date: {{ Carbon\Carbon::parse($admission->admission_date)->format('F d, Y') }}</p>
                    @endif
    
                    <table>
                        <tr>
                            <th> Drug</th>
                            <th> Dosage</th>
                            <th> Date</th>
                            <th> Time</th>
                            <th> Remarks</th>
                        </tr>
                        @forelse ($admission->treatmentplans as $treatmentplan)
                            <tr>
                                <td>{{ $treatmentplan?->drug }}</td>
                                <td>{{ $treatmentplan?->dosage }}</td>
                                <td>
                                    @if($treatmentplan->date)
                                        {{ Carbon\Carbon::parse($treatmentplan->date)->format('F d, Y') }}
                                    @endif
                                </td>
                                <td>
                                    @if($treatmentplan->time)
                                        {{ Carbon\Carbon::parse($treatmentplan->time)->format('h:i A') }}
                                    @endif
                                </td>
                                <td>{{ $treatmentplan?->remarks }}</td>
                            </tr>
                            {{-- Include monitors for this treatment plan --}}
                            @forelse ($treatmentplan->monitors as $monitor)
                                <tr>
                                    <td colspan="5">
                                        <strong>Monitor Date:</strong> 
                                        @if($monitor->date)
                                            {{ Carbon\Carbon::parse($monitor->date)->format('F d, Y') }}
                                        @endif
                                        <br>
                                        <strong>Monitor Time:</strong> 
                                        @if($monitor->time)
                                            {{ Carbon\Carbon::parse($monitor->time)->format('h:i A') }}
                                        @endif
                                        <br>
                                        <strong>Monitor Activity:</strong> {{ $monitor?->activity }}<br>
                                        <strong>Monitor Details:</strong> {{ $monitor?->details }}<br>
                                        <strong>Monitor Observation:</strong> {{ $monitor?->observation }}<br>
                                        <strong>Monitor Remarks:</strong> {{ $monitor?->remarks }}<br>
                                    
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No monitors found for this treatment plan.</td>
                                </tr>
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="5">No treatment plans found for this admission.</td>
                            </tr>
                        @endforelse
                    </table>
                @endforeach
            </div>
        </div>
    @endif

    @if($patient->payments)
    <div class="section">
        <p class="section-title">Payment Information</p>
        <div class="section-content">
            <table>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
              
                @forelse ($patient->payments as $payment)
                    <tr>
                        <td>{{ $payment?->title }}</td>
                        <td>{{ $payment?->description }}</td>
                        <td>{{ number_format($payment?->amount) }}</td>
                    </tr>
                   
                @empty
                    <tr>
                        <td colspan="3">No payment records found.</td>
                    </tr>
                @endforelse
                {{-- Display the total amount --}}
                @if ($patient->payments)
                    <tr>
                        <td colspan="2"><strong>Total Amount:</strong></td>
                        <td><strong> Php {{ number_format($patient->payments->sum('amount')) }}</strong></td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
@endif
    
    
    

        <div class="footer">
            &copy; {{now()->year}} Lil Paw Animal Clinic
        </div>
    </div>
</body>
</html>
