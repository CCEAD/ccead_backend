<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Solicitud De Salida</title>
  <style>
    .container {
      max-width: 100%;
      margin: auto;
      padding: 25px;
      font-size: 16px;
      line-height: 24px;
      font-family: 'Ubuntu', sans-serif;
      color: #555;
    }

    .row {
      width: 100%;  
      padding-top: 0;
      padding-bottom: 0;
      text-align: left;
    }

    .right-div {
      float: right;
      width: 300px; 
    }

    .left-div-container {
      vertical-align: middle;
      width: 100%;
      text-align: center;
    }

    .clearfix:after {
      clear: both;
    }

    .clearfix:before,
    .clearfix:after {
      content: " ";
      display: table;
    }

    .table {
      border: 2px solid #707070;
      border-radius: 7px;
      border-spacing: 0;
      box-sizing: border-box;
      clear: both;
      margin: 2mm 0mm;
      width: 100%;
    }
  
    .table td { vertical-align: top; font-size: 8pt; }

    .table-title {
      width: 100%;
      border-collapse:separate;
      border:solid black 2px;
      border-radius:6px;
    }

    .table-title td {
      border: 1px solid black;
    }
  </style>
</head>
<body>
  <div class="container ">
    <div class="row">
      <div class="left-div-container clearfix">
        <div style="float: left; position: relative;">
          <img src="{{url('/img/logo.png')}}" style="width:220px; height: 60px;">
        </div>
        <div class="right-div" style="padding: 5px;">
          <div style="display: inline-block; width: 90%;">
            <table class="table-title">
              <tr>
                <td colspan="3">FECHA</td>
              </tr>
              <tr>
                <td>{{ date('d', strtotime($salida['fecha_solicitud'])) }}</td>
                <td>{{ date('m', strtotime($salida['fecha_solicitud'])) }}</td>
                <td>{{ date('Y', strtotime($salida['fecha_solicitud'])) }}</td>
              </tr>
            </table>
          </div>
          <div class="right-div">
            <div style="display: inline-block; width: 90%;">
              <table class="table-title">
                <tr>
                  <td><span style="font-size: 20px; font-weight: bold;">N° {{ $salida['numero'] }}</span></td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div style="width: 620px; margin: 2px auto; margin-bottom: 1; text-align: center;">
      <span style="font-size: 30px; font-weight: bold;">SOLICITUD DE SALIDA DE CAJAS</span>
    </div>
    <div class="row" style="color: #000000;">
      <div style="border: 2px solid #707070; border-radius: 7px;">
        <div style="float: right; margin: 8px; width:200px; color: #555;"><b>Código: {{ $salida['codigo'] }}</b></div>
        <div style="margin: 2px 2px 2px 10px; color: #555;"><strong>Agencia / Empresa :</strong> {{ $salida['agencia'] }}</div>
        <div style="margin: 2px 2px 2px 10px; color: #555;"><strong>Fecha Solicitud :</strong> {{ date('d/m/Y', strtotime($salida['fecha_solicitud'])) }} &nbsp;&nbsp;<strong>Fecha Aprobación :</strong> {{ $salida['fecha_aprobacion'] }} &nbsp;&nbsp;<strong>Fecha Entrega :</strong> {{ $salida['fecha_entrega'] }} </div>
        <div style="margin: 2px 2px 2px 10px; color: #555;"><strong>Estado :</strong> {{ $salida['estado'] }}</div>
        <div style="margin: 2px 2px 2px 10px; color: #555;"><strong>Observaciones :</strong> {{ $salida['observacion'] }}</div>
      </div>
    </div>
    <div style="width: 620px; margin: 1px auto; text-align: center;">
      <span style="font-size: 17px; font-weight: bold; text-decoration: underline;">DETALLE DE CAJAS</span>
    </div>
    <span style="color: #2A2A2A; font-weight: bold; font-size: 12px;">Total Cajas: {{ $salida['total_cajas'] }}</span>
    <div class="row" style="margin-top: 2px;">
      <table class="table">
        @foreach($salida['cajas'] as $key => $caja)
          <tr style="background-color: #E8E8E8;">
            <th style="color: #707070;">
              <div style="font-size: 13px; padding: 3px;">
                <span style="color: #2A2A2A; font-weight: bold">N° {{ $key + 1 }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><span style="color: #2A2A2A; font-weight: bold">Gestión:</span> {{ $caja['gestion'] }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><span style="color: #2A2A2A; font-weight: bold">Código Interno:</span> {{ $caja['cod_interno'] }}</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span><span style="color: #2A2A2A; font-weight: bold">Código Almacén:</span> {{ $caja['cod_almacen'] }}</span>
                <span style="float: right; color: #005C0E">CUBI: {{ $caja['cubi'] }}</span>
              </div>
            </th>
            <tr style="background-color: #F2FFF4;">
              <td>
                <div style="padding: 5px;">
                  <span style="font-size: 12px; font-weight: bold; text-decoration: underline;">Detalle Contenido</span>
                  <div style="font-size: 10px;">
                    @foreach($caja['carpetas'] as $carpeta)
                      <div style="border: 1px solid black; width: 122px; display: inline-block; margin-left: 4px;">
                        <center>{{ $carpeta['nro_declaracion'] }}</center>
                      </div>
                    @endforeach
                  </div>
                </div>
              </td>
            </tr>
          </tr>                  
        @endforeach
      </table>
    </div>
    <div class="row" style="margin-top: 5px; page-break-before: avoid;">
      <div style="border: 2px solid #707070; border-radius: 7px; color: black; height: 100px; padding: 7px;">
        <div style="display: inline-block; margin: 0 10px 0 0; border: 1px solid #707070; color: black; height: 60px; width: 49%"></div>
        <div style="display: inline-block; border: 1px solid #707070; color: black; height: 60px; width: 49%"></div>
        <div style="display: inline-block; margin: 0 10px 0 0;border: 1px solid #707070; color: black; height: 30px; width: 49%">
          <span style="display: table; margin: 0 auto; font-weight: bold; width: 100px;">CCEAD S.A.</span>
        </div>
        <div style="display: inline-block; border: 1px solid #707070; color: black; height: 30px; width: 49%">
          <span style="display: table; margin: 0 auto; font-weight: bold;">DESPACHANTE</span>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
