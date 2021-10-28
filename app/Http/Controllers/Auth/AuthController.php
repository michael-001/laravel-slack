<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegistrationRequest;
use App\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @param UserRegistrationRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|JsonResponse|\Illuminate\Http\Response
     */
    public function register(UserRegistrationRequest $request)
    {
        try {
            return response([
                "data" => $this->registerUser($request),
                "message" => 'Successfully created user'
            ], 201);
        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage()
            ], 400);
        }
    }


    private function registerUser($request)
    {
        return User::create([
           'first_name'=>$request->first_name,
           'last_name'=>$request->last_name,
           'email'=>$request->email,
           'handle'=>$request->handle,
           'password'=>bcrypt($request->password),
        ]);

    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
                'password' => 'required'
            ]);

            return response([
                "data" => $this->loginUser($request),
                "message" => 'Successfully logged in!'
            ], 201);
        } catch (\Exception $e) {
            return response([
                "data" => '',
                "message" => $e->getMessage()
            ], 400);
        }
    }

    private function loginUser($request)
    {
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => ['The email and/or password combination does not exist']
            ]);
        }

        return [
            "access_token"=>$user->createToken('app token')->accessToken];
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            "data" => '',
            "message" => 'Successfully logout out!'
        ], 201);
    }
}
