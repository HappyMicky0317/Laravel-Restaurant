<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\Slider;
class SliderController extends Controller
{
    
    public function __construct() 
    {
        $this->middleware('auth');
    }
    
    public function index() 
    {
        $sliders = Slider::get();
        return view('backend.slider.index', ['title' => "Sliders" , 'sliders' => $sliders]);
    }
     
    public function save(Request $request) 
    { 
        $data = array(
        "title" => $request->input("title")
        );
        
        $destinationPath = "uploads/slider/";
        if ($request->hasFile("file")) {
            $fileName = rand(11111, 999999); // renameing image
            $request->file("file")->move($destinationPath, "$fileName.jpg");
            $data["image"] = "$fileName.jpg";
       
        }
        if($request->input("id")) { 
            Slider::where("id", $request->input("id"))->update($data);
        } else { 
            Slider::insert($data);
        }
        return redirect("sliders");
    }
     
    public function get(Request $request) 
    { 
        $id = $request->input("id");
        $proeprty = Slider::where("id", $id)->first();
        echo json_encode($proeprty);
    }
     
    public function delete(Request $request) 
    { 
        $id = $request->input("id");
        $property = Slider::where("id", $id)->delete();
        echo json_encode($property);
    }
}
