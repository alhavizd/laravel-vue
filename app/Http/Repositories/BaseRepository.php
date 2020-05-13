<?php

namespace App\Http\Repositories;

use App;
use Request;
use App\Exceptions\DataEmptyException;

class BaseRepository
{
    public $repository_name = '';

    public function setSortableAndSearchable($value='', $model_name = NULL)
    {
        if(!empty($model_name))$this->{$model_name}->sortableAndSearchableColumn = $value;
        if(empty($model_name))$this->model->sortableAndSearchableColumn = $value;
    }

    public function firstOrCreate($dataSame, $data)
    {
        $model = $this->model;
        return $model->firstOrCreate($dataSame, $data);
    }

    public function updateOrCreate($model, $data)
    {
        if(!is_object($model)) {
            $model = $this->model->find($model);
        }
        if($model === null) {
            return $this->create($data);
        }
        $data = $this->setUpdateDefaultAttribute($data, $model);
        $model->update($data);
        return  $model;
    }

    public function create($data)
    {
        $data = $this->setCreateDefaultAttribute($data);
        $model = $this->model;
        return $model->create($data);
    }

    public function update($model, $data)
    {
        if(!is_object($model))
        {
            $model = $this->model->where('status', '<>', 2)->find($model);
        }
        if($model === null) throw new DataEmptyException(__('admin/validation.dataNotExist',['attribute' => $this->repository_name]));
        $data = $this->setUpdateDefaultAttribute($data,$model);
        $model->update($data);
        return $model;
    }

    public function destroy($id)
    {
        if(!is_object($id))
        {
            $model = $this->model->where('status', '<>', 2)->find($id);
        }
        if($model === null) throw new DataEmptyException(__('admin/validation.dataNotExist',['attribute' => $this->repository_name]));
        $data = $this->setDestroyDefaultAttribute($model);
        $model->update($data);
        $model['repository_name'] = $this->repository_name;
        return $model;
    }

    public function setDestroyDefaultAttribute($model)
    {
        return [];
    }

    public function setCreateDefaultAttribute($data)
    {
    	return $data;
    }

    public function setUpdateDefaultAttribute($data, $model)
    {
    	return $data;
    }
}
