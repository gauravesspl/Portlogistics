<?php

namespace App\Repositories\Dashboard;

use Illuminate\Http\Request;
use App\Models\BtopPlanning;
use App\Models\BtopPlanningDetail;
use App\Models\Challan;
use App\Models\Shift;
use Exception;
use Log;
use DB;
use Carbon\Carbon;

Class DashboardRepository
{
	public function getDetails($allInput){
		/*For BTP details
		@author: Saibal Chakravarty
		@descripyion: Details to fetch from 
		@param array ['connection','date','vessel','cargo','customer'] 
		*/
		try {
		$dataArr = array();
		$dataArr['status'] = true;

		$challans = new Challan();
		$challans = $challans->leftJoin('btop_plannings','challans.btop_planning_id','btop_plannings.id')
			->where('challans.status', 2);
		if (array_key_exists('vessel', $allInput) && $allInput['vessel'] != 'all') {
			$challans = $challans->whereIn('btop_plannings.vessel_id',$allInput['vessel']);
		}
		
		if (array_key_exists('customer', $allInput) && $allInput['customer'] != 'all') {
			$challans = $challans->whereIn('challans.consignee_id',$allInput['customer']);
		}
		if (array_key_exists('cargo', $allInput) && $allInput['cargo'] != 'all') {
			$challans = $challans->whereIn('challans.cargo_id',$allInput['cargo']);
		}
		if (array_key_exists('date', $allInput) && $allInput['date']!=null) {
			$date =	Carbon::parse($allInput['date'])->format('Y-m-d');
			$challans = $challans->where(function ($query) use($date) {
				$query->whereDate('challans.loaded_at',$date)
				->orWhereDate('challans.unloaded_at',$date);
			});
		}
		
		 $c = $challans;

  		$shifts = Shift::get();


		foreach ($shifts as $shift) {
			$c =clone $challans;
		$dataArr['ttc'][$shift->type] = $c->where('shift_id', $shift->id)
					->whereNotNull('unloaded_by')
					->where('type', 1)
					->count();
					$d =clone $challans;
				$dataArr['ntu'][$shift->type] = $d->where('shift_id', $shift->id)
					->whereNotNull('unloaded_by')
					->where('type', 1)
					->distinct('truck_id')->count();
					$e =clone $challans;
				$dataArr['ca'][$shift->type] = $e->where('shift_id', $shift->id)
					->where('is_deposit', 1)
					->where('type', 1)
					->count();
		}
		
	} catch (Exception $e) {
		Log::error($e->getMessage());
		$dataArr['status'] = false;
		$dataArr['result'] = $e->getMessage();
		$dataArr['message'] = 'Something Went Wrong';
		return $dataArr;
	}
		return $dataArr;
	}
}
