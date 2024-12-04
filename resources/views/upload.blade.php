<x-app-layout>
    <div class="container mx-auto mt-10 p-5 max-w-lg">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-5">Upload CSV File</h2>

            @if(session('success'))
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

            <form action="{{ route('admin.upload.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="file" class="block text-gray-700 font-semibold mb-2">Choose CSV file</label>
                    <input type="file" name="file" id="file" class="block w-full border-2 border-dashed border-gray-300 rounded-lg p-3 text-gray-600 focus:outline-none focus:border-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300">Upload and Import</button>
            </form>
        </div>
    </div>
</x-app-layout>
