<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, UserGroupRole::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return int
     */
    public function getAuthIdentifier(): int
    {
        return (int)$this->id;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return ?string
     */
    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class)->orWhereNull('user_id');
    }

    /**
     * @param Group $group
     * @return HasOneThrough<Group>
     */
    public function roleOnGroup(Group $group): HasOneThrough
    {
        return $this->hasOneThrough(
            Role::class,
            UserGroupRole::class,
            'user_id', // The field referencing current model in the through class
            'id', // The local field in the related schema
            'id',
            'role_id' // The field referencing related pk in the through class
        )->where('group_id', $group->id);
    }

}
