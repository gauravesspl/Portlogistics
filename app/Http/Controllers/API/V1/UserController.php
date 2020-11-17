<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Crypt;
use App\Repositories\City\CityRepository;
use App\Repositories\State\StateRepository;
use App\Repositories\Country\CountryRepository;
use App\Repositories\UserRole\UserRoleRepository;
use App\Repositories\Department\DepartmentRepository;
use App\Traits\CustomTrait;
use App\Http\Requests\EditUserRequest;
use App\Http\Requests\UserRequest;
use App\Libraries\SendEmailLibrary;

class UserController extends BaseController {
    
    use CustomTrait;
    protected $userRepository, $cityRepository, $stateRepository, $countryRepository, $userRoleRepository, $departmentRepository, $sendEmailLibrary;
    
    public function __construct(UserRepository $userRepository, CityRepository $cityRepository, StateRepository $stateRepository, CountryRepository $countryRepository, UserRoleRepository $userRoleRepository, DepartmentRepository $departmentRepository, SendEmailLibrary $sendEmailLibrary){
        $this->userRepository = $userRepository;
        $this->cityRepository = $cityRepository;
        $this->stateRepository = $stateRepository;
        $this->countryRepository = $countryRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->departmentRepository = $departmentRepository;
        $this->sendEmailLibrary = $sendEmailLibrary;
    }
    
    /**
    * @OA\GET(
    *   path="/users",
    *   tags={"User"},
    *   summary="Get list of users",
    *   description="Returns list of users",
    *   @OA\Response(
    *       response=200,
    *       description="Success",
    *       @OA\MediaType(
    *           mediaType="application/json",
    *        )
    *       ),
    *   security={{ "apiAuth": {} }}
    *     )
    */
    public function index(Request $request) {
        $inputs = $request->all();
        $param  = $this->getAuth($inputs); //in post you will need to pass like $this->getAuth($request->all());
        $param['view'] = 'users.index';
        if(isset($inputs['id']) && !empty($inputs['id'])) {
            $param['id'] = $inputs['id'];
        }
        $response = $this->userRepository->getUsers($param);
        $response['privileges'] =   isset( $inputs['privilege_array'] )? $inputs['privilege_array'] : "";
        if($response['status'] == false){
           return $this->sendError($response,'No record found !!!', $param);
        }
        return $this->sendResponse($response,'User data fetched sucessfully', $param);
    }
    
    public function add(Request $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $result = $this->getMasterRecords($auth);
        $result['disabled'] = 'disabled';
        return view('users.add', compact('result'));
    }
    
    /**
    * @OA\POST(
    * path="/user",
    * tags={"User"},
    * summary="Insert User",
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass user details",
    *    @OA\JsonContent(
    *       required={"email", "first_name", "last_name", "mobile_no", "address1", "address2", "country_id", "pin_code", "state_id", "city_id", "department_id", "role_id"},
    *       @OA\Property(property="email", type="email", format="email", example="admin@esspl.com"),
    *       @OA\Property(property="first_name", type="string", format="string", example="Ess"),
    *       @OA\Property(property="last_name", type="string", format="string", example="Admin"),
    *       @OA\Property(property="mobile_no", type="integer", format="integer", example="99999999"),
    *       @OA\Property(property="address1", type="string", format="string", example="1"),
    *       @OA\Property(property="address2", type="string", format="string", example="1"),
    *       @OA\Property(property="country_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="pin_code", type="integer", format="integer", example="751023"),
    *       @OA\Property(property="state_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="city_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="department_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="role_id", type="integer", format="integer", example="1"),
    *    ),
    * ),
    * @OA\Response(
    *      response=200,
    *      description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
    * ),
    * security={{ "apiAuth": {} }}
    *)
    **/
    public function store(UserRequest $request) {
        $inputs = $request->all();
        $auth  = $this->getAuth($inputs);
        $inputs['is_active'] = '0';
        $inputs['created_by'] = $auth['user_id'];
        $inputs['connection'] = $auth['connection'];
        $userResponse = []; //Intialize Response
        $isUniqueEmailId = $this->validateEmail($inputs);
        if($isUniqueEmailId) {
            $response = $this->userRepository->saveUser($inputs);
            if(!$response['status']) {
                $userResponse['status'] = false;
                $userResponse['message'] = 'Some error occured during saving information';
            }
            //Send email to new user
            $user = $response['result'];
            $data = ['user_id' => $user->id, 'name' => $user->first_name.' '.$user->last_name, 'template' => 'register', 'email' => $user->email];
            $response = $this->sendEmailLibrary->modify($data);
            if($response['status'] == 'failed') {
                $userResponse['status'] = false;
                $userResponse['message'] = 'Error in sending email';
            }
            $userResponse['status'] = true;
            $userResponse['message'] = 'User information saved successfully';
        } else {
            $userResponse['status'] = false;
            $userResponse['message'] = 'Email id already exists';
        }
        
        //Return Response
        if($userResponse['status']) {
            return $this->sendResponse([],$userResponse['message'],$auth);
        } else {
            return $this->sendError($userResponse['message'],$userResponse['message'],$auth);
        }
    }
    
