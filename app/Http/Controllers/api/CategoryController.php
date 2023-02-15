<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use Response;

class CategoryController extends Controller
{
    /**
     * Getting All Categories
     */
    
     public function index()
     {
         $categories = Category::all();
         foreach($categories as $category) { 
             $category->image_thumb = url('uploads/category/thumb/' . $category->id . ".jpg");
             $category->image = url('uploads/category/' . $category->id . ".jpg");
         }
         return Response::json($categories, 200);
     }
}
