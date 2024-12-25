<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        $products=Product::all();
        return response(['data'=>$products],200);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'required',
        ]);
        // create image
        $image = $request->file('image');
        $image_name = time() . '.' . $image->extension();
        $image->move(public_path('images'), $image_name);
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $image_name,
        ]);
        return response(['data'=>$product],201);
    }

    public function show($id){
        $product=Product::find($id);
        if($product){
            return response(['data'=>$product],200);
        }
        else{
            return response(['message'=>'Product not found'],404);
        }
    }

    public function update(Request $request, $id){
        $product=Product::find($id);
        if($product){
            $validatedData = $request->validate([
                'name' => 'required',
                'description' => 'required',
                'price' => 'required',
                'image' => 'required',
            ]);
            if ($validatedData->fails()) {
                return response(['message' => $validator->errors()], 400);
            }
            // create image
            $image = $request->file('image');
            $image_name = time() . '.' . $image->extension();
            $image->move(public_path('images'), $image_name);
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->image = $image_name;
            $product->save();
            return response(['data'=>$product],200);
        }
        else{
            return response(['message'=>'Product not found'],404);
        }
    }

    public function destroy($id){
        $product=Product::find($id);
        if($product){
            $product->delete();
            return response(['message'=>'Product deleted successfully'],200);
        }
        else{
            return response(['message'=>'Product not found'],404);
        }
    }
}
