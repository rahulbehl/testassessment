<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationCode extends Model
{
    protected $table = 'activation_code';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'code',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = [];

    protected $casts = [];
}
