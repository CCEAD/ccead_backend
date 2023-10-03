<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Ubigeo;
use App\Http\Controllers\Api\V1\ApiController;
use Illuminate\Http\Request;
use App\Http\Requests\Ubigeo\StoreUbigeoRequest;
use App\Http\Resources\Ubigeo\UbigeoResource;
use App\Http\Resources\Ubigeo\UbigeoCollection;
use Illuminate\Support\Facades\DB;

class UbigeoController extends ApiController
{
    private $ubigeo;

    public function __construct(Ubigeo $ubigeo)
    {
        $this->ubigeo = $ubigeo;
    }

    public function index(Request $request)
    {
        $ubigeos = $this->ubigeo->orderBy('created_at', $request->sort)->paginate($request->per_page);
        return new UbigeoCollection($ubigeos);
    }

    public function store(StoreUbigeoRequest $request)
    {
        DB::beginTransaction();

        try {
            $ubigeo = $this->ubigeo->create($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondCreated($ubigeo);
    }

    public function show(Ubigeo $ubigeo)
    {
        return new UbigeoResource($ubigeo);
    }

    public function update(StoreUbigeoRequest $request, Ubigeo $ubigeo)
    {
        DB::beginTransaction();

        try {
            $ubigeo->update($request->all());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }

        return $this->respondUpdated($request->all());
    }

    public function destroy(Ubigeo $ubigeo)
    {
        DB::beginTransaction();
        try {
            $ubigeo->delete();
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->respondInternalError();
        }
        return $this->respondDeleted();
    }
}
