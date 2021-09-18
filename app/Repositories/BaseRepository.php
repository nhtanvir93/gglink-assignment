<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository
{
    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create($data)
    {
        return $this->model->create($data)->id;
    }

    public function update($data, $id)
    {
        $updated = $this->model->find($id)->update($data);

        return $updated ? $id : false;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }
}