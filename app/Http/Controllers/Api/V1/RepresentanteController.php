<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Representante;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Representante\StoreRepresentanteRequest;

class RepresentanteController extends ApiController
{
    public function index()
    {
        //
    }

    public function store(StoreRepresentanteRequest $request)
    {
        return $this->respond($request->all());
    }

    public function show(Representante $representante)
    {
        //
    }

    public function update(Request $request, Representante $representante)
    {
        //
    }

    public function destroy(Representante $representante)
    {
        //
    }
}
