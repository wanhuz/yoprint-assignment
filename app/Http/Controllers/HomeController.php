<?php

namespace App\Http\Controllers;

use App\Models\Upload;

class HomeController extends Controller
{
    public function index()
    {
        $uploads = Upload::latest()->get();

        return view('index', compact('uploads'));
    }
}
