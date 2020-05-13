<?php
namespace App\Exceptions;

/**
*
*/
class AuthenticationException extends \Exception
{
    public function responseJson()
    {
        return response()->json([
            'error' => [
                'message'     => (!empty($this->message)) ? $this->message : __('auth.failed'),
                'status_code' => $this->code !== 0 ? $this->code : 401,
                'error_code'  => 0,
                'error' => [
                  (!empty($this->message)) ? $this->message : __('auth.failed')
                ]
            ]
        ], $this->code !== 0 ? $this->code : 401);
    }
}
