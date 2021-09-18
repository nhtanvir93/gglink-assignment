<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class GroupUser extends Model
{
    use SoftDeletes;

    public $table = 'group_user';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected static function boot() {
        parent::boot();

        static::creating(function($query) {
            $query->created_at = Carbon::now();
        });

        static::updating(function($query) {
            $query->updated_at = Carbon::now();
        });
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
