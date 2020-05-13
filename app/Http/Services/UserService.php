<?php

namespace App\Http\Services;

use App\Exceptions\AuthenticationException;
use App\Http\Repositories\UserRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct()
    {
        $this->repository = new UserRepository;
    }

    public function create($data)
    {
        $data = Arr::only($data,[
            'name',
            'email',
            'password'
        ]);
        $result = $this->repository->create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => bcrypt($data['password'])
        ]);

        $http = new Client;
        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type'    => 'password',
                'client_id'     => env('API_CLIENT_ID'),
                'client_secret' => env('API_CLIENT_SECRET'),
                'username'      => $data['email'],
                'password'      => $data['password'],
                'scope'         => '',
            ]
        ]);
        $dataAuth = json_decode((string)$response->getBody(), true);
        $dataAuth['user'] = $result;
        return response([
            'data' => $dataAuth
        ]);
    }

    public function login($data)
    {
        $data = Arr::only($data,[
            'email',
            'password'
        ]);

        $user = \App\User::where('email',$data['email'])->first();
        if (!$user) {
            throw new AuthenticationException(__('admin/error.user_not_found'), 401);
        } else {
            if (Hash::check($data['password'], $user->password)) {
                $http = new Client;
                $response = $http->post(url('oauth/token'), [
                    'form_params' => [
                        'grant_type'    => 'password',
                        'client_id'     => env('API_CLIENT_ID'),
                        'client_secret' => env('API_CLIENT_SECRET'),
                        'username'      => $data['email'],
                        'password'      => $data['password'],
                        'scope'         => '',
                    ]
                ]);
                $dataAuth = json_decode((string)$response->getBody(), true);
                $dataAuth['user'] = $user;
                return response([
                    'data' => $dataAuth
                ]);
            } else {
                throw new AuthenticationException(__('admin/error.password_incorrect'), 401);
            }
        }
    }

    public function refreshToken($data)
    {
        $data = Arr::only($data,[
            'refresh_token'
        ]);

        $user = \App\User::where('email',Auth::user()->email)->first();

        $http = new Client;
        $response = $http->post(url('oauth/token'), [
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $data['refresh_token'],
                'client_id'     => env('API_CLIENT_ID'),
                'client_secret' => env('API_CLIENT_SECRET'),
                'scope'         => '',
            ]
        ]);

        $dataAuth = json_decode((string)$response->getBody(), true);
        $dataAuth['user'] = $user;
        return response([
            'data' => $dataAuth
        ]);
    }
}
