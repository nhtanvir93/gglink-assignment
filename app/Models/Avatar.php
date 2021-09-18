<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Avatar extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $appends = array('url');

    protected static function boot() {
        parent::boot();

        static::creating(function($query) {
            $query->created_at = Carbon::now();
        });

        static::updating(function($query) {
            $query->updated_at = Carbon::now();
        });
    }

    public function getUrlAttribute()
    {
        $file = 'storage/' .$this->path;
        return asset($file);
    }
}
