<?php

namespace App\Observers;

use App\Jobs\SendPushNotification;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\UserDetail;

class NotificationObserver
{
    /**
     * @param NotificationUser $notificationUser
     */
    public function created(NotificationUser $notificationUser)
    {

        if (strpos($notificationUser->notification->message, '[name]') !== false) {

            if ($notificationUser->notification->sender->name) {
                $notificationUser->notification->message = str_replace('[name]', $notificationUser->notification->sender->name, $notificationUser->notification->message);
            }
        }
        $message    = $notificationUser->notification->message;
        $deviceData = $notificationUser->user->devices->toArray();
//        $checkpush  = $notificationUser->user->details->toArray();

        $notification = Notification::where('id', $notificationUser->notification_id)->first();
        if (strpos($notification->message, '[name]') !== false) {

            if ($notification->sender->name) {
                $notification->message = str_replace('[name]', $notification->sender->name, $notification->message);
            }
        }
        $users = UserDetail::where('user_id', $notificationUser->user_id)->first();

        if ($users->push_notifications == 1) {

            $job = new SendPushNotification($message.' from observer', $deviceData, $notification->toArray());
            dispatch($job);
        }


    }
}
