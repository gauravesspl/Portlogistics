<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Plan\PlanRepository;
use App\Services\PlanService;
use App\Repositories\PlanTruck\PlanTruckRepository;
use App\Repositories\Consignee\ConsigneeRepository;
use App\Repositories\Location\LocationRepository;
use App\Repositories\Vessel\VesselRepository;
use App\Repositories\Cargo\CargoRepository;
use App\Repositories\Truck\TruckRepository;
use App\Repositories\TruckCompany\TruckCompanyRepository;
use Config;
use App\Repositories\Challan\ChallanRepository;
use App\Http\Requests\PlanEditDeleteRequest;
use App\Http\Requests\PlanningRequest;
use App\Http\Requests\PlanningTruckRequest;
use Carbon\Carbon;
use App\Traits\CustomTrait;
use App\Http\Requests\PlanningListFormRequest;

class PlanController extends BaseController {

    protected $planRepository;
    protected $planTruckRepository;
    protected $planService;
    protected $consigneeRepository;
    protected $locationRepository;
    protected $vesselRepository;
    protected $cargoRepository;
    protected $truckRepository;
    protected $truckCompanyRepository;

    use CustomTrait;

    public function __construct(PlanRepository $planRepository, PlanService $planService, ConsigneeRepository $consigneeRepository, LocationRepository $locationRepository, VesselRepository $vesselRepository, CargoRepository $cargoRepository,PlanTruckRepository $planTruckRepository,TruckRepository $truckRepository, TruckCompanyRepository $truckCompanyRepository) {
        $this->planRepository = $planRepository;
        $this->planTruckRepository = $planTruckRepository;
        $this->planService = $planService;
        $this->consigneeRepository = $consigneeRepository;
        $this->locationRepository = $locationRepository;
        $this->vesselRepository = $vesselRepository;
        $this->cargoRepository = $cargoRepository;
        $this->truckRepository = $truckRepository;
        $this->truckCompanyRepository = $truckCompanyRepository;
    }
  
    /**
    * @OA\Get(
    * path="/plan/{origin_id}",
    * tags={"Planning"},
    * summary="Plan list by origin id for android load page",
    *  @OA\Parameter(
     *    description="ID of berth location id",
     *    in="path",
     *    name="origin_id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     * )
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
    public function getPlanningDetailsByBerthAndDate(PlanningListFormRequest $request) {
        $allInput = $request->all();
        $param = $this->getAuth($allInput); 
        $allInput['connection'] = $param['connection'];
        $allInput['origin_id'] = $allInput['origin_id'];
        $response = $this->planRepository->getPlanningDetailsByBerthAndDate($allInput);
        if ($response['status'] == false) {
            return $this->sendError($response['data'], $response['message'], $param);
        }
        if (!empty($response['data'])) {
            $customeData = $this->planService->prepareCustomData($response['data']);
            $response['data'] = $customeData;
        }
        return $this->sendResponse($response['data'], $response['message'], $param);
    }

    /**
        @description : Planning List View
        @author : Madhusmita Das
    */
    public function list(Request $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $privileges =   isset( $inputs['privilege_array'] )? $inputs['privilege_array'] : "";
        
        $vessels = $berths = $cargos = [];
        //Get All Vessels
        $vessel_response = $this->vesselRepository->getAllVessel($auth);
        if ($vessel_response['status'] && !$vessel_response['vessel']->isEmpty()) {
            $vessels = $vessel_response['vessel'];
        }
        //Get All Berths
        $berth_input = ['connection' => $auth['connection'], 'type' => Config::get('constants.location_berth')];
        $berth_response = $this->locationRepository->getAllLocation($berth_input);
        if ($berth_response['status'] && !$berth_response['location']->isEmpty()) {
            $berths = $berth_response['location'];
        }
        //Get All Cargo
        $cargo_response = $this->cargoRepository->getAllCargo($auth);
        if ($cargo_response['status'] && !$cargo_response['cargo']->isEmpty()) {
            $cargos = $cargo_response['cargo'];
        }
        
        return view('planning.index', compact('privileges','vessels', 'berths', 'cargos'));
    }
    
