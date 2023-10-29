<?php

namespace App\Utils\Sunat;

class Unspsc
{
    public static function getUnspsc()
    {
        $file = './public/json/app.json';
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json);
            return $data->data;
        }
        return null;
    }
}
