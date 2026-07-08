<?php

namespace App\Helper;

use App\Models\Notification;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Support\Facades\Config;

class NotificationsHelper
{
    function sendPushNotifications($msg = '', $deviceObject = [], $extraPayLoadData = [])
    {


        $androidDeviceToken = [];
        $iosDeviceToken     = [];

        foreach ($deviceObject as $device) {
            if (strtolower($device['device_type']) == 'android') {
                $androidDeviceToken[] = $device['device_token'];
            } elseif (strtolower($device['device_type']) == 'ios') {
                $iosDeviceToken[] = $device['device_token'];
            }
        }

        if ($androidDeviceToken) {
//            dd(Config::get('pushNotification.fcm.apiKey'));
            $push = new PushNotification('fcm');
            $push->setMessage([
                'notification' => [
                    'title' => config('app.name'),
                    'body'  => $msg,
                    'sound' => 'default'
                ],
                'data'         => [
                    'extra_payload' => $extraPayLoadData
                ],
                'android'      => [
                    'ttl'          => '86400',
                    'notification' => [
                        'click_action' => 'MainActivity'
                    ]
                ]
            ])
                ->setApiKey(Config::get('pushnotification.fcm.apiKey'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($androidDeviceToken)
                ->send();

        }

        /*if ($androidDeviceToken) {
            $push = new PushNotification('fcm');
            $push->setMessage([
                'notification' => [
                    'title' => config('app.name'),
                    'body'  => $msg,
                    'sound' => 'default'
                ],
                'data'         => [
                    'action_type' => $extraPayLoadData['action_type'],
                    'ref_id'      => $extraPayLoadData['ref_id'],
                    'sender_id'   => $extraPayLoadData['sender_id']
                ],
                'android'      => [
                    'ttl'          => '86400',
                    'notification' => [
                        'click_action' => 'MainActivity'
                    ]
                ]
            ])
                ->setApiKey(Config::get('constants.pushNotification.fcm'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($androidDeviceToken)
                ->send();
        }*/

        /*Apn*/
        if ($iosDeviceToken) {

            $push = new PushNotification('fcm');
            $push->setMessage([
                'notification' => [
                    'title' => config('app.name'),
                    'body'  => $msg,
                    'sound' => 'default'
                ],
                'data'         => [
                    'extra_payload' => $extraPayLoadData
                ],
                'android'      => [
                    'ttl'          => '86400',
                    'notification' => [
                        'click_action' => 'MainActivity'
                    ]
                ]
            ])
                ->setApiKey(Config::get('pushnotification.fcm.apiKey'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($iosDeviceToken)
                ->send();

        }

        return true;
    }
    
    function sendPushNotificationsMessage($msg = '', $type, $user)
    {
        $androidDeviceToken = [];
        $iosDeviceToken     = [];
        $response = ['success' => false];
        $userDevices = $user->devices ?? [];
        $notificationUsers = null;

        foreach ($userDevices as $device) {
            if (strtolower($device['device_type']) == 'android') {
                $androidDeviceToken[] = $device['device_token'];
            } elseif (strtolower($device['device_type']) == 'ios') {
                $iosDeviceToken[] = $device['device_token'];
            }
        }

        if ($iosDeviceToken) {
            $push = new PushNotification('fcm');
            $notificationData = [
                'notification' => [
                    'title' => config('app.name'),
                    'body'  => $msg
                ],
                'data' => [
                    'type' => $type,
                ],
                // 'ios'   => [
                //     'ttl'   => '86400',
                //     'notification'  => [
                //         'click_action'  => 'MainActivity'
                //     ]
                // ]
            ];

            $responseData = $push->setMessage($notificationData)
                ->setApiKey(Config::get('pushnotification.fcm.apiKey'))
                ->setConfig(['dry_run' => false])
                ->setDevicesToken($iosDeviceToken)
                ->send();

            if($responseData->feedback)
                $response = $responseData->feedback;

            $notification = Notification::create([
                'sender_id' => null,
                'url' => null,
                'action_type' => $type,
                'ref_id' => null,
                'message' => $msg,
                'response' => $response,
                'devices' => [
                    'android' => $androidDeviceToken,
                    'ios' => $iosDeviceToken
                ],
                'status' => 1
            ]);

            $notificationUsers = $notification->notificationUsers()->create([
                'user_id' => $user->id,
                'status' => 1
            ]);
        }

        return $notificationUsers;
    }
}


