<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'store_name' => 'required|string|max:255',
            'email' => 'required|email|unique:merchants,email',
            'password' => 'required|string|min:9',
        ]);
        $merchant = Merchant::create($data);
        $token = $merchant->createToken('api')->plainTextToken;

        return response()->json(['merchant'=> $merchant, 'token'=> $token],201);
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $merchant = Merchant::where('email', $data['email'])->first();
        
        if (! $merchant ||! Hash::check($data['password'], $merchant->password)){
            return response()->json(['message'=>'Invalid Credentials'], 401);
        }
        $token = $merchant->createToken('api')->plainTextToken;
        return response()->json(['merchant'=>$merchant, 'token'=>$token],200);    

    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Logged out']);
    }
}
