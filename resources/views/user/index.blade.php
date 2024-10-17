<x-app-layout>



    <div class="relative overflow-x-hidden container mx-auto my-5 ">


        <h1 class="text-center text-xl font-extrabold py-2">All User List</h1>

        <table id="post" class="w-full mx-28 text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">

                <tr>
                    <th scope="col" class="px-6 py-3">#</th>
                    <th scope="col" class="px-6 py-3">Name</th>
                    <th scope="col" class="px-6 py-3">User Id</th>
                    <th scope="col" class="px-6 py-3">Action</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $i=0;
                @endphp
                @foreach ($users as $user)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">

                        <td class="px-6 py-4">{{ ++$i }}</td>

                        <td class="px-6 py-4">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->userid }}</td>


                        <td class="px-6 py-4">
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="checkbox" class="toggle toggle-success" name="status" value="1" {{ $user->status=='active' ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                       <span class="ml-2">
                                        <div class="badge {{ $user->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}  text-white font-bold">{{ $user->status }}</div>
                                    </span>
                            </form>
                        </td>


                    </tr>
                @endforeach

            </tbody>
            <tfoot></tfoot>
        </table>
        {{ $users->links() }}
    </div>

</x-app-layout>
