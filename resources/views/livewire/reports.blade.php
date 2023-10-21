<div>
    <div class="container mx-auto py-8">
        <h5 class="text-xl font-bold mb-4 dark:text-gray-300">Monthly Report</h5>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4">
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Upcoming Schedule</h5>
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{count($upcoming_schedule)}}</p>
    
                @if(count($upcoming_schedule) > 0)
                <div class="flex items-center justify-end mt-4">
                    <a href="/upcoming-appointment" target="_blank" class="underline text-blue-600 flex items-center text-indigo-600 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </a>
                </div>
                @endif
            </div>
{{--     
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Total Patient</h5>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{number_format($total_patients)}}</p>
    
                @if($total_patients > 0)
                <div class="flex items-center justify-end mt-4">
                    <a href="/total-patient" target="_blank" class="underline text-blue-600 flex items-center  dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </a>
                </div>
                @endif
            </div> --}}
    
            {{-- <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Total Revenue</h5>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">₱ {{number_format($total_revenue)}}</p>
    
                @if($total_revenue > 0)
                <div class="flex items-center justify-end mt-4">
                    <a href="/total-revenue" target="_blank" class="underline text-blue-600 flex items-center  dark:text-purple-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download
                    </a>
                </div>
                @endif
            </div> --}}
        </div>
    </div>
    
</div>
