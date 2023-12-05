<h1 class="bolder" style="text-transform: uppercase; font-weight: bolder"> 

    @auth
    @if (Auth::user()->hasAnyRole(['Veterenarian']) && (!empty(Auth::user()->clinic)))
    {{Auth::user()->clinic->name}} CLINIC    
    
    @elseif (Auth::user()->hasAnyRole(['Admin']))
    LIL PAW ANIMAL CLINIC Admin
    @else
    LIL PAW ANIMAL CLINIC
    @endif

    @endauth
    

</h1>