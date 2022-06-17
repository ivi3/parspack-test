<?php

namespace App\Repositories;

interface UserRepositoryInterface
{
    public function createUser(array $array);
    public function selectUser(array $array);
}
