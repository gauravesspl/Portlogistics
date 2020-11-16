<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Repositories\Challan\ChallanRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Location\LocationRepository;
use Config;
use App\Services\BarcodeService;
use App\Http\Requests\EndTripRequest;
use App\Http\Controllers\API\V1\ShiftController;
use App\Traits\CustomTrait;
use App\Repositories\Organization\OrganizationRepository;
use App\Http\Requests\ScanChallanRequest;
use App\Http\Requests\FetchBarcodeRequest;
use App\Http\Requests\CreateChallanRequest;
use App\Services\PdfService;
use App\Repositories\BtopPlanning\BtopPlanningRepository as PlanRepository;
use App\Repositories\BtopPlannedTruck\BtopPlannedTruckRepository as PlanTruckRepository;
use Carbon\Carbon;
use App\Http\Requests\InboundChallanListRequest;

class ChallanController extends BaseController {

    use CustomTrait;

    protected $challanRepository, $shiftController, $locationRepository, $organizationRepository, $planTruckRepository, $pdfService;
    public $plotType = null;
    public $berthType = null;
    public $defaultDateFormat = 'Y-m-d H:i:s';
    public $viewDateFormat = 'd/m/Y H:i:s';

    public function __construct(ChallanRepository $challanRepository, ShiftController $shiftController, LocationRepository $locationRepository, OrganizationRepository $organizationRepository, PlanRepository $planRepository, PlanTruckRepository $planTruckRepository, PdfService $pdfService) {
        $this->challanRepository = $challanRepository;
        $this->shiftController = $shiftController;
        $this->locationRepository = $locationRepository;
        $this->organizationRepository = $organizationRepository;
        $this->planRepository = $planRepository;
        $this->planTruckRepository = $planTruckRepository;
        $this->pdfService = $pdfService;
        $this->plotType = Config::get('constants.location_plot');
        $this->berthType = Config::get('constants.location_berth');
    }

    /**
      @description : End trip Api
      @author : Itishree Nath
     */

    /**
     * @OA\POST(
     * path="/trip/end",
     * tags={"Challan"},
     * summary="End a trip",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Plan Id, Truck Id and Destination Id",
     *    @OA\JsonContent(
     *       required={"plan_id","truck_id","destination_id"},
     *       @OA\Property(property="plan_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="truck_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="destination_id", type="integer", format="integer", example="1"),
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
     * )
     * */
    public function endTrip(EndTripRequest $request) {
        $allInput = $request->all();
        $param = $this->getAuth($allInput);
        $allInput['connection'] = $param['connection'];
        $allInput['user_id'] = $param['user_id'];
        //to check Location is plot or not;
        $return_response = []; //Intialize response array
        $location_response = $this->locationRepository->getLocationById($allInput, $this->plotType);
        if ($location_response['status']) {
            //if location is plot, check whether the Trip has already ended or not 
            $challan_result = $this->challanRepository->getChallanByInput($allInput);
            if ($challan_result['status']) {
                if (empty($challan_result['result']['unloaded_at']) && empty($challan_result['result']['unloaded_by'])) {
                    //if not ended then update challan table and end trip
                    $challan_response = $this->challanRepository->updateChallanToEndTrip($allInput);
                    if (!$challan_response['status']) {
                        $return_response = ['status' => false, 'message' => $challan_response['message']];
                    } else {
                        //update status as 2-unloaded of btop_planned_trucks
                        $truck_response = $this->planTruckRepository->updateStatusAsUnloaded($allInput);
                        if (!$truck_response['status']) {
                            $return_response = ['status' => false, 'message' => $truck_response['message']];
                        } else {
                            $return_response = ['status' => true, 'message' => 'The trip ended successfully', 'result' => []];
                        }
                    }
                } else {
                    $return_response = ['status' => false, 'message' => 'The trip has already ended'];
                }
            } else {
                $return_response = ['status' => false, 'message' => 'There is no such trip to be ended'];
            }
        } else {
            $return_response = ['status' => false, 'message' => $this->plotValidationMsg];
        }
        //Return Response
        if ($return_response['status']) {
            return $this->sendResponse($return_response['result'], $return_response['message'], $param);
        } else {
            return $this->sendError($return_response['message'], $return_response['message'], $param);
        }
    }

