<?php
/**
 * Created by PhpStorm.
 * User: aliehsan
 * Date: 2020-01-16
 * Time: 12:53
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\User;
use App\Models\COMPMS0;


class TenantMiddleware {

    public function handle(Request $request, Closure $next) {

        $url = $request->root();
        $urlHostArray = explode('.', parse_url($url, PHP_URL_HOST));
        if (count($urlHostArray) > 1) {
            $domainPrefix = $urlHostArray[0];
            $tenant = COMPMS0::withoutGlobalScopes()->where('DomainPrefix', $domainPrefix)->first();
            //return response()->json(["here" => $domainPrefix],404);
            $token = $request->bearerToken();
            $user = User::withoutGlobalScopes()->where('api_token', $token)->first();
            // return $user;
            if(!empty($tenant) && !empty($user) && $user->CompanyNo == $tenant->CompanyNo) {
                $request->headers->add(['CompanyNo' => $tenant->CompanyNo]);
                return $next($request);
            }
            else {
                //error
                return response()->json(['error' => 'Invalid domain prefix'], 404);
            }
        } else {
            return response()->json(['error' => 'Invalid domain prefix'], 404);
        }
        return response()->json(['error' => 'URL format is incorrect.'], 404);
    }

}
