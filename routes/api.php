<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Controladores
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AduanaController;
use App\Http\Controllers\Api\V1\AgenciaController;
use App\Http\Controllers\Api\V1\RepresentanteController;
use App\Http\Controllers\Api\V1\UbigeoController;
use App\Http\Controllers\Api\V1\CajaController;
use App\Http\Controllers\Api\V1\CarpetaController;
use App\Http\Controllers\Api\V1\IngresoController;
use App\Http\Controllers\Api\V1\SalidaController;
use App\Http\Controllers\Api\V1\ReingresoController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\PermisoController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ReporteController;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Mail;
use App\Mail\SolicitudIngreso;

Route::prefix('v1')->group(function () {

    Route::get('invoice-download', [SalidaController::class, 'download']);
    Route::get('invoice', [SalidaController::class, 'test']);

    Route::post('login', [AuthController::class, 'login']);
    Route::post('user/verification', [AuthController::class, 'userVerification']);
    Route::post('otp/verification', [AuthController::class, 'otpVerification']);
    Route::post('password/register', [AuthController::class, 'passwordRegister']);
    Route::post('password/email', [AuthController::class, 'forgotPassword']);
    Route::post('password/reset', [AuthController::class, 'resetPassword']); 

    Route::post('agencias', [AgenciaController::class, 'store']);
    Route::post('representantes', [RepresentanteController::class, 'store']);
    Route::post('registro/agencia', [AgenciaController::class, 'registroAgencia']);

    Route::post('carpetas/importar', [CarpetaController::class, 'importarCarpetasExcel']);

    Route::get('carga', function() {

        $detalle = ['701/C11633', '771/C11634'];

        foreach ($detalle as $key => $row) {
            echo substr(substr($row, 0), 0, 3);
        }

        // $cajas = [2561, 2562, 2563, 2564, 2565, 2566, 2567, 2568, 2569, 2570, 2571, 2572, 2573, 2574, 2575, 2576, 2577, 2578, 2579, 2580, 2581, 2582, 2583, 2584, 2585, 2586, 2587, 2588, 2589, 2590, 2591, 2592, 2593, 2594, 2595, 2596, 2597, 2598, 2599, 2600, 2601];

        // foreach ($cajas as $key => $caja) {
        //     $carpetas = DB::table('carpetas')->where('caja_id', $caja)->get();

        //     foreach ($carpetas as $key => $carpeta) {
                
        //         DB::table('carpetas')
        //         ->where('id', $carpeta->id)
        //         ->update(['nro_declaracion' => str_replace( 'C ', '', strtoupper($carpeta->nro_declaracion))]);
        //     }
        // }
    });
    Route::get('cajas/cubi/{caja}', [CajaController::class, 'generarCajaCubiPdf']);

    //api para PI
    Route::get('general/agencia', [ReporteController::class, 'ApiGeneralAgencia']);

    Route::get('reportes/inventario/agencia', [ReporteController::class, 'totalInventarioAgencia']);
    Route::get('reportes/total/agencia', [ReporteController::class, 'totalInvGestionAgencia']);//1
    Route::get('reportes/general/agencia', [ReporteController::class, 'totalGeneralAgencia']);//2
    Route::get('reportes/cajas/grafico', [ReporteController::class, 'totalCajasGrafico']);

    Route::group(['middleware' => ['auth:api', 'teams_permission:api', 'user_active:api']], function() {

        Route::get('permisos', [AuthController::class, 'permisos']);

        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('aduanas/listing', [AduanaController::class, 'listing']);

        Route::get('agencias', [AgenciaController::class, 'index'])->middleware('permission:agencia.listar');
        Route::post('agencias/alta', [AgenciaController::class, 'altaAgencia'])->middleware('permission:agencia.alta');
        Route::get('agencias/lista', [AgenciaController::class, 'lista']);

        //roles
        Route::get('roles/admin', [RoleController::class, 'rolesAdmin'])->middleware('is_admin::api');
        Route::get('roles/agencia', [RoleController::class, 'rolesAgencia'])->middleware('permission:roles.listar');
        Route::post('roles', [RoleController::class, 'store'])->middleware('permission:roles.registrar');
        Route::get('roles/{rol}/edit',  [RoleController::class, 'edit'])->middleware('permission:roles.modificar');
        Route::put('roles/{rol}', [RoleController::class, 'update'])->middleware('permission:roles.modificar');
        Route::delete('roles/{rol}', [RoleController::class, 'destroy'])->middleware('permission:roles.eliminar');
        Route::get('roles/lista', [RoleController::class, 'lista']);

        //permisos
        Route::get('permisos/lista', [PermisoController::class, 'lista']);

        //ubigeos
        Route::get('ubigeos', [UbigeoController::class, 'index'])->middleware('permission:ubigeo.listar');
        Route::get('ubigeos/{ubigeo}',  [UbigeoController::class, 'show'])->middleware('permission:ubigeo.ver');
        Route::post('ubigeos', [UbigeoController::class, 'store'])->middleware('permission:ubigeo.registrar');
        Route::put('ubigeos/{ubigeo}', [UbigeoController::class, 'update'])->middleware('permission:ubigeo.modificar');
        Route::delete('ubigeos/{ubigeo}', [UbigeoController::class, 'destroy'])->middleware('permission:ubigeo.eliminar');

        //cajas - almacen
        Route::get('cajas', [CajaController::class, 'index'])->middleware('permission:caja.listar');
        Route::get('cajas/carpetas/{caja}', [CajaController::class, 'carpetasPorCaja']);//revisar permisos

        //cajas - digitalizacion
        Route::get('cajas/agencia/{id}/admin',  [CajaController::class, 'cajasPorAgenciaAdmin'])->middleware('is_admin::api');
        Route::get('cajas/agencia',  [CajaController::class, 'cajasPorAgencia'])->middleware('permission:caja.listar');

        Route::get('cajas/{caja}/edit',  [CajaController::class, 'edit'])->middleware('permission:caja.modificar');
        Route::post('cajas/admin', [CajaController::class, 'storeAdmin'])->middleware('permission:caja.registrar');
        Route::post('cajas/agencia', [CajaController::class, 'storeAgencia'])->middleware('permission:caja.registrar');
        Route::put('cajas/{caja}', [CajaController::class, 'update'])->middleware('permission:caja.modificar');
        Route::delete('cajas', [CajaController::class, 'destroy'])->middleware('permission:caja.eliminar');
        Route::get('cajas/pendientes/{id}',  [CajaController::class, 'cajasPendientesPorAgenciaAdmin']);
        Route::get('cajas/pendientes',  [CajaController::class, 'cajasPendientesPorAgencia']);
        Route::get('cajas/activas/{id}',  [CajaController::class, 'cajasActivasPorAgenciaAdmin']);
        Route::get('cajas/activas',  [CajaController::class, 'cajasActivasPorAgencia']);
        Route::get('cajas/carpetas/{caja}/list-pdf', [CajaController::class, 'listCarpetasCajaPdf']);
        Route::get('cajas/carpetas/{caja}/list-excel', [CajaController::class, 'listCarpetasCajaExcel']);
        Route::post('cajas/list-pdf', [CajaController::class, 'listPdf']);
    	Route::post('cajas/list-excel', [CajaController::class, 'listExcel']);


        //carpetas
        Route::get('carpetas', [CarpetaController::class, 'index']);
        Route::get('carpetas/{carpeta}/show',  [CarpetaController::class, 'show'])->middleware('permission:carpetas.modificar');
        Route::post('carpetas', [CarpetaController::class, 'store'])->middleware('permission:carpetas.registrar');
        Route::put('carpetas/{carpeta}', [CarpetaController::class, 'update'])->middleware('permission:carpetas.modificar');
        Route::delete('carpetas/{carpeta}', [CarpetaController::class, 'destroy'])->middleware('permission:carpetas.eliminar');
        Route::get('carpetas/caja/{id}/admin',  [CarpetaController::class, 'carpetasPorCajaAdmin'])->middleware('is_admin::api');
        Route::get('carpetas/caja/{id}',  [CarpetaController::class, 'carpetasPorCaja'])->middleware('permission:carpetas.listar');
        Route::get('carpetas/caja/{id}/activas',  [CarpetaController::class, 'carpetasActivasPorCaja']);
        Route::post('carpetas/escaner', [CarpetaController::class, 'escanerCarpeta']);
        Route::post('carpetas/download', [CarpetaController::class, 'descargarDocumentoCarpeta']);
        Route::post('carpetas/alldownload', [CarpetaController::class, 'descargarTotalCarpeta']);
        
        //ingresos
        Route::get('ingresos/agencia/{id}/admin',  [IngresoController::class, 'ingresosPorAgenciaAdmin'])->middleware('is_admin::api');
        Route::get('ingresos/agencia',  [IngresoController::class, 'ingresosPorAgencia'])->middleware('permission:ingresos.listar');
        Route::get('ingresos/detalle/{id}',  [IngresoController::class, 'verDetalleIngreso'])->middleware('permission:ingresos.detalle');
        Route::post('ingresos/admin', [IngresoController::class, 'storeAdmin'])->middleware('is_admin::api');
        Route::post('ingresos/agencia', [IngresoController::class, 'storeAgencia'])->middleware('permission:ingresos.registrar');
        Route::get('ingresos/descarga/{ingreso}', [IngresoController::class, 'ingresoPdf'])->middleware('permission:ingresos.descargar');
        Route::get('ingresos/{ingreso}/edit', [IngresoController::class, 'edit'])->middleware('permission:ingresos.modificar');
        Route::put('ingresos/{ingreso}', [IngresoController::class, 'update'])->middleware('permission:ingresos.modificar');
        Route::delete('ingresos/{ingreso}', [IngresoController::class, 'destroy'])->middleware('permission:ingresos.eliminar');
        Route::post('ingresos/aprobar', [IngresoController::class, 'aprobarIngreso'])->middleware('is_admin::api');
        Route::post('ingresos/ejecutar', [IngresoController::class, 'ejecutarIngreso'])->middleware('is_admin::api');
        Route::post('ingresos/rechazar', [IngresoController::class, 'rechazarIngreso'])->middleware('is_admin::api');
        Route::get('ingresos/cubi/{ingreso}', [IngresoController::class, 'generarListaCubiPdf']);
        Route::post('ingresos/list-pdf', [IngresoController::class, 'listPdf']);
    	Route::post('ingresos/list-excel', [IngresoController::class, 'listExcel']);

        //salidas
        Route::get('salidas/agencia/{id}/admin',  [SalidaController::class, 'salidasPorAgenciaAdmin'])->middleware('is_admin::api');
        Route::get('salidas/agencia',  [SalidaController::class, 'salidasPorAgencia'])->middleware('permission:salidas.listar');
        Route::post('salidas', [SalidaController::class, 'store'])->middleware('permission:salidas.registrar');
        Route::get('salidas/descarga/{salida}', [SalidaController::class, 'salidaPdf'])->middleware('permission:salidas.descargar');
        Route::get('salidas/{salida}/edit', [SalidaController::class, 'edit'])->middleware('permission:salidas.modificar');//revisar
        Route::get('salidas/detalle/{id}', [SalidaController::class, 'verDetalleSalida'])->middleware('permission:salidas.detalle');
        Route::put('salidas/{salida}', [SalidaController::class, 'update'])->middleware('permission:salidas.modificar');
        Route::delete('salidas/{salida}', [SalidaController::class, 'destroy'])->middleware('permission:salidas.eliminar');
        Route::post('salidas/aprobar', [SalidaController::class, 'aprobarSalida'])->middleware('is_admin::api');
        Route::post('salidas/ejecutar', [SalidaController::class, 'ejecutarSalida'])->middleware('is_admin::api');
        Route::post('salidas/rechazar', [SalidaController::class, 'rechazarSalida'])->middleware('is_admin::api');
        Route::post('salidas/list-pdf', [SalidaController::class, 'listPdf']);
    	Route::post('salidas/list-excel', [SalidaController::class, 'listExcel']);

        //reingresos
        Route::post('reingresos', [ReingresoController::class, 'store'])->middleware('is_admin::api');
        Route::post('reingresos/salida', [ReingresoController::class, 'obtenerDatosReingreso'])->middleware('is_admin::api');
        Route::post('reingresos/detalle', [ReingresoController::class, 'verDetalleReingreso'])->middleware('is_admin::api');
        Route::get('reingresos/descarga/{reingreso}', [ReingresoController::class, 'reingresoPdf']);

        //usuarios
        Route::get('usuarios/admin', [UserController::class, 'listaUsuariosAdmin'])->middleware('is_admin::api');
        Route::get('usuarios/agencia', [UserController::class, 'listaUsuariosAgencia'])->middleware('permission:usuarios.listar');
        Route::post('usuarios', [UserController::class, 'store'])->middleware('permission:usuarios.registrar');
        Route::get('usuarios/{user}/edit', [UserController::class, 'edit'])->middleware('permission:usuarios.modificar');
        Route::put('usuarios/{user}', [UserController::class, 'update'])->middleware('permission:usuarios.modificar');
        Route::put('usuarios/{user}/password', [UserController::class, 'password']);
        Route::delete('usuarios/{user}', [UserController::class, 'destroy'])->middleware('permission:usuarios.eliminar');

        //Reportes
        Route::post('reportes/inventario/agencia', [ReporteController::class, 'totalInventarioAgencia']);
        Route::get('reportes/cajas/grafico', [ReporteController::class, 'totalCajasGrafico']);
        Route::get('reportes/general/agencia', [ReporteController::class, 'totalGeneralAgencia']);
        //usuario
        Route::post('reportes/total/agencia', [ReporteController::class, 'totalInvGestionAgencia']);
        Route::post('reportes/aduana/agencia', [ReporteController::class, 'totalInvGestionAgencia']);
        Route::post('reportes/pdf_download', [ReporteController::class, 'pdfDownload']);
    });
});