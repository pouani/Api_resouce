<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.'
        ];

        $input     = $request->only('email','password');
        $validator = Validator::make($input, $rules, $customMessages);

        $user = User::where('email',$request->email)->first();

        $credentials = request(['email', 'password']);

            if ($validator->fails())
            {
                return response()->json(['message' => $validator->errors()->first()],401);
            }

            if(User::where('email',$request->email)->doesntExist()){
                return response()->json([
                    'message' => 'This account doesn\'t exists'
                ],401);
            }
            // elseif($user->email_verified==0)
            // {
            //     return response()->json([
            //         'message' => 'Please go to check your email verification in your mailbox'
            //     ],401);
            // }
            elseif(!Auth::attempt($credentials))
            {
                return response()->json([
                    'message' => 'Your email or password is incorrect'
                ],401);
            }
            else{
                $user = $request->user();
                $token = $user->createToken($user->name);
                $response['token'] = 'Bearer ' . $token->accessToken;
                DB::table('oauth_access_tokens')->where('id', $token->token->id)
                ->update(['expires_at'=>Carbon::now()->addMinutes(60)]);

                return response()->json([
                    'user'=>auth()->user()->name,
                    'access_token' => $token->accessToken,
                    'token_type' => 'Bearer',
                    'expires_at'=>Carbon::now()->addMinutes(60),
                    'created_at'=>Carbon::now()
                ],200);
            }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);
   
        if($validator->fails()){
            return response()->json([
                "success" => false,
                'message' => $validator->errors()
            ]);       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('authToken')->accessToken;
        $success['name'] =  $user->name;
        $token = $user->createToken($user->name);

        return response()->json([
            'success' => true,
            'access_token' => $token->accessToken,
            'message' => 'Enregistrement réussi.',
        ]);
    }
}
