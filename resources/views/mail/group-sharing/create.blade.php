<p>
    Hello {{ $groupRole->user->name }}!
</p>

<p>
    You have been granted {{ $groupRole->role->name }} access to
    <strong>
        <a href="{{ route('group.show', ['group' => $groupRole->group]) }}">{{ $groupRole->group->title }}</a>
    </strong>
    group by {{ $owner->name }}.
</p>

<br />
Regards, <br>
{{ config('app.name') }}
