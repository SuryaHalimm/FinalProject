<x-app-layout>
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Daftar Pengguna</h1>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="text-left px-4 py-2 border-b">No</th>
                        <th class="text-left px-4 py-2 border-b">Nama</th>
                        <th class="text-left px-4 py-2 border-b">Email</th>
                        <th class="text-left px-4 py-2 border-b">Role</th>
                        <th class="text-left px-4 py-2 border-b">Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-4 py-2 border-b">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 border-b">{{ $user->name }}</td>
                            <td class="px-4 py-2 border-b">{{ $user->email }}</td>
                            <td class="px-4 py-2 border-b">
                                {{ implode(', ', $user->getRoleNames()->toArray() ?? ['Tidak ada']) }}
                            </td>
                            <td class="px-4 py-2 border-b">{{ $user->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
