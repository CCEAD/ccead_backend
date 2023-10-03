<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reporte</title>
  <style>
    .container {
      max-width: 100%;
      margin: auto;
      font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }

    .light-table {
      width: 100%;  
      padding-top: 0;
      padding-bottom: 0;
      margin-bottom: 5px;
      text-align: left;
    }

    .leftdiv {
      float: left;
      position: relative;
      width: 33%; 
    }

    .leftdiv p {
      display: block;
      width: 75%;
      /* margin: 0 auto !important; */
    }

    .leftdivcontainer {
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

    table {
      border-collapse: collapse;
      width: 100%;
    }

    table th,
    table td {
      border: 1px solid #807979;
      padding: 0.625em;
      text-align: center;
      font-weight: bold;
    }

    table tbody tr {
      border: 1px solid #ddd;
      font-size: 13px;
      background-color: #f2f4f8;
    }

    table thead th {
      padding-top: 6px;
      padding-bottom: 6px;
      background-color: #287729;
      color: white;
      text-transform: uppercase;
      font-size: 0.85em;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="light-table">
      <div class="leftdivcontainer clearfix">
        <div class="leftdiv">
          <div style="border: 2px solid #287729; border-radius: 7px; text-align: left; width: 60%; padding: 10px;">
            <span style="color: #000; font-weight: bold; font-size: 15px; letter-spacing: 1px;">{{ $ingresos[0]['agencia'] }}</span>
          </div>
        </div>
        <div class="leftdiv" style="margin-top: 0;">
          <div style="color: #000; font-size: 22px; font-weight: bold; text-decoration: underline;">LISTA GENERAL DE INGRESOS ALMACÉN</div>
        </div>
        <div class="leftdiv">
          <div style="text-align: right;"><img src="{{url('/img/logo.png')}}" style="width:180px; height:50px;"></div>
          <div style="text-align: right;"><span style="font-weight: bold;">Fecha: </span>{{ date('d/m/Y') }}</div>
        </div>
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th width="50px">Código</th>
          <th width="50px">Fecha de Solicitud</th>
          <th width="50px">Fecha de Approbación</th>
          <th width="50px">Fecha de Entrega</th>
          <th width="50px">Estado</th>
          <th width="50px">Cant. de cajas</th>
        </tr>
      </thead>
      <tbody>
        @foreach($ingresos as $ingreso)
          <tr style="page-break-inside: avoid;">
            <td class="text-content">{{ $ingreso['codigo'] }}</td>
            <td class="text-content">{{ $ingreso['fecha_solicitud'] }}</td>
            <td class="text-content">{{ $ingreso['fecha_aprobacion'] }}</td>
            <td class="text-content">{{ $ingreso['fecha_entrega'] }}</td>
            <td class="text-content">{{ $ingreso['estado'] }}</td>
            <td class="text-content">{{ $ingreso['total_cajas'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</body>
</html>
