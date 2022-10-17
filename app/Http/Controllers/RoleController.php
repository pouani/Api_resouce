<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    
    public function index()
    {
        \Gate::authorize('view', 'roles');

        return RoleResource::collection(Role::all());
    }

    public function store(Request $request)
    {
        \Gate::authorize('edit', 'roles');

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

        if($permissions = $request->input('permissions')){
            foreach($permissions as $permission_id){
                \DB::table('role_permission')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission_id
                ]);
            }
        }

        return response()->json([
            "success" => true,
            new RoleResource($role),
        ], 201);
    }

    
    public function show($id)
    {
        return new RoleResource(Role::find($id));
    }

    public function update(Request $request, $id)
    {
        \Gate::authorize('edit', 'roles');

        $role = Role::find($id);

        $role->update($request->only('name'));

        \DB::table('role_permission')->where('role_id', $role->id)->delete();

        if($permissions = $request->input('permissions')){
            foreach($permissions as $permission_id){
                \DB::table('role_permission')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permission_id
                ]);
            }
        }

        return response()->json([
            "success" => true,
            new RoleResource($role)
        ], 201);
    }

    public function destroy($id)
    {
        \Gate::authorize('edit', 'roles');
        
        \DB::table('role_permission')->where('role_id', $id)->delete();

        Role::destroy($id);

        return response()->json([
            "success" => true,
        ], 201);
    }
}
