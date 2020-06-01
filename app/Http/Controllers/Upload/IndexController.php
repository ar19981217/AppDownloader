<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use App\Jobs\SetUrl;
use App\Model\Download;
use Hamcrest\Core\Set;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function getIndex()
    {
//        CreateDb::dispatch();

//        dd(SetUrl::dispatch('Test set url'));
        $files = Download::paginate(10);
        return view('upload.index', ['files'=>$files]);
    }

    public function fileListAjax()
    {
        $files = Download::paginate(10);
        return response()->json($files);
    }


}
