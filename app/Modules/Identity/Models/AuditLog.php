<?php

namespace App\Modules\Identity\Models;

use App\Support\Tenancy\BelongsToTenant;
use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['tenant_id', 'user_id', 'action', 'subject_type', 'subject_id', 'before_json', 'after_json', 'ip', 'created_at'])]
class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use BelongsToTenant, HasFactory;

    public $timestamps = false;

    protected static function newFactory(): Factory
    {
        return AuditLogFactory::new();
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
