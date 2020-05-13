<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->service = new UserService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => [
                    'message'     => 'The given data is invalid',
                    'status_code' =>  422,
                    'error_code'  => 0,
                    'error'       => $validator->errors()
                ]], 422);
        }

        $data = $this->service->login($request->all());
        return $data;
    }

    public function signup(UserRequest $request)
    {
        $data = $this->service->create($request->all());
        return $data;
    }
}
