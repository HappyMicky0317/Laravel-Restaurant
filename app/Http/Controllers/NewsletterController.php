<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use DB;
class NewsletterController extends Controller
{
    public function store(Request $request) { 
		$email = $request->input('email');
		$email_already = DB::table("newsletters")->where("email" , $email)->first();
		if(count($email_already) > 0) { 
			echo "already"; exit;
		}
		DB::table("newsletters")->insert(array("email" => $email));
		echo "Thank you for Subscribe With Us";
	}
}