    /**
      @description : Challan List View
      @author : Madhusmita Das
     */
    public function index(Request $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        $inputs['user_id'] = $auth['user_id'];
        $shifts = $this->getDataFromJsonResponse($this->shiftController->index($request));
        return view('challans.index', compact('shifts'));
    }

    /**
      @description : Challan List by Ajax
      @author : Madhusmita Das
     */
    public function getChallanList(Request $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);

        //For Pagination
        $columns = $inputs['columns'];
        $start = $inputs['start'];
        $limit = $inputs['length'];
        $order = [];
        $where = trim($inputs['search']['value']);
        if (!empty($where)) {
            $where = preg_replace('/[^a-zA-Z0-9 ]/s', '', $where);
        }

        if (isset($inputs['order'])) {
            $orderBy = $inputs['order'][0];
            if ($orderBy['column'] > 0) {
                $order['field'] = $columns[$orderBy['column']]['data'];
                $order['dir'] = $orderBy['dir'];
            }
        }

        $filterCondition = $this->getFilterConditions($inputs['filterParams']);
        $challans = $this->challanRepository->getListChallans($auth, $start, $limit, $order, $where, $filterCondition, false);
        $allRecord = $this->challanRepository->getListChallans($auth, $start, $limit, $order, $where, $filterCondition, true);

        /* Start :: Get Challan Counts Based On Filter */
        $challanCountResponse = $this->challanRepository->getReconciledCounts($auth, $filterCondition);
        $challanCounts = [];
        if ($challanCountResponse) {
            $challanCounts = $challanCountResponse['result'];
        }
        /* End :: Get Challan Counts Based On Filter */

