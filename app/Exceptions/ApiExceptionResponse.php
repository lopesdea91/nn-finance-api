<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiExceptionResponse extends Exception
{
	public function report()
	{
		$sts = Response::HTTP_FAILED_DEPENDENCY;
		$rtn = ['message' => '$e->getMessage()'];

		return response()->json($rtn, $sts);
	}

	public function render($request)
	{
		$sts = Response::HTTP_FAILED_DEPENDENCY;
		$rtn = ['message' => $this->getMessage()];

		return response()->json($rtn, $sts);
	}
}
