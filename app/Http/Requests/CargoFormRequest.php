<?php
namespace App\Http\Requests;
use App\Http\Requests\JsonRequest;
use GuzzleHttp\Psr7\Request;
use Route;
class CargoFormRequest extends JsonRequest
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
        if ($this->isMethod('post')) {
            return [
                'name' => 'required|max:30|unique:cargos,name,' . $this->id,
                'instruction' => 'nullable|max:150',
            ];
        }
        if ($this->isMethod('delete') || $this->isMethod('get')) {
            return [
                'id' => 'integer|gt:0'
            ];
        }
        if ($this->isMethod('put')) {
            return [
                'id' => 'integer|gt:0',
                'name' => 'required|max:30|unique:cargos,name,' . $this->id,
                'instruction' => 'nullable|max:150',
            ];
        }
    }

    public function all($keys = null)
    {
        $data = parent::all();
        return  array_merge($data, $this->route()->parameters());
    }
    public function messages()
    {
        return [
            'name.required' => 'Cargo name is required',
            'name.unique' => 'Cargo name should be unique',
        ];
    }
    public function attributes()
    {
        return [
            'id' => 'url key',
        ];
    }
}
