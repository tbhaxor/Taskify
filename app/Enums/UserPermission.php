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
            'Editor' => self::all()->filter(fn($permission) => !Str::contains($permission->name, 'CREATE')),
            'Viewer' => self::all()
                ->filter(fn($permission) => Str::startsWith($permission->name, 'VIEW'))
                ->values(),
            'Task Manager' => self::all()
                ->filter(fn($permission) => Str::endsWith($permission->name, 'TASKS'))
                ->push(self::VIEW_GROUPS)
                ->values(),
            default => collect(),
        };
    }
}
