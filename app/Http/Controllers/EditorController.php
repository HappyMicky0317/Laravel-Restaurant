<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Session;

class EditorController extends Controller
{

    public function siteHtml() 
    {
        $path = realpath(__DIR__ . '/../../../resources/views/pages');
        return view('backend.editor.views', ['path' => $path, "title" => "Language Editor"]);
    }

    public function saveHtml(Request $request) 
    {
        $real_file = $request->get('file');
        if (!empty($request->input('editor_code'))) {
            $newcontent = $this->stripslashes_deep($request->input('editor_code'));
            if (is_writeable($real_file)) {
                $f = fopen($real_file, 'w+');
                fwrite($f, $newcontent);
                fclose($f);
                \Session::flash('message', 'File Changed Successfully');
            } else {
                \Session::flash('message', 'File is not Writeable');
            }
        }
        return redirect("editor/html?file=$real_file");
    }

    function stripslashes_deep($value) 
    {
        $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

        return $value;
    }

}
