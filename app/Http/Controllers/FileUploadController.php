<?php

namespace App\Http\Controllers;

use App\Models\DocumentHistory;
use App\Models\SerialNumber;
use App\Models\FolderStore;
use App\Models\filehistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;


class FileUploadController extends Controller
{
    public function index()
    {
        // $this->getSubfolders();
        $documents = DocumentHistory::with('getByUser:userid,name')->get();
        return view('document.index');
    }

    public function addFolder(Request $request)
    {

        $request->validate([
            'folder_name' => 'required|string|max:255',
        ]);

        $disk = Storage::disk('ftp');
        $folderName = $request->folder_name;

        if ($disk->exists($folderName)) {
            return redirect()->back()->with('error', "Folder '$folderName' already exists.");
        }

        if ($disk->makeDirectory($folderName)) {
            return redirect()->back()->with('success', "Folder '$folderName' created successfully.");
        } else {
            return redirect()->back()->with('error', "Failed to create folder '$folderName.");
        }
    }

    public function folderList()
    {
        $this->getSubfolders();
        $folderList = FolderStore::orderByDesc("status")->paginate(15);

        return view('document.folder_list', compact('folderList'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'nullable|boolean', // Make sure status is boolean
        ]);

        // Find the folder and update its status
        $folder = FolderStore::findOrFail($id);
        $folder->status = $request->has('status') ? 1 : 0; // Check if status is sent
        $folder->save();

        return redirect()->back()->with('success', 'Folder status updated successfully!');
    }

    public function uploadGet()
    {
        $successMessage = null;


        if (session()->has('serial') && (now()->timestamp - session()->get('success_time') <= 20)) {
            $successMessage = session()->get('serial');
        } else {

            session()->forget(['serial', 'success_time']);
        }
        $subfolders = FolderStore::where('status', 1)->orderByDesc('id')->pluck('folder_name');
        // $subfolders = $this->getSubfolders();
        return view('upload', compact('subfolders', 'successMessage'));
    }



