<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $hidden = [
        'password'
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function($query) {
            $query->created_at = Carbon::now();
        });

        static::updating(function($query) {
            $query->updated_at = Carbon::now();
        });
    }

    public function avatar() {
        return $this->belongsTo(Avatar::class);
    }

    public function groupUser() {
        return $this->hasMany(GroupUser::class)->whereNull('deleted_at');
    }

    public function setPasswordAttribute($password) {
        if(strlen($password) > 0) {
            $this->attributes['password'] = Hash::make($password);
        }
    }
}
