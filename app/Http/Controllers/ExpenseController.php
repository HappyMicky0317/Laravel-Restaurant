<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'expenses' => Expense::paginate(20),
        ];

        return view('backend.expenses.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.expenses.create');
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
        $data = $request->all();
		unset($data['_token']);
		unset($data['id']);

		 if($request->input("id")) { 
            Expense::where("id", $request->input("id"))->update($data);
			return redirect('expenses')
            ->with('message-success', 'Expense updated!');
        } else { 
            Expense::insert($data);
			return redirect('expenses')
            ->with('message-success', 'Expense created!');
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
        $expense = Expense::findOrFail($id);

        return view('backend.expenses.show', compact('expense'));
    }

     public function get(Request $request) 
    { 
        $id = $request->input("id");
        $expnese = Expense::where("id", $id)->first();
        echo json_encode($expnese);
    }


    public function delete(Request $request)
    {
        $id = $request->input("id");
        $expnese = Expense::where("id", $id)->delete();
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
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect('expenses')
            ->with('message-success', 'Expense deleted!');
    }
}
