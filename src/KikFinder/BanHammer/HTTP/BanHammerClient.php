<?php

namespace KikFinder\BanHammer\HTTP;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class BanHammerClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => config('banhammer.endpoint'),
            'defaults' => [
                'query' => [
                    'secret' => config('banhammer.secret')
                ]
            ]
        ]);
    }

    private function request($method, $uri, $body = null)
    {
        $response = $this->client->{$method}($uri, [], $body);

        if ($response->getBody()) {
            $json = $response->json();

            return $json;
        } else {
            throw new BadResponseException("Invalid format provided", $response->getRequest());
        }
    }

    public function retrieveBans()
    {
        $response = $this->request('get', '/sync');

        $ips       = $response['data']['ip'];
        $usernames = $response['data']['usernames'];

        return (object)[
            'ips'       => $ips,
            'usernames' => $usernames,
        ];
    }

    public function uploadBans(array $bans)
    {
        $this->request('post', '/sync', json_encode($bans));
    }
}
