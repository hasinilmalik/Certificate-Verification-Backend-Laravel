<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            // 'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        
        $user = User::with('student','score')->where('email', $request->email)->first();

        if($user->role_id==1){
            if (!$user|| !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login Failed!',
                ]);
            }
            $user = User::find($user->id);
            return response()->json([
                'success' => true,
                'isAdmin' => true,
                'message' => 'Login Success!',
                'data'    => $user,
                'token'   => $user->createToken('authToken')->accessToken    
            ]);
        }else{
            return response()->json([
                'success' => true,
                'isAdmin' => false,
                'message' => 'Login Success!',
                'data'    => $user,
                'token'   => $user->createToken('authToken')->accessToken    
            ]);
        }
        
        
    }
    
    /**
    * logout
    *
    * @param  mixed $request
    * @return void
    */
    public function logout(Request $request)
    {
        $removeToken = $request->user()->tokens()->delete();
        
        if($removeToken) {
            return response()->json([
                'success' => true,
                'message' => 'Logout Success!',  
            ]);
        }
    }
}
