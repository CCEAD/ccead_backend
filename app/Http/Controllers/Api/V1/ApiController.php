<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    protected function respond($data, $statusCode = 200, $headers = [])
    {
        return response()->json($data, $statusCode, $headers);
    }

    protected function respondCreated($data = null)
    {
        return $this->respond([
            'success' => true,
            'data' => $data,
            'message' => message('MSG001')
        ], 201);
    }

    protected function respondUpdated($data = null)
    {
        return $this->respond([
            'success' => true,
            'data' => $data,
            'message' => message('MSG002')
        ]);
    }

    protected function respondDeleted($data = null)
    {
        return $this->respond([
            'success' => true,
            'data' => $data,
            'message' => message('MSG003')
        ]);
    }

    protected function respondCanceled($data = null)
    {
        return $this->respond([
            'success' => true,
            'data' => $data,
            'message' => message('MSG003')
        ]);
    }

    protected function respondError($message = null, $statusCode)
    {
        return $this->respond(['message'=> $message], $statusCode);
    }

    protected function respondInternalError()
    {
        return $this->respondError(message('MSG010'), 500);
    }
}
