<?php

namespace App\Repositories\Export;
use App\Models\Export;
use DB;
use Exception;
 class ExportRepository{
 	/*
 	*Description : It will return the Export key  to Service for building the CSV.
 	* Author : Ashish Barick
 	* @param : $allInput = Other necessary data like connection,export_key etc...
 	* Retrun  : @array()
 	*/
 	public function getExportHeaders($allInput)
 	{
 		$response['status'] = true;
 		try{
 			$export = new Export();
			$export->setConnection($allInput['connection']);
			$export->where('export_key',$allInput['export_key'])->first();

			$headers = json_decode($export->excel_column);
			$key = $allInput['export_key'];
			$headerColumn = [];
			$headerColumn[0] = 'Sl No'; // Adding in Serial No in first column of row
			//Header columns store from DB
			foreach($headers->header as $header)
			{
				$headerColumn[] = $header->$key;
			}
			if($headerColumn)
			{
				$response['result'] = $headerColumn;
				$response['message'] = "Export headers fetched";
			}
			else
			{
				$response['status'] = false;
            	$response['message'] = "No Data available for this key!";
			}
 		}catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        unset($export);
        unset($exportData);
        return $response;
 	} 
 	/*
 	*Description : It will return the all Export data to Service for building the CSV.
 	* Author : Ashish Barick
 	* @param : $model =  Base Model coming from DB
 	*		   $dbColumns = List of columns for Select()
 	*          $modelFunction = For JOIN()
 	*		   $allInput = Other necessary data like connection etc...
 	* Retrun  : @array()
 	*/
 	public function getExportDetail($allInput)
 	{
 		$response['status'] = true;
 		try{
 			$export 			= new Export();
			$export->setConnection($allInput['connection']);
			$export->where('export_key',$allInput['export_key'])->first();
			if($export)
			{
				$model 			= $export->model_name;
				$dbColumns 		= $export->db_column;
				$modelFunction 	= $export->model_function;
	 			$exportKey 		= $allInput['export_key'];
	 			$model 			= str_replace(' ', '','App\Models\ '.$model);
	 			$modelObj 		= new $model();
		 		$modelObj->setConnection($allInput['connection']);
		 		$dbColumns 		= json_decode($dbColumns);
		 		$selectColumn 	= [];
				foreach($dbColumns->master as $column)
				{
					$selectColumn[]= $column->$exportKey;
				}
				$modelsData = $modelObj->select($selectColumn);
		 		if($modelFunction)
		 		{
		 			$modelFunList 	= json_decode($modelFunction);	 			
		 			foreach($modelFunList->join as $joins)
		 			{
		 				foreach( $joins as $key=>$value)
		 				{
		 					$joinSplits = explode(',',$value);
		 					$modelsData->join($key,$joinSplits[0],'=',$joinSplits[1]);
		 				}
		 			}
		 		}
		 		$query = $modelsData->get()->toArray();
		 		$i = 1;
		 		foreach($query as &$sub_array) { // & reference
				    array_unshift($sub_array, $i);
				    $i++;
				}
		 		if($query)
		 		{
		 			$response['result'] = $query;
		 		}
		 		else
		 		{
		 			$response['message'] = "No data found for this export key!";
		 			$response['status'] = false;
		 		}
		 	}
		 	else
			{
				$response['status'] = false;
            	$response['message'] = "No Data available for this key!";
			}
 		}catch (Exception $e) {
            Log::error($e->getMessage());
            $response['status'] = false;
            $response['result'] = $e->getMessage();
        }
        unset($export);
        unset($exportData);
        unset($model);
        unset($modelsData);
        unset($query);
        return $response;
 	}
 }