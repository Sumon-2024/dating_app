<?php

namespace App\Http\Controllers;
use Illuminate\Routing\Controller;
class BaseController extends Controller
{
    // Success Response
    protected function successResponse($data, $message = "Success", $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    // Error Response
    protected function errorResponse($message = "An error occurred", $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }
}
