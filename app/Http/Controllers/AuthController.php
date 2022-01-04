<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
   public function register(Request $request){
       $fileds = $request->validate([
           "name"=>"required|string",
           "email"=>"required|string|unique:users,email",
           "password"=>'required|string|confirmed'
       ]);

       $user = User::create([
           'name'=>$fileds['name'],
           'email'=>$fileds['email'],
           "password"=>bcrypt($fileds['password'])
       ]);

       $token = $user->createToken("myapptoken")->plainTextToken;

       $response =[
           "user"=>$user , 
           'token'=>$token
       ];

       return response($response , 201);
   }

    public function login(Request $request){
        $fileds = $request->validate([
            "email"=>"required|string",
            "password"=>'required|string'
        ]);
    $user = User::where('email',$fileds['email'])->first();

    if(!$user || !Hash::check($fileds['password'] , $user->password ) ){
        return response([
            "message"=>"Bad login"
        ] , 401);
    }
 
    $token = $user->createToken("myapptoken")->plainTextToken;
    $response =[
        "user"=>$user , 
        'token'=>$token
    ];

    return response($response , 201);

    }




   public function logout(Request $request){
       auth()->user()->tokens()->delete();
       return [
           "message"=>"Logged Out"
       ];
   }


}
