<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'image' =>'required|mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }
        
        $file = $request->file('image');
        $name = Str::random(10);

        $url = \Storage::putFileAs('image', $file, $name . '.' . $file->extension());

        return env('APP_URL') . '/' . $url;
    }
}
