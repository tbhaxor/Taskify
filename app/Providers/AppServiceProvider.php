<?php

namespace App\Providers;

use App\Events\GroupSharing\CreateGroupSharingEvent;
use App\Events\GroupSharing\DeleteGroupSharingEvent;
use App\Events\GroupSharing\EditGroupSharingEvent;
use App\Events\UserInvite\CreateUserInviteEvent;
use App\Events\UserInvite\DeleteUserInviteEvent;
use App\Listeners\GroupSharingListener;
use App\Listeners\UserInviteListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $socialiteWasCalled) {
            $socialiteWasCalled->extendSocialite('zitadel', \SocialiteProviders\Zitadel\Provider::class);
        });

        if (!$this->app->isLocal()) {
            URL::forceScheme('https');
        }

        Event::listen(CreateUserInviteEvent::class, [UserInviteListener::class, 'handleCreated']);
        Event::listen(DeleteUserInviteEvent::class, [UserInviteListener::class, 'handleDeleted']);
        Event::listen(CreateGroupSharingEvent::class, [GroupSharingListener::class, 'handleCreated']);
        Event::listen(DeleteGroupSharingEvent::class, [GroupSharingListener::class, 'handleDeleted']);
        Event::listen(EditGroupSharingEvent::class, [GroupSharingListener::class, 'handleEdit']);
    }
}
