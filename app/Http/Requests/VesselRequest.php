<?php

namespace App\Http\Requests;

use App\Http\Requests\JsonRequest;

class VesselRequest extends JsonRequest
{

    protected $nullable_numeric;
    public function __construct()
    {
        $this->nullable_numeric = 'nullable|numeric';
    }
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
        if ($this->isMethod('post')) {
            return [
                'name'          => 'required|max:50|unique:vessels,name,' . $this->id,
                'description'   => 'nullable|max:150|',
                'loa'           => $this->nullable_numeric,
                'beam'          => $this->nullable_numeric,
                'draft'         => $this->nullable_numeric,
            ];
        }
        if ($this->isMethod('delete') || $this->isMethod('get')) {
            return [
                'id' => 'integer|gt:0',
                'key'=>'nullable|string'
            ];
        }
        if ($this->isMethod('put')) {
            return [
                'id' => 'integer|gt:0',
                'name'          => 'required|max:50|unique:vessels,name,' . $this->id,
                'description'   => 'nullable|max:150|',
                'loa'           => $this->nullable_numeric,
                'beam'          => $this->nullable_numeric,
                'draft'         => $this->nullable_numeric
            ];
        }
    }

    public function all($keys = null)
    {
        $data = parent::all();
        return array_merge($data, $this->route()->parameters());
    }
    public function attributes()
    {
        return [
            'id' => 'url key',
            'key'=>'keyword'
        ];
    }

    public function messages()
    {

        return [
            'name'          => 'Vessel name is required',
            'name.unique'   => 'Vessel name should be unique',
            'description'   => 'Description is Alphanumeric',
            'loa'           => 'LOA is decimal format',
            'beam'          => 'Beam is decimal format',
            'draft'         => 'Draft is decimal format',
        ];
    }
}
