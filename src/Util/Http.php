<?php

namespace TeleBot\Util;

class Http
{
    public static function post($url, $fields = [], $proxy = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (is_array($proxy) && count($proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, "{$proxy['ip']}:{$proxy['port']}");
            curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['type']);
        }

        if (is_array($fields) && count($fields)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result);
    }
}
