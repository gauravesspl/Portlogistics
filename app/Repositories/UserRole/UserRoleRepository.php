<?php

namespace App\Repositories\UserRole;

use App\Models\UserRole;
use App\Models\User;
use App\Models\AccessList;
use Exception;
use Log;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use App\Libraries\CustomExceptionLibrary;
use DB;

class UserRoleRepository
{
    public $catchErrorMsg = 'Something Went Wrong';
    public $noRecordMsg = 'No Record Found';
    /**
     * @description Get all user roles
     * @author
     * @param array ['user_id','user_auth','browser','connection','cache_array'] 
     * @return array ['status','message','result']
     */
    public function getRoles($inputs)
    {
        global $catchErrorMsg;
        $response['status'] = true;
        try {
            $role = new UserRole();
            $role->setConnection($inputs['connection']);
            if (isset($inputs['id']) && !empty($inputs['id'])) {
                $role = $role->where('id', $inputs['id']);
            }
            $role = $role->get();
            $response['result'] = $role;
            $response['message'] = 'Records fetched successfully';
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($role);
        return $response;
    }



    /**
     * Author : Ashish Barick
     * 
     * 
     */
    public function getRolesForPriviliges($allInput)
    {
        global $catchErrorMsg;
        $fetch['status'] = true;
        try {
            $roles = new UserRole();
            $roles->setConnection($allInput['connection']);
            $roles = $roles->select('*');
            $roles = $roles->get();
            $fetch['message'] = 'Role fetched successfully';
            $fetch['role'] = $roles;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $fetch['status'] = false;
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($roles);
        return $fetch;
    }

    /*
    * Author : Ashish Barick
    */

    public function store($allInput)
    {
        global $catchErrorMsg;
        $fetch['status'] = true;
        $roles = new UserRole();

        try {
            $roles->setConnection($allInput['connection']);
            $roleSave = $roles->create($allInput);
            if (!$roleSave) {
                $fetch['status'] = false;
            } else {
                $fetch['message'] = 'Role data saved successfully';
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();    
            $fetch['message'] = $catchErrorMsg;
        }
        unset($roles);
        return $fetch;
    }

    /*
    * Author : Ashish Barick
    */

    public function update($allInput)
    {
        global $catchErrorMsg;
        $fetch['status'] = true;
        $roleUpdate = new UserRole();

        try {

            $roleUpdate->setConnection($allInput['connection']);
            $roleUpdate = $roleUpdate->where('id', $allInput['id']);
            $roleUpdate->update([
                'name'              => $allInput['name'],
                'updated_by'        => $allInput['updated_by']
            ]);
            if (!$roleUpdate) {
                $fetch['status'] = false;
            } else {
                $fetch['message'] = 'Role data updated successfully';
            }
        } catch (Exception $e) {
            $fetch['status'] =false;
            $fetch['result'] = $e->getMessage();         
            $fetch['message'] = $catchErrorMsg;
            Log::error($e->getMessage());
        }
        return $fetch;
    }

    /*
    * Author : Ashish Barick
    */

    public function destroy($allInput)
    {
        global $noRecordMsg;
        $fetch['status'] = true;
        $user = new User();
        $user->setConnection($allInput['connection']);
        try 
        {
            $roles = new UserRole();
            $roles->setConnection($allInput['connection']);
            $role = $roles->findOrFail($allInput['id']);
            $role = $role->delete();
            if(!$role)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                $fetch['message'] ='Role deleted successfully';    
            }
        }catch(Exception $e){
            Log::error($e->getMessage());
            $errors = new CustomExceptionLibrary();
            $fetch = $errors->handleException($e, 'Role');
        }
        unset($user);
        unset($role);
        return $fetch;
    }

    /*
    * Author : Ashish Barick
    */

    public function edit($allInput)
    {
        global $catchErrorMsg;
        global $noRecordMsg;
        $fetch['status'] = true;
        try {
            $roleData = new UserRole();
            $roleData->setConnection($allInput['connection']);
            $roleData = $roleData->where('id',$allInput['id']);
            $roleData = $roleData->firstOrFail();
            if(!$roleData)
            {
                $fetch['status'] = false;
                $fetch['message'] = $noRecordMsg;
            }
            else{
                 $fetch['message'] = 'Role data edited successfully';    
                 $fetch['result'] =  $roleData->toArray(); 
            }
        } catch (Exception $e) {
            $fetch['status'] = false;
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($roleData);
        return $fetch;
    }

    /**
     *Author : Ashish Barick 
     *Function :  Get List of Menues and its Privileges
     * Updated_at : 23/09/2020
     * 
     */
    public function getAllMenuPrivilegesList($allInput)
    {
        global $catchErrorMsg;
        $fetch['status'] = true;
        try {
            $menu = new AccessList();
            $menu->setConnection($allInput['connection']);
            $menu = $menu->where('parent_id', 0)->with('children.subChild')->get(); // Realtionship binded with Parent Menu

            $fetch['message'] = 'Privileges fetched successfully';
            $fetch['menu'] = $menu;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $fetch['status'] = false;
            $fetch['result'] = $e->getMessage();
            $fetch['message'] = $catchErrorMsg;
        }
        unset($menu);
        return $fetch;
    }
}
