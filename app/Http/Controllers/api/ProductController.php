<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\Product;
use Response;

class ProductController extends Controller
{
    /**
     * Getting All Products
     */
    
     public function index()
     {
         $products = Product::all();
         foreach($products as $product) { 
             $product->image_thumb = url('uploads/products/thumb/' . $product->id . ".jpg");
             $product->image = url('uploads/products/' . $product->id . ".jpg");
         }
         return Response::json($products, 200);
     }

     /**
     * Getting All Products And Category
     */
    
     public function categoryAndProducts()
     {
        $categories = Category::all();
        foreach($categories as $category) { 
            $category->image_thumb = url('uploads/category/thumb/' . $category->id . ".jpg");
            $category->image = url('uploads/category/' . $category->id . ".jpg");
            $category->products = Product::where('category_id' , $category->id)->get();
            foreach($category->products as $product) { 
                $product->image_thumb = url('uploads/products/thumb/' . $category->id . ".jpg");
                $product->image = url('uploads/products/' . $category->id . ".jpg");
            }
        }

         return Response::json($categories, 200);
     }


     /**
     * Getting All Products And Category
     */
    
     public function productsByCategory($category_id)
     {
        $products = Product::where('category_id' , $category_id)->get();
        foreach($products as $product) { 
            $product->image_thumb = url('uploads/products/thumb/' . $product->id . ".jpg");
            $product->image = url('uploads/products/' . $product->id . ".jpg");
        }
        return Response::json($categories, 200);
     }
}
