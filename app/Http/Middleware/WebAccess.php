<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\AccessList;
use App\Models\RoleAcces;
class WebAccess
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
        $errorMsg =  "Something Went Wrong";
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
            if($getAccessListDetails->hierarchy == 'C') // For Sub Menu Action like : Cargo,Vessel etc
            {
                $roleAccessDetails = $roleAccessObj->where('access_id',$getAccessListDetails->id)->where('user_role_id','LIKE',$userRoleId)->first();
                if(null == $roleAccessDetails)
                {
                    $response = [
                        'status' => 'failed',
                        'result'    => ["You have not permission to access this page!"],
                        'message' => $errorMsg,
                        'status_code' =>101
                    ];
                    return response()->json($response);
                }
                else
                {
                    $subChildArr = array();
                    $getSubChilds =  $accessListObj->join('role_access','access_lists.id','=','role_access.access_id')
                                                    ->where('parent_id',$getAccessListDetails->id)
                                                    ->where('role_access.user_role_id',$userRoleId)->get();
                    foreach($getSubChilds as $getSubChild)
                    {
                        
                        $subChildArr['display_name'][]      =  $getSubChild->display_name;
                        $subChildArr['hierarchy'][]         =  $getSubChild->hierarchy;
                        $subChildArr['privilege_id'][]      =  $getSubChild->id;
                    }
                   
                    $request->merge([
                        'privilege_array' => $subChildArr
                    ]);
                }  
            }
            else if($getAccessListDetails->hierarchy == 'S') // For Sub child Action
            {
                $roleAccessObj = $roleAccessObj->where('access_id',$getAccessListDetails->id)->where('user_role_id',$userRoleId)->first();
                if(null == $roleAccessObj)
                {
                    $response = [
                        'status' => 'failed',
                        'result'    => ["You don't have permission to access data"],
                        'message' => $errorMsg,
                        'status_code' =>101
                    ];
                }
            }
            return response()->json($response);
        }
        else
        {
            
            $response = [
                'status' => 'failed',
                'result'    => ["Requested Route is not found in Database!"],
                'message' => $errorMsg,
                'status_code' =>101
            ];
            return response()->json($response);
        }
        $cacheArray = array(
            'cache_key' => $getAccessListDetails->cache_key,
            'user_id' => $userId,
            'organization_id' => Auth::user()->organization_id,
            'source' => 'browser',
            'requestType' => $request->method()
        );
        $request->merge([
           'cache_array' => $cacheArray
        ]);
         return $next($request);
    }
}
