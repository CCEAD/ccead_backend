<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Aduana;
use Illuminate\Http\Request;

class AduanaController extends ApiController
{
    private $aduana;

    public function __construct(Aduana $aduana)
    {
        $this->aduana = $aduana;
    }

    public function listing()
    {
        $aduanas = $this->aduana->listAduanas();
        return $this->respond($aduanas);
    }
}
