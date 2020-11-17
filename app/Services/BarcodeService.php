<?php

namespace App\Services;

use Milon\Barcode\DNS1D;
use Illuminate\Support\Facades\Storage;

class BarcodeService {
    
    public function generate($challan, $auth) {
        $response['status'] = true;
        try {
            $imgPath = '';
            $DNS1D = new DNS1D();
            $directory = 'org_'.$auth['user_auth']->organization_id . '/plan_'.$challan['plan_id'].'_'.$challan['type'].'/barcode/';
            Storage::makeDirectory($directory, '0777', true, true);
            $barcodeImg = $DNS1D->getBarcodePNG($challan['challan_no'], config('barcode.format'));
            $imgPath = $directory . $challan['challan_no'] . '.png';
            Storage::put($imgPath, base64_decode($barcodeImg));
            $response['barcode_path'] = 'storage/' . $imgPath;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $response['message'] = $e->getMessage();
            $response['status'] = false;
        }
        return $response;
    }
    
}

