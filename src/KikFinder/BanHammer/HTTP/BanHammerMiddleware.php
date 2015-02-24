<?php

namespace KikFinder\BanHammer\HTTP;

use KikFinder\BanHammer\BannedUserException;
use KikFinder\BanHammer\Hammer;

class BanHammerMiddleware
{
    protected $hammer;

    public function __construct(Hammer $hammer)
    {
        $this->hammer = $hammer;
    }

    public function handle($request, \Closure $next)
    {
        $usernameField = null;

        // Get the parameters for the route
        $actions = $request->route()->getAction();

        // Check if there's a 'hammer' key
        if (array_key_exists('hammer', $actions)) {
            // Get the field name
            $usernameField = $actions['hammer'];

            // Check that the field exists in the request
            if (!$request->has($usernameField)) {
                throw new \InvalidArgumentException("Specified username field '${usernameField}' was null");
            }
        }

        // Retrieve the fields
        $username = (!is_null($usernameField)) ? $request->get($usernameField) : null;
        $ip       = $request->ip();

        // Check if banned
        if ($this->hammer->isBanned($username, $ip)) {
            throw new BannedUserException();
        }

        // Proceed processing middleware.
        return $next($request);
    }
}