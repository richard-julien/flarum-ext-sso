<?php
/**
 * Created by PhpStorm.
 * User: julienrichard
 * Date: 30/03/2016
 * Time: 07:59
 */

namespace Flarum\Auth\Sso;


class FlarumSingleSignOn
{
    public static function toUrl($url, $secret, $payload)
    {
        $unsignedPayload = FlarumSingleSignOn::unsignedPayload($payload);

        $data = [
                'sso' => $unsignedPayload,
                'sig' => hash_hmac('sha256', $unsignedPayload , $secret)
            ] + $_GET;

        return $url . "?" . http_build_query($data);
    }

    private static function unsignedPayload($payload)
    {
        return base64_encode(http_build_query($payload));
    }

    public static function validate($payload, $sig, $secret)
    {
        $payload = urldecode($payload);
        if (hash_hmac("sha256", $payload, $secret) === $sig) {
            return true;
        } else {
            return false;
        }
    }

    public static function parsePayload($payload) {
        $payload = urldecode($payload);
        $query = [];
        parse_str(base64_decode($payload), $query);
        return $query;
    }

    public static function data($key, $payload) {
        return isset($payload[$key]) ? $payload[$key] : "";
    }
}