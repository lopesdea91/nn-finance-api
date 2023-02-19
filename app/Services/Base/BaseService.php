<?php

namespace App\Services\Base;


class BaseService
{
  protected $model;
  protected $repository;

  public function all($args)
  {
    return $this->repository->all($args)->get();
  }

  public function id($id)
  {
    return $this->repository->id($id);
  }

  public function delete($id)
  {
    return $this->repository->delete($id);
  }

  public function enabled($id)
  {
    return $this->repository->id($id)->update(["enable" => "1"]);
  }

  public function disabled($id)
  {
    return $this->repository->id($id)->update(["enable" => "0"]);
  }

  public function query($args)
  {
    return $this->repository->all($args);
  }

  public function exist($id)
  {
    return $this->repository->existId($id);
  }
}
