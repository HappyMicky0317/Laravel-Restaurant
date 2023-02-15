<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Printer;
use DB;
class PrinterController extends Controller
{

   
  


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $printers = Printer::get();
        
        $data = [
            'printers' => $printers,
        ];

        return view('backend.printers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.printers.create');
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
       $data = array(
           "name" => $request->input("name"),
           "ip" => $request->input("ip"),
           "port" => $request->input("port"),
           "status" => $request->input("status"),
       );
		
		 if($request->input("id")) { 
            Printer::where("id", $request->input("id"))->update($data);
			return redirect('printers')
            ->with('message-success', 'Printer updated!');
        } else { 
            Printer::insert($data);
			return redirect('printers')
            ->with('message-success', 'Printer created!');
        }

        
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
        $expense = Printer::findOrFail($id);

        return view('backend.printers.show', compact('expense'));
    }

     public function get(Request $request) 
    { 
        $id = $request->input("id");
        $expnese = Printer::where("id", $id)->first();
        echo json_encode($expnese);
    }


    public function delete(Request $request)
    {
        $id = $request->input("id");
        $expnese = Printer::where("id", $id)->delete();
        echo json_encode($expnese);
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
        $expense = Printer::findOrFail($id);
        $expense->delete();

        return redirect('printers')
            ->with('message-success', 'Printer deleted!');
    }
}
