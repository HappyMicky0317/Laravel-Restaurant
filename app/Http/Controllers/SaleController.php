<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Sale;
use App\Category;
use App\Product;
use Auth;
use Validator;
use DB;
use App\User;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('q', '');
        $data["q"] = $keyword;
        $ids = array();
        if($keyword) {
            $users = User::where("role_id", "!=", 4)->where("name", "like", "%$keyword%")->get();
            foreach($users as $user) { 
                $ids[] = $user->id;
            }
           
        }

    
        if(Auth::user()->role_id == 1) { 
            
            if(!empty($ids))  {
                $sales = Sale::where("sales.status" , "!=" , 0)->whereIn("cashier_id", $ids)->orderBy("sales.id", "DESC")->paginate(25);
            } else {
                $sales = Sale::where("sales.status" , "!=" , 0)->groupBy("sales.id")->orderBy("sales.id", "DESC")->paginate(25);
            }

            $sales = !empty($keyword) ? $sales->appends(['q' => $keyword]) : $sales;
            $data['sales'] = $sales;   
           
        } else { 
            $data['sales'] = Sale::where("cashier_id", Auth::user()->id)->where("status" , "!=" , 0)->orderBy("sales.id", "DESC")->paginate(25);
        }
        
        return view('backend.sales.index', $data);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function create()
    {
        if(!empty($_GET["h"]) and $_GET["h"] > 0) {
            $holdId = $_GET["h"];
            $holdOrder = DB::table("hold_order")->where("id" , $holdId)->first();
            $data["holdOrder"] = $holdOrder;
        } else { 
            $data["holdOrder"] = array();
        }
        $data['categories'] = Category::get();
        $data['products'] = Product::get();
        $data['rooms'] = DB::table("rooms")->get();
        $tables = DB::table("tables")->get();
        foreach($tables as $table) { 
            $table->hold = DB::table("hold_order")->where("table_id" , $table->id)->exists();       //table exists in hold_order?
            $table->hold_id = DB::table("hold_order")->where("table_id" , $table->id)->value("id"); //hold_order id of table.
        }
        $data['tables'] = $tables;
        
        $cartall = collect(session('cart'));
        $cart = array();
        $table_id = session()->get('table')['table_number'];                //Current Table Number that was stored in Session. 
        foreach ($cartall as $key => $value) {
            # code...
            $value = str_replace("'", "", $value);                          //Eliminate ' of cart because of error.
            if($table_id == $value['table_id']) {
                array_push($cart, $value);
            }
        }
        
   
        $table_room = session('table');  
        $holder_id = session('holder_id');                                     //Room Number of Current Table
        return view('backend.sales.create', $data, compact('cart', 'table_room', 'table_id', 'holder_id'));
    }

    public function product_table(Request $request)
    {
        session()->forget('table');
        $room_number = $request->room_number;
        $table_number = $request->table_number;
        $table_room = session()->get('table', []);
        $table_room["room_number"] = $room_number;
        $table_room["table_number"] = $table_number;
        session()->put('table', $table_room);
    }

    public function session_flush()
    {
        session()->forget('cart');
        //session()->forget('table');
    }


    public function add_to_cart(Request $request)
    {
        $cart_id = $request->id;
        $cart = session()->get('cart', []);
        if(isset($cart[$cart_id]))
        {
            //$cart[$cart_id]['quantity']++;
        }else
        {
            $cart[$cart_id] = [
                "id" => $cart_id,
                "product_id" => $request->product_id,
                "price" => $request->price,
                "size" => $request->size,
                "name" => $request->name,
                "quantity" => $request->quantity,
                "note" => $request->note,
                "table_id" => $request->table_id,
                "deleted" => 0
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['message' => __('Aggiunto al carrello')]);
    }

    public function decrease_cart(Request $request)
    {
        if($request->id){
            $cart = session()->get('cart');
            if($cart[$request->id]["quantity"] > 0)
            {
                $cart[$request->id]["quantity"]--;
                session()->put('cart', $cart);
            }else{
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
    }

    public function delete_to_cart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
    }

    public function increase_to_cart(Request $request)
    {
        if($request->id){
            $cart = session()->get('cart');
            if($cart[$request->id]["quantity"] > 0)
            {
                $cart[$request->id]["quantity"]++;
                session()->put('cart', $cart);
            }else{
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
        }
    }

    public function remove_final_cart(Request $request)
    {

    }

    public function receipt($id)
    {
        
        $sale = Sale::findOrFail($id);
        $data["sale"] = $sale;
        $data["table_name"] = DB::table("tables")->where("id" , $sale->id)->value("table_name");
        return view('backend.sales.receipt', $data);
    }


    public function kitchenReceipt($id)
    {
        $table = DB::table("hold_order")->where("id", $id)->first();
        $cart = json_decode($table->cart , true);
        $newcart = array();
        $print_cart = array();
        if(!empty($cart)) { 
            foreach($cart as $k=> $c) {  
                if($c['deleted'] == 1)
                {
                    $newcart[$k] = $c;
                    continue;
                }
                if(empty($c["print"])) { 
                    $print_cart[] = $c;
                    $newcart[$k] = $c;
                    $newcart[$k]["print"] = "ok";
                } else { 
                    $newcart[$k] = $c;
                }
            }
            DB::table("hold_order")->where("id", $id)->update(["cart" => json_encode($newcart)]);
        }
       
   
        $data["table"] = $table;
        $data["cart"] = $print_cart;
        $data["hold_id"] = $id;
        
        return view('backend.sales.kitchen_receipt', $data);
    }
    

    
    public function completeSale(Request $request)
    {
        $form = $request->all();
		$items = $request->input('items');
		$amount = 0;
		foreach($items as $item) { 
			$amount += $item['price'] * $item['quantity'];
		}	
		$amount += $request->input('vat') + $request->input('delivery_cost') - $request->input('discount');
		$form['amount'] = $amount;
		
		 $rules = Sale::$rules;
        $rules['items'] = 'required';

        $validator = Validator::make($form, $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                'errors' => $validator->errors()->all(),
                ], 400
            );
        }
      
     
        $sale = Sale::createAll($form);
        session()->forget('cart');
        session()->forget('table');
        return url("sales/receipt/".$sale->id);
    }
    
    public function cancel($id)
    {
        Sale::where("id", $id)->update(array("status" => 0));
        return redirect("sales");
    }


    public function holdOrder(Request $request)
    {
        $id = $request->input("id");
        $comment = $request->input("comment");
        $table_id = $request->input("table_id");
        $room_id = $request->input("room_id");
        $discount = $request->input("discount");
        $cart = json_encode($request->input("cart"));
        $istableexist = DB::table("hold_order")->where("table_id", $table_id)->count();
        
        //When Red Table is in Hold Order and Status is not 0 goes to 
        
        if($istableexist > 0) {
            //$isorderbletableexist = DB::table("hold_order")->where("table_id", $table_id)->where("status" , 0)->count();

            //if($isorderbletableexist > 0) {
                DB::table("hold_order")->where("id", $id)->update(array("table_id" => $table_id, "discount" => $discount, "room_id" => $room_id, "cart" => $cart, "comment" => $comment, "status" => 1, "user_id" => Auth::user()->id));
                echo json_encode(["status" => true, "url" => url("sales/kreceipt/".$id)]);
                exit;
            /*}
            else {
                echo json_encode(["status" => false, "message" => "Table already on Hold"]);
                exit;
            }*/
        }
        else {

            if($request->input("cart")) {
                $id = DB::table("hold_order")->insertGetId(array("room_id" => $room_id, "discount" => $discount,"table_id" => $table_id, "cart" => $cart, "status" => 1, "comment" => $comment, "user_id" => Auth::user()->id));
                echo json_encode(["status" => true, "url" => url("sales/kreceipt/".$id)]);
                exit;
            }
            else {
                echo json_encode(["status" => false, "message" => "Table is Empty."]);
                exit;
            }
        }
            //Green button process:Insert New Green Hold table to Hold order and Give new ID to Green Table

            // $id = DB::table("hold_order")->insertGetId(array("room_id" => $room_id, "discount" => $discount,"table_id" => $table_id, "cart" => $cart, "comment" => $comment, "user_id" => Auth::user()->id));
            // echo json_encode(["status" => true, "url" => url("sales/kreceipt/".$id)]);
            // exit;


        /*if ($table < 0) {
            var_dump($table);
            return ;
            DB::table("hold_order")->where("id", $id)->update(array("table_id" => $table_id, "discount" => $discount, "room_id" => $room_id, "cart" => $cart, "comment" => $comment, "user_id" => Auth::user()->id));
            echo json_encode(["status" => true, "url" => url("sales/kreceipt/".$id)]);
            exit;
        }

        //When Red Table is in Hold Order and Status is 0 return Alert.
        $table = DB::table("hold_order")->where("table_id", $table_id)->where("room_id" , $room_id)->where("status" , 0)->count();
        if ($table > 0) {
            echo json_encode(["status" => false, "message" => "Table already on Hold"]);
            exit;
        }
        //Green button process:Insert New Green Hold table to Hold order and Give new ID to Green Table
        $id = DB::table("hold_order")->insertGetId(array("room_id" => $room_id, "discount" => $discount,"table_id" => $table_id, "cart" => $cart, "comment" => $comment, "user_id" => Auth::user()->id));
        echo json_encode(["status" => true, "url" => url("sales/kreceipt/".$id)]);
        exit;*/
        
        // return url("sales/kreceipt/".$id);
    }

    public function viewHoldOrder(Request $request)
    {
        $id = $request->input("id");
        $order = DB::table("hold_order")->where("id", $id)->first();
        
        session()->put('holder_id', $id);

        echo $order->cart;
    }

    public function holdOrders(Request $request)
    {
        $orders = DB::table("hold_order")->where("status", 0)->get();
        foreach ($orders as $order) {
            $user = User::find($order->user_id);
            $table = DB::table("tables")->where("id", $order->table_id)->first();
            $order->username = "";
            if (!empty($user)) {
                $order->username = $user->name;
                $order->table = "No Table Found";
                if(!empty($table))
                $order->table = $table->table_name;
            }
        }
        echo json_encode($orders);
    }

    public function removeHoldOrder(Request $request)
    {
        $id = $request->input("id");
        DB::table("hold_order")->where("id", $id)->delete();
    }
    
    
    public function storeProduct(Request $request)
    {
        $prices = array();
        $titles = array();
        $prices[] = $request->input("price");
        $titles[] = "M";
        $name = $request->input("name");
        $description = $request->input("description");
       
        $data['prices'] = json_encode($prices);
        $data['titles'] = json_encode($titles);
        $data['name'] = $name;
        $data['description'] = $description;
        
        $product = \App\Product::insertGetId($data);
        return  $product;
    }

    
}
