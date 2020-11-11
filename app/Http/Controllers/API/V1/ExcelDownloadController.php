<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExcelOrCsvExportService;
use App\Repositories\Export\ExportRepository;
use App\Http\Requests\ExportRequest;
use Config;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExcelExport;
class ExcelDownloadController extends BaseController
{
    protected $exportRepository;

    public function __construct(ExportRepository $exportRepository){
        $this->exportRepository = $exportRepository;
    }
    /*
    * Description : To download the csv export
    * Author : Ashish Barick
    * @param : $request-> @key(export key), method->post
    * @return :$url as Response
    */
    public function csvExport(ExportRequest $request)
    {
    	$allInput = $request->all();
    	$param  = $this->getAuth( $allInput);
        $allInput['connection'] = $param['connection'];
        $allInput['export_key'] = $request->key;
        $allInput['organization_id']    = $param['user_auth']['organization_id'];
        $configFilename = Config::get('export.filename');
        $configFilePath = Config::get('export.path');
        $fileName       = $configFilename.$allInput['export_key'].'DataExport_'.date('d-m-Y').'.xlsx';
		$path           = $configFilePath.$allInput['organization_id'].'/'.$allInput['export_key'].'/';
    	$excelheader =  $this->exportRepository->getExportHeaders($allInput);
        $excelRowData = $this->exportRepository->getExportDetail($allInput);
        //dd($excelRowData);
    	/*if($response){
            return $response;
        }*/
        // $data = ;
        // dd($data);
        return Excel::download(new ExcelOrCsvExportService($excelRowData,$excelheader,$allInput), $fileName);
    }
}
