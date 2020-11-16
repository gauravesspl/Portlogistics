<?php

namespace App\Http\Requests;

use App\Http\Requests\JsonRequest;
use GuzzleHttp\Psr7\Request;
use Route;


class PlanningTruckRequest extends JsonRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        if ($this->isMethod('post')) {
            return [
                'plan_id' => 'required',
                'trucks' => 'required',
                'trucks.*.truck_id' => 'required',
                'trucks.*.truck_company_id' => 'required',
            ];
        }
        if ($this->isMethod('delete')) {
            return [
                'plan_id' => 'integer|gt:0',
                'truck_id' => 'integer|gt:0'
            ];
        }        
    }

    public function messages() {
        return [
            'plan_id.required' => 'Please provide Plan id',
            //'plan_details.required' => 'Please provide planning details',
            'trucks.*.truck_id.required' => 'Please provide Truck',
            'trucks.*.truck_company_id.required' => 'Please provide Trucking Company',
        ];
    }
}