    /** 
    * @OA\GET(
    * path="/user/{id}",
    * tags={"User"},
    * summary="Get User Details By Id",
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass User Id",
    *    @OA\JsonContent(
    *       required={"id"},
    *       @OA\Property(property="id", type="integer", format="integer", example="1")
    *    ),
    * ),
    * @OA\Response(
    *      response=200,
    *      description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
    * ),
    * security={{ "apiAuth": {} }}
    *)
    **/
    public function edit(EditUserRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        if ($auth['browser'] == 1) {
            $result = $this->getMasterRecords($auth);
            $result['disabled'] = '';
        }
        $user = $this->getDataFromJsonResponse($this->getData($inputs));
        if(!empty($user)) {
            $auth['view'] = 'users.add';
            $result['user'] = $user;
            return $this->sendResponse($result, 'User details fetched successfully', $auth);
        } else {
            return $this->sendError('Some error occured. Please validate your inputs', 'Some error occured. Please validate your inputs', $auth);
        }
        
    }
    
    /**
    * @OA\PUT(
    * path="/user/{id}",
    * tags={"User"},
    * summary="Update User",
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass user details",
    *    @OA\JsonContent(
    *       required={"id", "email", "first_name", "last_name", "mobile_no", "address1", "address2", "country_id", "pin_code", "state_id", "city_id", "department_id", "role_id"},
    *       @OA\Property(property="id", type="integer", format="integer", example="1"), 
    *       @OA\Property(property="email", type="email", format="email", example="admin@esspl.com"),
    *       @OA\Property(property="first_name", type="string", format="string", example="Ess"),
    *       @OA\Property(property="last_name", type="string", format="string", example="Admin"),
    *       @OA\Property(property="mobile_no", type="integer", format="integer", example="99999999"),
    *       @OA\Property(property="address1", type="string", format="string", example="1"),
    *       @OA\Property(property="address2", type="string", format="string", example="1"),
    *       @OA\Property(property="country_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="pin_code", type="integer", format="integer", example="751023"),
    *       @OA\Property(property="state_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="city_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="department_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="role_id", type="integer", format="integer", example="1"),
    *    ),
    * ),
    * @OA\Response(
    *      response=200,
    *      description="Success",
    *      @OA\MediaType(
    *           mediaType="application/json",
    *      )
    * ),
    * security={{ "apiAuth": {} }}
    *)
    **/
    public function update(UserRequest $request) {
        $inputs = $request->all();
        $auth  = $this->getAuth($inputs);
        $inputs['updated_by'] = $auth['user_id'];
        $inputs['connection'] = $auth['connection'];
        $userResponse = []; //Intialize Response
        $isUniqueEmailId = $this->validateEmail($inputs);
        if($isUniqueEmailId) {
            $response = $this->userRepository->saveUser($inputs);
            if(!$response['status']) {
                $userResponse['status'] = false;
                $userResponse['message'] = 'Some error occured during saving information';
            }
            $userResponse['status'] = true;
            $userResponse['message'] = 'User information saved successfully';
        } else {
            $userResponse['status'] = false;
            $userResponse['message'] = 'Email id already exists';
        }
        
        //Return Response
        if($userResponse['status']) {
            return $this->sendResponse([],$userResponse['message'],$auth);
        } else {
            return $this->sendError($userResponse['message'],$userResponse['message'],$auth);
        }
    }
    
    public function getData($inputs) {
        $auth = $this->getAuth($inputs);
        $response = $this->userRepository->getUser($inputs);
        if ($response['status']) {
            return $this->sendResponse($response['result'], 'Record Fetched Sucessfully', $auth);
        } else {
            return $this->sendError($response['message'], $response['message'], $auth);
        }
    }
    
    public function validateEmail($inputs) {
        $validateInput = ['email' => $inputs['email'], 'connection' => $inputs['connection']];
        $response = $this->userRepository->getUser($validateInput);
        if($response['status']) {
            if(isset($inputs['id']) && !empty($inputs['id']) && ($response['result']->id == $inputs['id'])) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    
    //Get Department, Role, City, State, COuntry Records for Add/Edit User
    public function getMasterRecords($auth) {
        $data = [];
        //Get All Departments
        $department_response = $this->departmentRepository->getDepartments($auth);
        if ($department_response['status'] && !$department_response['result']->isEmpty()) {
            $data['departmentArr'] = $department_response['result'];
        }

        //Get All User Roles
        $role_response = $this->userRoleRepository->getRoles($auth);
        if ($role_response['status'] && !$role_response['result']->isEmpty()) {
            $data['userRoleArr'] = $role_response['result'];
        }

        //Get All Countries
        $country_response = $this->countryRepository->getCountries($auth);
        if ($country_response['status'] && !$country_response['result']->isEmpty()) {
            $data['countryArr'] = $country_response['result'];
        }

        //Get All States
        $state_response = $this->stateRepository->getStates($auth);
        if ($state_response['status'] && !$state_response['result']->isEmpty()) {
            $data['stateArr'] = $state_response['result'];
        }
        
        //Get All Cities
        $city_response = $this->cityRepository->getcities($auth);
        if ($city_response['status'] && !$city_response['result']->isEmpty()) {
            $data['cityArr'] = $city_response['result'];
        }
        
        return $data;
    }
    
}
