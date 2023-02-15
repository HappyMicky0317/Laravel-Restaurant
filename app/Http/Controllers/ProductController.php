<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Product;
use App\Category;
use Illuminate\Http\Request;
use Auth;
use Intervention\Image\ImageManagerStatic as Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $products = Product::where("category_id", ">" , 0)->orderBy("id" , "DESC")->get();
		
       
        $data = [
            'products' => $products
        ];

        return view('backend.products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::get();
        return view('backend.products.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $form = $request->all();
        $price = $request->input("price");
        $titles = $request->input("title");
        unset($form['price']);
        unset($form['title']);
        unset($form['price_counter']);
        $form['prices'] = json_encode($price);
        $form['titles'] = json_encode($titles);
        
        $product = Product::create($form);
        $name = $product->id;
        if (file_exists("uploads/products/temp.jpg")) {
            rename("uploads/products/temp.jpg", "uploads/products/$name.jpg");
            rename("uploads/products/thumb/temp.jpg", "uploads/products/thumb/$name.jpg");
        }
            

        return redirect('products')
            ->with('message-success', 'Product created!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::get();
        return view('backend.products.show', compact('product', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::get();
        return view('backend.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests $request
     * @param int               $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateProduct $request, $id)
    {
        $form = $request->all();
        $price = $request->input("price");
        $titles = $request->input("title");
        unset($form['price']);
        unset($form['title']);
        unset($form['price_counter']);
        $form['prices'] = json_encode($price);
        $form['titles'] = json_encode($titles);
        
        $product = Product::findOrFail($id);
        $product->update($form);
        $name = $product->id;
        

        return redirect('products')
            ->with('message-success', 'Product updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect('products')
            ->with('message-success', 'Product deleted!');
    }
    
    public function uploadPhoto(Request $request) 
    { 
        $file = $request->file('croppedImage');
            
        if ($request->hasFile('croppedImage')) {

            $file_name = "temp.jpg";
            $extension = $file->getClientOriginalExtension();
            $path = $file->storeAs("uploads/products/", $file_name);
            $img = Image::make($file->getRealPath());
            if($img->exif('Orientation')) { 
                $img = orientate($img, $img->exif('Orientation'));
            }
                
            $path2 = public_path("storage/products/$file_name"); 
            $img->fit(250)->save($path2);
                
            echo url("storage/products/" . $file_name);
        }
    }
    
    ////// User upload photo and resize to 145x145 to Thumb
    public function updatePhotoCrop(Request $request) 
    {
        $cropped_value = $request->input("cropped_value"); 
        $image_edit = $request->input("image_edit"); 
        $cp_v = explode(",", $cropped_value);
            
        $file = $request->file('file');
        $file_name = $image_edit . ".jpg";
        if(empty($image_edit)) { 
            $file_name = "temp.jpg";
        }
            
        if ($request->hasFile('file')) {
                
            $extension = $file->getClientOriginalExtension();
            $store_path = public_path("uploads/products"); 
            $path = $file->move($store_path, $file_name); 
            $img = Image::make($store_path . "/$file_name"); 
            if($img->exif('Orientation')) { 
                $img = orientate($img, $img->exif('Orientation'));
            }
                
            $path2 = public_path("uploads/products/thumb/$file_name"); 
            $img->rotate($cp_v[4] * -1);
            $img->crop($cp_v[0], $cp_v[1], $cp_v[2], $cp_v[3]);
            $img->fit(250)->save($path2);
                
            echo url("uploads/products/thumb/$file_name"); exit;
        }
            
        if($image_edit != "") {
            $path = public_path("uploads/products/$file_name");
            $img = Image::make($path);
            $path2 = public_path("uploads/products/thumb/$file_name");
            $img->rotate($cp_v[4] * -1);                
            $img->crop($cp_v[0], $cp_v[1], $cp_v[2], $cp_v[3]);
            $img->fit(250)->save($path2);
            echo url("uploads/products/thumb/$file_name"); exit;
        }
        
    }
	
	
	 public function addToArchive(Request $request) {
        $id = $request->input("product_id");
        $product = Product::find($id);
        if ($product->is_delete == 1) {
            $value = 0;
        }

        if ($product->is_delete == 0) {
            $value = 1;
        }
        Product::where("id", $id)->update(array('is_delete' => $value));
    }

    
}
