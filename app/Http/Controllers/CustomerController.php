<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'customers' => Customer::paginate(),
        ];

        return view('customers.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\StoreCustomer $request)
    {
        $form = $request->all();

        $customer = Customer::create($form);

        return redirect('customers')
            ->with('message-success', 'Customer created!');
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
        $customer = Customer::findOrFail($id);

        return view('customers.show', compact('customer'));
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
        $customer = Customer::findOrFail($id);

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests $request
     * @param int               $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\UpdateCustomer $request, $id)
    {
        $form = $request->all();

        $customer = Customer::findOrFail($id);
        $customer->update($form);

        return redirect('customers')
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
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect('customers')
            ->with('message-success', 'Customer deleted!');
    }
	
	public function findcustomer() { 
		$phone = Input::get('phone'); 
		$record = Customer::where("phone",$phone)->first();
		echo json_encode($record);
	}
    
	
	
	public function storeCustomer(Request $request) { 
		$id = $request->input("id");
		$data_array = array();
		$data = array(
			"name" => $request->input("name"),
			"phone" => $request->input("phone"),
			"neighborhood" => $request->input("neighborhood"),
			"address" => $request->input("address"),
			"comments" => $request->input("comments")
		);
		$data_array["message"] = "OK";
		if($id) { 
			Customer::where("id" , $id)->update($data);
			$data_array["id"] = $id;
		} else { 
			$insert_id = Customer::insertGetId($data);
			$data_array["id"] = $insert_id;
		}
		
		echo json_encode($data_array);
	}
}
