<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends ApiController
{
    private $grupo;

    public function __construct(Grupo $grupo)
    {
        $this->grupo = $grupo;
    }

    public function listing()
    {
        $grupos = $this->grupo->listGrupos();
        return $this->respond($grupos);
    }
}
