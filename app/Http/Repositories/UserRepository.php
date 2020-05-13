<?php

namespace App\Http\Repositories;

use App\User;

class UserRepository extends BaseRepository
{
    public $repository_name = 'User';

    public function __construct()
    {
        $this->model = new User;
    }

    public function setCreateDefaultAttribute($data)
    {
        $data = $data+[];
        return $data;
    }

    public function setUpdateDefaultAttribute($data, $model)
    {
        $data = $data+[];
        return $data;
    }

    public function setDestroyDefaultAttribute($model)
    {
        return [];
    }
}
