<?php
namespace App\Services;
use Auth;
use Log;
use App\Libraries\ServerUpload;
use App\Repositories\User\UserRepository;
use Storage;
use Exception;
class ImageUploadService{
	/*
	* Description : Service function for profile image upload. Here Handle the functionality part
	* @author : Ashish Barick
	* @param : @allInput : consists of parameter like Image,user_id,organization_id etc..
				@path : path in the storage file for profile image.
				@fileName : desired filename of profile image.
				@filePath : Destination path to upload
	* @created_at : 10/09/2020
	* @updated_by : 04/11/2020
	* @return : array
	*/
	protected $serverUpload;
	public function __construct(UserRepository $userRepository, ServerUpload $serverUpload) {
        $this->serverUpload = $serverUpload;
        $this->userRepository = $userRepository;
    }
	public function customImageUpload($allInput)
	{
		$fetch['status'] = true;
		$imageDetail = Config::get('filesystems.image_upload');
		$staticStoragePath = $imageDetail['storage_path'];
		try{
			if($allInput['image_upload']['upload_directory'] == 'local') // For Local upload
			{
				if ($allInput['image']) {
					$path = 'org_'.$allInput['organization_id'].'/user_'. $allInput['user_id'].'/profile/';
					if(!Storage::exists($path)) {
						Storage::makeDirectory($path); 
					}
		           	
		            $fileName =  $allInput['user_id']."_".time().'.'.$allInput['image']->getClientOriginalExtension();
		            $filePath = storage_path($staticStoragePath.$path);
		            if(isset($allInput['last_image']))
		            {
		            	if(file_exists(storage_path($staticStoragePath.$allInput['last_image']))){
					        unlink(storage_path($staticStoragePath.$allInput['last_image']));
					    }
		            }
		           	$uploadResponse = $this->serverUpload->upload($allInput['image'], $filePath,$fileName); // Sending to upload custom library function for Upload
		            if($uploadResponse['status'] )
					{
						$repoData = ["image_path" =>$path.$fileName,"user_id" =>$allInput['user_id'],"connection" =>$allInput['connection']];
						$repoResponse =  $this->userRepository->saveProfileImage($repoData);
						if($repoResponse['status'] )
						{
							$fetch['message'] = 'Profile image uploaded successfully';
						}
						else
						{
							$fetch['message'] = $repoResponse['message'];
							$fetch['status'] = false;
						}
					}
					else
					{
						$fetch['message'] = 'Problem while uploading Profile Image!';
						$fetch['status'] = false;
					}
		        }
		        else
		        {
		        	$fetch['status'] = false;
		        	$fetch['message'] = 'Image file not Added';
		        }
		        return $fetch;
		    }
		}catch (Exception $e) {
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['status'] = false;
            $fetch['message'] = 'Something Went Wrong';
            return $fetch;
        }

	}
}