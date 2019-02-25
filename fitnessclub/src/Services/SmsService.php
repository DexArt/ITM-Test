<?php
/**
 * Created by PhpStorm.
 * User: desig
 * Date: 25-Feb-19
 * Time: 16:10
 */

namespace App\Services;

class SmsService
{

    public function sendSms()
    {
        $responseCodes = [200, 500, 404];
        $rand          = array_rand($responseCodes);
        $responseCode =  $responseCodes[$rand];
        return $responseCode;
    }

}