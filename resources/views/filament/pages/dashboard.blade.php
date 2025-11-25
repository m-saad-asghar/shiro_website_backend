<x-filament::page>
    <div class="space-y-10 px-4 py-8 min-h-screen text-gray-900" data-aos="fade-up">
        {{-- Cards Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            @foreach ($cards as $card)
                <div class="bg-white rounded-2xl p-6 shadow-lg flex items-center gap-4 hover:shadow-xl transition-shadow duration-300 cursor-pointer">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center"
                         style="background: {{ $card['color'] }}33; color: {{ $card['color'] }};">
                        {{-- يمكنك وضع أيقونة هنا، مثال svg بسيط --}}
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-gray-500 font-semibold text-sm">{{ $card['label'] }}</div>
                        <div class="text-2xl font-extrabold text-gray-900">{{ $card['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            {{-- Users Growth --}}
            <div class="bg-white p-6 rounded-2xl shadow-xl" data-aos="fade-up">
                <h3 class="text-lg font-semibold text-center mb-4">Monthly User Growth</h3>
                <canvas id="usersChart" height="140"></canvas>
            </div>

            {{-- Sales Growth --}}
            <div class="bg-white p-6 rounded-2xl shadow-xl" data-aos="fade-up">
                <h3 class="text-lg font-semibold text-center mb-4">Monthly Sales Count</h3>
                <canvas id="salesChart" height="140"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            {{-- Top Agents by Sales --}}
            <div class="bg-white p-6 rounded-2xl shadow-xl" data-aos="fade-up">
                <h3 class="text-lg font-semibold text-center mb-4">Top 5 Agents by Sales</h3>
                <canvas id="agentsSalesChart" height="140"></canvas>
            </div>

            {{-- Properties by Developer --}}
            <div class="bg-white p-6 rounded-2xl shadow-xl" data-aos="fade-up">
                <h3 class="text-lg font-semibold text-center mb-4">Top 5 Developers by Properties</h3>
                <canvas id="propertiesByDeveloperChart" height="140"></canvas>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />

    <script>
        AOS.init();

        const softColors = {
            primary: 'rgba(9, 72, 52, 0.4)',
            primaryBorder: '#094834',
            secondary: 'rgba(211, 194, 148, 0.6)',
            danger: 'rgba(169, 50, 38, 0.6)',
            success: 'rgba(30, 132, 73, 0.6)',
            gray: 'rgba(159, 129, 81, 0.6)',
        };

        // Users Growth Chart
        new Chart(document.getElementById('usersChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($usersChart['labels']) !!},
                datasets: [{
                    label: 'Users',
                    data: {!! json_encode($usersChart['data']) !!},
                    backgroundColor: softColors.primary,
                    borderColor: softColors.primaryBorder,
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    fill: true,
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Sales Growth Chart
        new Chart(document.getElementById('salesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesChart['labels']) !!},
                datasets: [{
                    label: 'Sales',
                    data: {!! json_encode($salesChart['data']) !!},
                    backgroundColor: softColors.secondary,
                    borderRadius: 8,
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Top Agents Sales Chart
        new Chart(document.getElementById('agentsSalesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($agentsSalesChart['labels']) !!},
                datasets: [{
                    label: 'Sales',
                    data: {!! json_encode($agentsSalesChart['data']) !!},
                    backgroundColor: softColors.success,
                    borderRadius: 8,
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Properties by Developer Chart
        new Chart(document.getElementById('propertiesByDeveloperChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($propertiesByDeveloperChart['labels']) !!},
                datasets: [{
                    label: 'Properties',
                    data: {!! json_encode($propertiesByDeveloperChart['data']) !!},
                    backgroundColor: softColors.gray,
                    borderRadius: 8,
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</x-filament::page>
