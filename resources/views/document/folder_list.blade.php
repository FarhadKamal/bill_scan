<x-app-layout>

    <div class="relative overflow-x-auto container mx-auto my-5">


        <h1 class="text-center text-xl font-extrabold py-2">All Folder List</h1>

        <table id="post" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                <tr>
                    <th scope="col" class="px-6 py-3">#</th>
                    <th scope="col" class="px-6 py-3">Folder Name</th>
                    <th scope="col" class="px-6 py-3">Status</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $i=0;
                @endphp
                @foreach ($folderList as $folder)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">

                        <td class="px-6 py-4">{{ ++$i }}</td>

                        <td class="px-6 py-4">{{ $folder->folder_name }}</td>

                        <td class="px-6 py-4">
                            <form action="{{ route('folders.update', $folder->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="checkbox" name="status" value="1" {{ $folder->status ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                            </form>
                        </td>


                    </tr>
                @endforeach

            </tbody>
            <tfoot></tfoot>
        </table>
        {{ $folderList->links() }}
    </div>

</x-app-layout>
