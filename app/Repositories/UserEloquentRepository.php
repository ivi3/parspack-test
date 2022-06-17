<?php

namespace App\Repositories;

use App\Models\User;

class UserEloquentRepository implements UserRepositoryInterface
{

    public function __construct(protected User $user)
    {
    }

    public function createUser(array $array)
    {
        return $this->user->create($array);
    }

    public function selectUser(array $array)
    {
        return $this->user->where($array)->first();
    }
}
