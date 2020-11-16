<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Log;

abstract class JsonRequest extends FormRequest
{
    protected function failedValidation(Validator $validator){

    	$response = [           
        'status' => 'failed',
        'message' => 'Validatoin Failed',
        ];
        $status_code = 429;
        $response['status_code'] = $status_code;
        $response['result'] = $validator->errors();
        throw new HttpResponseException(response()->json($response, 200));
    }
}
