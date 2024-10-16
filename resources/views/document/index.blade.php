<link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<x-app-layout>

    <style>
        #users-table thead th {

        }


        #users-table tbody tr .f_link {
            background-color: #ffffff;
           color: blue;
           font-weight: 900;

        }
    </style>

    <div class="container mx-auto my-5 ">
        <table id="users-table" class="display  ">
            <thead>
                <tr>

                    <th>User</th>
                    <th>File</th>
                    <th>Remarks</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            console.log(document.data);
            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('document.data') }}',
                columns: [
                    {
                        data: 'name',
                        className: 'text-center'
                    },
                    {
                        data: 'file_link',
                        orderable: false,
                        searchable: false,
                        className: ' f_link text-center',

                    },
                    {
                        data: 'remarks',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            // Format the date (assuming data is in ISO 8601 format)
                            return new Date(data).toLocaleDateString(
                            'en-GB'); // Change 'en-GB' to your preferred locale
                        },
                        className: 'text-center'
                    },
                ]
            });
        });
    </script>

</x-app-layout>
