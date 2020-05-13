<?php
namespace App\Exceptions;

/**
*
*/
class DataEmptyException extends \Exception
{
		public function responseJson()
		{
				return \Response::json([
						'data' => [
								'message'			=> (!empty($this->message)) ? $this->message : __('admin/error.data_not_found'),
								'status_code'	=> 204,
								'error_code'  => 0
						]
				], 204);
		}
}
