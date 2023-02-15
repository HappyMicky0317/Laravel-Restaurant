<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Hash;
class ProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $data = [
            'user'  => Auth::user(),
			'roles' => Role::get()
        ];

        return view('backend.settings.users.profile', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
		 $form = $request->all();
         if(empty($request->input('password'))) { 
			unset($form['password']);
		 }
		 

        $user = Auth::user();
        $user->update($form);

        return redirect('settings/profile')
            ->with('message-success', 'Profile updated!');
    }
	
	
	//// User Password Change request from profile
    public function updatePassword(Request $request) 
    {
       $user = Auth::user();
        $user_id = Auth::user()->id;
        $old_password = $request->input("user_password");
        
        if(strlen($request->input("user_password")) < 6) { 
            $success = array(
            "error" => 1,
            "message" => "Password must be at least 6 character"
            );
                    
            echo  json_encode($success);exit;
        }
            
        if(Hash::check($old_password, $user->password)) {
            
    
            if(!empty($request->input("new_email"))) {
                $user = User::where("email", $request->input("new_email"))->first();
                if(count($user) > 0) { 
                    $success = array(
                    "error" => 1,
                    "message" => ""
                    );
                    
                    echo  json_encode($success);exit;
                }
            
                $data = array(
					'email' => $request->input('new_email')
                );
                User::where("id", $user_id)->update($data);
                $success = array(
                "error" => 0,
                "message" => "Password Changed"
                );
                    
                echo  json_encode($success);exit;
            }
        
            if(!empty($request->input("new_password"))) { 
                $data = array(
                'password' => bcrypt($request->input("new_password"))
                );
                User::where("id", $user_id)->update($data);
                $success = array(
                "error" => 0,
                "message" => "Password Changed"
                );
                    
                echo  json_encode($success);exit;
            }
        
        } else {
            $success = array(
            "error" => 1,
            "message" => "Incorrect Current Password"
            );
                    
            echo  json_encode($success);exit;
        }
        

    }
}
