<?php
namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Log;
use App\Repositories\User\UserRepository;
use Cache;
use App\Repositories\UserRole\UserRoleRepository;
use Jenssegers\Agent\Agent;

class BaseController extends Controller
{

    public function __construct()
    {
        $this->cacheClearMessage =  'Cache Clear Succesfully';
    }
    /**
     * Get Auth details based on is_browser param
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuth($param = array())
    {
        $is_api_request = true;
        
        $browser = !empty($param['is_browser_request'])?$param['is_browser_request']:false;

        $user_id = '';
        $user_auth = array();
        if (empty($param['user_id']) && $browser == 1)
        {
          $is_api_request = Auth::User()->auth_browser;
          $user_id = Auth::user()->id;
          $user_auth = Auth::user();
        }
        else
        {
          $user_id = isset($param['user_id']) ? $param['user_id'] : 0;
        if (!empty($user_id))
        {
          $user_auth = new UserRepository();
          $user_auth = $user_auth->getUserByUserId($user_id);
        }
        }
       
        $all['user_id'] = $user_id;
        $all['user_auth'] = $user_auth;
        $all['browser'] = $browser;
        $all['connection'] = $this->getConnection();
        $all['cache_array'] = !empty($param['cache_array'])?$param['cache_array']:'';
        $all['is_api_request'] = $is_api_request;
        return $all;
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function getConnection()
    {
        return 'mysql';
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */

    public function sendResponse($result, $message, $param = array())
    {
        $response = [
          'status' => 'success', 
          'result' => $result, 
          'message' => $message, 
          'status_code' => 200
        ];
        $key = !empty($param['cache_array']['cache_key'])?$param['cache_array']['cache_key']:'';
        $cache_urls = config('constants.cache_url');
        if(in_array($key,$cache_urls))
        { 
          $response['key'] = $this->cacheData($param,$response);
        }
        if (isset($param['browser']) && $param['browser'] == 1 && !empty($param['view']))
        {
            return view($param['view'], $response);
        }
        Log::info(json_encode($response));
        return response()->json($response, 200);
    }

    public function cacheData($param,$response)
    {
      $key = '';
      if(!empty($param['cache_array']) && $param['cache_array']['requestType'] == 'GET')
      { 
        $cache_array = array();
        $org_id = $param['cache_array']['organization_id'];
        $user_id = $param['cache_array']['user_id'];
        $cache_key = $param['cache_array']['cache_key']; 
        $cache_array[$org_id] = array();
        $user_id_array = array();
        $routeArray = array();
        $routeArray['cache_array'] = $param['cache_array'];
        $routeArray['view'] = !empty($param['view']) ? $param['view'] : '';
        $routeArray['connection'] = $param['connection'];
        $routeArray['response'] = $response;
        $routeArray['result'] = $response['result'];
        $user_id_array[$cache_key] =  $routeArray;
        $cache_array[$org_id][$user_id] = $user_id_array;
        $seconds = config('constants.cache_time');
        $key = $org_id.':'.$user_id.':'.$cache_key;
        Cache::put($key, $cache_array , $seconds);      
      }
      if(!empty($param['cache_array']) && $param['cache_array']['requestType'] != 'GET')
      {
        $org_id = !empty($param['cache_array']['organization_id'])?$param['cache_array']['organization_id']:'';
        $user_id = !empty($param['cache_array']['user_id'])?$param['cache_array']['user_id']:'';
        $cache_key = !empty($param['cache_array']['cache_key'])?$param['cache_array']['cache_key']:'';
        $key = $org_id.':'.$user_id.':'.$cache_key;
        Log::info($key);
        Cache::forget($key);
      }
      return $key;
    }

    public function clearCacheMenu()
    {
        $org_id = Auth::user()->organization_id;
        $getAllRoles = new UserRoleRepository();
        $getAuth = $this->getAuth();
        $getAllRoles = $getAllRoles->getRoles($getAuth);
        foreach ($getAllRoles['result'] as $key => $value) {
           $roles = $value['id'];
           $key = $org_id.':'.$roles;
           Cache::forget($key);
        }
        $result = [$this->cacheClearMessage];
        $message = $this->cacheClearMessage;
        return $this->sendResponse($result, $message);
    }

    public function clearCache()
    {
        $org_id = Auth::user()->organization_id;
        $user_id = Auth::user()->id;
        $cache_urls = config('constants.cache_url');
        foreach ($cache_urls as $key => $value) {
           $urls = $value;
           $key = $org_id.':'.$user_id.':'.$urls;
           Cache::forget($key);
        }
        $result = [$this->cacheClearMessage];
        $message = $this->cacheClearMessage;
        return $this->sendResponse($result, $message);
    }

    public function clearCacheView(Request $request)
    {
      $allInput = $request->all();
      $param  = $this->getAuth($allInput);
      $param['view'] = 'cache.setting';
      $cachedata['privileges'] = isset( $allInput['privilege_array'] )? $allInput['privilege_array'] : "";
      return $this->sendResponse($cachedata,'Cache data fetch sucessfully', $param);

    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */

    public function sendError($result, $message, $param = array())
    {
        $response = [
          'status' => 'failed', 
          'message' => $message, 
        ];
        $status_code = 429;
        $response['status_code'] = $status_code;
        $response['result'] = !empty($result) ? $result : ['Something Went Wrong'];
        if (isset($param['browser']) && $param['browser'] == 1 && !empty($param['view']))
        {
            return view($param['view'], $response);
        }
        return response()->json($response, 200);
    }




}
