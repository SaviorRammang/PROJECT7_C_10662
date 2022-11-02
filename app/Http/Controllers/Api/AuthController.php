<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
// use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request){
        $registrationData = $request->all(); // Mengambil seluruh data input dan menyimpan dalam variabel registrationData
        
        $validate = Validator::make($registrationData, [
            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8|regex:/^(?=.*?[A-Z])(?=.*?[0-9])(?=.*[#?!@$%^&*-]).{0,7}$/',
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',

        ]); // rule validasi input saat register

        if($validate->fails()) // mengecek apakah inputan sudah sesuai dengan rule validasi
            return response(['message' => $validate -> errors(), 400]); // megembalikan error validasi input

            $registrationData['password'] = bcrypt($request->password); // untuk meng-enkripsi password

            $user = User::create($registrationData); // membuat user baru

            return response([
                'message' => 'Register Success',
                'user' => $user
            ], 200); // return data dalam bentuk json
    }

    public function login(Request $request){
        $loginData = $request->all();

        $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
        ]);

        if($validate->fails()) 
            return response(['message' => $validate -> errors(), 400]); 

        if(!Auth::attempt($loginData))
        return response(['message' => 'Invalid Credentials'], 401);// mengembalikan error gagal login

        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken; // generate token

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]); // return data user dan token dalam bentuk json
    }
}
