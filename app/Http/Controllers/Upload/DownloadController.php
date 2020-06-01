<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Model\Download;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $size
     * @return string
     */

    function get_file_size($size)
    {
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $units[$i];
    }

    public function store(Request $request)
    {
        $url = $request->file;
        $contents = File::extension($url);
        $file_full_path = 'public/';
        $file_name = $request->name . '.' . $contents;
        $download = Download::find($request->id);
        if (!Storage::exists($file_full_path . '/' . $file_name)) {
            $tmpFile = file_get_contents($url);
            $fileSize = $this->get_file_size(strlen($tmpFile));
            Storage::disk('local')->put($file_full_path . $file_name, $tmpFile, 'public');
            $download->update([
                'status' => 'success',
                'size' => $fileSize
            ]);
            return $download->id;
        } else {
            $download->update([
                'status' => 'A file with the same name already exists.',
                'size' => null
            ]);
            return response()->json(false, 200);
        }


    }


    public function destroy(Download $download)
    {
        if (!Storage::disk('public')->delete($download->file_name)) return;
        if ($download->delete()) {
            return response()->json(true, 200);
        }
    }
}