    /**
        @description : Get Planning List By Ajax
        @author : Madhusmita Das
    */
    public function getPlanningList(Request $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        //For Pagination
        $columns = $inputs['columns'];
        $start = $inputs['start'];
        $limit = $inputs['length'];
        $orderBy = $inputs['order'][0];
        $order = [];
        $where = trim($inputs['search']['value']);
        if (!empty($where)) {
            $where = preg_replace('/[^a-zA-Z0-9- ]/s', '', $where);
        }
        if ($orderBy['column'] > 0) {
            $order['field'] = $columns[$orderBy['column']]['data'];
            $order['dir'] = $orderBy['dir'];
        }
        
        $filterCondition = $this->getFilterConditions($inputs['filterParams']);
        $additionalCondition = []; 
        if(!empty($filterCondition)) {
            foreach($filterCondition as $key => $value) {
                if($value['table_field'] == 'truck_count') {
                    $additionalCondition[] = $value;
                    unset($filterCondition[$key]);
                }
            }
        }
        
        $plannings = $this->planRepository->getListPlannings($auth, $start, $limit, $order, $where, $filterCondition, $additionalCondition, false);
        $allRecords = $this->planRepository->getListPlannings($auth, $start, $limit, $order, $where, $filterCondition, $additionalCondition, true);
        
        $data = [];
        $ObjChallan = new ChallanRepository();
        foreach ($plannings as $planning) {
            $consignees_plots = [];
            $planning_details_input = ['plan_id' => $planning->id, 'connection' => $auth['connection']];
            $trucks = $this->planTruckRepository->getTrucksByPlanningId($planning_details_input);
            $planning_details_response = $this->planRepository->getConsigneePlotListByPlanningId($planning_details_input);
            if ($planning_details_response['status'] == true) {
                foreach ($planning_details_response['result'] as $consignee_plot) {
                    array_push($consignees_plots, ['consignee' => $consignee_plot['consignee_name'], 'destinations' => $consignee_plot['destination_names']]);
                }
            }
            //Get challans
            $disabled = '';
            $challan_response = $ObjChallan->getFilteredChallans($planning_details_input);
            if ($challan_response['status'] == true && !$challan_response['result']->isEmpty()) {
                $disabled = 'disabled';
            }
            
            //Truck Icon
            $truckIcon = "";
            if(count($trucks['result']) > 0){
                $truckIcon = '<form class="text-right" action="planning/truck/add" name="add_truck" method="post">'.csrf_field().'<input type="hidden" name="plan_id" value='.$planning->id.'><button class="btn-transparent" type="submit" style="border:none; padding: 0;" title="Update Truck"><i style="cursor: pointer;" class="fa fa-truck add_truck truck-icon text-primary"></i></button></form>';
            }else{
            $truckIcon = '<form class="text-right" action="planning/truck/add" name="add_truck" method="post">'.csrf_field().'<input type="hidden" name="plan_id" value='.$planning->id.'><button class="btn-transparent" type="submit" style="border:none; padding: 0;" title="Add Truck"><i style="cursor: pointer;" class="fa fa-truck truck-icon add_truck"></i></button></form>'; 
            }
            $data[] = [
                'id' => $planning->id,
                'details' => $consignees_plots,
                'vessel_name' => $planning->vessel_name,
                'berth_name' => $planning->berth_name,
                'date_from' => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning->date_from)->format('d/m/Y H:i'),
                'date_to' => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning->date_to)->format('d/m/Y H:i'),
                'cargo_name' => $planning->cargo_name,
                'created_at' => \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning->created_at)->format('d/m/Y H:i'),
                'truck_list' => $truckIcon,
                'action' => '<div class="float-left ml20"><a href="'.url('plan/'.$planning->id).'" class="btn" title="Edit Plan" ' . $disabled . '><i style="font-size: 20px;" class="fas fa-edit text-success tooltips"></i></a></div><div class="float-right mr20"><a class="btn delete-plan" data-id="'.$planning->id.'" title="Delete Plan" ' . $disabled . '><i style="font-size: 20px;" class="fas fa-trash text-danger tooltips"></a></div><div class="clear-both"></div>'
            ];
        }

        $response = array(
            "recordsTotal" => count($allRecords),
            "recordsFiltered" => count($allRecords),
            "data" => $data
        );
        return $response;
    }
    
    /**
        @description : Add Plan View
        @author : Madhusmita Das
    */
    public function add(Request $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $result = $this->getMasterRecords($auth);
        return view('planning.add_plan', compact('result'));
    }
    
    /**
    * @OA\GET(
    * path="/plan/{id?}",
    * tags={"Planning"},
    * summary="Get planning details by plan id",
    *  @OA\Parameter(
     *    description="ID of plan",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     * )
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
    public function edit(PlanEditDeleteRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        if(isset($inputs['id'])) {
            $inputs['plan_id'] = $inputs['id'];
            unset($inputs['id']);
        }
        $planning = $this->getPlanId($inputs);
        if (!empty($planning)) {
            if ($auth['browser'] == 1) {
                $data = $this->getMasterRecords($auth);
                $auth['view'] = 'planning.add_plan';
            }
            $data['planning'] = $planning;
            return $this->sendResponse($data, 'Planning details fetched successfully', $auth);
        } else {
            return $this->sendError('Some error occured. Please validate your inputs', 'Some error occured. Please validate your inputs', $auth);
        }
    }
    
    
    
    /**
    * @OA\POST(
    * path="/plan/",
    * tags={"Planning"},
    * summary="Store Plan",
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass plan details",
    *    @OA\JsonContent(
    *       required={"vessel_name", "origin_id", "date_from", "date_to", "cargo_id", plan_details"},
    *       @OA\Property(property="vessel_name", type="string", format="string", example="Ship Bird"),
    *       @OA\Property(property="origin_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="date_from", type="string", format="string", example="01/11/2020 00:00"),
    *       @OA\Property(property="date_to", type="string", format="string", example="10/11/2020 23:59"),
    *       @OA\Property(property="cargo_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="plan_details", type="object",
    *           type="array",
    *              @OA\Items(
    *                 @OA\Property(property="consignee_id", type="string", example="2"),
    *                 @OA\Property(property="destination_id", type="string", example="8"),
    *              ),
    *           ),
    *       ),
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
    public function store(PlanningRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $response = $this->save($inputs, $auth);
        return $response;
    }
    
    /**
    * @OA\PUT(
    * path="/plan/{id?}",
    * tags={"Planning"},
    * summary="Update Plan",
    *  @OA\Parameter(
    *    description="ID of berth location id",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *  @OA\Schema(
    *    type="integer",
    *    format="int64"
    * )
    * ),
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass plan details",
    *    @OA\JsonContent(
    *       required={"vessel_name", "origin_id", "date_from", "date_to", "cargo_id", "plan_details"},
    *       @OA\Property(property="vessel_name", type="string", format="string", example="Ship Bird"),
    *       @OA\Property(property="origin_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="date_from", type="string", format="string", example="01/11/2020 00:00"),
    *       @OA\Property(property="date_to", type="string", format="string", example="10/11/2020 23:59"),
    *       @OA\Property(property="cargo_id", type="integer", format="integer", example="1"),
    *       @OA\Property(property="plan_details", type="object",
    *           type="array",
    *              @OA\Items(
    *                 @OA\Property(property="consignee_id", type="string", example="2"),
    *                 @OA\Property(property="destination_id", type="string", example="8"),
    *              ),
    *           ),
    *       ),
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
    public function update(PlanningRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $response = $this->save($inputs, $auth);
        return $response;
    }
    
    /**
        @description : Insert/Update Plan
        @author : Madhusmita Das
    */
    public function save($inputs, $auth) {
        $inputs['connection'] = $auth['connection'];
        $inputs['date_from'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $inputs['date_from'])->format('Y-m-d H:i:s');
        $inputs['date_to'] = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $inputs['date_to'])->format('Y-m-d H:i:s');
        $inputs['type']=1;
        $plan_details = $inputs['plan_details'];
        $vessel_name = trim($inputs['vessel_name']);
        
        /******************Start of Validations for API*********************/
        if($auth['browser'] == 0) {
            //Validate Planning Id
            if (isset($inputs['id']) && !empty($inputs['id'])) {
                $validate_planId_input = ['connection' => $auth['connection'], 'plan_id' => $inputs['id']];
                $validate_planId_response = $this->planRepository->getPlanningInfo($validate_planId_input);
                if($validate_planId_response['status'] == false) {
                    return $this->sendError('Invalid planning id provided', 'Invalid planning id provided', $auth);
                }
            }
            //Validate From and To Date
            if((\Carbon\Carbon::parse($inputs['date_to'])->timestamp) <= \Carbon\Carbon::parse($inputs['date_from'])->timestamp) {
                return $this->sendError('To Date cannot be prior to or equal to From Date', 'To Date cannot be prior to or equal to From Date', $auth);
            }
            
            //Validate Berth
            $berth_input = ['connection' => $auth['connection'], 'location_id' => $inputs['origin_id']];
            $berth_response = $this->locationRepository->getLocationById($berth_input, Config::get('constants.location_berth'));
            if ($berth_response['status'] == false) {
                return $this->sendError('Please provide a correct berth id', 'Please provide a correct berth id', $auth);
            }
            
            //Validate Plots
            foreach($inputs['plan_details'] as $plandetails) {
                if(isset($plandetails['destination_id']) && !empty($plandetails['destination_id'])) {
                    $plot_input = ['connection' => $auth['connection'], 'location_id' => $plandetails['destination_id']];
                    $plot_response = $this->locationRepository->getLocationById($plot_input, Config::get('constants.location_plot'));
                    if ($plot_response['status'] == false) {
                        return $this->sendError('Please provide a correct plot id', 'Please provide a correct plot id', $auth);
                    }
                }
            }
        }
        /******************End of Validations for API*********************/
        
        if ($this->checkIfPairExist($plan_details)) {
            return $this->sendError('Duplicate input for Customer name and plot given', ['Duplicate input for Customer name and plot given'], $auth);
        }
        unset($inputs['plan_details']);
        if (isset($inputs['id']) && !empty($inputs['id'])) {
            //Check If Challan Exists
            $ObjChallan = new ChallanRepository();
            $challan_inputs = ['connection' => $auth['connection'], 'plan_id' => $inputs['id']];
            $challan_response = $ObjChallan->getFilteredChallans($challan_inputs);
            if ($challan_response['status'] == true && !$challan_response['result']->isEmpty()) {
                return $this->sendError('Cannot edit this plan. The plan is in execution', 'Cannot edit this plan. The plan is in execution', $auth);
            }
            $inputs['updated_by'] = $auth['user_id'];
        } else {
            $inputs['created_by'] = $auth['user_id'];
            unset($inputs['id']);
        }
        
        /*Start: Add or Get Vessel By Name*/
        $vessel_inputs = ['connection' => $auth['connection'], 'vessel_name' => $vessel_name];
        $vessel_response = $this->vesselRepository->storeOrFetchVesselsByName($vessel_inputs);
        if($vessel_response == true) {
            $vessel = $vessel_response['result'];
            unset($inputs['vessel_name']);
            $inputs['vessel_id'] = $vessel->id;
        } else {
            return $this->sendError($vessel_response['result'], $vessel_response['result'], $auth);
        }
        /*End: Add or Get Vessel By Name*/
        
        $validate_input = $this->validatePlanningInput($inputs);
        if ($validate_input['status'] == true) {
            $planning_response = $this->planRepository->save($inputs);
            if ($planning_response['status'] == false) {
                return $this->sendError($planning_response['result'], $planning_response['result'], $auth);
            } else {
                $planning = $planning_response['result'];
                foreach ($plan_details as $key => $plan_detail) {
                    if (array_key_exists('id', $plan_detail)) {
                        unset($plan_details[$key]['id']);
                    }
                    $plan_details[$key]['plan_id'] = $planning->id;
                    $plan_details[$key]['created_by'] = $auth['user_id'];
                }
                $planning_detail_response = $this->planRepository->insertPlanningDetails($auth, $planning->id, $plan_details);
                if ($planning_detail_response == true) {
                    return $this->sendResponse([], 'Planning information saved successfully', $auth);
                } else {
                    return $this->sendError($planning_detail_response['result'], $planning_detail_response['result'], $auth);
                }
            }
        } else {
            return $this->sendError($validate_input['message'], $validate_input['message'], $auth);
        }
    }
    
    /**
        @description : Delete Plan
        @author : Madhusmita Das
    */
    /**
    * @OA\DELETE(
    * path="/plan/{id}",
    * tags={"Planning"},
    * summary="Delete a plan",
    *  @OA\Parameter(
    *    description="ID of berth location id",
    *    in="path",
    *    name="id",
    *    required=true,
    *    example="1",
    *  @OA\Schema(
    *    type="integer",
    *    format="int64"
    * )
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
    public function delete(PlanEditDeleteRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        if(isset($inputs['id'])) {
            $inputs['plan_id'] = $inputs['id'];
            unset($inputs['id']);
        }
        /******************Start of Validations for API*********************/
        if($auth['browser'] == 0) {
            //Validate Planning Id
            $validate_planId_response = $this->planRepository->getPlanningInfo($inputs);
            if($validate_planId_response['status'] == false) {
                return $this->sendError('Some error occured. Please validate your inputs', 'Some error occured. Please validate your inputs', $auth);
            }
        }
        /******************End of Validations for API*********************/
        
        //Check if any challan exist
        $ObjChallan = new ChallanRepository();
        $challan_response = $ObjChallan->getFilteredChallans($inputs);
        if ($challan_response['status'] == true && !$challan_response['result']->isEmpty()) {
            return $this->sendError('Cannot delete this plan. The plan is in execution', 'Cannot delete this plan. The plan is in execution', $auth);
        }
        $planning_response = $this->planRepository->getPlanningInfo($inputs);
        if ($planning_response['status'] == true) {
            $response = $this->planRepository->delete($inputs);
            if ($response['status'] == true) {
                return $this->sendResponse([], 'Planning records deleted successfully', $auth);
            } else {
                return $this->sendError($response['result'], $response['result'], $auth);
            }
        } else {
            return $this->sendError($planning_response['result'], $planning_response['result'], $auth);
        }
    }
    
    /**
        @description : Get Master Data for Add/Edit Plan View Page
        @author : Madhusmita Das
    */
    public function getMasterRecords($auth) {
        $data = [];
        //Get All Shifts
        $consignee_response = $this->consigneeRepository->getAllConsignees($auth);
        if ($consignee_response['status'] && !$consignee_response['data']->isEmpty()) {
            $data['consignees'] = $consignee_response['data'];
        }

        //Get All Plots
        $plot_input = ['connection' => $auth['connection'], 'type' => Config::get('constants.location_plot')];
        $plot_response = $this->locationRepository->getAllLocation($plot_input);
        if ($plot_response['status'] && !$plot_response['location']->isEmpty()) {
            $data['plots'] = $plot_response['location'];
        }

        //Get All Berths
        $berth_input = ['connection' => $auth['connection'], 'type' => Config::get('constants.location_berth')];
        $berth_response = $this->locationRepository->getAllLocation($berth_input);
        if ($berth_response['status'] && !$berth_response['location']->isEmpty()) {
            $data['berths'] = $berth_response['location'];
        }

        //Get All Cargo
        $cargo_response = $this->cargoRepository->getAllCargo($auth);
        if ($cargo_response['status'] && !$cargo_response['cargo']->isEmpty()) {
            $data['cargos'] = $cargo_response['cargo'];
        }

        return $data;
    }
    
    /**
        @description : Get Plan Details for Add/Edit Plan View Page
        @author : Madhusmita Das
    */
    public function getPlanId($inputs) {
        $data = [];
        $planning_response = $this->planRepository->getPlanningInfo($inputs);
        if ($planning_response['status'] == true) {
            $data = $planning_response['result'];
            $data['planning_details'] = [];
            $planning_details_response = $this->planRepository->getPlanningDetailsPlanningId($inputs);
            if (($planning_details_response['status'] == true) && (!$planning_details_response['result']->isEmpty())) {
                $data['planning_details'] = $planning_details_response['result'];
            }
        }
        return $data;
    }
    
    /**
        @description : Validate Input of Insert/Update Plan
        @author : Madhusmita Das
    */
    public function validatePlanningInput($planning_inputs) {
        $response['status'] = true;
        $planning_response = $this->planRepository->validatePlanningInputs($planning_inputs);
        if(($planning_response['status'] == true) && ((\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning_response['result']->date_from)->format('Y-m-d H:i')) == (\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning_inputs['date_from'])->format('Y-m-d H:i'))) && ((\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning_response['result']->date_to)->format('Y-m-d H:i')) == (\Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $planning_inputs['date_to'])->format('Y-m-d H:i')))) {
            $response['status'] = false;
            $response['message'] = 'Duplicate plan. A plan with the selected parameters already exists';
        } else {
            $inputs = ['connection' => $planning_inputs['connection'], 'origin_id' => $planning_inputs['origin_id'], 'date_from' => $planning_inputs['date_from'], 'date_to' => $planning_inputs['date_to']];
            if(isset($planning_inputs['id']) && !empty($planning_inputs['id'])) {
                $inputs['id'] = $planning_inputs['id'];
            }
            $planning_response = $this->planRepository->validatePlanningInputs($inputs);
            if($planning_response['status'] == true) {
                $response['status'] = false;
                $response['message'] = 'Berth is occupied for the selected date range';
            } else {
                $inputs = ['connection' => $planning_inputs['connection'], 'vessel_id' => $planning_inputs['vessel_id'], 'date_from' => $planning_inputs['date_from'], 'date_to' => $planning_inputs['date_to']];
                if(isset($planning_inputs['id']) && !empty($planning_inputs['id'])) {
                    $inputs['id'] = $planning_inputs['id'];
                }
                $planning_response = $this->planRepository->validatePlanningInputs($inputs);
                if($planning_response['status'] == true) {
                    $response['status'] = false;
                    $response['message'] = 'The vessel is already docked at another berth for the selected date range';
                }
            }
        }
        return $response;
    }
    
    /**
        @description : Add Truck to Plan View
        @author : Soumya Ranjan Das
    */
    public function create(Request $request){
        $allInput = $request->all();
        $param = $this->getAuth(); //in post you will need to pass like $this->getAuth($request->all());
        $allInput['connection'] = $param['connection'];
        $response = [];
        $response['truck_companies'] =$this->truckCompanyRepository->getAllTruckCompanies($param);
        $response['planning_details'] = $this->planTruckRepository->getPlanningDetailsById($allInput);
        $response['truck_details'] = $this->planTruckRepository->getTrucksByPlanningId($allInput);
        $response['all_truck_details'] = $this->truckRepository->getAllTrucks($allInput);
        return view('planning.truck.create',compact('response')); 
    }
    
    /**
        @description : Fetch Planned Trucks
        @author : Soumya Ranjan Das
    */
    /**
    * @OA\POST(
    * path="/planning/get-trucks",
    * tags={"Planning"},
    * summary="Planning load page api",
    * @OA\RequestBody(
    *    required=true,
    *    description="Pass Berth Id",
    *    @OA\JsonContent(
    *       required={}
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
    public function getTrucks(Request $request){
        $allInput = $request->all();
        $param = $this->getAuth(); //in post you will need to pass like $this->getAuth($request->all());
        $allInput['connection'] = $param['connection'];
        $response = $this->planTruckRepository->getAllTrucks($allInput);
        if ($response['status'] == false) {
            return $this->sendError($response['data'], $response['message'], $param);
        }       
        return $this->sendResponse($response['data'], $response['message'], $param);
    }
    
    /**
    * @OA\Post(
    * path="/plan/truck",
    *  tags={"Planning"},
    *  summary="Data Post for Planned Truck",
    *     @OA\RequestBody(
    *        required = true,
    *        description = "Planned Truck save Data",
    *        @OA\JsonContent(
    *             required={"plan_id","truck_id","truck_company_id"},
    *             @OA\Property(property="plan_id", type="integer", format="int64", example="1"), 
    *             type="object",
    *             @OA\Property(
    *                property="trucks",
    *                type="array",
    *                @OA\Items(
    *                      @OA\Property(
    *                         property="truck_id",
    *                         type="integer",
    *                         format="int64",
    *                         example="4"
    *                      ),
    *                      @OA\Property(
    *                         property="id",
    *                         type="integer",
    *                         format="int64",
    *                         example="null"
    *                      ),
    *                      @OA\Property(
    *                         property="truck_company_id",
    *                         type="integer",
    *                         format="int64",
    *                         example="1"
    *                      ),
    *                ),
    *             ),
    *        ),
    *     ),
    *  @OA\Response(
    *  response=200,
    *  description="Success",
    *  @OA\MediaType(
    *  mediaType="application/json",
    *  )
    *  ),
    * security={{ "apiAuth": {} }}
    *)
    */
    
    public function storeTruckForBtopPlan(PlanningTruckRequest $request){
        $inputs = $request->input();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        $inputs['created_by'] = $auth['user_id'];
        $trucks = $inputs['trucks'];
        if ($this->checkIfPairExist($trucks)) {
            return $this->sendError('Duplicate input for truck number given', ['Duplicate input for truck number given'], $auth);
        }
        $response = $this->planTruckRepository->saveTruckForBtopPlan($inputs);
        if ($response['status'] == true) {
            return $this->sendResponse([], $response['message'], $auth);
        }else {
            return $this->sendError($response, $response['message'], $auth);
        }
    }
    
    /**
     * @OA\DELETE(
     ** path="/plan/truck/{plan_id}/{truck_id}",
     *  tags={"Planning"},
     *  summary="Delete Planning Truck By Truck ID and Planning ID",
     *  description="Delete Planning Truck",
     *  @OA\Parameter(
     *    description="ID of Planning",
     *    in="path",
     *    name="plan_id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     * )
     * ),
     * @OA\Parameter(
     *    description="ID of Truck",
     *    in="path",
     *    name="truck_id",
     *    required=true,
     *    example="1",
     *  @OA\Schema(
     *    type="integer",
     *    format="int64"
     * )
     * ),
     *  @OA\Response(
     *  response=200,
     *  description="Success",
     *  @OA\MediaType(
     *  mediaType="application/json",
     *  )
     *  ),
     * security={{ "apiAuth": {} }}
     *)
     */
    
    public function deleteTruckForBtopPlan(PlanningTruckRequest $request){
        $allInput = $request->all();
        $param  = $this->getAuth($allInput);//in post you will need to pass like $this->getAuth($request->all());
        $allInput['connection'] = $param['connection'];
        $allInput['id'] = $request->truck_id;
        $allInput['plan_id'] = $request->plan_id;
        $response =  $this->planTruckRepository->deleteTruckForBtopPlan($allInput);
        
        if($response['status'] == false){
            return $this->sendError($response,$response['message'],$param);
        }
        return $this->sendResponse($response,$response['message'],$param);
    }

}