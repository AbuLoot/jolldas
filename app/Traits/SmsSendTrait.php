<?php

namespace App\Traits;

use Auth;
use Mobizon\MobizonApi;

trait SmsSendTrait {

    /**
     * This example illustrates how to send single SMS message using Mobizon API.
     *
     * API documentation: https://help.mobizon.com/help/api-docs
     */

    public function sendSms() {

        $api = new MobizonApi('kz7b06e6a6963ff8743a5b02fcbe59316cee88351356b45a5f02608698124a45af0194', 'api.mobizon.kz');

        echo 'Send message...' . PHP_EOL;

        $alphaname = 'Jolldas Cargo';

        if ($api->call('message', 'sendSMSMessage', [
                'recipient' => '+77078875631',
                'text' => 'Test sms message to',
                'from' => $alphaname,
                //Optional, if you don't have registered alphaname, just skip this param and your message will be sent with our free common alphaname.
            ])) {

            $messageId = $api->getData('messageId');

            echo 'Message created with ID:' . $messageId . PHP_EOL;

            if ($messageId) {

                echo 'Get message info...' . PHP_EOL;

                $messageStatuses = $api->call('message', 'getSMSStatus', ['ids' => [$messageId, '13394', '11345', '4393']], [], true);

                if ($api->hasData()) {
                    foreach ($api->getData() as $messageInfo) {
                        echo 'Message # ' . $messageInfo->id . " status:\t" . $messageInfo->status . PHP_EOL;
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