<x-app-layout>
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>
        </div>
    </header>
    <div class="container mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <!-- Statistik Total Pengunjung -->
            {{-- <div class="bg-white shadow-md rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Kota Terpopuler</h3>
                <ul>
                    @foreach ($topKota as $kota)
                        <li class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-gray-800 font-medium">{{ $kota->nama_kota }}</span>
                            <span class="text-gray-600">{{ number_format($kota->total_kunjungan, 0, ',', '.') }}
                                Pengunjung</span>
                        </li>
                    @endforeach
                </ul>
            </div> --}}

            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Kota Terpopuler</h3>
                <canvas id="topKotaChart" width="400" height="400"></canvas>
            </div>

            <!-- Grafik Tren Pengunjung -->
            <div class="bg-white p-4 shadow-md rounded col-span-2">
                <h2 class="text-xl font-semibold">Grafik Tren Pengunjung</h2>
                <div class="h-96">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabel Negara Pengunjung Terbanyak -->
        {{-- <div class="bg-white p-4 shadow-md rounded mt-4">
            <h2 class="text-xl font-semibold">Negara Pengunjung Terbanyak</h2>
            <table class="min-w-full divide-y divide-gray-200 mt-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Negara</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Jumlah Kunjungan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($topCountries as $pengunjung)
                        <!-- Updated variable to pass the top countries -->
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700">
                                {{ $pengunjung->negara->nama_negara ?? 'Tidak Diketahui' }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700">{{ number_format($pengunjung->total_kunjungan, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
        <div class="bg-white p-6 shadow-md rounded-lg mt-4 w-full">
            <h2 class="text-xl font-semibold">Negara Pengunjung Terbanyak</h2>
            <canvas id="countryVisitsChart" class="mt-4 w-full max-h-96"></canvas>
        </div>
    </div>

    <!-- Script untuk Chart.js dan jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('trendChart').getContext('2d');

            // Data dari PHP ke JavaScript
            const grafikData = @json($grafikData);
            const countries = @json($country);

            const colors = [
                'rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)',
            ];
            const backgroundColors = [
                'rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)',
            ];

            const labels = grafikData[Object.keys(grafikData)[0]].map(item => `${item.bulan} ${item.tahun}`);
            const datasets = [];

            // Membuat dataset untuk setiap negara
            Object.keys(grafikData).forEach((countryId, index) => {
                const countryData = grafikData[countryId];
                const jumlahKunjungan = countryData.map(item => item.jumlah_kunjungan);

                datasets.push({
                    label: countries[countryId],
                    data: jumlahKunjungan,
                    borderColor: colors[index % colors.length],
                    backgroundColor: backgroundColors[index % backgroundColors.length],
                    borderWidth: 1,
                    hidden: false
                });
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            onClick: (e, legendItem, legend) => {
                                const index = legendItem.datasetIndex;
                                const ci = legend.chart;
                                const meta = ci.getDatasetMeta(index);
                                meta.hidden = meta.hidden === null ? !ci.data.datasets[index].hidden :
                                    null;
                                ci.update();
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.dataset.label}: ${tooltipItem.raw.toLocaleString()} Pengunjung`;
                                }
                            }
                        }
                    }
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('countryVisitsChart').getContext('2d');

            const countries = @json($countries);
            const visits = @json($visits);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: countries,
                    datasets: [{
                        label: 'Jumlah Kunjungan',
                        data: visits,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Ambil data kota dan kunjungan dari Blade ke dalam JavaScript
            const kotaLabels = @json($topKota->pluck('nama_kota'));
            const kotaData = @json($topKota->pluck('total_kunjungan'));

            // Buat Pie Chart
            const ctx = document.getElementById('topKotaChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: kotaLabels,
                    datasets: [{
                        label: 'Jumlah Kunjungan',
                        data: kotaData,
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                            '#FF9F40', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'
                        ],
                        borderColor: '#fff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `${tooltipItem.label}: ${tooltipItem.raw.toLocaleString()} Pengunjung`;
                                }
                            }
                        }
                    }
                }
            });
        });

        function showNoDataMessage(chartId, message = 'Data Kosong') {
            const canvas = document.getElementById(chartId);
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;

            ctx.clearRect(0, 0, width, height); // Bersihkan canvas
            ctx.font = '16px Arial';
            ctx.fillStyle = 'gray';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(message, width / 2, height / 2);
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Validasi untuk trendChart
            const grafikData = @json($grafikData || []);
            if (Object.keys(grafikData).length === 0) {
                showNoDataMessage('trendChart');
            }

            // Validasi untuk countryVisitsChart
            const visits = @json($visits || []);
            if (!visits || visits.length === 0) {
                showNoDataMessage('countryVisitsChart');
            }

            // Validasi untuk topKotaChart
            const topKotaData = @json($topKota || []);
            if (!topKotaData || topKotaData.length === 0) {
                showNoDataMessage('topKotaChart');
            }
        });
    </script>
</x-app-layout>
