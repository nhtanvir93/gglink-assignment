<?php

namespace App\Repositories;

use App\Models\Avatar;
use Illuminate\Support\Facades\Storage;

class AvatarRepository extends BaseRepository
{
    private $model;

    public function __construct(Avatar $model)
    {
        parent::__construct($model);

        $this->model = $model;
    }

    private function upload($file, $path) {
        return Storage::putFile($path, $file);
    }

    private function remove($path) {
        Storage::delete($path);
    }

    public function getDetails($id) {
        return $this->model
            ->select('id', 'path', 'content_type', 'extension', 'size')
            ->find($id);
    }

    public function uploadAndCreate($file, $path) {
        $path = $this->upload($file, $path);

        return parent::create([
            'path' => $path,
            'content_type' => $file->getClientMimeType(),
            'extension' => $file->extension(),
            'size' => $file->getSize(),
        ]);
    }

    public function uploadAndUpdate($id, $file, $path) {
        $avatar = $this->getDetails($id);

        $this->remove($avatar->path);
        $path = $this->upload($file, $path);

        return parent::update([
            'path' => $path,
            'content_type' => $file->getClientMimeType(),
            'extension' => $file->extension(),
            'size' => $file->getSize(),
        ], $id);
    }
}