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
            ],
            'config'   => [
                'stream_context' => [
                    'ssl' => [
                        'ciphers' => 'DHE-RSA-AES256-SHA:DHE-DSS-AES256-SHA:AES256-SHA:KRB5-DES-CBC3-MD5:'
                            . 'KRB5-DES-CBC3-SHA:EDH-RSA-DES-CBC3-SHA:EDH-DSS-DES-CBC3-SHA:DES-CBC3-SHA:DES-CBC3-MD5:'
                            . 'DHE-RSA-AES128-SHA:DHE-DSS-AES128-SHA:AES128-SHA:RC2-CBC-MD5:KRB5-RC4-MD5:KRB5-RC4-SHA:'
                            . 'RC4-SHA:RC4-MD5:RC4-MD5:KRB5-DES-CBC-MD5:KRB5-DES-CBC-SHA:EDH-RSA-DES-CBC-SHA:'
                            . 'EDH-DSS-DES-CBC-SHA:DES-CBC-SHA:DES-CBC-MD5:EXP-KRB5-RC2-CBC-MD5:EXP-KRB5-DES-CBC-MD5:'
                            . 'EXP-KRB5-RC2-CBC-SHA:EXP-KRB5-DES-CBC-SHA:EXP-EDH-RSA-DES-CBC-SHA:EXP-EDH-DSS-DES-CBC-SHA:'
                            . 'EXP-DES-CBC-SHA:EXP-RC2-CBC-MD5:EXP-RC2-CBC-MD5:EXP-KRB5-RC4-MD5:EXP-KRB5-RC4-SHA'
                            . ':EXP-RC4-MD5:EXP-RC4-MD5',
                    ]
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

        return (object) [
            'ips'       => $ips,
            'usernames' => $usernames,
        ];
    }

    public function uploadBans(array $bans)
    {
        $this->request('post', '/sync', json_encode($bans));
    }
}
