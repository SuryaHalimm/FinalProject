<x-app-layout>
    <div class="min-h-full">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Kota</h1>

                <!-- Tampilkan tombol tambah kota hanya untuk admin -->
                @hasrole('admin')
                    <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                        onclick="openModal('addKotaModal')">Tambah Kota</button>
                @endhasrole
            </div>
        </header>
        <main>
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="flex flex-wrap gap-5 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                @if ($kota->isEmpty())
                    <div class="w-full text-center">
                        <p class="text-lg text-gray-500">Data Kosong</p>
                    </div>
                @else
                    @foreach ($kota as $city)
                        <div class="max-w-sm rounded overflow-hidden shadow-lg flex flex-col">
                            <!-- Link ke halaman detail kota hanya mencakup gambar dan nama kota -->
                            <a href="{{ route('kota.info', encrypt($city->id)) }}"
                                class="group relative h-72 w-full overflow-hidden">
                                <img src="{{ asset('storage/image/' . $city->nama_kota . '.jpg') }}"
                                    alt="{{ $city->nama_kota }}" class="h-72 w-full object-cover object-top" />
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-5xl text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100">
                                    {{ $city->nama_kota }}
                                </div>
                            </a>
                            <div class="px-6 py-4">
                                <div class="font-bold text-xl mb-2">{{ $city->nama_kota }}</div>
                                <p class="text-gray-700 text-base text-justify">
                                    {{ Str::limit($city->deskripsi, 150, '...') }}
                                </p>
                            </div>

                            <!-- Tampilkan tombol edit dan hapus hanya untuk admin -->
                            @hasrole('admin')
                                <div class="px-6 pt-4 pb-2 flex justify-between">
                                    <button
                                        class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 edit-btn"
                                        data-id="{{ encrypt($city->id) }}" data-nama="{{ $city->nama_kota }}"
                                        data-deskripsi="{{ $city->deskripsi }}"
                                        onclick="openEditModal('{{ encrypt($city->id) }}', '{{ $city->nama_kota }}', '{{ $city->deskripsi }}')">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.kota.destroy', encrypt($city->id)) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600"
                                            onclick="return confirm('Yakin ingin menghapus kota ini?')">Hapus</button>
                                    </form>
                                </div>
                            @endhasrole
                        </div>
                    @endforeach
                @endif
            </div>
        </main>
    </div>

    <!-- Modal Tambah Kota -->
    <div id="addKotaModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-75 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4">Tambah Kota</h2>
            <form action="{{ route('admin.kota.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="nama_kota" class="block text-sm font-medium text-gray-700">Nama Kota:</label>
                    <input type="text" name="nama_kota" id="nama_kota"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        required>
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi:</label>
                    <textarea name="deskripsi" id="deskripsi"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="mb-4">
                    <label for="gambar" class="block text-sm font-medium text-gray-700">Upload Gambar:</label>
                    <input type="file" name="gambar" id="gambar"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        accept="image/*" onchange="previewImage(event, 'addImagePreview')">
                    <img id="addImagePreview" src="#" alt="Pratinjau Gambar"
                        class="hidden mt-4 w-full h-40 object-cover rounded-md shadow-md">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeModal('addKotaModal')"
                        class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Batal</button>
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kota -->
    @if ($kota->isEmpty())

    @else
        <div id="editKotaModal"
            class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h2 class="text-2xl font-semibold mb-4">Edit Kota</h2>
                <form action="{{ route('admin.kota.update', encrypt($city->id)) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="edit_nama_kota" class="block text-sm font-medium text-gray-700">Nama Kota:</label>
                        <input type="text" id="edit_nama_kota" name="nama_kota"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="edit_deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi:</label>
                        <textarea id="edit_deskripsi" name="deskripsi"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_gambar" class="block text-sm font-medium text-gray-700">Upload Gambar:</label>
                        <input type="file" name="gambar" id="edit_gambar"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            accept="image/*" onchange="previewImage(event, 'editImagePreview')">
                        <img id="editImagePreview" src="#" alt="Pratinjau Gambar"
                            class="hidden mt-4 w-full h-40 object-cover rounded-md shadow-md">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeModal('editKotaModal')"
                            class="mr-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">Batal</button>
                        <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <!-- JavaScript for Modal and Image Preview -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Fungsi untuk menampilkan pratinjau gambar saat file dipilih
        function previewImage(event, previewId) {
            const input = event.target;
            const reader = new FileReader();

            reader.onload = function() {
                const preview = document.getElementById(previewId);
                preview.src = reader.result;
                preview.classList.remove('hidden'); // Tampilkan pratinjau setelah gambar dimuat
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            }
        }

        function openEditModal(id, nama, deskripsi) {
            document.getElementById('edit_nama_kota').value = nama;
            document.getElementById('edit_deskripsi').value = deskripsi;

            // Sembunyikan preview gambar jika tidak ada gambar baru yang dipilih
            const editPreview = document.getElementById('editImagePreview');
            editPreview.classList.add('hidden');
            editPreview.src = "#";

            openModal('editKotaModal');
        }
    </script>
</x-app-layout>
