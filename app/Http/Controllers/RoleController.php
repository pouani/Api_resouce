<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    
    public function index()
    {
        return Role::all();
    }

    public function store(Request $request)
    {
        $validator =  Validator::make($request->all(),[
            'name' =>'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => $validator->errors()
            ]);
        }

        $role = Role::create($request->only('name'));

        return response()->json([
            "success" => true,
            "role" => $role
        ], 201);
    }

    
    public function show($id)
    {
        return Role::find($id);
    }

    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        $role->update($request->only('name'));

        return response()->json([
            "success" => true,
            "role" => $role
        ], 201);
    }

    public function destroy($id)
    {
        Role::destroy($id);

        return response()->json([
            "success" => true,
        ], 201);
    }
}
