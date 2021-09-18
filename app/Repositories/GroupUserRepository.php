<?php

namespace App\Repositories;

use App\Models\GroupUser;

class GroupUserRepository extends BaseRepository
{
    private $model;

    public function __construct(GroupUser $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    public function createAll($groups, $userId)
    {
        $groupUsers = [];

        foreach($groups as $groupId) {
            $groupUsers[] = [
                'user_id' => $userId,
                'group_id' => $groupId
            ];
        }

        $this->model->insert($groupUsers);
    }

    private function deleteAll($userId)
    {
        $this->model->where('user_id', $userId)->delete();
    }

    public function updateAll($groups, $userId)
    {
        $this->deleteAll($userId);
        $this->createAll($groups, $userId);
    }
}