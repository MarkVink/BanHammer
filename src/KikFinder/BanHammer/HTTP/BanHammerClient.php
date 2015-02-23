<?php

namespace KikFinder\BanHammer\HTTP;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class BanHammerClient
{
    protected $client;
    protected $endpoint, $secret;

    public function __construct()
    {
        $this->endpoint = config('banhammer.endpoint');
        $this->secret   = config('banhammer.secret');

        $this->client = new Client();
    }

    private function request($method, $uri, $body = null)
    {
        $uri = $this->endpoint . $uri . '?secret=' . $this->secret;

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
