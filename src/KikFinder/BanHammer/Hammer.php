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

    public function ban($username, $address)
    {
        $existingUser = Ban::where('type', '=', 'username')->where('address', 'LIKE', $username)->first();
        $existingIP   = Ban::where('type', '=', 'ip')->where('address', '=', $address)->first();

        if ($existingIP && $existingUser) return;

        if (!$existingIP) {
            $ipBan = Ban::create([
                'buid'    => $this->generateBUID(),
                'type'    => 'ip',
                'address' => $username
            ]);
        }

        if (!$existingUser) {
            $userBan = Ban::create([
                'buid'    => $this->generateBUID(),
                'type'    => 'username',
                'address' => $address
            ]);
        }

        return [$ipBan, $userBan];
    }

    public function isBanned($username, $ip)
    {
        $userBan = Ban::where('type', '=', 'username')->where('address', 'LIKE', $username)->first();
        $ipBan   = Ban::where('type', '=', 'ip')->where('address', '=', $ip)->first();

        if ($userBan || $ipBan) {
            return true;
        }
    }

	public function generateBUID($length = 24)
	{
		return str_random($length);
	}
}