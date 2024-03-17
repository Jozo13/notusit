<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ImageController extends Controller
{
    /**
     * Create a new ImageController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function createImage(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'image' => 'required',
        ]);

        $image = $request->file('image');

        // save image to storage and get its url
        $url = 'image_url';

        $image = Image::create([
            'url' => $url,
        ]);

        return $this->returnData($image);
    }

    public function getImage(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $image = Image::find($request->id);

        if (!$image) {
            abort(404);
        }

        return $this->returnData($image);
    }

    public function getImages(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $images = Image::paginate(5);

        return $this->returnData($images);
    }

    public function deleteImage(Request $request)
    {

        if (!Gate::allows('admin', auth()->user())) {
            abort(403);
        }

        $request->validate([
            'id' => 'required|integer'
        ]);

        $image = Image::find($request->id);

        if (!$image) {
            abort(404);
        }

        // delete image from storage

        $image->delete();

        return $this->returnData($image);
    }
}
