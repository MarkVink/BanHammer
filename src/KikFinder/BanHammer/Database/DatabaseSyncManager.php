<?php

namespace KikFinder\BanHammer\Database;

use KikFinder\BanHammer\Ban;
use KikFinder\BanHammer\HTTP\BanHammerClient;

class DatabaseSyncManager
{

    private $client;

    public function __construct(BanHammerClient $client)
    {
        $this->client = $client;
    }

    public function sync()
    {
        $remoteBans = $this->client->retrieveBans();

        $remoteIPBanBuids   = array_keys($remoteBans->ips);
        $remoteUserBanBuids = array_keys($remoteBans->usernames);

        $localBans = Ban::all(['type', 'buid', 'address'])->toArray();

        $localIPBans       = [];
        $localUsernameBans = [];

        foreach ($localBans as $ban) {
            if ($ban['type'] == "ip") {
                $localIPBans[$ban['buid']] = $ban['address'];
            } elseif ($ban['type'] == "username") {
                $localUsernameBans[$ban['buid']] = $ban['address'];
            }
        }

        $localBanIPBuids       = array_keys($localIPBans);
        $localBanUsernameBuids = array_keys($localUsernameBans);

        $ipToAdd       = [];
        $usernameToAdd = [];

        $ipToUpload       = [];
        $usernameToUpload = [];

        // Find IPs that we need to add to the remote database
        foreach ($localBanIPBuids as $localBuid) {
            if (!in_array($localBuid, $remoteIPBanBuids)) {
                $ipToUpload[$localBuid] = $localIPBans[$localBuid];
            }
        }

        // Find usernames that we need to add to the remote database
        foreach ($localBanUsernameBuids as $localBuid) {
            if (!in_array($localBuid, $remoteUserBanBuids)) {
                $usernameToUpload[$localBuid] = $localUsernameBans[$localBuid];
            }
        }

        // Find IPs that we need to upload to the BanHammer server
        foreach ($remoteIPBanBuids as $remoteBuid) {
            if (!in_array($remoteBuid, $localBanIPBuids)) {
                $ipToAdd[$remoteBuid] = $remoteBans->ips[$remoteBuid];
            }
        }

        // Find usernames that we need to add to the local server
        foreach ($remoteUserBanBuids as $remoteBuid) {
            if (!in_array($remoteBuid, $localBanUsernameBuids)) {
                $usernameToAdd[$remoteBuid] = $remoteBans->usernames[$remoteBuid];
            }
        }

        // Begin adding local bans
        foreach ($ipToAdd as $buid => $address) {
            Ban::create([
                'type'    => 'ip',
                'buid'    => $buid,
                'address' => $address
            ]);
        }

        foreach ($usernameToAdd as $buid => $username) {
            Ban::create([
                'type'    => 'username',
                'buid'    => $buid,
                'address' => $username
            ]);
        }

        // Compile list to upload
        $this->upload($usernameToUpload, $ipToUpload);
    }

    protected function upload(array $usernames, array $ips)
    {
        $content = [
            'usernames' => $usernames,
            'ip'        => $ips,
        ];

        $this->client->uploadBans($content);
    }
}
