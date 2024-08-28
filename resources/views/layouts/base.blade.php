@php use Illuminate\Support\Env; @endphp
    <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    @yield('styles')
</head>

<body>
@if (Env::get('APP_SHOW_DEPLOY_BANNER') == 'true')
    <div style="margin: 0; padding: 1em" class="alert alert-warning text-center" role="alert">
        This is <strong>deployed on a free tier</strong> of a <a href="https://render.com/">https://render.com</a>,
        <strong>after inactivity all the data will be wiped!</strong>
    </div>
@endif
@auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('group.index') }}">Taskify</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown"
                    aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link @if (Route::getCurrentRoute()->getName() == 'group.index') active @endif"
                           href="{{ route('group.index') }}">Task Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if (Route::getCurrentRoute()->getName() == 'role.index') active @endif"
                           href="{{ route('role.index') }}">Roles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page"
                           href="{{ config('services.zitadel.base_url') . '/ui/console' }}">Profile Settings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-link" href="{{ route('auth.logout') }}">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
@endauth

<main class="container mt-4">
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
</script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@yield('scripts')
</body>

</html>