    public function getDocuments()
    {
        $user = Auth()->user();

        $documents = DocumentHistory::with('getByUser:userid,name');

        if ($user->role === 'admin') {
            $documents = $documents->get();
        } else {

            $documents = $documents->where('userId', $user->userid)->get();
        }

        return DataTables::of($documents)
            ->addColumn('file_link', function ($document) {
                $filePath = $document->filePath;
                $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);


                if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    // Return a link to view the image
                    return '<a href="files/view/' . $filePath . '" target="_blank">' . $filePath . '</a>';
                } else {
                    // Return a download link for other file types (PDF, DOC, DOCX, etc.)
                    return '<a href="files/view/' . $filePath . '" target="_blank">' . $filePath . '</a>';
                }
            })
            ->addColumn('name', function ($document) {
                return $document->getByUser->name; // Access the user's name
            })
            ->rawColumns(['file_link'])
            ->make(true);
    }
    public function upload(Request $request)
    {
        $request->validate([
            'folder' => 'required|string',
            'file' => 'required|file|max:2048', // Max 2MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $maxSize = $this->getMaxSize($extension);

        if ($file->getSize() > $maxSize) {
            return back()->withErrors(['file' => $this->getErrorMessage($extension)]);
        }


        $serial = $this->generateSerialNumber();
        $timestamp = now()->format('dmy');
        $userId = Auth::user()->userid;
        $filename = "{$timestamp}{$userId}_{$serial}.{$extension}";

        try {
            // Upload to FTP
            $this->uploadToFtp($request->folder, $file, $filename);

            // Log the upload

            $filePath = "{$request->folder}/{$filename}";

            $this->logUpload($filePath, $request->remarks);
            session()->put('serial', " Scan ID: {$filePath}");
            session()->put('success_time', now()->timestamp);
            return back()->with('success', "File uploaded successfully. Scan ID: {$filePath}");

            // return redirect()->route('upload', ['success' => "File uploaded successfully. Scan ID: {$filePath}", 'filePath' => $filePath]);
            // return back()->with('success', "File uploaded successfully. Scan ID: {$filePath}")->with('filePath', $filePath);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Upload failed: ' . $e->getMessage()]);
        }
    }
    private function getMaxSize($extension)
    {
        switch ($extension) {
            case 'jpg':
                return 250 * 1024;
            case 'doc':
                return 1 * 1024 * 1024;
            case 'pde':
            case 'xlsx':
                return 2 * 1024 * 1024;
            default:
                return 2 * 1024 * 1024;
        }
    }

    private function getErrorMessage($extension)
    {
        switch ($extension) {
            case 'jpg':
                return "The JPG file exceeds the maximum size of 250 KB.";
            case 'doc':
                return  "The DOC file exceeds the maximum size of 1 MB.";
            case 'pdf':
                return "The DOC file exceeds the maximum size of 1 MB.";
            case 'xlsx':
                return "The XLSX or PDE file exceeds the maximum size of 2 MB.";
            default:
                return "The uploaded file exceeds the maximum allowed size.";
        }
    }

    private function generateSerialNumber()
    {
        $today = now()->format('dmy');
        $tokenId= Auth::user()->userid=="admin"?"001":Auth::user()->userid;
        $currentToken = $today . $tokenId;
        $lastSerial = $this->getLastSerialNumber($currentToken);

        $newSerial = $lastSerial + 1;
        $this->saveSerialNumber($currentToken, $newSerial);
        return str_pad($newSerial, 3, '0', STR_PAD_LEFT);
    }

    private function getLastSerialNumber($currentToken)
    {
        $serialNumberEntry = SerialNumber::where('current_token', $currentToken)->first();
        return $serialNumberEntry ? $serialNumberEntry->serial_number : 0;
    }

    private function saveSerialNumber($currentToken, $serialNumber)
    {

        SerialNumber::updateOrCreate(
            ['current_token' => $currentToken],
            ['serial_number' => $serialNumber]
        );
    }


    private function uploadToFtp($subfolder, $file, $filename)
    {
        $ftpHost = env('FTP_HOST');
        $ftpUser = env('FTP_USERNAME');
        $ftpPass = env('FTP_PASSWORD');
        $targetFolder = env('FTP_ROOT') . '/' . $subfolder;


        $connection = ftp_connect($ftpHost);
        $login = ftp_login($connection, $ftpUser, $ftpPass);

        if (!$connection || !$login) {
            throw new \Exception("Could not connect to FTP server.");
        }

        if (!ftp_chdir($connection, $targetFolder)) {
            throw new \Exception("Could not change to directory: {$targetFolder}");
        }

        $localFilePath = $file->getPathname();
        $upload = ftp_put($connection, $filename, $localFilePath, FTP_BINARY);

        ftp_close($connection);

        if (!$upload) {
            throw new \Exception("There was a problem uploading the file.");
        }
    }


    private function logUpload($filePath, $remarks = null)
    {
        DocumentHistory::create([
            'filePath' => $filePath,
            'remarks' => $remarks,
            'userId' => Auth::user()->userid
        ]);
    }



    public function viewFile($folder = null, $filename = null)
    {
        // Construct the full path
        $filePath = "$filename";

        if ($folder != "") {
            $filePath = "$folder/$filename";
        }

        // Check if the file exists
        if (Storage::disk('ftp')->exists($filePath)) {
            // Get the file content
            $fileContent = Storage::disk('ftp')->get($filePath);

            // Get the file extension
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            // Set the appropriate Content-Type based on the extension
            $mimeType = match ($extension) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf',
                'doc', 'docx' => 'application/msword',
                default => 'application/octet-stream', // Fallback for unknown types
            };

            // Return the file content as a response
            return response($fileContent)
                ->header('Content-Type', $mimeType);
        }

        // Return a 404 response if the file doesn't exist
        return abort(404, 'File not found');
    }



    private function getSubfolders()
    {
        $ftpHost = env('FTP_HOST');
        $ftpUsername = env('FTP_USERNAME');
        $ftpPassword = env('FTP_PASSWORD');
        // dd(env('APP_NAME'));

        $ftpConnection = ftp_connect($ftpHost);
        if (ftp_login($ftpConnection, $ftpUsername, $ftpPassword)) {
            // Navigate to the "bills" directory
            // Attempt to change directory to "bills"
            if (ftp_chdir($ftpConnection, 'BILL')) {
                $subfolders = ftp_nlist($ftpConnection, '.');

                // Filter out directories
                $subfolders = array_filter($subfolders, function ($item) {
                    return $item !== '.' && $item !== '..' && strpos($item, '.') === false;
                });

                $existingFolders = FolderStore::pluck('folder_name')->toArray(); // Assuming 'name' is the column storing folder names



                $subfolderSet = array_flip($subfolders);
                foreach ($existingFolders as $folderName) {
                    if (!isset($subfolderSet[$folderName])) {
                        FolderStore::where('folder_name', $folderName)->delete(); // Delete non-existing folder
                    }
                }

                foreach ($subfolders as $subfolder) {
                    $this->insertFolder($subfolder);
                }

                ftp_close($ftpConnection);
                return $subfolders;
            } else {
                Log::error('Failed to change directory to "bills". Directory may not exist.');
                return []; // Return empty if directory change fails
            }
        }

        return [];
    }

    private function insertFolder($folderName)
    {
        // Check if the subfolder already exists in the database
        $existingFolder = DB::table('folder_stores')->where('folder_name', $folderName)->first();

        if (!$existingFolder) {
            // Insert the new subfolder
            DB::table('folder_stores')->insert([
                'folder_name' => $folderName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            Log::info("Subfolder '{$folderName}' already exists in the database.");
        }
    }

    // public function download($filename)
    // {
    //     $filePath = 'BILL/' . $filename; // Adjust the path as needed

    //     if (!Storage::disk('ftp')->exists($filePath)) {
    //         return response()->json(['message' => 'File not found'], 404);
    //     }

    //     // Get the file content from FTP
    //     $stream = Storage::disk('ftp')->get($filePath);

    //     // Create a response and set the headers for download
    //     return response()->stream(function () use ($stream) {
    //         echo $stream;
    //     }, 200, [
    //         'Content-Type' => 'application/octet-stream', // Set appropriate content type
    //         'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
    //     ]);
    // }
}
