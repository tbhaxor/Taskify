<p>
    Hello {{ $groupRole->user->name }}!
</p>

<p>
    Your access to <strong>{{ $groupRole->group->title }}</strong> group has been revoked.
</p>

<br />
Regards, <br>
{{ config('app.name') }}
