<?php

namespace App\Repositories\PlanTruck;

use App\Models\Plan;
use App\Models\Truck;
use App\Models\Challan;
use App\Models\PlanTruck;
use Exception;
use Log;

class PlanTruckRepository
{
    /**
     * Fetch Planned Loaded Trucks by plan_id
     *  
     * @param array $allInput ['connection','plan_id']
     * 
     * @return array $response ['result','status','message']
     * 
     */
    public function getPlannedLoadedTrucksById($allInput)
    {
        $response['status'] = true;
        try{
            $planned_trucks = new PlanTruck();
            $planned_trucks->setConnection($allInput['connection']);
            $planned_trucks = $planned_trucks::with('truck','truck.truckCompany');
            $planned_trucks = $planned_trucks->where('plan_id',$allInput['plan_id']);
            $planned_trucks = $planned_trucks->where('status',1);
            $planned_trucks = $planned_trucks->get();
            $response['result'] = $planned_trucks;
            $response['message'] = 'Planned Truck Records fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = 'Something Went Wrong';
            $response['status'] = false;
            $response['result'] = $e->getMessage();
            return $response;
        }
        return $response;
    }

    /**
     * Fetch Trucks by plan id
     *  
     * @param array $allInput ['connection','plan_id']
     * 
     * @return array $response ['result','status','message']
     * 
     */
    public function getTrucksByPlanningId($allInput)
    {
        $response['status'] = true;
        try{
            $planned_trucks = new PlanTruck();
            $planned_trucks->setConnection($allInput['connection']);
            $planned_trucks = $planned_trucks::with([
                'truck','truck.truckCompany'
                ]);
           
            $planned_trucks = $planned_trucks->where('plan_id',$allInput['plan_id']);
            $planned_trucks = $planned_trucks->get();
            $response['result'] = $planned_trucks;
            $response['message'] = 'Planned Truck Records fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
            return $response;
        }
        return $response;
    }

    /**
     * Update status as 2-unloaded in plan_trucks table(end trip api) 
     * @param array $allInput ['connection','plan_id','truck_id','updated_by']
     * 
     * @param return array $response ['result','status','message']
     */
    public function updateStatusAsUnloaded($allInput) {
        $response['status'] = true;
        try {
            $planned_trucks = new PlanTruck();
            $planned_trucks->setConnection($allInput['connection']);
            $planned_trucks = $planned_trucks->where('plan_id', $allInput['plan_id']);
            $planned_trucks = $planned_trucks->where('truck_id', $allInput['truck_id']);
            $planned_trucks = $planned_trucks->update(['status' => 2, 'updated_by' => $allInput['user_id']]);
            if (!$planned_trucks) {
                $response['status'] = false;
                $response['message'] = 'No Record Found';
            } else {
                $response['message'] = 'Planned Truck Data Updated Successfully';
            }
            return $response;
        } catch (Exception $e) {
            $response['message'] = 'Something Went Wrong';
            $response['status'] = false;
            $response['result'] = $e->getMessage();
            Log::error($e->getMessage());
            return $response;
        }
    }
    
    /**
     * Get Planning details by plan id
     * @param array $allInput ['connection','plan_id']
     * 
     * @param return array $response ['result','status','message']
     */
    public function getPlanningDetailsById($allInput){
        $response['status'] = true;
        try{
            $planning = new Plan();
            $planning->setConnection($allInput['connection']);
            $planning = $planning::with('cargo','vessel','location');
            $planning = $planning->where('id',$allInput['plan_id']);
            $planning = $planning->first();
            $response['result'] = $planning;
            $response['message'] = 'Planning Records fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
            return $response;
        }
        return $response;
    }
    
    /**
     * Get All trucks
     * @param array $allInput ['connection','id']
     * 
     * @param return array $response ['result','status','message']
     */
    public function getAllTrucks($allInput){
        $response['status'] = true;
        try{
            $truck = new Truck();
            $truck->setConnection($allInput['connection']);
            $truck = $truck::with('truckCompany');
            if(isset($allInput['id']) && !empty($allInput['id'])) {
                $truck = $truck->whereNotIn('id',$allInput['id']);
            }
            $truck = $truck->get();
            $response['data'] = $truck;
            $response['message'] = 'Truck data fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['data'] = $e->getMessage();
            return $response;
        }
        return $response;
    }
    
