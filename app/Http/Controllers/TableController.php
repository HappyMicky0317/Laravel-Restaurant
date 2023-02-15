<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Table;
use DB;
class TableController extends Controller
{

   
    public function tableStatus()
    {
        $tables = Table::get();
        foreach($tables as $table) { 
            $table->hold = DB::table("hold_order")->where("table_id" , $table->id)->exists();
            $hold_id = 0; 
            $total = 0;
            if($table->hold) { 
                $hold_id = DB::table("hold_order")->where("table_id" , $table->id)->value("id");
                $cart = DB::table("hold_order")->where("table_id" , $table->id)->value("cart");
                $cart =json_decode($cart);
                if(!empty($cart))
                foreach($cart as $c) { 
                    if($c->deleted == 1)
                    {
                        continue;
                    }
                    $total += $c->price * $c->quantity;
                }
               
            }
            $table->total = $total;
            $table->hold_id = $hold_id;
        }
 
        $rooms = DB::table("rooms")->get();
        $data = [
            'rooms' => $rooms,
            'tables' => $tables,
        ];

        return view('backend.tables.map', $data);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = Table::get();
        foreach($tables as $table) { 
            $table->room = DB::table("rooms")->where("id" , $table->room_id)->value("name");
        }
        $rooms = DB::table("rooms")->get();
        $data = [
            'rooms' => $rooms,
            'tables' => $tables,
        ];

        return view('backend.tables.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.tables.create');
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
        $data["table_name"] = $request->input("table_name");
        $data["room_id"] = $request->input("room_id");
		unset($data['_token']);
		unset($data['id']);

		 if($request->input("id")) { 
            Table::where("id", $request->input("id"))->update($data);
			return redirect('tables')
            ->with('message-success', 'Table updated!');
        } else { 
            Table::insert($data);
			return redirect('tables')
            ->with('message-success', 'Table created!');
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
        $expense = Table::findOrFail($id);

        return view('backend.tables.show', compact('expense'));
    }

     public function get(Request $request) 
    { 
        $id = $request->input("id");
        $expnese = Table::where("id", $id)->first();
        echo json_encode($expnese);
    }


    public function delete(Request $request)
    {
        $id = $request->input("id");
        $expnese = Table::where("id", $id)->delete();
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
        $expense = Table::findOrFail($id);
        $expense->delete();

        return redirect('rooms')
            ->with('message-success', 'Table deleted!');
    }

    public function truncateTable(Request $request)
    {
        DB::table('hold_order')->delete();
        DB::table('sales')->delete();
        DB::table('sale_items')->delete();


        return back() ->with('message-success', 'Cancellazione eseguita con successo!');
    }
}
