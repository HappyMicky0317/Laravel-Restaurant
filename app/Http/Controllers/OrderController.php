<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Sale;
use Session;
use Validator;
use App\Http\Requests;

class OrderController extends Controller
{

    public function __construct() 
    {
        $this->middleware('auth');
    }

    /**
     * Page Lisitng on admin.
     */
    public function index() 
    {
        $data['incomplete'] = Sale::where("status", 2)->orderBy("id", "DESC")->limit(10)->get();
        $data['completed'] = Sale::where("status", 1)->orderBy("id", "DESC")->limit(10)->get();
        $data['canceled'] = Sale::where("status", 0)->orderBy("id", "DESC")->limit(10)->get();
        $data['title'] = "Orders";
        return view('backend.orders.index', $data);
    }

    public function orders() 
    {
        $orders = Sale::select("*" , "sales.id as id")->where("type", "order")->leftJoin("sale_items as s" , "s.sale_id" , '=', "sales.id" )->orderBy("sales.id", "DESC")->paginate(25);
        return view('backend.orders.allorders', ["orders" => $orders, "title" => "Orders"]);
    }

    public function ChangeStatus(Request $request) 
    {
        $incomplete = $request->input('incomplete');
        $canceled = $request->input('canceled');
        $completed = $request->input('completed');
        $IncompleteIds = array();
        $canceledIds = array();
        $CompletedIds = array();
        if (!empty($incomplete)) {
            foreach ($incomplete as $todo) {
                $IncompleteIds[] = $todo;
            }
        }
        if (!empty($completed)) {
            foreach ($completed as $inp) {
                $CompletedIds[] = $inp;
            }
        }
        if (!empty($canceled)) {
            foreach ($canceled as $com) {
                $canceledIds[] = $com;
            }
        }
        Sale::whereIn('id', $IncompleteIds)->update(array("status" => 2));
        Sale::whereIn('id', $CompletedIds)->update(array("status" => 1));
        Sale::whereIn('id', $canceledIds)->update(array("status" => 0));
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
		
		
		if($request->input("payment_with") == "card") { 
			$cc_number = $request->input("cc_number");
			$cc_month = $request->input("cc_month");
			$cc_year = $request->input("cc_year");
			$cc_code = $request->input("cc_code");
			$amount = $request->input("total_cost");
			$amount *= 100;
			\Stripe\Stripe::setApiKey(env("STRIPE_SECRET"));
			try {
                    $token = \Stripe\Token::create(
                        array(
                        "card" => array(
                        "number" => $cc_number,
                        "exp_month" => $cc_month,
                        "exp_year" => $cc_year,
                        "cvc" => $cc_code
                        )
                        )
                    );
                } catch (\Stripe\Error\Card $e) {
                    $token = $e->getJsonBody();
                    $errors = array(
                    "error" => 1,
                    "message" => $token['error']['message']
                    );

                    echo  json_encode($errors);exit;
                }

                // Get the payment token submitted by the form:
                $stripeToken = $token['id'];

                // Create a Customer:
                $customer = \Stripe\Customer::create(
                    array(
                    "email" => Auth::user()->email,
                    "source" => $token,
                    )
                );

                // Charge the Customer instead of the card:
                $charge = \Stripe\Charge::create(
                    array(
                    "amount" => round($amount),
                    "currency" => "USD",
                    "customer" => $customer->id
                    )
                );
		}
		
			unset($form["cc_number"]);
			unset($form["cc_month"]);
			unset($form["cc_year"]);
			unset($form["cc_code"]);
			unset($form["total_cost"]);
			
			$sale = Sale::createAll($form);

				$errors = array(
                    "error" => 0,
                    "message" => "Thank you for your Order. We will contact you soon."
                    );
				echo  json_encode($errors);exit;
    }
	

}
