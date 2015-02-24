<?php namespace KikFinder\BanHammer\Console\Commands;

use Illuminate\Console\Command;

class SyncCommand extends Command
{

    protected $name = 'hammer:sync';

    protected $description = 'Sync bans with remote system';

    public function fire()
    {
        app('banhammer.hammer')->sync();

        $this->info('Synced with remote system');
    }
}
