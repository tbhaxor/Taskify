<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        return view('groups.index', [
            'groups' => Group::withCount('tasks')->get(),
        ]);
    }

    public function show(Group $group)
    {
        return view('groups.show', [
            'group' => $group
        ]);
    }

    public function edit(Request $request, Group $group)
    {
        if ($request->isMethod('GET')) {
            return view('groups.edit', [
                'group' => $group
            ]);
        }

        $group->update($request->validate([
            'title' => 'required|max:64',
            'decription' => ''
        ]));

        return to_route('group.show', [
            'group' => $group
        ]);
    }

    public function delete(Request $request, Group $group)
    {
        if ($request->isMethod('GET')) {
            return view('groups.delete', [
                'group' => $group
            ]);
        }

        $group->delete();

        return to_route('group.index', [
            'message' => 'Group is deleted.'
        ]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('GET')) {
            return view('groups.create');
        }

        Group::create($request->validate([
            'title' => 'required|max:64',
            'description' => ''
        ]));

        return to_route('group.index', [
            'message' => 'New group has been created.'
        ]);
    }
}
