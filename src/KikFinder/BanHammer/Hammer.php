<?php

namespace KikFinder\BanHammer;

use Illuminate\Contracts\Foundation\Application;
use KikFinder\BanHammer\Database\DatabaseSyncManager;
use KikFinder\BanHammer\HTTP\BanHammerClient;

class Hammer
{

    protected $app;
    protected $manager;
    protected $client;

    public function __construct(Application $app)
    {
        $this->app     = $app;
        $this->client  = new BanHammerClient();
        $this->manager = new DatabaseSyncManager($this->client);
    }

    public function sync()
    {
        $this->manager->sync();
    }

    public function ban($username, $ip)
    {
        return [
            $this->banUsername($username),
            $this->banIpAddress($ip)
        ];
    }

    public function banUsername($username)
    {
        return $this->banType('username', $username);
    }

    public function banIpAddress($ip)
    {
        return $this->banType('ip', $ip);
    }

    private function banType($type, $address)
    {
        $entry = Ban::firstOrNew(compact('type', 'address'));

        if (!$entry->buid) {
            $entry->buid = $this->generateBUID();
            $entry->save();
        }

        return $entry;
    }

    public function isBanned($ip, $username = null)
    {
        return Ban::where(function ($query) use ($ip) {
            $query->where('type', '=', 'ip')->where('address', '=', $ip);
        })->orWhere(function ($query) use ($username) {
            if (!is_null($username)) {
                $query->where('type', '=', 'username')->where('address', 'LIKE', $username);
            }
        })->exists();
    }

    public function generateBUID($length = 24)
    {
        return str_random($length);
    }
}