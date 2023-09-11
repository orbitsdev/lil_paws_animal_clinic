<x-filament-panels::page>
    <div class="container mx-auto py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Total Patients</h5>
                <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{number_format($total_patients)}}</p>
                <p class="text-sm mt-2 text-gray-500 dark:text-gray-400">As of September 2023</p>
            </div>
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Total Appointments</h5>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{number_format($total_appointments)}}</p>
                <p class="text-sm mt-2 text-gray-500 dark:text-gray-400">This month</p>
                <a href="#" class="text-blue-500 hover:underline mt-2 dark:text-blue-400">Download</a>
            </div>
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Total Revenue</h5>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{number_format($total_revenue)}}</p>
                <p class="text-sm mt-2 text-gray-500 dark:text-gray-400">This year</p>
                <a href="#" class="text-blue-500 hover:underline mt-2 dark:text-blue-400">Download</a>
            </div>
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Total Patient Base On Pet Category</h5>

                @foreach ($categoriesWithAnimalsCount as $category)
                    <div class="flex justify-between  ">
                        <p class=" font-bold text-purple-600 dark:text-purple-400">{{ Str::plural($category->name) }} ( {{ $category->animal_count }} )</p>
                    </div>
                @endforeach
            </div>
            
            
            
            
            
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Appointment Types</h5>
                <ul class="list-inside list-none  pl-4">
                    <li class="text-sm text-gray-700 dark:text-gray-400">Follow-up: 250</li>
                    <li class="text-sm text-gray-700 dark:text-gray-400">New Patient: 100</li>
                    <li class="text-sm text-gray-700 dark:text-gray-400">Specialty Consultation: 50</li>
                    <!-- Add more list items as needed -->
                </ul>
            </div>
            <div class="bg-white shadow-lg dark:bg-gray-800 p-4">
                <h5 class="text-lg font-semibold mb-2 dark:text-gray-300">Top Referring Doctors</h5>
                <ul class="list-inside list-none  pl-4">
                    <li class="text-sm text-gray-700 dark:text-gray-400">Dr. Smith: 75 referrals</li>
                    <li class="text-sm text-gray-700 dark:text-gray-400">Dr. Johnson: 60 referrals</li>
                    <li class="text-sm text-gray-700 dark:text-gray-400">Dr. Williams: 45 referrals</li>
                    <!-- Add more list items as needed -->
                </ul>
            </div>
        </div>
    </div>
    
</x-filament-panels::page>
