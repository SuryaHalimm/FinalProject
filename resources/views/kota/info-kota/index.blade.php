<head>
    <title>{{ $kota->nama_kota }}</title>
</head>
<x-app-layout>
    <div class="min-h-full bg-gray-100">
        <header class="bg-white shadow mb-6">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $kota->nama_kota }}</h1>
            </div>
        </header>
        <main>
            <div class="container mx-auto flex flex-wrap">
                <!-- Kolom Kiri: Deskripsi dan Gambar -->
                <div class="w-full lg:w-2/3 px-4">
                    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                        <h2 class="text-2xl font-semibold mb-4">Deskripsi</h2>
                        <p class="text-gray-700 mb-6 text-justify leading-relaxed">{{ $kota->deskripsi }}</p>
                        <div class="flex flex-wrap gap-4">
                            <div class="w-full sm:w-1/2 lg:w-1/3">
                                <img src="{{ asset('storage/image/' . $kota->nama_kota . '.jpg') }}" alt="{{ $kota->nama_kota }}" class="w-full h-50 object-cover rounded-lg shadow-md">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grafik Pengunjung dan Prediksi -->
                    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
                        <h2 class="text-2xl font-semibold mb-4">Grafik Pengunjung dan Prediksi</h2>
                        <div class="bg-gray-200 rounded-lg overflow-hidden">
                            <canvas id="visitorChart" class="w-full h-96"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Tabel Negara Pengunjung -->
                <div class="w-full lg:w-1/3 px-4 mt-6 lg:mt-0">
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <h2 class="text-xl font-semibold mb-4">Negara Pengunjung</h2>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Negara</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Jumlah Pengunjung
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($negaraPengunjung as $pengunjung)
                                    <tr class="country-click cursor-pointer"
                                        data-country="{{ $pengunjung->nama_negara }}">
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ $pengunjung->nama_negara ?? 'Tidak Diketahui' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">
                                            {{ number_format($pengunjung->total_kunjungan) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>

@if (!empty($data))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.0.2"></script>
    <script>
        const data = @json($data);
        const ctx = document.getElementById('visitorChart').getContext('2d');

        const datasets = [];
        let labels = [];
        const colors = [
            'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
            'rgba(201, 203, 207, 1)', 'rgba(255, 99, 71, 1)',
        ];
        let colorIndex = 0;
        let forecastStartIndex = 0;

        for (const country in data) {
            const actualData = JSON.parse(data[country]['historical_data'] || '{}');
            const forecastData = JSON.parse(data[country]['forecasted_data'] || '{}');

            if (actualData && actualData.data && forecastData && forecastData.data) {
                const actualValues = actualData.data;
                const forecastValues = forecastData.data;

                labels = actualData.index.concat(forecastData.index);

                if (forecastStartIndex === 0) {
                    forecastStartIndex = actualValues.length;
                }

                datasets.push({
                    label: `${country}`,
                    data: actualValues.concat(forecastValues),
                    borderColor: colors[colorIndex % colors.length],
                    backgroundColor: colors[colorIndex % colors.length].replace('1)', '0.2)'),
                    fill: false,
                });

                colorIndex++;
            }
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets,
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    annotation: {
                        annotations: {
                            forecastDivider: {
                                type: 'line',
                                xMin: forecastStartIndex - 0.5,
                                xMax: forecastStartIndex - 0.5,
                                borderColor: 'rgba(0, 0, 0, 0.5)',
                                borderWidth: 2,
                                borderDash: [6, 6],
                            }
                        }
                    }
                }
            }
        });
    </script>
@endif