    /**
     * Save truck for plan
     * @param array $allInput ['connection','created_by','plan_id','trucks'=>['id','truck_id','truck_company_id']]
     * 
     * @param return array $response ['result','status','message']
     */
    public function saveTruckForBtopPlan($allInput){
        $fetch['status'] = true;
        try{ 
            foreach($allInput['trucks'] as $key => $truckDetails){
            $planned_trucks = new PlanTruck();
            $planned_trucks->setConnection($allInput['connection']);
            $trucks = new Truck();
            $trucks->setConnection($allInput['connection']);
               if($truckDetails['id'] == ''){
                   if(!is_numeric($truckDetails['truck_id'])){
                    $trucks['truck_no'] =  strtolower($truckDetails['truck_id']); 
                    $trucks['truck_company_id'] =  $truckDetails['truck_company_id'];
                    $trucks['is_active'] =  1;
                    $trucks['created_by'] = $allInput['created_by'];
                    $trucks->save();
                    $planned_trucks['truck_id'] = $trucks->id;
                   }else{
                    $planned_trucks['truck_id'] = $truckDetails['truck_id'];
                   }
                    $planned_trucks['plan_id'] = $allInput['plan_id'];                    
                    $planned_trucks['status'] = 2;
                    $planned_trucks['created_by'] = $allInput['created_by'];
                    $planned_trucks->save();
                }                
            } 
            if($planned_trucks == null)
            {
                $fetch['status'] = false; 
                $fetch['message'] = 'Error in saving truck data';
            }
            else{
                 $fetch['message'] = 'Planning truck data saved successfully';
            }
            return $fetch;

        }catch(Exception $e){
            $fetch['status'] = false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            return $fetch;
        } 
    }
    
    /**
     * delete trucks for plan
     * @param array $allInput ['connection','id','plan_id']
     * 
     * @param return array $fetch ['result','status','message']
     */
    public function deleteTruckForBtopPlan($allInput){
        $fetch['status'] =true;
            
        try{            
            $challan = new Challan();
            $challan->setConnection($allInput['connection']);
            $challan = $challan->where('truck_id',$allInput['id']);
            $challan = $challan->where('plan_id',$allInput['plan_id']);
            $challan = $challan->count();
            
            if($challan == 0){
                $planned_trucks = new PlanTruck(); 
                $planned_trucks->setConnection($allInput['connection']);
                $planned_trucks = $planned_trucks->where('truck_id',$allInput['id']);
                $planned_trucks = $planned_trucks->where('plan_id',$allInput['plan_id']);
                $planned_trucks = $planned_trucks->first();
                if(!empty($planned_trucks)){
                    $planned_trucks->delete();
                    $fetch['message'] ='Planning Truck deleted successfully';
                }else{
                    $fetch['status'] = false;
                    $fetch['message'] ='No Resource found!'; 
                }
            }else{
                 $fetch['status'] = false;
                 $fetch['message'] ='Challan has been generated for selected.It can`t delete';
            }
            return $fetch;
        }catch(Exception $e){
            $fetch['status'] =false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            return $fetch;
        }
    }
    
    /**
     * Update status as 1-loaded in btop_planned_truck table(create challan api) 
     * @param array $inputs
     */
    public function updateStatusAsLoaded($inputs) {
        $response['status'] = true;
        try {
            $planned_trucks = new PlanTruck();
            $planned_trucks->setConnection($inputs['connection']);
            $planned_trucks = $planned_trucks->where('plan_id', $inputs['plan_id']);
            $planned_trucks = $planned_trucks->where('truck_id', $inputs['truck_id']);
            $planned_trucks = $planned_trucks->update(['status' => 1, 'updated_by' => $inputs['user_id']]);
            if (!$planned_trucks) {
                $response['status'] = false;
            } else {
                $response['message'] = 'Planned Truck Data Updated Successfully';
            }
            return $response;
        } catch (Exception $e) {
            $response['status'] = false;
            $response['result'] = $e->getMessage();
            Log::error($e->getMessage());
            return $response;
        }
    }

}