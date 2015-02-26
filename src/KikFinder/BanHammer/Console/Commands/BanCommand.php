<?php namespace KikFinder\BanHammer\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class BanCommand extends Command
{

    protected $name = 'hammer:ban';

    protected $description = 'Ban users based on IP-address or username';

    private $hammer;

    public function __construct()
    {
        parent::__construct();

        $this->hammer = app('banhammer.hammer');
    }

    public function fire()
    {
        $ips = $this->option('ip');
        $usernames = $this->option('username');

        if (count($ips) == 0 && count($usernames) == 0)
            return $this->error('Please provide users to ban');

        $this->banIpAddresses($ips);
        $this->banUsernames($usernames);

        $this->info('Users added to the banlist');
    }

    protected function getOptions()
    {
        return [
            ['ip', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'IP-address of the user', []],
            ['username', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Username of the user', []]
        ];
    }

    private function banIpAddresses($ips)
    {
        foreach ($ips as $ip) {
            $this->hammer->banIpAddress($ip);
        }
    }

    private function banUsernames($usernames)
    {
        foreach ($usernames as $username) {
            $this->hammer->banUsername($username);
        }
    }

}
