<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Model\Download;
use Illuminate\Http\Request;

class listController extends Controller
{
    public function getFiles(Request $request)
    {
        $files = Download::all();
        if ($request->expectsJson()) return response()->json($files->toArray());
        else{
            return response()->json($files->toArray());
        }
    }
}
