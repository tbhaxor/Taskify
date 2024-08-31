<p>Hello User!</p>

<p>
    Your friend {{ $invite->invitedBy->name }} has invited you on taskify to collaborate on {{ $invite->group->title }}.
</p>

<p>You are receiving this email because you don't have account on the application. Please <a
        href="{{ route('auth.login', ['email' => $invite->email]) }}">create an account</a> to access the group.
</p>

<br />

Regards, <br>
{{ config('app.name') }}
