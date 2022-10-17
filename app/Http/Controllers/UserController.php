<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        \Gate::authorize('view', 'users');

        $users = User::paginate();
        return UserResource::collection($users);
    }

    public function show($id)
    {
        \Gate::authorize('view', 'users');

        $user = User::find($id);
        return new UserResource($user);
    }

    public function store(Request $request)
    {
        \Gate::authorize('edit', 'users');

        $validator =  Validator::make($request->all(),[
            'name' =>'required|string',
            'email' =>'required|string|email|unique:users,email',
            'password' =>'required|string|min:8',
            'role_id' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }

        $input = $request->all();

        try {
            $user = User::create($input);
            return response()->json([
                new UserResource($user),
                'success' =>true,
                'message' => 'Nouveau utilisateur créé!!!'
            ], 201);
        }catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        \Gate::authorize('edit', 'users');
        
        $user = User::find($id);

        $validator =  Validator::make($request->all(),[
            'name' =>'required|string',
            'email' =>'required|string|email|unique:users,email',
            'password' =>'required|string|min:8',
            'role_id' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }

        try {
            $user->update([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'last_name' => $request->input('last_name'),
                'password' => Hash::make($request->input('password')),
                'role_id' => $request->input('role_id'),
            ]);

            return response()->json([
                new UserResource($user),
                'success' =>true,
                'message' => 'utilisateur modifié!!!'
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        \Gate::authorize('edit', 'users');
        
        try{
            User::destroy($id);
            return response()->json([
                'success' =>true,
                'message' => 'utilisateur supprimé!!!'
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function user()
    {
        $user = \Auth::user();
        return (new UserResource($user))->additional([
            'data' => [
                'permissions' => $user->permissions(),
            ]
        ]);
    }

    public function updateInfo(Request $request)
    {
        $user = \Auth::user();

        $validator =  Validator::make($request->all(),[
            'name' =>'required|string',
            'last_name' =>'required|string',
            'email' =>'required|string|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }

        $user->update($request->only('name', 'last_name', 'email', 'role_id'));

        return response()->json([
            new UserResource($user),
            'message' => 'profile updated successfully',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = \Auth::user();

        $validator =  Validator::make($request->all(),[
            'password' =>'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);
    }
}
