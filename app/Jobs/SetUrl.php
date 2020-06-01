<?php

namespace App\Jobs;

use App\Model\Download;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use File;
use Illuminate\Support\Facades\Storage;

class SetUrl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $arr;
    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($arr)
    {
        $this->arr = $arr;

    }

    /**
     * Execute the job.
     *
     * @return string
     */

    public function handle()
    {
        $contents = $this->arr->mime;
        $url = $this->arr->url;
        $file_full_path = 'public/';
        $file_name = $this->arr->name . '.' . $contents;
        $path = storage_path('/app/' . $file_full_path . $file_name);
        $tmpFile = file_get_contents($url);
        $download = Download::find($this->arr->id);
        $download->update([
            'title' => $this->arr->name,
            'path' => $path,
            'file_name' => $file_name,
            'mime' => $contents,
            'status' => 'success',
            'size' => null
        ]);
        $fileSize = $this->get_file_size(strlen($tmpFile));
        if (!Storage::exists($file_full_path.'/'.$file_name)) {
            Storage::disk('local')->put($file_full_path . $file_name, $tmpFile, 'public');
            $download->update([
                'title' => $this->arr->name,
                'path' => $path,
                'file_name' => $file_name,
                'mime' => $contents,
                'status' => 'success',
                'size' => $fileSize
            ]);
        }else{
            $download->update([
                'title' => $this->arr->name,
                'path' => $path,
                'file_name' => $file_name,
                'mime' => $contents,
                'status' => 'error',
                'size' => $fileSize
            ]);
            return response()->json(false, 200);
        }
        info($this->arr->name);
    }
}
