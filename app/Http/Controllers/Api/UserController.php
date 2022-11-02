<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        if(count($users) > 0){

            return response([
                'message' => 'Retrieve All Success',
                'data' => $users
            ], 200);
        }

        return response([

            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    public function show($id)
    {
        $user = User::find($id);

        if(!is_null($user)) {

            return response([
                'message' => 'Retrieve User Success',
                'data' => $user
            ], 200);
        }

        return response([
            'message' => 'User Not Found',
            'data' => null
        ], 404);
    }

   
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if(is_null($user)) {
            return response([
                'message' => 'User Not Found',
                'data' => null
            ]. 404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [

            'name' => 'required|max:60',
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => 'required|min:8|regex:/^(?=.*?[A-Z])(?=.*?[0-9])(?=.*[#?!@$%^&*-]).{0,7}$/',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
            $user->name = $updateData['name'];
            $user->email = $updateData['email'];
            $user->password = $updateData['password'];

        if($user->save()) {

            return response([
                'message' => 'Update User Success',
                'data' => $user
            ], 200);
        }

        return response([
            'message' => 'Update User Failed',
            'data' => null
        ], 400);
    
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if(is_null($user)) {

            return response([
                'message' => 'User Not Found',
                'data' => null
            ], 404);
        }

        if($user->delete()) {
            
            return response([
                'message' => 'Delete User Success',
                'data' => $user
            ], 200);
        }

        return response([
            'message' => 'Delete User Failed',
            'data' => null
        ], 400);
    }
}
