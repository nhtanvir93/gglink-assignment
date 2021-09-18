<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckingPermission
{
    public function handle(Request $request, Closure $next)
    {
        $permissions = config('custom_settings.permissions');
        $uri = substr(request()->getPathInfo(),1);

        $exceptGroupIds = isset($permissions[$uri]) ? $permissions[$uri]['except_groups'] : [];
        $groupIds = auth()->user()->groupUser->pluck('group_id')->toArray();

        $matchedExceptionGroupIds = array_intersect($groupIds, $exceptGroupIds);

        if(count($matchedExceptionGroupIds) > 0) {
            return response()->json([
                'Status' => false,
                'Message' => Response::$statusTexts[Response::HTTP_FORBIDDEN]
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
