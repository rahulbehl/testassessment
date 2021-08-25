<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;
    use Authenticatable,
        Authorizable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'user_name',
        'password',
        'role',
        'status',
        'registered_at',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
    ];

    protected $casts = [
    ];

}
