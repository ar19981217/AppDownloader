<?php

namespace App\Console\Commands;

use App\Model\Download;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-url {url} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set url';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    function get_file_size($size)
    {
        $units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $units[$i];
    }

    public function handle()
    {
        $contents = File::extension($this->argument('url'));
        $url = $this->argument('url');
        $file_full_path = 'public/';
        $file_name = $this->argument('name') . '.' . $contents;
        $path = storage_path('/app/' . $file_full_path . $file_name);
        $download = Download::create([
            'title' => $this->argument('name'),
            'path' => $path,
            'file_name' => $file_name,
            'mime' => $contents,
            'status' => 'wait',
            'size' => null
        ]);
        $path = storage_path('/app/' . $file_full_path . $file_name);
        if (!Storage::exists($file_full_path.'/'.$file_name)) {
            $tmpFile = file_get_contents($url);
            $fileSize = $this->get_file_size(strlen($tmpFile));
            Storage::disk('local')->put($file_full_path . $file_name, $tmpFile, 'public');
            $download->update([
                'title' => $this->argument('name'),
                'path' => $path,
                'file_name' => $file_name,
                'mime' => $contents,
                'status' => 'success',
                'size' => $fileSize
            ]);
            $this->info('Added: ' . $this->argument('url') . ' ' . $this->argument('name'));
            return $download->id;
        }else{
            $download->update([
                'title' => $this->argument('name'),
                'path' => $path,
                'file_name' => $file_name,
                'mime' => $contents,
                'status' => 'A file with the same name already exists.',
                'size' => null
            ]);
            $this->info('Added: ' . $this->argument('url') . ' ' . $this->argument('name'));
            return response()->json(false, 200);
        }
    }
}










