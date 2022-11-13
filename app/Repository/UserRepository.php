<?php

namespace App\Repository;

use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    static public function create($fields)
    {
        return UserModel::create([
            "name"      => $fields['name'],
            "email"     => $fields['email'],
            "password"  => Hash::make($fields['password']),
        ]);
    }
}
