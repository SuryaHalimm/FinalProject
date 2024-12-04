<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<x-app-layout>
    <div class="min-h-full">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Report</h1>
            </div>
        </header>
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <!-- Your content -->
                <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-5">
                    <!-- Kota Dropdown -->
                    <div>
                        <select id="kota_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kota</option>
                            @foreach ($kotas as $kota)
                                <option value="{{ $kota->id }}">{{ $kota->nama_kota }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Month Dropdown -->
                    <div>
                        <select id="month"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Bulan</option>
                            @foreach (['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] as $month)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Dropdown -->
                    <div>
                        <select id="year"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Tahun</option>
                            @for ($year = 2021; $year <= date('Y'); $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Negara Dropdown -->
                    <div>
                        <select id="negara_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Asal Kebangsaan</option>
                            @foreach ($negaras as $negara)
                                <option value="{{ $negara->id }}">{{ $negara->nama_negara }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search and Download Buttons -->
                    <div class="flex space-x-2">
                        <button id="searchButton"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">Search</button>
                        <button id="downloadButton"
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400">Download
                            To PDF</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
                        <thead class="bg-blue-500 text-white">
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">Kota</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">Bulan</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">Tahun</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">Asal Kebangsaan</th>
                                <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">Jumlah Pengunjung</th>
                            </tr>
                        </thead>
                        <tbody id="results" class="text-gray-700">
                            <!-- Results will be displayed here via AJAX -->
                        </tbody>
                    </table>

                </div>

                <!-- Pagination Links -->
                <div id="pagination" class="flex justify-center mt-4 space-x-2">
                    <!-- Pagination buttons will be appended here via AJAX -->
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
<script>
    $('#searchButton').click(function() {
        loadResults(); // Call loadResults function on search button click
    });

    function loadResults(page = 1) {
        const data = {
            kota_id: $('#kota_id').val(),
            month: $('#month').val(),
            year: $('#year').val(),
            negara_id: $('#negara_id').val(),
            _token: '{{ csrf_token() }}',
            page: page // Pass page number
        };

        $.ajax({
            url: "{{ route('admin.report.search') }}",
            method: 'POST',
            data: data,
            success: function(response) {
                let rows = '';
                response.data.forEach(function(item) {
                    rows += `
                    <tr>
                        <td class="px-6 py-4 border-b border-gray-200">${item.kota ? item.kota.nama_kota : 'N/A'}</td>
                        <td class="px-6 py-4 border-b border-gray-200">${item.bulan}</td>
                        <td class="px-6 py-4 border-b border-gray-200">${item.tahun}</td>
                        <td class="px-6 py-4 border-b border-gray-200">${item.negara ? item.negara.nama_negara : 'N/A'}</td>
                        <td class="px-6 py-4 border-b border-gray-200">${item.jumlah_kunjungan.toLocaleString('id-ID')}</td>
                    </tr>
                `;
                });
                $('#results').html(rows);

                // Generate pagination links with Tailwind styling
                let paginationLinks = '';
                if (response.links) {
                    response.links.forEach(link => {
                        const isActive = link.active ? 'bg-blue-500 text-white' :
                            'bg-white text-blue-500 hover:bg-gray-200';
                        paginationLinks += `
                        <button class="pagination-link ${isActive} px-3 py-1 mx-1 rounded-md border border-blue-500"
                                data-page="${link.url ? new URL(link.url).searchParams.get('page') : ''}">
                            ${link.label}
                        </button>
                    `;
                    });
                }
                $('#pagination').html(paginationLinks);
            }
        });
    }

    // Event listener for pagination buttons
    $(document).on('click', '.pagination-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadResults(page); // Load results for selected page
        }
    });

    $('#downloadButton').click(function() {
        const data = {
            kota_id: $('#kota_id').val(),
            month: $('#month').val(),
            year: $('#year').val(),
            negara_id: $('#negara_id').val(),
            _token: '{{ csrf_token() }}'
        };

        // Kirim permintaan download PDF ke server
        $.ajax({
            url: "{{ route('admin.report.download') }}",
            method: 'POST',
            data: data,
            xhrFields: {
                responseType: 'blob' // Menangani respons sebagai blob (data biner)
            },
            success: function(response) {
                // Buat URL dari respons dan download file PDF
                const url = window.URL.createObjectURL(new Blob([response]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'laporan_kunjungan.pdf'); // Nama file PDF
                document.body.appendChild(link);
                link.click();
                link.remove();
            }
        });
    });
</script>
