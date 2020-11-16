<?php

namespace App\Http\Requests;
use App\Http\Requests\JsonRequest;

class PlanEditDeleteRequest extends JsonRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->isMethod('get') || $this->isMethod('delete')) {
            return [
                'id' => 'required|integer|gt:0|digits_between:1,15'
            ];
        }
    }

    /**
     * Get the validation messages
     */
    public function messages()
    {
        return [
            'id.required'=>'Plan id is required'
        ];
    }
    
    public function all($keys = null) 
    {
        $data = parent::all($keys);
        $data['id'] = $this->route('id');
        return $data;
    }
}
