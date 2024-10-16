<h1>Files</h1>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<ul>
    @foreach ($directories as $directory)
        <li><a href="{{ route('file.list', ['folder' => $directory]) }}">{{ basename($directory) }}</a></li>
    @endforeach

    @foreach ($files as $file)

        @php
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
            $folderName = strpos($file, '/') !== false ? dirname($file) : '.'; // Change 'Test' to your default folder if necessary
            $filename = basename($file); // Get the file name without the path
        @endphp

        <li>
            @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']))
                <a
                    href="{{ route('file.view', ['folder' => $folderName, 'filename' => $filename]) }}">{{ basename($file) }}</a>
            @else
                <a href="{{ Storage::disk('ftp')->url($file) }}" download>{{ basename($file) }}</a>
            @endif
        </li>
    @endforeach

</ul>

<a href="{{ route('file.list') }}">Back to root</a>
