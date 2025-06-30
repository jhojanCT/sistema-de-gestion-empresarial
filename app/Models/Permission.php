<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'group'
    ];

    protected $casts = [
        'name' => 'string',
        'guard_name' => 'string',
        'description' => 'string',
        'group' => 'string'
    ];
} 