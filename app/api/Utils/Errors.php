<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Response;

use App\Utils\Utils;

class Errors
{

    public static function List($sms, $code): object
    {
        $REQUEST_URI = explode("?", $_SERVER['REQUEST_URI']);
        return (object)[
            2400 => [
                "error" => Response::$statusTexts[$code],
                "code" => 2400,
                "message" => "(#2400)" . $sms,
                "statusCode" => $code,
                "timestamp" => Utils::Now(),
                "path" => $REQUEST_URI[0],
                "debug" => "(#2400)" . $sms
            ],
            2200 => [
                "error" => Response::$statusTexts[$code],
                "code" => 2200,
                "message" => "(#2200)" . $sms,
                "statusCode" => $code,
                "timestamp" => Utils::Now(),
                "path" => $REQUEST_URI[0],
                "debug" => "(#2200)" . $sms
            ],
            2203 => [
                "error" => Response::$statusTexts[$code],
                "code" => 2203,
                "message" => "(#2203)" . $sms,
                "statusCode" => $code,
                "timestamp" => Utils::Now(),
                "path" => $REQUEST_URI[0],
                "debug" => "(#2203)" . $sms
            ]
        ];
    }
}
