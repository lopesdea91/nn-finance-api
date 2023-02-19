<?php

namespace App\Repository;

use App\Models\UserModel;
use App\Repository\Base\CrudRepository;

class UserRepository extends CrudRepository
{
    protected $model;
    protected $fields = ['name', 'email', 'password', 'type'];
    // protected $relationships = [''];

    public function __construct()
    {
        $this->model = new UserModel();
    }
}
