<x-app-layout>

    <h1 class="text-center text-xl font-extrabold underline shadow-xl pb-2"> Upload Bill  Document</h1>

    <div class=" container mx-auto py-2 my-4 ">
        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <div class="max-w-sm mx-auto card card-compact bg-base-100 w-96 shadow-xl p-5 bg-lime-200">
            <form action="/upload" class="max-w-sm mx-auto" method="POST" enctype="multipart/form-data">
                @csrf

                <label class="form-control w-full max-w-xs">
                    <div class="label">
                      <span class="label-text">Select Destination Folder</span>

                    </div>
                    <select name="folder" class="select select-bordered">
                        <option disabled selected>Pick one</option>
                        @foreach ($subfolders as $subfolder)
                        <option value="{{ $subfolder }}">{{ $subfolder }}</option>
                    @endforeach
                      </select>
                    <x-input-error :messages="$errors->get('folder')" class="mt-2" />

                  </label>


                  <label class="form-control w-full max-w-xs">
                    <div class="label">
                      <span class="label-text">Pick a file</span>
                    </div>
                    <input type="file" name="file" class="file-input file-input-bordered w-full max-w-xs" accept=".jpg,.pdf,.docx,.doc,.xls,.xlsx"/>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                  </label>

                  <label class="form-control w-full max-w-xs">
                    <div class="label">
                      <span class="label-text">Select Destination Folder</span>

                    </div>
                    <textarea name="remarks" class="textarea textarea-bordered h-24" placeholder="Remarks">{{ old('remarks') }}</textarea>

                  </label>

                <div class="flex justify-center my-4 space-x-2">

                    <button  class="btn btn-sm btn-success" type="submit">Upload</button>
                    <a href="/"  class=" btn btn-sm btn-neutral" >Back To List</a>
                </div>
            </form>
        </div>


    </div>


</x-app-layout>
