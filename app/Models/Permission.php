<?php

namespace App\Models;

use App\Enums\UserPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperPermission
 */
class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'value'
    ];

    protected $casts = [
        'value' => UserPermission::class
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
