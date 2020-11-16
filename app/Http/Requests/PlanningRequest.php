<?php

namespace App\Http\Requests;

use App\Http\Requests\JsonRequest;

class PlanningRequest extends JsonRequest {

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
        if($this->isMethod('put')) {
            return [
                'id' => 'required|integer|gt:0|digits_between:1,15',
                'vessel_name' => 'required|max:50',
                'origin_id' => 'required',
                'date_from' => 'required',
                'date_to' => 'required',
                'cargo_id' => 'required',
                //'type'=>'required|integer|gt:0|min:1|max:2',
                'plan_details' => 'required',
                'plan_details.*.consignee_id' => 'required',
                //'plan_details.*.destination_id' => 'required'
            ];
        } else {
            return [
                'vessel_name' => 'required|max:50',
                'origin_id' => 'required',
                'date_from' => 'required',
                'date_to' => 'required',
                'cargo_id' => 'required',
                //'type'=>'required|integer|min:1|max:2',
                'plan_details' => 'required',
                'plan_details.*.consignee_id' => 'required',
                //'plan_details.*.destination_id' => 'required'
            ];
        }
    }

    public function messages() {
        if($this->isMethod('put')) {
            return [
                'id.required' => 'Plan id is required',
                'vessel_name.required' => 'Please provide vessel name',
                'vessel_name.max' => 'Vessel name should not exceed 50 characters',
                'origin_id.required' => 'Please provide berth',
                'date_from.required' => 'Please provide from date',
                'date_to.required' => 'Please provide to date',
                'cargo_id.required' => 'Please provide cargo',
                //'type.required'=>'Plan type required',
                'plan_details.required' => 'Please provide planning details',
                'plan_details.*.consignee_id.required' => 'Please provide customer',
                //'plan_details.*.destination_id.required' => 'Please provide plot'
            ];
        } else {
            return [
                'vessel_name.required' => 'Please provide vessel name',
                'vessel_name.max' => 'Vessel name should not exceed 50 characters',
                'origin_id.required' => 'Please provide berth',
                'date_from.required' => 'Please provide from date',
                'date_to.required' => 'Please provide to date',
                'cargo_id.required' => 'Please provide cargo',
                //'type.required'=>'Plan type required',
                'plan_details.required' => 'Please provide planning details',
                'plan_details.*.consignee_id.required' => 'Please provide customer',
                //'plan_details.*.destination_id.required' => 'Please provide plot'
            ];
        }
    }
    
    public function all($keys = null) {
        $data = parent::all($keys);
        $data['id'] = $this->route('id');
        return $data;
    }

}
