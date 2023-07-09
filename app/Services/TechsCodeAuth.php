<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TechsCodeAuth
{
    private static string $base_url = "https://auth.techscode.com";

    /**
     * @param string $callback_url
     * @return string
     * @throws Exception
     */
    public static function getAuthUrl(string $callback_url): string
    {
        if(empty($callback_url)) {
            throw new Exception("Callback url is not set");
        }
        return TechsCodeAuth::$base_url . "/login/discord?callback_url=$callback_url";
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public static function getUser(string $auth_token, string $ot_token): array
    {
        if(empty($auth_token)) {
            throw new Exception("Auth token is empty");
        }
        if(empty($ot_token)) {
            throw new Exception("OT token is empty");
        }

        $url = TechsCodeAuth::$base_url . "/auth/user";

        $client = new Client();
        $response = $client->request('GET', $url, [
            'query' => [
                "auth_token" => $auth_token,
                "ot_token" => $ot_token
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

}
