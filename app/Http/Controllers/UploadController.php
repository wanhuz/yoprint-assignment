<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Jobs\ProcessCsvJob;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::latest()->get();

        return view('uploads.index', compact('uploads'));
    }

    public function store(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv']);

        $file = $request->file('file');
        $path = $file->store('uploads');

        $upload = Upload::create([
            'filename' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'status' => 'pending',
        ]);

        ProcessCsvJob::dispatch($upload->id);

        return back()->with('success', 'File uploaded and queued for processing!');
    }

    public function refresh()
    {
        return Upload::latest()->get();
    }
}
