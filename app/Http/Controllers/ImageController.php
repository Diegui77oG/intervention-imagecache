<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Image;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('images.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('images.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('profile_image')) {
            $filenamewithextension = $request->file('profile_image')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('profile_image')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . '_' . time() . '.' . $extension;

            //Upload File
            $request->file('profile_image')->storeAs('public/profile_images', $filenametostore);
            $request->file('profile_image')->storeAs('public/profile_images/thumbnail', $filenametostore);

            //Resize image here
            $thumbnailpath = public_path('storage/profile_images/thumbnail/' . $filenametostore);

            // $img = Image::make($thumbnailpath);

            // create a cached image and set a lifetime and return as object instead of string
            $img = Image::cache(function ($image) use ($filenametostore) {
                $image->make('storage/profile_images/thumbnail/' . $filenametostore)->resize(300, 200)->greyscale();
            }, 10, true);

            $img->save($thumbnailpath);

            return redirect('index')->with('success', "Image uploaded successfully.");
        }
    }
}
