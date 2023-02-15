<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Page;
use Session;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;

use Intervention\Image\ImageManagerStatic as Image;

class PageController extends Controller
{
    public function __construct() 
    {
        $this->middleware('auth');
        // if (Auth::user()->AccessLevel !=0) {
        // return redirect("admin/dashboard");
        // }
    }
    
    /**
     * Page Lisitng on admin.
     */
     
    public function index() 
    {
        
            $pages = Page::paginate(25);
        return view('backend.pages.home', ['pages' => $pages, "title" => "Pages"]);
    }
        
    /**
     * Page Add Form.
     */
    public function add() 
    {
        $pages = Page::where("is_delete", 0)->get();
         return view('backend.pages.form', ['pages' => $pages, "title" => "Add New Page"]);
    }
    /**
     * Page Edit.
     */
    public function edit($id) 
    {
        $page = Page::find($id);
        return view('backend.pages.edit', ['page' => $page,"title" => "Edit Page"]);
    }
    
    
    /**
     * Property Store.
     */
    public function save(Request $request) 
    {
        $id = $request->input("id");
        $data = array(
            "title" => $request->input("title"),
            "body" => $request->input("body")
        );
        $file = Input::file('file');
        
        if ($id) {
            $file_name = "page$id.jpg";
            $data['image'] =  $file_name;
            if (Input::hasFile('file')) {
                $extension = $file->getClientOriginalExtension();
                $store_path = public_path("uploads/pages"); 
                $path = $file->move($store_path, $file_name); 
            }
             
            \Session::flash('flash_message', 'Updated Successfully');
            Page::where("id", $id)->update($data);
         
        } else {
            $file_name = "page" . rand(1, 999) . ".jpg";
            $data['image'] = $file_name;
            if (Input::hasFile('file')) {
                $extension = $file->getClientOriginalExtension();
                $store_path = public_path("uploads/pages"); 
                $path = $file->move($store_path, $file_name); 
            }
            
            $insert_id = Page::insertGetId($data);
            \Session::flash('flash_message', 'Page Successfully Added. Change in Translation and Publish if you are using Multi Language');

        }
        return redirect("pages");
    }
    
    
    /**
     * Delete Page.
     */
    public function delete(Request $request) 
    {
        $id = $request->input("id");
        Blog::where("id", $id)->update(array('is_delete' => 1));
    }

}
