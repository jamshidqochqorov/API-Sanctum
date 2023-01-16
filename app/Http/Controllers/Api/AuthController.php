<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
    public function me(Request $request){
        return $request->user();
    }

    public function login(Request $request):Response
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
           return response(['text'=>'The provided credentials are incorrect.']);
        }
;
        return response(['token'=>$user->createToken($user->name)->plainTextToken]);
    }

    public function logout(Request $request):Response
    {
        // Revoke the token that was used to authenticate the current request...
        $request->user()->currentAccessToken()->delete();
        return response([
            "status"=>true,
            "text"=>'logging out'
        ]);
    }
    public function register(Request $request){

       $request->validate([
           'name'=>'required|max:255',
           'email' => 'required|email',
           'password' => 'required',
       ]);
       $user = User::create([
           'name'=>$request->name,
           'email'=>$request->email,
           'password'=>$request->password,
       ]);
       $token = $user->createToken($user->name)->plainTextToken;

       return response([
          'user'=>$user,
           'token'=>$token
       ]);
    }
}
