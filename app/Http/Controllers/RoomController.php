<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use DB;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::get();
        
        foreach($rooms as $room) { 
            $tables = DB::table("tables")->get();
            $aa = array();
            foreach($tables as $table) { 
                $aa[] = $table->table_name;
            }
            $room->tables = implode(", " , $aa);
        }
        $data = [
            'rooms' => $rooms,
        ];

        return view('backend.rooms.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.rooms.create');
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
        $data["name"] = $request->input("name");
		unset($data['_token']);
		unset($data['id']);

		 if($request->input("id")) { 
            Room::where("id", $request->input("id"))->update($data);
			return redirect('rooms')
            ->with('message-success', 'Table updated!');
        } else { 
            Room::insert($data);
			return redirect('rooms')
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
        $expense = Room::findOrFail($id);

        return view('backend.rooms.show', compact('expense'));
    }

     public function get(Request $request) 
    { 
        $id = $request->input("id");
        $expnese = Room::where("id", $id)->first();
        echo json_encode($expnese);
    }


    public function delete(Request $request)
    {
        $id = $request->input("id");
        $expnese = Room::where("id", $id)->delete();
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
        $expense = Room::findOrFail($id);
        $expense->delete();

        return redirect('rooms')
            ->with('message-success', 'Table deleted!');
    }
}
