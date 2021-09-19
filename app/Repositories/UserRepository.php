<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository extends BaseRepository
{
    private $model;

    private $with = [
        'avatar:id,path', 'groupUser:id,user_id,group_id', 'groupUser.group:id,name'
    ];

    public function __construct(User $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    public function getDetails($id)
    {
        return $this->model
            ->select('id', 'username', 'email', 'avatar_id')
            ->with($this->with)
            ->findOrFail($id);
    }

    public function getDetailsByUsername($username)
    {
        return $this->model
            ->select('id', 'username', 'email', 'avatar_id', 'password')
            ->with($this->with)
            ->where('username', $username)
            ->first();
    }

    public function getDetailsByToken($token)
    {
        return $this->model
            ->select('id', 'username', 'email', 'avatar_id')
            ->with($this->with)
            ->where([
                'token' => $token,
                [
                    'token_last_validity_timestamp', '>=', now()
                ]
            ])
            ->first();
    }

    public function getAllUsers() {
        $filter = request()->query('Filter', null);
        $field = request()->query('Field', '*');
        $start = request()->query('Start', 0);
        $limit = request()->query('Limit', 10);

        $whereConditions = [];
        $groupIds = [];

        if($filter) {
            $data = $this->getFilterData($filter);
            $whereConditions = $data['whereConditions'];
            $groupIds = $data['groupIds'];
        }

        return $this->model
            ->with($this->with)
            ->when(count($whereConditions) > 0, function($query) use ($whereConditions) {
                $query->where($whereConditions);
            })
            ->whereHas('groupUser', function($query) use ($groupIds) {
                $query->when(count($groupIds) > 0, function ($query) use ($groupIds) {
                    $query->whereIn('group_id', $groupIds);
                });
            })
            ->selectRaw($field)
            ->skip($start)
            ->take($limit)
            ->get();
    }

    private function getFilterData($filter) {
        $filter = explode(';', preg_replace("#'#",'', $filter));

        $whereConditions = [];
        $groupIds = [];

        foreach($filter as $singleFilter) {
            $exactSearch = explode('=', $singleFilter);
            $whereInSearch = explode(':', $singleFilter);

            if(count($exactSearch) == 2 && $exactSearch[0] == 'group' && count($whereInSearch) == 1) {
                $groupIds = [$exactSearch[1]];
            } elseif(count($exactSearch) == 2 && count($whereInSearch) == 1) {
                $whereConditions[$exactSearch[0]] = $exactSearch[1];
            } elseif(count($whereInSearch) > 1) {
                $groupIds = explode(':', $exactSearch[1]);
            }
        }

        return [
            'whereConditions' => $whereConditions,
            'groupIds' => $groupIds,
        ];
    }
}