<?php

namespace App\Services;

use App\Models\User;
use App\Models\Alumni;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            // Jika rolenya alumni, buatkan record di tabel alumni
            if ($user->role === 'alumni') {
                Alumni::create([
                    'user_id' => $user->id,
                    // 'name' => $user->username,
                    'name' => $data['name'],
                ]);
            }

            return $user;
        });
    }

    public function updateUser(int $id, array $data)
    {
        $user = User::findOrFail($id);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return $user;
    }
}
