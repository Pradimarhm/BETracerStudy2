<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json(['data' => User::all()]);
        // $users = User::with('alumni')->where('role', 'alumni')->get();
        // return response()->json(['data' => $users]);
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->service->createUser($request->validated());
        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->service->updateUser($id, $request->validated());
        return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    }

    public function destroy($id)
    {
        User::destroy($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}
