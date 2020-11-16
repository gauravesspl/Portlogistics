<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Log;
use Exception;
use JWTAuth;
use JWTException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use App\Services\LoginService;
use Illuminate\Http\Request;
use App\Http\Requests\JwtMiddlewareRequest;
use Illuminate\Support\Facades\Validator;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected $loginService;
    
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function handle(Request $request, Closure $next)
    {
        $returnBack = false;
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $api_array = ['status' => 'failed','result'=>['Token is Invalid']];
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $api_array = ['result' => ['Token is Expired'], 'status' => 'failed'];
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException){
                $api_array = ['result' => ['Token is Blacklisted'], 'status' => 'failed'];
            }else{
                $api_array = ['result' => ['Authorization Token not found'], 'status' => 'failed'];
            }
            $response['message'] = 'Something Went Wrong';
            $returnBack = true;
        }
        if($returnBack || empty($user))
        {
            return response()->json($response);
        }

        $user_id = 0; $getUser = $jwtStatus = true;
        $token = $request->bearerToken();
        $all = $request->all();
        $all['token'] = $token;
        $jwtToken = $this->loginService->getUserByToken($all);
        if($jwtToken['status'])
        {
            $user_id = $jwtToken['user']->user_id;
            $getUser = $this->loginService->getUserByUserId($user_id);
            if($getUser){
                $request->merge([
                    'user_array' => $getUser,
                    'user_id' => $user_id
                ]);
                return $next($request);
            }
            else{
                $getUser = false;
            }           

        }
        else
        {
            $jwtStatus = false;
        }
        if(!$jwtStatus)
        {
            $api_array['status'] = "failed";
            $api_array['message'] = "Unauthorized Token";
            $api_array['result'] = ["Unauthorized Token"];
        }
        if(!$getUser)
        {
            $api_array['status'] = "failed";
            $api_array['message'] = "User details not found";
            $api_array['result'] = ["User details not found"];          
        }
        return response()->json($api_array);            
    }
}
