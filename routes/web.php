<?php

use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('document');
});
Route::middleware(['auth', 'role:admin'])->get('/folder', [FileUploadController::class,'folderList'])->name('folder');
Route::put('/folders/{id}', [FileUploadController::class, 'update'])->name('folders.update');

Route::get('/document', [FileUploadController::class, 'index'])->middleware(['auth', 'verified'])->name('document');
Route::get('/document/get', [FileUploadController::class, 'getDocuments'])->name('document.data');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->get('/upload', [FileUploadController::class, 'uploadGet'])->name('upload');


Route::post('/upload', [FileUploadController::class, 'upload']);
Route::get('files/view/{folder?}/{filename?}', [FileUploadController::class, 'viewFile'])->name('file.view');
Route::get('files/download/{filename}', [FileUploadController::class, 'download'])->name('files.download');


require __DIR__ . '/auth.php';
