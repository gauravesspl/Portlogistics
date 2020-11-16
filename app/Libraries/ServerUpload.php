<?php

namespace App\Libraries;
use Exception;
class ServerUpload {

	/*
	* Author : Ashish Barick
	* @param : generic Image Upload function based on upload type like LOCAL
	* created_at : 10/09/2020
	*/
	public function upload($file,$filepath,$filename)
	{
		$fetch['status'] = true;
		try{
			if($filename != '' && $filepath != '')
			{
				$upload = $file->move($filepath, $filename);
				if($upload)
				{
					$fetch['status'] = true;
					$fetch['message'] = 'File uploaded successfully';
				}
				else
				{
					$fetch['status'] = false;
					$fetch['message'] = 'Unable to upload file!';
				}
			}
			else
			{
				$fetch['status'] = false;
				$fetch['message'] = 'Required filename or filepath not found!';
			}	
			return $fetch;
	    }catch (Exception $e) {
            Log::error($e->getMessage());
            $fetch['result'] = $e->getMessage();
            $fetch['status'] = false;
            $fetch['message'] = 'Something Went Wrong';
            return $fetch;
        }
	}
}