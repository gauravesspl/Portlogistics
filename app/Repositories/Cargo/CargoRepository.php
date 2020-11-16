<?php

namespace App\Repositories\Cargo;
use App\Libraries\CustomExceptionLibrary;
use App\Models\Cargo;
use Exception;
use Log;

Class CargoRepository {

    /**
     * @description Get all cargos
     * @author
     * @param array ['user_id','user_auth','browser','connection','cache_array','view'] 
     * @return array ['status','message','cargo']
     */
    public $catchErrorMsg = 'Something Went Wrong';
    public $noRecordMsg = 'No Record Found';
    public function getAllCargo($allInput) {
        global $catchErrorMsg;
        $fetch['status'] = true;
        try{
        $cargos = new Cargo();
        $cargos->setConnection($allInput['connection']);
        $cargos = $cargos->select('*');
        $cargos = $cargos->get();
        $fetch['message'] ='Cargo fetched successfully';
        $fetch['cargo'] =$cargos;
        }catch(Exception $e){
            Log::error($e->getMessage());
            $fetch['status'] = false;
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($cargos);
        return $fetch;
    }
    
    /**
     * @description Save Cargo
     * @author
     * @param array $param ['name','instruction','user_array','created_by','connection'] 
     * @return array ['status','message']
     */
    public function saveCargo($allInput){
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try{   
            $cargos = new Cargo();
            $cargos->setConnection($allInput['connection']);
            $cargoSave = $cargos->create($allInput);
            if(!$cargoSave)
            {
                $fetch['status'] =false;
                $fetch['message'] = $noRecordMsg;                  
            }
            else{
                 $fetch['message'] = 'Cargo data saved successfully';
            }

        }catch(Exception $e){
            $fetch['status'] =false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($cargos);
        unset($cargoSave);
        return $fetch;        
    }
    
     /**
     * @description Delete Cargo details by cargo id
     * @author
     * @param array ['id',user_array','user_id','connection'] 
     * @return array ['status','message','result']
     */
    public function deleteCargo($allInput){
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] =true;
        $fetch['result']=[];   
        try{
            $cargos = new Cargo();
            $cargos->setConnection($allInput['connection']);
            $cargo = $cargos->findOrFail($allInput['id']);
            $cargo = $cargo->delete();
            if(!$cargo)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                $fetch['message'] ='Cargo deleted successfully';    
            }
            unset($cargos);
            unset($cargo);
            return $fetch;
        }catch(Exception $e){
            Log::error($e->getMessage());
            $errors = new CustomExceptionLibrary();
           return $errors->handleException($e, 'Cargo');
        }
    }
    
    /**
     * @description Get Cargo details by cargo id
     * @author
     * @param array ['id',user_array','user_id','connection'] 
     * @return array ['status','message','result']
     */
    public function editCargo($allInput){
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        $fetch['result']=[];
        try{
            $cargoData = new Cargo();
            $cargoData->setConnection($allInput['connection']);
            $cargoData = $cargoData->where('id',$allInput['id']);
            $cargoData = $cargoData->firstOrFail();
            if(!$cargoData)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                 $fetch['message'] = 'Cargo Edited Successfully';    
                 $fetch['result'] =  $cargoData->toArray(); 
            }
        }catch(Exception $e){
            $fetch['status'] =false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($cargoData);
        return $fetch;        
    }
    
    /**
     * @description Update Cargo
     * @author
     * @param array $param ['id','name','instruction','user_array','updated_by','connection'] 
     * @return array ['status','message']
     */
    public function updateCargo($allInput){
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] =true;
        $fetch['result']=[];
        try{
            $cargoUpdate = new Cargo();
            $cargoUpdate->setConnection($allInput['connection']);
            $cargoUpdate = $cargoUpdate->where('id', $allInput['id']);
            $cargoUpdate = $cargoUpdate->update(['name' => $allInput['name'],'instruction' => $allInput['instruction'],'updated_by' =>$allInput['updated_by']]);
            if(!$cargoUpdate)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                $fetch['message'] = 'Cargo data updated successfully';
            }
        }catch(Exception $e){
            $fetch['status'] =false;
            $fetch['result'] = $e->getMessage();
            Log::error($e->getMessage());
            $fetch['message'] = $catchErrorMsg;
        }
        unset($cargoUpdate);
        return $fetch;
    }        
}
