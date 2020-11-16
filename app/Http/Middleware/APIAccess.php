<?php
namespace App\Http\Middleware;
use Closure;
use Auth;
use Config;
use App\Models\AccessList;
use App\Models\RoleAcces;
use Log;
class APIAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response['message'] = "";
        $response['status'] = true;
        $routeUrl = $request->route()->uri;
        $userId = Auth::user()->id;
        $userRoleId = Auth::user()->role_id;
        $accessListObj = new AccessList();
        $roleAccessObj = new RoleAcces();
        $getAccessListDetails = $accessListObj->where('web_route',$routeUrl)->where('operation',$request->getMethod())->first();
        if($getAccessListDetails)
        {
            $roleAccessObj = $roleAccessObj->where('access_id',$getAccessListDetails->id)->where('user_role_id',$userRoleId)->first();
            if(null == $roleAccessObj)
            {
                $response = [
                    'status' => 'failed',
                    'result'    => ["Permission denied to access the reuested API"],
                    'message' => "Something Went Wrong",
                    'status_code' =>101
                ];
                return response()->json($response);
            }  
        }
        else
        {
            $response = [
                'status' => 'failed',
                'result'    => ["Permission denied to access the reuested API"],
                'message' => "Something Went Wrong",
                'status_code' =>101
            ];
            return response()->json($response);
        } 
        $cacheArray = array(
            'cache_key' => $getAccessListDetails->cache_key,
            'user_id' => $userId,
            'organization_id' => Auth::user()->organization_id,
            'source' => 'api',
            'requestType' => $request->method()
        );
        $request->merge([
           'cache_array' => $cacheArray
        ]);        
        return $next($request);
    }
}
