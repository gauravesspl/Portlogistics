<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Log;
use Validator;
use App\Services\LoginService;

class LoginMiddlewareRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    protected $loginService;
    define("ERRORMSG", "Something Went Wrong");
    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }
    public function handle($request, Closure $next)
    {
        $api_array['status'] = "true";
        $all = $request->all();
        $rules = [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:31|max:32|regex:/^[a-zA-Z0-9]*$/'
        ];
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails())
        {
            $api_array['status'] = "failed";
            $api_array['result'] = [$validator->messages()->first()];
            $api_array['message'] = ERRORMSG;
        }   
        $emailExist = $this->loginService->checkIfEmailExist($all);
        if(!$emailExist['status'])
        {
            $api_array['status'] = "failed";
            $api_array['result'] = [$emailExist['result']];
            $api_array['message'] = ERRORMSG;
        }
        $hash_passcode = $emailExist['user']->hash_passcode;
        if($hash_passcode != $all['password'])
        {
            $api_array['status'] = "failed";
            $api_array['result'] = ["Hash Code Doesn't match"];
            $api_array['message'] = ERRORMSG;
        }
        $user = $emailExist['user'];
        $request->merge([
            'user_array' => $user
        ]);
        if($api_array['status'] ==  "failed")
        {
            return response()->json($api_array);
        }
        return $next($request);
    }
}
