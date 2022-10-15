<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate();
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        return new ProductResource(Product::find($id));
    }

    public function store(Request $request)
    {
        
        
        $validator =  Validator::make($request->all(),[
            'title' =>'required|string',
            'image' =>'required',
            'price' =>'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }

        try {
            $product = Product::create($request->only(
                'title',
                'description',
                'image',
                'price',
            ));

            return response()->json([
                "success" => true,
                $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $validator =  Validator::make($request->all(),[
            'title' =>'required|string',
            'image' =>'required',
            'price' =>'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }
        
        $product = Product::find($id);
        try {
            $product->update($request->only(
                'title',
                'description',
                'image',
                'price',
            ));

            return response()->json([
                "success" => true,
                $product
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        Product::destroy($id);

        return response()->json([
           'status' =>'success',
        ]);
    }
}
