<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index()
     {
         if (auth('sanctum')->check()){
             $products = Product::all();
             return response()->json([
                 'status' => true,
                 'products' => $products
             ],201);
         }
         else{
             return response()->json([
                 'message' => 'please login first',
             ], 401);
         }

     }

     /**
      * Store a newly created resource in storage.
      *
             * Store Product data
        * @param  [string] product_name
        * @param  [string] product_detail
        * @return [string] message
      */

     public function store(Request $request)
     {

         if (auth('sanctum')->check()){
             $request->validate([
                 'product_name' => 'required',
                 'product_detail' => 'required',
             ]);

             $input = $request->all();
             $product = Product::create($input);
             return response()->json([
                 'status' => true,
                 'message' => "Product Created successfully!",
                 'product' => $product
             ], 201);
         }
         else{
             return response()->json([
                 'message' => 'please login first',
                 ], 401);
             }

      }
    /**
      * Show the Porduct Data
        * @return [string] message & product details
      */
     public function show($id)
     {
         if (auth('sanctum')->check()){
             $product = Product::find($id);
             return response()->json([
                 'status' => true,
                 'message' => "Product Detail Below",
                 'product' => $product
             ], 201);
         }
         else{
             return response()->json([
                 'message' => 'please login first',
                 ], 401);
             }
     }
     /**
      * Update Products Data Using By ID.
        * @param  [string] product_name
        * @param  [string] product_detail
        * @return [string] message
      */
     public function update(Request $request, $id)
     {
         if (auth('sanctum')->check()){
             $request->validate([
                     'product_name' => 'required',
                     'product_detail' => 'required',
                 ]);
                 $product=Product::find($id);
                 $product->update($request->all());
                 return response()->json([
                     'status' => true,
                     'message' => "Product Update successfully!",
                     'product' => $product
                 ], 201);
         }
         else{
             return response()->json([
                 'message' => 'please login first',
                 ], 401);
             }
     }
     /**
      * Product Delete Using By ID
        * @return [string] message
      */
     public function destroy($id)
     {
         if (auth('sanctum')->check()){
             $product = Product::find($id);
             $product->delete();
             return response()->json([
                 'status' => true,
                 'message' => "Product Delete successfully!",
                 'product' => $product
             ], 201);
         }
         else{
             return response()->json([
                 'message' => 'please login first',
                 ], 401);
             }
     }
}
