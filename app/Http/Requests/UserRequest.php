<?php

namespace App\Http\Requests;

use Response;
use App\User;
use App\Exceptions\DataEmptyException;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $request_name = 'User';
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            case 'POST':
            {
                return [
                    'name'      => 'required',
                    'email'     => 'required|unique:users,email|email',
                    'password'  => 'required'
                ];
            }
            case 'PUT':
            {
                $user = User::find($this->id);
                if ($user == null) {
                    throw new DataEmptyException(__('admin/validation.dataNotExist',['attribute' => $request_name]));
                }
                return [
                    'name'      => 'required',
                    'email'     => 'required|email|unique:users,email,'.$user->id,
                    'password'  => 'required'
                ];
            }
            default:break;
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $response = response()->json([
            'error' => [
                'message'     => 'The given data is invalid',
                'status_code' =>  422,
                'error_code'  => 0,
                'error'       => $validator->errors()
            ]], 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
