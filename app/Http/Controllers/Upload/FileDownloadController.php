<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Model\Download;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function fileDownload($name = null)
    {
        return response()->download($name);
    }

    public function fileDelete($id)
    {
        $download = Download::find($id);
        Storage::disk('public')->delete($download->file_name);
        $download->delete();
        return response()->json('File delete', 200);

    }

    public function fileadd(Request $request)
    {
        $url = $request->file;
        $contents = File::extension($url);
        $file_full_path = 'public/';
        $file_name = $request->name . '.' . $contents;
        $path = storage_path('/app/' . $file_full_path . $file_name);
        $download = Download::create([
            'title' => $request->name,
            'path' => $path,
            'file_name' => $file_name,
            'mime' => $contents,
            'status' => 'wait',
            'size' => null
        ]);
        return $download->id;
    }
}
