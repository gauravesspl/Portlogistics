<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use JWTAuth;
use JWTException;
use Auth;
use App\User;
use Log;
use App\Services\LoginService;
use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\Request;
use App\Http\Requests\GenerateTokenRequest;


class ManageJwtTokenController extends BaseController
{
    
    protected $loginService;
     
    public function __construct(LoginService $loginService)
    {
        $this->inactiveAccount = 'This account is not activated. Please contact the administrator.';
        $this->invalidUpdateToken = 'The token is invalid';
        $this->failedUpdateToken = 'Failed to update token';
        $this->successUpdateToken = 'Token generated successfully';
        $this->loginService = $loginService;
    }
    
    /**
     * @OA\Post(
     ** path="/login",
     *  tags={"Auth"},
     *  summary="Login",
     *  @OA\RequestBody(
     *  required=true,
     *  description="Pass user credentials",
     *  @OA\JsonContent(
     *  required={"email","password"},
     *  @OA\Property(property="email", type="string", format="email", example="port@esspl.com"),
     *  @OA\Property(property="password", type="string", format="password", example="fdf59c3ba5873c1787f874e490bc688e"),
     *  ),
     * ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\MediaType(
     *  mediaType="application/json",
     *  )
     *  ),
     *)
     **/
    public function loginRequestToken(UserLoginRequest $request)
    {
        $user = $request->all()['user_array'];
        if(!$user['is_active']) {
            return $this->sendError([$this->inactiveAccount],$this->inactiveAccount);
        }
        $userToken = $this->loginService->generateTokenUsingLoginId($request);
        if (!$userToken['status']) {
            return $this->sendError([$this->failedUpdateToken],$this->failedUpdateToken);
        }
        $status = $this->loginService->saveUpdateToken($userToken['token']);
        if (!$status) {
            return $this->sendError([$this->failedUpdateToken],$this->failedUpdateToken);
        }
        $token = array('token'=>$userToken['token'],'username'=>$userToken['username']);
        return $this->sendResponse( $token, $this->successUpdateToken);
    }
         
    
    /**
     * Refresh Token by token
     * 
     * @param string $token
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken()
    {
        try {
            $token     = JWTAuth::getToken();
            $refreshed = JWTAuth::refresh($token);
            $status    = $this->loginService->saveUpdateToken($refreshed);
            if (!$status) {
            return $this->sendError([$this->failedUpdateToken],$this->failedUpdateToken);
            }
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->sendError($this->invalidUpdateToken,$this->invalidUpdateToken);
        }
        return $this->respondWithToken($refreshed);
    }
    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        header('Authorization: Bearer ' . $token);
        $response = [
            'access_token' => $token,
            'token_type' => 'bearer',
        ];
        Log::info(json_encode($response));
        return response()->json($response);
    }
    
}