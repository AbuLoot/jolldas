<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

use Mobizon\MobizonApi;

trait SmsSendTrait {

    /**
     * This example illustrates how to send single SMS message using Mobizon API.
     *
     * API documentation: https://help.mobizon.com/help/api-docs
     */

    public function sendSms($user) {

        $api = new MobizonApi('kz7b06e6a6963ff8743a5b02fcbe59316cee88351356b45a5f02608698124a45af0194', 'api.mobizon.kz');

        echo 'Send message...' . PHP_EOL;

        // Create sms-log.txt if not exists
        if (!Storage::exists('sms-log.txt')) {
            Storage::disk('local')->put('sms-log.txt', 'Start');
        }

        $alphaname = 'Jolldas Cargo';
        $data = [
            'recipient' => $user->tel,
            'text' => 'Test sms message to'.$user->name.' '.$user->lastname,
            'from' => $alphaname, //Optional, if you don't have registered alphaname, just skip this param and your message will be sent with our free common alphaname.
        ];

        // dd($api);

        if ($api->call('message', 'sendSMSMessage', $data)) {

            $messageId = $api->getData('messageId');

            // Record to sms-log.txt
            Storage::prepend('sms-log.txt', 'Message created with ID:' . $messageId . PHP_EOL);

            if ($messageId) {

                echo 'Get message info...' . PHP_EOL;

                $messageStatuses = $api->call('message', 'getSMSStatus', ['ids' => [$messageId, '13394', '11345', '4393']], [], true);

                if ($api->hasData()) {
                    foreach ($api->getData() as $messageInfo) {
                        echo 'Message # ' . $messageInfo->id . " status:\t" . $messageInfo->status . PHP_EOL;
                        // Storage::prepend('sms-log.txt', 'Message # ' . $messageInfo->id . " status:\t" . $messageInfo->status);
                    }
                }
            }
        }
        else {
            echo 'An error occurred while sending message: [' . $api->getCode() . '] ' . $api->getMessage() . 'See details below:' . PHP_EOL;
            var_dump(array($api->getCode(), $api->getData(), $api->getMessage()));
        }
    }
}