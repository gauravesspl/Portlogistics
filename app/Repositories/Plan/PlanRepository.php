<?php

namespace App\Repositories\Plan;

use App\Models\Plan;
use Exception;
use Log;
use App\Models\PlanDetail;
use App\Models\PlanTruck;
use Carbon\Carbon;

class PlanRepository
{

    /**
     * List Plans based on origin id
     * @author Gaurav Agrawal
     * @param array $allInput ['origin_id','connection']
     * 
     * @return array ['status','result','message']
     */
    public function getPlanningDetailsByBerthAndDate($allInput)
    {
        $result['status'] = false;
        $result['data'] = [];
        try {
            $planning = new Plan();
            $planning->setConnection($allInput['connection']);
            $allPlanning = $planning
                ->with(['cargo' => function ($query) {
                    $query->select('id', 'name');
                }, 'vessel' => function ($query) {
                    $query->select('id', 'name');
                }, 'location' => function ($query) {
                    $query->select('id', 'location');
                }, 'consignees' => function ($query) {
                    $query->distinct()->select('consignee_id', 'plan_id', 'name');
                }, 'plots' => function ($query) {
                    $query->select('destination_id', 'consignee_id', 'plan_id', 'location');
                }, 'trucks' => function ($query) {
                    $query->select('truck_id', 'truck_no')->where('status', '2');
                }])
                ->where('date_from', '<=', now())
                ->where('date_to', '>=', now());
            $allPlanning = $allPlanning->where('origin_id', $allInput['origin_id']);
            $allPlanning = $allPlanning->first();
            $result['status'] = true;
            if ($allPlanning == null) {
                $result['message'] = 'No data available!';
                return $result;
            }
            $result['message'] = 'Planning data fetch successfully.';
            $result['data'] = $allPlanning->toArray();
            return $result;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $result['message'] = 'Something went wrong';
            $result['data'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * Get Planning info with vessel details by plan id
     * 
     * @param array $inputs ['plan_id','connection']
     * 
     * @return array ['status','result','message']
     */
    public function getPlanningInfo($inputs) {
        $response['status'] = true;
        try {
            $planning = new Plan();
            $planning->setConnection($inputs['connection']);
            if (isset($inputs['plan_id']) && !empty($inputs['plan_id'])) {
                $planning = $planning->where('id', $inputs['plan_id']);
            }
            $planning = $planning->with('vessel')->firstOrFail();
            if ($planning == null) {
                $response['status'] = false;
                $response['message'] = $response['result'] = 'Please provide a valid plan id';
            } else {
                $response['result'] = $planning;
                $response['message'] = 'Record fetched successfully';
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = 'Something Went Wrong';
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Get Planned trucks by plan id and truck id
     * 
     * @param array $inputs ['plan_id','connection','truck_id']
     * 
     * @return array ['status','result','message']
     */
    public function getPlannedTrucksByPlanningId($inputs) {
        $response['status'] = true;
        try {
            $plannedTruck = new PlanTruck();
            $plannedTruck->setConnection($inputs['connection']);
            if (isset($inputs['plan_id']) && !empty($inputs['plan_id'])) {
                $plannedTruck = $plannedTruck->where('plan_id', $inputs['plan_id']);
            }
            if (isset($inputs['truck_id']) && !empty($inputs['truck_id'])) {
                $plannedTruck = $plannedTruck->where('truck_id', $inputs['truck_id']);
            }
            $plannedTrucks = $plannedTruck->get();
            $response['result'] = $plannedTrucks;
            $response['message'] = 'Record fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = 'Something Went Wrong';
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Get Planning details by plan id, consignee id and destination id
     * 
     * @param array $inputs ['plan_id','connection','consignee_id','destination_id']
     * 
     * @return array ['status','result','message']
     */
    public function getPlanningDetailsPlanningId($inputs) {
        $response['status'] = true;
        try {
            $planningDetail = new PlanDetail();
            $planningDetail->setConnection($inputs['connection']);
            if (isset($inputs['plan_id']) && !empty($inputs['plan_id'])) {
                $planningDetail = $planningDetail->where('plan_id', $inputs['plan_id']);
            }
            if (isset($inputs['consignee_id']) && !empty($inputs['consignee_id'])) {
                $planningDetail = $planningDetail->where('consignee_id', $inputs['consignee_id']);
            }
            if (isset($inputs['destination_id']) && !empty($inputs['destination_id'])) {
                $planningDetail = $planningDetail->where('destination_id', $inputs['destination_id']);
            }
            $planningDetails = $planningDetail->get();
            $response['result'] = $planningDetails;
            $response['message'] = 'Record fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = 'Something Went Wrong';
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Get Plan list using ajax for web display in data table
     * 
     * @param array $auth
     * @param integer $start
     * @param integer $limit
     * @param array $order
     * @param string $where condition expression
     * @param array $filterCondition condition expression to filter
     * @param array $additionalCondition condition expression
     * @param boolean $countOnly
     * 
     * @return array $data data to display in datatable
     */
    public function getListPlannings($auth, $start, $limit, $order = [], $where = '', $filterCondition = [], $additionalCondition = [], $countOnly = false)
    {
        $data = [];
        $fields = "
            plans.id,
            vessels.name as vessel_name,
            origins.location as berth_name,
            plans.date_from,
            plans.date_to,
            cargos.name as cargo_name,
            plans.created_at,
            count(plan_trucks.id) as truck_count
        ";
        $joins = "
            INNER JOIN vessels ON vessels.id = plans.vessel_id
            INNER JOIN locations AS origins ON origins.id = plans.origin_id
            INNER JOIN cargos ON cargos.id = plans.cargo_id
            LEFT JOIN plan_trucks ON plan_trucks.plan_id = plans.id
        ";
        try {
            $btopPlanning = new Plan();
            $btopPlanning->setConnection($auth['connection']);
            $query = "SELECT tbl.* FROM (";
            $query .= " SELECT " . $fields . " FROM plans " . $joins;
            if (!empty($filterCondition)) {
                $query .= " WHERE ";
                foreach ($filterCondition as $key => $value) {
                    if ($key > 0) {
                        $query .= " AND ";
                    }
                    if (\DateTime::createFromFormat('d/m/Y H:i', $value['value']) !== FALSE) {
                        $datetime = \Carbon\Carbon::createFromFormat('d/m/Y H:i', $value['value'])->format('Y-m-d H:i:s');
                        if ($value['table_field'] == 'plans.date_from') {
                            $query .= "(plans.date_from >= '" . $datetime . "' OR plans.date_to >= '" . $datetime . "')";
                        } else if ($value['table_field'] == 'plans.date_to') {
                            $query .= "(plans.date_from <= '" . $datetime . "' OR plans.date_to <= '" . $datetime . "')";
                        }
                    } else {
                        $query .= $value['table_field'] . " = '" . $value['value'] . "'";
                    }
                }
            }
            $query .= " GROUP BY plans.id";
            $query .= " ) AS tbl";

            //Additional Condition will be added here
            $having = "";
            if (!empty($where)) {
                $having .= " HAVING (tbl.vessel_name like '%" . $where . "%' OR tbl.berth_name like '%" . $where . "%' OR tbl.date_from like '%" . $where . "%' OR tbl.date_to like '%" . $where . "%' OR tbl.cargo_name like '%" . $where . "%' OR tbl.created_at like '%" . $where . "%')";
            }
            if (!empty($additionalCondition)) {
                foreach ($additionalCondition as $condition) {
                    if (empty($having)) {
                        $having .= " HAVING ";
                    } else {
                        $having .= " AND ";
                    }
                    if ($condition['table_field'] == 'truck_count') {
                        $having .= ($condition['value'] == 0) ? "tbl." . $condition['table_field'] . " = 0" : "tbl." . $condition['table_field'] . " > 0";
                    }
                }
            }
            $query .= (!empty($having)) ? $having : '';

            if (!$countOnly) {
                if (empty($order)) {
                    $query .= " ORDER BY tbl.created_at desc";
                } else {
                    $query .= " ORDER BY tbl." . $order['field'] . " " . $order['dir'];
                }
            }
            $query .= (!$countOnly && $limit != -1) ? " LIMIT $start, $limit" : '';
            $data = \DB::select(\DB::raw($query));
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
        return $data;
    }

    /**
     * Get Consignee with allocted plot details by plan id
     * 
     * @param array $inputs ['connection','plan_id']
     * 
     * @param return array ['result','status','message']
     */
    public function getConsigneePlotListByPlanningId($inputs)
    {
        $response['status'] = true;
        try {
            $planningDetail = new PlanDetail();
            $planningDetail->setConnection($inputs['connection']);
            $planningDetail = $planningDetail->select(
                'consignees.name as consignee_name',
                \DB::raw('GROUP_CONCAT(destinations.location) AS destination_names')
            )
                ->join('consignees', 'consignees.id', '=', 'plan_details.consignee_id')
                ->leftJoin('locations as destinations', 'destinations.id', '=', 'plan_details.destination_id')
                ->where('plan_details.plan_id', $inputs['plan_id'])
                ->groupBy('plan_details.consignee_id')
                ->get();
            $response['result'] = $planningDetail;
            $response['message'] = 'Record fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = 'Something Went Wrong';
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Delete Plan details along with plan details and plan trucks by plan id
     * 
     * @param array $inputs ['connection','plan_id']
     * 
     * @param return array ['result','status']
     */
    public function delete($inputs)
    {
        $response['status'] = true;
        try {
            $planning = new Plan();
            $planningDetail = new PlanDetail();
            $plannedTruck = new PlanTruck();
            $planning->setConnection($inputs['connection']);
            if (isset($inputs['plan_id']) && !empty($inputs['plan_id'])) {
                $planningDetails = $planningDetail->where('plan_id', $inputs['plan_id'])->get();
                if (!$planningDetails->isEmpty()) {
                    foreach ($planningDetails as $planning_detail) {
                        $planningDetail::where('id', $planning_detail->id)->firstOrFail()->delete();
                    }
                }
                $plannedTrucks = $plannedTruck->where('plan_id', $inputs['plan_id'])->get();
                if (!$plannedTrucks->isEmpty()) {
                    foreach ($plannedTrucks as $planned_truck) {
                        $plannedTruck::where('id', $planned_truck->id)->firstOrFail()->delete();
                    }
                }
                $planning = $planning->findOrFail($inputs['plan_id']);
                $planning = $planning->delete();
                if (!$planning) {
                    $response['status'] = false;
                    $response['result'] = 'Some error occured';
                } else {
                    $response['status'] = true;
                }
            } else {
                $response['status'] = false;
                $response['result'] = 'Please provide a planning id';
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Save/update Plan details id is passed only when update old plan details and value of type may be 1 for BtoP or 2 for PtoP
     * 
     * @param array $inputs ['connection','id','origin_id','cargo_id','vessel_id','date_from','date_to','type']
     * 
     * @param return array ['result','status']
     */
    public function save($inputs)
    {
        $response['status'] = true;
        try {
            $planning = new Plan();
            $planning->setConnection($inputs['connection']);

            if (isset($inputs['id']) && !empty($inputs['id'])) {
                $planning = $planning->where('id', $inputs['id'])->firstOrFail();
            }

            $planning->origin_id = $inputs['origin_id'];
            $planning->cargo_id = $inputs['cargo_id'];
            $planning->vessel_id = $inputs['vessel_id'];
            $planning->date_from = $inputs['date_from'];
            $planning->date_to = $inputs['date_to'];
            $planning->type = $inputs['type'];
            if ($planning->save()) {
                $response['result'] = $planning;
            } else {
                $response['status'] = false;
                $response['result'] = 'Some error occured';
            }
        } catch (Exception $e) {
            $response['status'] = false;
            Log::error($e->getMessage());
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Insert Plan details for plan by plan id and plan details
     * 
     * @param array $auth
     * @param integer $plan_id
     * @param array $plan_details ['plan_id','created_by']
     * 
     * @return array ['result','status']
     */
    public function insertPlanningDetails($auth, $plan_id, $plan_details)
    {
        $response['status'] = true;
        try {
            $planningDetail = new PlanDetail();
            $planningDetail->setConnection($auth['connection']);
            $planningDetails = $planningDetail->where('plan_id', $plan_id)->get();
            if (!$planningDetails->isEmpty()) {
                foreach ($planningDetails as $planning_detail) {
                    $planningDetail::where('id', $planning_detail->id)->first()->delete();
                }
            }
            $planningDetail = $planningDetail->insert($plan_details);
            if (!$planningDetail) {
                $response['status'] = false;
                $response['result'] = 'Some error occured';
            }
        } catch (Exception $e) {
            $response['status'] = false;
            Log::error($e->getMessage());
            $response['result'] = $e->getMessage();
        }
        return $response;
    }

    /**
     * Validate input field when to add plan and id is passed only when update plan
     * 
     * @param array $auth
     * @param integer $plan_id
     * @param array $inputs ['connection','vessel_id','origin_id','date_from','type','id']
     * 
     * @return array ['result','status','message']
     */
    public function validatePlanningInputs($inputs)
    {
        $response['status'] = true;
        try {
            $planning = new Plan();
            $planning->setConnection($inputs['connection']);
            if (isset($inputs['vessel_id']) && !empty($inputs['vessel_id'])) {
                $planning = $planning->where('vessel_id', $inputs['vessel_id']);
            }
            if (isset($inputs['origin_id']) && !empty($inputs['origin_id'])) {
                $planning = $planning->where('origin_id', $inputs['origin_id']);
            }
            if (isset($inputs['date_from']) && !empty($inputs['date_from']) && isset($inputs['date_to']) && !empty($inputs['date_to'])) {
                $planning = $planning->whereRaw("((date_from <= '" . $inputs['date_from'] . "' AND date_to >= '" . $inputs['date_to'] . "') OR (date_from BETWEEN '" . $inputs['date_from'] . "' AND '" . $inputs['date_to'] . "') OR (date_to BETWEEN '" . $inputs['date_from'] . "' AND '" . $inputs['date_to'] . "'))");
            }
            if (isset($inputs['type']) && !empty($inputs['type'])) {
                $planning = $planning->where('type', $inputs['type']);
            }
            if (isset($inputs['id']) && !empty($inputs['id'])) {
                $planning = $planning->where('id', '<>', $inputs['id']);
            }
            $planning = $planning->firstOrFail();
            $response['result'] = $planning;
            $response['message'] = 'Record fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        return $response;
    }
}