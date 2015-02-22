<?php

namespace KikFinder\BanHammer\HTTP;

use Illuminate\Support\Facades\Input;
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
        // Get the parameters for the route
        $actions = $request->route()->getAction();

        // Check if there's a 'hammer' key
        if (array_key_exists('hammer', $actions)) {
            // Get the field name
            $field = $actions['hammer'];
        } else {
            throw new \InvalidArgumentException(
                "You must specify the 'hammer' key for BanHammer to find the input"
            );
        }

        // Check that the field exists in the request
        if (!Input::has($field)) {
            throw new \InvalidArgumentException("Specified username field '${field}' was null");
        }

        // Retrieve the fields
        $username = Input::get($field);
        $ip       = $_SERVER['REMOTE_ADDR'];

        // Check if banned
        if ($this->hammer->isBanned($username, $ip)) {
            throw new BannedUserException();
        }

        // Proceed processing middleware.
        return $next($request);
    }
}