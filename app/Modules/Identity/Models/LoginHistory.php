<?php

namespace App\Modules\Identity\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'ip', 'user_agent', 'logged_at'])]
class LoginHistory extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }
}
