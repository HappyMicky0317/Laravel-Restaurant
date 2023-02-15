<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'categories' => Category::paginate(15),
        ];

        return view('backend.category.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.category.create');
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

        $category = Category::create($form);
        $name = $category->id;
        if (file_exists("uploads/category/temp.jpg")) {
            rename("uploads/category/temp.jpg", "uploads/category/$name.jpg");
            rename("uploads/category/thumb/temp.jpg", "uploads/category/thumb/$name.jpg");
        }
        return redirect('categories')
            ->with('message-success', 'Category created!');
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
        $category = Category::findOrFail($id);

        return view('backend.category.show', compact('category'));
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
        $category = Category::findOrFail($id);

        return view('backend.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests $request
     * @param int               $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $form = $request->all();

        $customer = Category::findOrFail($id);
        $customer->update($form);

        return redirect('categories')
            ->with('message-success', 'Customer updated!');
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
        $customer = Category::findOrFail($id);
        $customer->delete();

        return redirect('categories')
            ->with('message-success', 'Category deleted!');
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
            $store_path = public_path("uploads/category"); 
            $path = $file->move($store_path, $file_name); 
            $img = Image::make($store_path . "/$file_name"); 
            if($img->exif('Orientation')) { 
                $img = orientate($img, $img->exif('Orientation'));
            }
            
            $path2 = public_path("uploads/category/thumb/$file_name"); 
            $img->rotate($cp_v[4] * -1);
            $img->crop($cp_v[0], $cp_v[1], $cp_v[2], $cp_v[3]);
            $img->fit(265, 205)->save($path2);
            
            echo url("uploads/category/thumb/$file_name"); exit;
        }
        
        if($image_edit != "") {
            $path = public_path("uploads/category/$file_name");
            $img = Image::make($path);
            $path2 = public_path("uploads/category/thumb/$file_name");
            $img->rotate($cp_v[4] * -1);                
            $img->crop($cp_v[0], $cp_v[1], $cp_v[2], $cp_v[3]);
             $img->fit(265, 205)->save($path2);
            echo url("uploads/category/thumb/$file_name"); exit;
        }
    
    }

}
