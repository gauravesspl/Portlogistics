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
            return $this->sendError(['This account is not activated. Please contact the administrator.'],'This account is not activated. Please contact the administrator.');
        }
        $userToken = $this->loginService->generateTokenUsingLoginId($request);
        if ($userToken['status'] == false) {
            return $this->sendError(['Failed to update token'],'Failed to update token');
        }
        $status = $this->loginService->saveUpdateToken($userToken['token']);
        if (!$status) {
            return $this->sendError(['Failed to update token'],'Failed to update token');
        }
        $token = array('token'=>$userToken['token'],'username'=>$userToken['username']);
        return $this->sendResponse( $token, 'Token generated successfully.');
    }
     
    /**
     * @OA\Post(
     ** path="/token",
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
    public function generateToken(Request $request)
    {
        $user = $request->all()['user_array'];
        if(!$user['is_active']) {
            return $this->sendError(['This account is not activated. Please contact the administrator.'],'This account is not activated. Please contact the administrator.');
        }
        $userToken = $this->loginService->generateTokenUsingLoginId($request);
        if ($userToken['status'] == false) {
            return $this->sendError(['Failed to update token'],'Failed to update token');
        }
        $status = $this->loginService->saveUpdateToken($userToken['token']);
        if (!$status) {
            return $this->sendError(['Failed to update token'],'Failed to update token');
        }
       $token = array('token'=>$userToken['token'],'username'=>$userToken['username']);
        return $this->sendResponse($token, 'Token generated successfully.');
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
            return $this->sendError(['Failed to update token'],'Failed to update token');
            }
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return $this->sendError(['The token is invalid'],'The token is invalid');
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