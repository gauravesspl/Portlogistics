<?php
namespace App\Http\Requests;
use App\Http\Requests\JsonRequest;
class OrganizationNameRequest extends JsonRequest {
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
        /**
        * @author : Ashish Barick
        * @description: This two condition because we are handle Org_details and  org_rate in one API. THat's why added handler @$this->input('org_type')
        **/
        $msgArr = [];
        $numberRules = 'required|regex:/[0-9]+$/|numeric|digits_between:1,20';
        if($this->input('org_type') == 'org_info') // Organization Information
        {
             $msgArr = [
                'name' => 'required|max:60',
                'mobile_no' => $numberRules,
                'address' => 'required',
                'primary_contact' => 'required|max:40',
                'primary_mobile_no' => $numberRules,
                'primary_email' => 'required|email|max:60',
                'secondary_contact' => 'nullable|max:40',
                'secondary_mobile_no' => 'nullable|numeric|digits_between:1,20',
                'secondary_email' => 'nullable|email|max:60',
            ];
            if ($this->isMethod('put')) {
                $msgArr = [
                    'id' => 'integer|gt:0',
                    'name' => 'required|max:60',
                    'mobile_no' => $numberRules,
                    'address' => 'required',
                    'primary_contact' => 'required|max:40',
                    'primary_mobile_no' => $numberRules,
                    'primary_email' => 'required|email|max:60',
                    'secondary_contact' => 'nullable|max:40',
                    'secondary_mobile_no' => 'nullable|numeric|digits_between:1,20',
                    'secondary_email' => 'nullable|email|max:60',
                ];
            }
        }
        if($this->input('org_type') == 'org_rate') // Organization Rate
        {
            $msgArr = [
                'currency_id' => 'required',
                'rate_per_trip' => 'required|numeric',
            ];
            if ($this->isMethod('put')) {
                $msgArr = [
                    'id' => 'integer|gt:0',
                    'currency_id' => 'required',
                    'rate_per_trip' => 'required|numeric',
                ];
            }
        }
        return  $msgArr;
    }
    public function all($keys = null)
    {
        $data = parent::all();
        return  array_merge($data, $this->route()->parameters());
    }

    public function messages() {
        $msgArr = [];
        if($this->input('org_type') == 'org_info') // Organization Information
        {
            $msgArr = [
                'name.required' => 'Please enter  Organization Name',
                'name.max' => 'Organization Name length should not exceed 20 characters',
                'mobile_no.required' => 'Please enter phone number',
                'mobile_no.regex' => 'Phone number is not in proper format',
                'mobile_no.max' => 'Phone number length should not exceed 20 characters',
                'address.required' => 'Please enter address',
                'primary_contact.required' => 'Please enter primary contact name',
                'primary_contact.max' => 'Primary Contact Name length cannot exceed 40',
                'primary_mobile_no.required' => 'Please enter primary phone number',
                'primary_email.required' => 'Please enter primary email',
                'primary_email.max' => 'Primary email cannot exceed 60 charcter',
            ];
        }
        if($this->input('org_type') == 'org_rate') // Organization Rate
        {
            $msgArr = [
                'currency_id.required' => 'Please provide currency_id',
                'rate_per_trip.required' => 'Please provide rate_per_trip',
                'rate_per_trip.numeric' => 'Please provide deecimal value',
            ];
        }
        return $msgArr;
    }
}