        $data = [];
        foreach ($challans as $challan) {
            $checked = ($challan->is_deposit == '1') ? 'checked' : '';
            $disabled = ($challan->is_deposit == '1') ? 'disabled' : '';
            $data[] = [
                'id' => '<input type="checkbox"  class="challan_id" value="' . $challan->id . '" data-reconcile-status = "' . $challan->is_deposit . '" ' . $checked . ' ' . $disabled . '/>',
                'challan_no' => '<a href="' . url('/') . '/' . $challan->pdf_path . '" target="_blank">' . $challan->challan_no . '</a>',
                'type' => $challan->type,
                'truck_no' => $challan->truck_no,
                'origin' => $challan->origin,
                'cargo_name' => $challan->cargo_name,
                'date_from' => \Carbon\Carbon::createFromFormat($this->defaultDateFormat, $challan->date_from)->format($this->viewDateFormat),
                'date_to' => \Carbon\Carbon::createFromFormat($this->defaultDateFormat, $challan->date_to)->format($this->viewDateFormat),
                'shift_name' => $challan->shift_name,
                'status' => $challan->status
            ];
        }
        $response = array(
            "recordsTotal" => count($allRecord),
            "recordsFiltered" => count($allRecord),
            "data" => $data,
            'challanCounts' => $challanCounts
        );
        return json_encode($response);
    }

    /**
      @description : Inbound Challan List Api
      @author : Itishree Nath
     */

    /**
     * @OA\GET(
     * path="/challan/inbound-list/{plan_id}/{destination_id}",
     * tags={"Challan"},
     * summary="Inbound Challan List",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Plan Id and Plot Id",
     *    @OA\JsonContent(
     *       required={"plan_id","plot_id"},
     *       @OA\Property(property="plan_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="destination_id", type="integer", format="integer", example="1"),
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
     * )
     * */
    public function getInboundChallanList(InboundChallanListRequest $request) {
        $allInput = $request->all();
        $param = $this->getAuth($allInput);
        $allInput['connection'] = $param['connection'];
        $allInput['user_id'] = $param['user_id'];
        $return_response = []; //Intialize response array
        //to get Organization details
        $org_response = $this->organizationRepository->getOrganizationById($param['user_auth']->organization_id, $allInput);
        if (!$org_response['status']) {
            $return_response = ['status' => false, 'message' => $org_response['message']];
        } else {
            $organization = $org_response['result'];
            //to check Location is plot or not;
            $location_response = $this->locationRepository->getLocationById($allInput, $this->plotType);
            if (!$location_response['status']) {
                $return_response = ['status' => false, 'message' => 'Please provide a correct plot no.'];
            } else {
                //fetch planned loaded truck details from btop_planned_trucks (input:planning_id)
                $planned_trucks_response = $this->planTruckRepository->getPlannedLoadedTrucksById($allInput);
                if (!$planned_trucks_response['status']) {
                    $return_response = ['status' => false, 'message' => 'There is no planned trucks for this plot'];
                } else {
                    if ($planned_trucks_response['result']->isEmpty()) {
                        $return_response = ['status' => false, 'message' => 'No record found. Please provide correct information.'];
                    } else {
                        $data = [];
                        $data['plan_id'] = $allInput['plan_id'];
                        $data['organization'] = ['id' => $organization->id, 'name' => $organization->name];
                        $data['trucks'] = [];
                        foreach ($planned_trucks_response['result'] as $key => $trucks_response) {
                            $row = [];
                            $allInput['truck_id'] = $trucks_response->truck_id;
                            $challan_response = $this->challanRepository->getInboundChallanList($allInput);
                            if ($challan_response['status']) {
                                $row['truck'] = ['id' => $trucks_response->truck->id, 'name' => $trucks_response->truck->truck_no];
                                $challan = $challan_response['result'];
                                $row['challan'] = ['id' => $challan->id, 'challan_no' => $challan->challan_no, 'loaded_at' => $challan->loaded_at, 'shift_id' => $challan->shift->id, 'shift_name' => $challan->shift->name];
                                $data['trucks'][] = $row;
                            }
                        }
                        if (empty($data['trucks'])) {
                            $return_response = ['status' => false, 'result' => $data, 'message' => 'No challan found for this destination'];
                        } else {
                            $return_response = ['status' => true, 'result' => $data, 'message' => 'Inbound challan list fetched successfully'];
                        }
                    }
                }
            }
        }
        //Return Response
        if ($return_response['status']) {
            return $this->sendResponse($return_response['result'], $return_response['message'], $param);
        } else {
            return $this->sendError($return_response['message'], $return_response['message'], $param);
        }
    }

    /**
      @description : Reconcile Challan
      @author : Madhusmita Das
     */

    /**
     * @OA\POST(
     * path="/challan/reconcile",
     * tags={"Challan"},
     * summary="Reconcile Challan(s)",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Challan Id(s) in an array",
     *    @OA\JsonContent(
     *       required={"challan_ids"},
     *       @OA\Property(
     *           property="challan_ids", 
     *           type="array",
     *           @OA\Items(
     *               type="string",
     *               example="1"
     *           ),
     *      ),
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
     * )
     * */
    public function reconcileChallan(Request $request) {
        $inputs = $request->input();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        $inputs['user_id'] = $auth['user_id'];
        $return_response = []; //Intialize response array
        $response = $this->challanRepository->reconcileChallan($inputs);
        if (!$response['status']) {
            $return_response = ['status' => false, 'message' => 'Unable to reconcile the selected challan(s)'];
        } else {
            $return_response = ['status' => true, 'result' => [], 'message' => 'Selected challan(s) reconciled successfully'];
        }
        //Return Response
        if ($return_response['status']) {
            return $this->sendResponse($return_response['result'], $return_response['message'], $auth);
        } else {
            return $this->sendError($return_response['message'], $return_response['message'], $auth);
        }
    }

    /**
      @description : Scan Challan Api
      @author : Madhusmita Das
     */

    /**
     * @OA\POST(
     * path="/challan/scan",
     * tags={"Challan"},
     * summary="Scan Challan",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Challan No. and Destination Id",
     *    @OA\JsonContent(
     *       required={"plan_id","destination_id"},
     *       @OA\Property(property="challan_no", type="string", format="string", example="2020092570488"),
     *       @OA\Property(property="destination_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     * */
    public function scanChallanApi(ScanChallanRequest $request) {
        $inputs = $request->input();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        $inputs['user_id'] = $auth['user_id'];
        $return_response = []; //Intialize response array
        $org_response = $this->organizationRepository->getOrganizationById($auth['user_auth']->organization_id, $inputs);
        //Get organization details
        if (!$org_response['status']) {
            $return_response = ['status' => false, 'message' => $org_response['message']];
        } else {
            $organization = $org_response['result'];
            //Get challan details
            $challan_response = $this->challanRepository->getChallanDetails($inputs);
            if (!$challan_response['status']) {
                $return_response = ['status' => false, 'message' => $challan_response['message']];
            } else {
                //Validate Inputs
                $challan = $challan_response['result'];
                if ($challan->destination->type != $this->plotType) {
                    $return_response = ['status' => false, 'message' => 'Please provide a correct plot no.'];
                } else if ($challan->destination_id != $inputs['destination_id']) {
                    $return_response = ['status' => false, 'message' => 'This challan belongs to a different plot'];
                } else if ((!empty($challan->unloaded_at)) || ($challan->status == '2')) {
                    $return_response = ['status' => false, 'message' => 'The trip is already ended'];
                } else if ($challan->is_scanned == '1') {
                    $return_response = ['status' => false, 'message' => 'This challan is already scanned'];
                } else {
                    //Update challan scan status
                    $update_challan = $this->challanRepository->updateChallanScanStatus($inputs);
                    if (!$update_challan['status']) {
                        $return_response = ['status' => false, 'message' => 'Unable to update the challan scan status'];
                    } else {
                        $data = [
                            'plan_id' => $challan->plan_id,
                            'trucks' => ['id' => $challan->truck->id, 'truck_no' => $challan->truck->truck_no],
                            'organization' => ['id' => $organization->id, 'name' => $organization->name],
                            'plot' => ['id' => $challan->destination->id, 'name' => $challan->destination->location],
                        ];
                        $return_response = ['status' => true, 'result' => $data, 'message' => 'Challan scanned successfully'];
                    }
                }
            }
        }
        if ($return_response['status']) {
            return $this->sendResponse($return_response['result'], $return_response['message'], $auth);
        } else {
            return $this->sendError($return_response['message'], $return_response['message'], $auth);
        }
    }

    /**
      @description : Fetch Barcode Api
      @author : Madhusmita Das
     */

    /**
     * @OA\GET(
     * path="/challan/barcode/{plan_id?}/{destination_id?}/{truck_id?}",
     * tags={"Challan"},
     * summary="Fetch Barcode",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Plan Id, Truck Id and Destination Id",
     *    @OA\JsonContent(
     *       required={"plan_id","truck_id","location_id"},
     *       @OA\Property(property="plan_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="destination_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="truck_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     * */
    public function fetchBarcodeApi(FetchBarcodeRequest $request) {
        $inputs = $request->all();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        $inputs['user_id'] = $auth['user_id'];
        $return_response = []; //Intialize response array
        //Get organization details
        $org_response = $this->organizationRepository->getOrganizationById($auth['user_auth']->organization_id, $inputs);
        if (!$org_response['status']) {
            $return_response = ['status' => false, 'message' => $org_response['message']];
        } else {
            $organization = $org_response['result'];
            //Get location details
            $location_response = $this->locationRepository->getLocationById($inputs, $this->plotType);
            if (!$location_response['status']) {
                $return_response = ['status' => false, 'message' => 'Please provide a correct plot no.'];
            } else {
                //Get challan details
                $challan_response = $this->challanRepository->getFilteredChallans($inputs);
                if (!$challan_response['status']) {
                    $return_response = ['status' => false, 'message' => $challan_response['message']];
                } else {
                    $challans = [];
                    foreach ($challan_response['result'] as $challan) {
                        $challan->barcode_path = (isset($challan->barcode_path) && !empty($challan->barcode_path)) ? url('/') . '/' . $challan->barcode_path : '';
                        $challans[] = ['id' => $challan->id, 'challan_no' => $challan->challan_no, 'barcode_path' => $challan->barcode_path];
                    }
                    $data = [
                        'challan' => $challans,
                        'plan_id' => $inputs['plan_id'],
                        'destination_id' => $inputs['destination_id'],
                        'organization' => ['id' => $organization->id, 'name' => $organization->name]
                    ];
                    $return_response = ['status' => true, 'result' => $data, 'message' => 'Challan scanned successfully'];
                }
            }
        }
        //Return Response
        if ($return_response['status']) {
            return $this->sendResponse($return_response['result'], $return_response['message'], $auth);
        } else {
            return $this->sendError($return_response['message'], $return_response['message'], $auth);
        }
    }

    /**
      @description : Create Challan Api
      @author : Madhusmita Das
     */

    /**
     * @OA\POST(
     * path="/challan",
     * tags={"Challan"},
     * summary="Create Challan",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass Plan Id, Type, Truck Id, Origin Location Id i.e Berth Id, Destination Location Id i.e Plot Id, Consignee Id, Shift Id",
     *    @OA\JsonContent(
     *       required={"plan_id", "type", "truck_id", "origin_location_id", "destination_location_id", "consignee_id", "shift_id"},
     *       @OA\Property(property="plan_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="type", type="integer", format="integer", example="1"),
     *       @OA\Property(property="truck_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="origin_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="destination_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="consignee_id", type="integer", format="integer", example="1"),
     *       @OA\Property(property="shift_id", type="integer", format="integer", example="1"),
     *    ),
     * ),
     * @OA\Response(
     *      response=200,
     *      description="Success",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     * security={{ "apiAuth": {} }}
     * )
     * */
    public function createChallan(CreateChallanRequest $request) {
        $inputs = $request->input();
        $auth = $this->getAuth($inputs);
        $inputs['connection'] = $auth['connection'];
        $return_response = []; //Intialize response array
        $ObjPlanRepository = new PlanRepository();
        //Get organization details
        $org_response = $this->organizationRepository->getOrganizationById($auth['user_auth']->organization_id, $inputs);
        if (!$org_response['status']) {
            $return_response = ['status' => false, 'message' => $org_response['message']];
        } else {
            $organization = $org_response['result'];
            //Validate origin
            $origin_inputs = ['connection' => $auth['connection'], 'origin_id' => $inputs['origin_id']];
            $origin_response = $this->locationRepository->getLocationById($origin_inputs, $this->berthType);
            if (!$origin_response['status']) {
                $return_response = ['status' => false, 'message' => 'Please provide a correct origin id'];
            } else {//valid origin id provided
                $origin = $origin_response['result'];
                //Validate destination
                $destination_inputs = ['connection' => $auth['connection'], 'destination_id' => $inputs['destination_id']];
                $destination_response = $this->locationRepository->getLocationById($destination_inputs, $this->plotType);
                if (!$destination_response['status']) {
                    $return_response = ['status' => false, 'message' => 'Please provide a correct destination id'];
                } else {//valid destination id provided
                    $destination = $destination_response['result'];
                    //Validate Planning Details
                    $planning_response = $ObjPlanRepository->getPlanningInfo($inputs);
                    if (!$planning_response['status']) {
                        $return_response = ['status' => false, 'message' => $planning_response['message']];
                    } else {//planning information fetched
                        $planning = $planning_response['result'];
                        if ($planning->origin_id != $inputs['origin_id']) {
                            $return_response = ['status' => false, 'message' => 'The provided origin id does not belongs to this plan id. Please provide correct information.'];
                        } else {//correct origin id provided
                            //Validate truck id
                            $planned_truck_response = $ObjPlanRepository->getPlannedTrucksByPlanningId($inputs);
                            if (!$planned_truck_response['status']) {
                                $return_response = ['status' => false, 'message' => $planned_truck_response['message']];
                            } else if ($planned_truck_response['result']->isEmpty()) {
                                $return_response = ['status' => false, 'message' => 'The given truck does not belongs to this plan. Please provide correct information.'];
                            } else { //correct truck id provided
                                //Validate consignee id
                                $planning_detail_response = $ObjPlanRepository->getPlanningDetailsPlanningId($inputs);
                                if (!$planning_detail_response['status']) {
                                    $return_response = ['status' => false, 'message' => $planning_detail_response['message']];
                                } else if ($planning_detail_response['result']->isEmpty()) {
                                    $return_response = ['status' => false, 'message' => 'The given consignee or plot does not belongs to this plan. Please provide correct information.'];
                                } else {//correct consignee id provided
                                    //Validate challan
                                    $inputs['status'] = '1'; //unload pending
                                    $challan_response = $this->challanRepository->getFilteredChallans($inputs);
                                    if (!$challan_response['status']) {
                                        $return_response = ['status' => false, 'message' => $challan_response['message']];
                                    } else if(!$challan_response['result']->isEmpty()) {
                                        $return_response = ['status' => false, 'message' => 'Challan is already created for this trip'];
                                    } else {
                                        //Insert Challan
                                        $challan_input['connection'] = $auth['connection'];
                                        $challan_input['plan_id'] = $inputs['plan_id'];
                                        $challan_input['origin_id'] = $inputs['origin_id'];
                                        $challan_input['destination_id'] = $inputs['destination_id'];
                                        $challan_input['truck_id'] = $inputs['truck_id'];
                                        $challan_input['shift_id'] = $inputs['shift_id'];
                                        $challan_input['cargo_id'] = $planning->cargo_id;
                                        $challan_input['consignee_id'] = $inputs['consignee_id'];
                                        $challan_input['challan_no'] = date('Ymd') . rand(100, 100000);
                                        $challan_input['type'] = (isset($planning->type) && !empty($planning->type)) ? $planning->type : '1'; //btop
                                        $challan_input['status'] = '1'; //unload pending
                                        $challan_input['is_deposit'] = '0'; //not reconciled
                                        $challan_input['is_scanned'] = '0';
                                        $challan_input['created_by'] = $challan_input['loaded_by'] = $auth['user_id'];
                                        $challan_input['loaded_at'] = date($this->defaultDateFormat);

                                        //Barcode Generation
                                        $ObjBarcodeService = new BarcodeService();
                                        $barcode_response = $ObjBarcodeService->generate($challan_input, $auth);
                                        if ($barcode_response['status'] && !empty($barcode_response['barcode_path'])) {
                                            $challan_input['barcode_path'] = $barcode_response['barcode_path'];
                                            //PDF Generation and Update
                                            $pdf_response = $this->pdfService->createPdf($challan_input, $planning, $organization, $origin, $destination, $auth);
                                            if ($pdf_response['status']) {
                                                $challan_input['pdf_path'] = $pdf_response['pdf_path'];
                                                //Insert Challan In DB
                                                $challan_result = $this->challanRepository->saveChallan($challan_input);
                                                if (!$challan_result['status']) {
                                                    $return_response = ['status' => false, 'message' => 'Error occured in updating challan data'];
                                                } else {
                                                    //Update Status of Truck
                                                    $truck_inputs = ['connection' => $auth['connection'], 'truck_id' => $inputs['truck_id'], 'user_id' => $auth['user_id'], 'plan_id' => $inputs['plan_id']];
                                                    $truck_response = $this->planTruckRepository->updateStatusAsLoaded($truck_inputs);
                                                    if (!$truck_response['status']) {
                                                        $return_response = ['status' => false, 'message' => 'Error occured in updating truck status'];
                                                    } else {
                                                        $challan = $challan_result['result'];
                                                        $response_data = $pdf_response['data'];
                                                        $response_data['challan'] = ['id' => $challan->id, 'challan_no' => $challan->challan_no];
                                                        $response_data['barcode_path'] = url('/') . '/' . $challan_input['barcode_path'];
                                                        $response_data['pdf_path'] = url('/') . '/' . $challan_input['pdf_path'];
                                                        $response_data['plan_id'] = $planning->id;
                                                        $response_data['date_from'] = $planning->date_from;
                                                        $response_data['date_to'] = $planning->date_to;
                                                        $return_response = ['status' => true, 'result' => $response_data, 'message' => 'Challan generated successfully'];
                                                    }
                                                }
                                            } else {
                                                $return_response = ['status' => false, 'message' => 'Unable to generate challan. Please try again.'];
                                            }
                                        } else {
                                            $return_response = ['status' => false, 'message' => 'Unable to generate barcode. Please try again.'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //Return Response
        if ($return_response['status']) {
            return $this->sendResponse($return_response['result'], $return_response['message'], $auth);
        } else {
            return $this->sendError($return_response['message'], $return_response['message'], $auth);
        }
    }
}
