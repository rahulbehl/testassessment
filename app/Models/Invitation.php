<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'email',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $appends = [];

    protected $casts = [];
}
