<?php

namespace App\Http\Middleware;

use App\Helper\Util;
use App\Models\Client;
use App\Repositories\Admin\ClientRepository;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckClientStatus
{
    const CONNETION_PREFIX = "connection_limit:";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // check status from client repository where id = config('constants.server_id');
        /** @var ClientRepository $clientRepo */
        $clientRepo = app(ClientRepository::class);
        $client     = $clientRepo->findWithoutFail(config('constants.server_id'));

        // IF Server Type==Master
        if (config('constants.server_type') == Util::SERVER_TYPE_MASTER) {
            return $next($request);
        }
        // check if client status is active or not
        if ($client && $client->status) {
            // Client status is active, check client connection limits.
            if (!$this->checkConnectionLimit($client)) {
                return abort(403, "Connection Limit Exceeded");
            }
            return $next($request);
        } else {
            // Else: Check if the client exists and its status is active.
            return abort(403, $client ? $client->status_message : "License not Activated");
        }
    }

    /**
     * @param $client
     * @return bool
     */
    protected function checkConnectionLimit(Client $client)
    {
        if (\Auth::check()) {
            $expiry   = JWTAuth::parseToken()->getPayload()->get('exp');
            $expireAt = ($expiry - time()) / 60;
            $user     = \Auth::id() . ":" . $expiry;
            $cache    = app('cache');
            if ($cache->has(self::CONNETION_PREFIX . $user)) {
                return true;
            } else if (count($cache->getStore()->getRedis()->keys($cache->getStore()->getPrefix() . self::CONNETION_PREFIX . "*")) < $client->connection_limit) {
                // TODO: check connection count;
                $cache->remember(self::CONNETION_PREFIX . $user, $expireAt, function () use ($expiry) {
                    return $expiry;
                });
                return true;
            }
            return false;
        }
        return true;
    }
}
