<?php

namespace App\Http\Controllers;

use App\Setting;
use App\Homepage;
use App\Category;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Input;
use Image;
class SettingController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $data = [
            'settings' => Setting::all(),
        ];

        return view('backend.settings.general.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $form = $request->except('_method', '_token' , 'logo');
        $form = collect($form);
		
		

        $form->each(
            function ($value, $key) {
                $setting = Setting::where(['key' => $key])->first();
                $setting->value = $value;
                $setting->save();
            }
        );
		
		$file = $request->file('logo');

		//$path = public_path("uploads/"); 
		if ($request->file('logo')) {
			$file_name = "logo.jpg";
			$store_path = public_path("uploads"); 
            $path = $file->move($store_path, $file_name); 
			//$path = $file->storeAs("uploads/", $file_name);	
		}	
		

		

        return redirect('settings/general')
            ->with('message-success', 'Setting updated!');
    }
    
    public function homePage() 
    {
        $data = [
            'homepage' => Homepage::where("type", "!=", "")->get(),
            'categories' => Category::all(),
        ];
        return view('backend.settings.homepage', $data);
    }    
    
    public function homePageUpdate(Request $request)
    {
        $form = $request->except('_method', '_token' );
        $form = collect($form);

        $form->each(
            function ($value, $key) {
                $setting = Homepage::where(['key' => $key])->first();
                if($setting->key == "category") { 
                    $value = implode(",", $value);
                }
                $setting->value = $value;
                $setting->save();
            }
        );
		
		

        return redirect('settings/homepage')
            ->with('message-success', 'Homepage updated!');
    }
    
    
    public function MenuManagement(Request $request)
    {
         $inactive_pages = DB::table("menus")->where("active", 0)->orderBy('order_by', "ASC")->get();;
            $activemenus = DB::table("menus")->where("parent_id", 0)->where("active", 1)->orderBy('order_by', "ASC")->get();
        foreach($activemenus as $menu) { 
            $menu->child = DB::table("menus")->where("parent_id", $menu->menu_id)->orderBy('order_by', "ASC")->get();
        }

        return view('backend.settings.menu_manage', ["pages" => $inactive_pages,"menus" => $activemenus , "title" => "Menus"]);
    }


    function saveList(Request $request) 
    {
        $list = $request->all();
        $p_o = 1;
        foreach($request->all() as $key=>$item) {
            foreach($item as $l) {
                $menu_id = $l['id'];
                $parent_record = DB::table("menus")->where("menu_id", $menu_id)->first();
                if(!empty($parent_record)) { 
                    $data = array(
                    "active" => 1,
                    "parent_id" => 0,
                    "order_by" => $p_o
                    );
                    DB::table("menus")->where("menu_id", $menu_id)->update($data);
                } 
                if(!empty($l['children'])) {
                    $c_o = 1;
                    foreach($l['children'] as $child) { 
                        $data = array(
                        "active" => 1,
                        "parent_id" => $menu_id,
                        "order_by" => $c_o
                        );
                    
                        $child_record = DB::table("menus")->where("menu_id", $menu_id)->first();
                        if(!empty($child_record)) { 
                            DB::table("menus")->where("menu_id", $child['id'])->update($data);
                        } 
                        $c_o++;
                    } 
                }
                $p_o++;
            }
             
        }
    }

    function saveRemoved(Request $request) 
    {
        $list = $request->all();
        $p_o = 1;
        foreach($request->all() as $key=>$item) {
            foreach($item as $l) {
                $menu_id = $l['id'];
                $parent_record = DB::table("menus")->where("menu_id", $menu_id)->first();
                if(!empty($parent_record)) { 
                    $data = array(
                    "active" => 0,
                    "parent_id" => 0,
                    "order_by" => $p_o
                    );
                    DB::table("menus")->where("menu_id", $menu_id)->update($data);
                } 
                if(!empty($l['children'])) {
                    $c_o = 1;
                    foreach($l['children'] as $child) { 
                        $data = array(
                        "active" => 0,
                        "parent_id" => $menu_id,
                        "order_by" => $c_o
                        );
                    
                        $child_record = DB::table("menus")->where("menu_id", $menu_id)->first();
                        if(!empty($child_record)) { 
                            DB::table("menus")->where("menu_id", $child['id'])->update($data);
                        } 
                        $c_o++;
                    } 
                }
                $p_o++;
            }
             
        }
    }

    
}
