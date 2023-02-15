<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\User;
use App\Order;
use App\Vehicle;
use Validator;
use Response;
use Auth;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
     public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(), [
            'email' => 'required',
            'password' => 'required'
            ]
        );
            
        if ($validator->fails()) {
            return Response::json(
                array(
                        'error' => $validator->getMessageBag()->toArray()
                ), 404
            );
        }
        
        $email = $request->input("email");
        $password = $request->input("password");
        
        if (Auth::attempt(['email' => $email, 'password' => $password ])) {
            $user = Auth::user();
            $data = array(
				"is_loggedin" => true,
				"name" => $user->name,
				"user_id" => $user->id
            );
			
			$response = array(
				"status" => true,
				"data" => $data
			);
            return Response::json($data, 200);
        } else {
            return Response::json(
                array(
                'error' => "Something wrong with your email or password"
                ), 404
            );
        }
    }

}
