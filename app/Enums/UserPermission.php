<?php

namespace App\Enums;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

enum UserPermission: string
{
    case VIEW_TASKS = 'view:tasks';
    case CREATE_TASKS = 'create:tasks';
    case EDIT_TASKS = 'edit:tasks';
    case DELETE_TASKS = 'delete:tasks';

    case VIEW_GROUPS = 'view:groups';
    case CREATE_GROUPS = 'create:groups';
    case EDIT_GROUPS = 'edit:groups';
    case DELETE_GROUPS = 'delete:groups';

    case VIEW_GROUP_INVITES = 'view:group_invites';
    case CREATE_GROUP_INVITES = 'create:group_invites';
    case EDIT_GROUP_INVITES = 'edit:group_invites';
    case DELETE_GROUP_INVITES = 'delete:group_invites';

    public function description(): string
    {
        return Str::apa(Str::replace('_', ' ', $this->name));
    }

    /**
     * @return Collection<int, static>
     */
    static function all(): Collection
    {
        return collect(self::cases());
    }

    /**
     * @param string $name
     * @return Collection<int, static>
     */
    static function forRole(string $name): Collection
    {
        return match ($name) {
            'Admin' => self::all()->values(),
            'Editor' => self::all()
                ->filter(fn($permission) => !Str::endsWith($permission->name, 'GROUP_INVITES'))
                ->values(),
            'Viewer' => self::all()
                ->filter(fn($permission) => Str::startsWith($permission->name, 'VIEW'))
                ->values(),
            'Task Manager' => collect([self::VIEW_GROUPS])
                ->merge(self::all())->filter(fn($permission) => Str::endsWith($permission->name, 'TASKS'))
                ->values(),
            'Group Manager' => self::all()
                ->filter(fn($permission) => $permission->name === 'VIEW_GROUPS' || Str::endsWith($permission->name, 'GROUP_INVITES'))
                ->values(),
            default => collect(),
        };
    }
}
