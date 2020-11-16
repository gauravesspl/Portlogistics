<?php
namespace App\Repositories\Location;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Libraries\CustomExceptionLibrary;
use Exception;
use Log;
class LocationRepository
{

    public $catchErrorMsg = 'Something Went Wrong';
    public $noRecordMsg = 'No Record Found';
    /**
     * @description Get all location details
     * @author
     * @param array ['user_id','user_auth','browser','connection','cache_array','view'] 
     * @return array ['status','message','location']
     */
    public function getAllLocation($allInput)
    {
        global $catchErrorMsg;
        $fetch['status'] = true;

        try {
            $locations = new Location();
            $locations->setConnection($allInput['connection']);
            $locations = $locations->select('*');
            if (isset($allInput['type']) && !empty($allInput['type'])) {
                $locations = $locations->where('type', $allInput['type']);
            }
            $locations = $locations->get();
            $fetch['message'] = 'Location fetched successfully';
            $fetch['location'] = $locations;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $fetch['status'] = false;
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($locations);
        return $fetch;
    }

    /**
     * @description Save location
     * @author
     * @param array $param ['location','description','type','user_array','created_by','connection'] 
     * @return array ['status','message']
     */
    public function store($allInput)
    {
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try {
            $locations = new Location();
            $locations->setConnection($allInput['connection']);
            $locationSave = $locations->create($allInput);
            if(!$locationSave)
            {
                $fetch['status'] =false;
                $fetch['message'] = $noRecordMsg;                  
            }
            else{
                $fetch['message'] = 'Location data saved successfully';
            }
        } catch (Exception $e) {
            $fetch['status'] = false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($locations);
        unset($locationSave);
        return $fetch;
    }

    /**
     * @description Update location
     * @author
     * @param array $param ['id','location','description','type','user_array','updated_by','connection'] 
     * @return array ['status','message']
     */
    public function update($allInput)
    {
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try {
            $locationUpdate = new Location();
            $locationUpdate->setConnection($allInput['connection']);
            $locationUpdate = $locationUpdate->where('id', $allInput['id']);
            $locationUpdate = $locationUpdate->update(['location' => $allInput['location'], 'description' => $allInput['description'], 'type' => $allInput['type'], 'updated_by' => $allInput['updated_by']]);
            if (!$locationUpdate) {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                $fetch['message'] = 'Location data updated successfully';
            }
        } catch (Exception $e) {
            $fetch['status'] = false;
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
            Log::error($e->getMessage());
        }
        unset($locationUpdate);
        return $fetch;
    }

    /**
     * @description Delete location details by location id
     * @author
     * @param array ['id',user_array','user_id','connection'] 
     * @return array ['status','message','result']
     */
    public function destroy($allInput)
    {
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try {
            $locations = new Location();
            $locations->setConnection($allInput['connection']);
            $location = $locations->findOrFail($allInput['id']);
            $location = $location->delete();
            if (!$location) {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                $fetch['message'] ='Location deleted successfully';    
            }
        }catch(Exception $e){
            Log::error($e->getMessage());
            $errors = new CustomExceptionLibrary();
            $fetch = $errors->handleException($e,'Location');
        }
        unset($locations);
        return $fetch;
    }

    /**
     * @description Get location details by location id
     * @author
     * @param array ['id',user_array','user_id','connection'] 
     * @return array ['status','message','result']
     */
    public function edit($allInput)
    {
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try {
            $locationData = new Location();
            $locationData->setConnection($allInput['connection']);
            $locationData = $locationData->where('id',$allInput['id']);
            $locationData = $locationData->firstOrFail();
            if(!$locationData)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                 $fetch['message'] = 'Location Edited Successfully';    
                 $fetch['result'] =  $locationData->toArray(); 
            }
        } catch (Exception $e) {
            $fetch['status'] = false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($locationData);
        return $fetch;
    }
    /**
     * Fetch Location by location_id
     * 
     */
    public function getLocationById($allInput, $type)
    {
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try {
            $locationData = new Location();
            $locationData->setConnection($allInput['connection']);
            if(isset($allInput['location_id']) && !empty($allInput['location_id'])) {
                $locationData = $locationData->where('id',$allInput['location_id']);
            } else if(isset($allInput['destination_id']) && !empty($allInput['destination_id'])) {
                $locationData = $locationData->where('id',$allInput['destination_id']);
            } else if(isset($allInput['origin_id']) && !empty($allInput['origin_id'])) {
                $locationData = $locationData->where('id',$allInput['origin_id']);
            }
            $locationData = $locationData->where('type',$type);
            $locationData = $locationData->firstOrFail();
            if(!$locationData)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                 $fetch['message'] = 'Location Data Fetched Successfully';    
                 $fetch['result'] =  $locationData->toArray(); 
            }
        } catch (Exception $e) {
            $fetch['status'] = false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($locationData);
        return $fetch;
    }
}
