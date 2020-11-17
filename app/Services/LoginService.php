<?php

namespace App\Services;

use App\Repositories\JwtToken\JwtTokenRepository;
use Auth;
use JWTException;
use Log;
use JWTAuth;
use App\Repositories\User\UserRepository;
use Exception;

class LoginService
{

    protected $jwtTokenRepository;
    protected $userRepository;

    public function __construct(JwtTokenRepository $jwtTokenRepository, UserRepository $userRepository)
    {
        $this->jwtTokenRepository = $jwtTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @description Save Update Token
     * @author
     * @param string $userToken 
     * @return bool success or failure
     */
    public function saveUpdateToken($userToken)
    {
        \JWTAuth::setToken($userToken);
        $user                = \JWTAuth::toUser();
        $data['userToken']   = $userToken;
        $data['userId']      = $user->id;
        $data['expiry_time'] = $this->guard()->factory()->getTTL() * 60;
        $status = $this->jwtTokenRepository->insertOrUpdateJwtTokenByUserId($data);
        return $status;
    }

    /**
     * Generate Token
     * 
     * @param Object $request
     * 
     * @return string $token
     */

    public function generateTokenUsingEmail($request)
    {
        try {
            if (!$userToken = $this->guard()->attempt($request->all())) {
                return false;
            }
        } catch (JWTException $e) {
            Log::error($e->getMessage());
            return false;
        }
        return $userToken;
    }

    /**
     * @description Generate Token using login id
     * @author
     * @param object ['email','password'] 
     * @return array ['status','message','token','username'] 
     */
    public function generateTokenUsingLoginId($request)
    {
        $fetch['status'] = false;
        $fetch['message'] = 'Something went wrong';
        try {
            $all = $request->all();
            $user = $all['user_array'];
            if ($token = JWTAuth::fromUser($user)) {
                $fetch['status'] = true;
                $fetch['message'] = 'Token Generated Successfully';
                $fetch['token'] = $token;
                $fetch['username'] = $user['first_name'] . ' ' . $user['last_name'];
            }
            return $fetch;
        } catch (Exception $e) {
            $fetch['message'] = Log::error($e->getMessage());
            Log::error($e->getMessage());
            return $fetch;
        }
        return $token;
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }

    public function checkIfExistJwtToken($data)
    {
        $status = true;
        $tokenDetails        = $this->jwtTokenRepository->checkIfExistJwtToken($data);
        if (!($tokenDetails)) {
            $status = false;
        }
        return $status;
    }

    public function checkIfEmailExist($data)
    {
        $getData = $this->userRepository->checkIfEmailExist($data);
        return $getData;
    }

    public function getUserByUserId($user_id)
    {
        $getData = $this->userRepository->getUserByUserId($user_id);
        return $getData;
    }

    public function getUserByToken($all)
    {

        $getData = $this->jwtTokenRepository->getUserByToken($all);
        return $getData;
    }
}
