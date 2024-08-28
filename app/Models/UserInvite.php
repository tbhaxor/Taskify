<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property User $invitedBy
 * @mixin IdeHelperUserInvite
 */
class UserInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'group_id',
        'email',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function __get($key)
    {
        return match ($key) {
            'invitedBy' => $this->group->owner,
            default => parent::__get($key)
        };
    }
}

