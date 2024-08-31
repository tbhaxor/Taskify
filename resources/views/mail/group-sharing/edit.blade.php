<p>
    Hello {{ $groupRole->user->name }}!
</p>

<p>
    Your access on
    <strong>
        <a href="{{ route('group.show', ['group' => $groupRole->group]) }}">{{ $groupRole->group->title }}</a>
    </strong>
    group has been updated to {{ $groupRole->role->name }}.
</p>

<br />
Regards, <br>
{{ config('app.name') }}
