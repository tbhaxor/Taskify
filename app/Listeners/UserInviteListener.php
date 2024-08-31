<?php

namespace App\Listeners;

use App\Events\UserInvite\CreateUserInviteEvent;
use App\Events\UserInvite\DeleteUserInviteEvent;
use App\Mail\UserInvite\CreateUserInviteMail;
use App\Mail\UserInvite\DeleteUserInviteMail;
use Illuminate\Support\Facades\Mail;

class UserInviteListener
{
    public function handleCreated(CreateUserInviteEvent $event): void
    {
        Mail::to($event->invite)->send(new CreateUserInviteMail($event->invite));
    }


    public function handleDeleted(DeleteUserInviteEvent $event): void
    {
        Mail::to($event->invite)->send(new DeleteUserInviteMail($event->invite));
    }

}
