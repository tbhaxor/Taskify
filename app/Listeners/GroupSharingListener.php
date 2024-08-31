<?php

namespace App\Listeners;

use App\Events\GroupSharing\CreateGroupSharingEvent;
use App\Events\GroupSharing\DeleteGroupSharingEvent;
use App\Events\GroupSharing\EditGroupSharingEvent;
use App\Mail\GroupSharing\CreateGroupSharingMail;
use App\Mail\GroupSharing\DeleteGroupSharingMail;
use App\Mail\GroupSharing\EditGroupSharingMail;
use Illuminate\Support\Facades\Mail;

class GroupSharingListener
{

    public function handleCreated(CreateGroupSharingEvent $event): void
    {
        Mail::to($event->groupRole->user)->send(new CreateGroupSharingMail($event->groupRole, $event->groupRole->group->owner));
    }

    public function handleDeleted(DeleteGroupSharingEvent $event): void
    {
        Mail::to($event->groupRole->user)->send(new DeleteGroupSharingMail($event->groupRole));
    }

    public function handleEdit(EditGroupSharingEvent $event): void
    {
        Mail::to($event->groupRole->user)->send(new EditGroupSharingMail($event->groupRole));
    }

}
