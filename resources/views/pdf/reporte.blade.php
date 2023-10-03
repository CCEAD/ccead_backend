<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Total Cajas</title>
    
    <style>
    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-content {
        text-align: center;
        font-weight: bold;
        font-size: 11pt;
    }

    .text-title {
        text-align: center;
        font-weight: bold;
        font-size: 12pt;
        color: #000;
    }
    
    .invoice-box {
        max-width: 100%;
        margin: auto;
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }

    .car-items table {
        border-collapse: collapse;
        width: 100%;
    }

    .car-items table td, th {
        border: 1px solid #000;
        padding: 3px;
    }

    .car-items table thead tr {
        background: #1E692D;
        color: #fff;
    }

    </style>
</head>

<body>
    <div class="invoice-box">
        <div class="car-items">
            <div style="float: right;">
                <span style="font-size: 20px; font-weight: bold;">{{ date('d/m/Y') }}</span>
            </div>
            <div style="margin: 10px;">
                <img src="{{url('/img/logo.png')}}" style="width:220px; height: 60px;">
            </div>
        	<div style="border: 1px solid black; border-bottom: none; text-align: center; font-size: 20px; padding: 20px; font-weight: bold;">{{ strtoupper($title) }} - {{ strtoupper($agency) }}</div>
            @php
                $total_cajas = 0;
                $total_car = 0;
            @endphp
            @foreach($items as $key => $item) 
                @php
                    $total_cajas += collect($item)->count();
                @endphp 
                <table>
                    <thead class="text-center" style="font-weight: bold;">
                        <tr>
                            <td>
                                <span style="padding-left: 10px; font-size: 20px; float:left;">Gestión {{ $key }}</span>
                                <span style="padding-right: 10px; font-size: 20px; float:right;">Total Cajas: {{ collect($item)->count() }}</span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <table class="detalle">
                            </thead>
                                <tr style="background: #efefef; color: #00000; text-align: center; font-weight: bold;">
                                    <td width="15%">N°</td>
                                    <td width="25%">Codigo Interno</td>
                                    <td width="25%">Cantidad de Carpetas</td>
                                    <td width="20%">Registro</td>
                                <tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_carpetas = 0;
                                @endphp
                                @foreach($item as $keyi => $row)   
                                    @php
                                        $total_carpetas += $row['cant_carpetas'];
                                        $total_car += $row['cant_carpetas'];
                                    @endphp  
                                    <tr>
                                        <td class="text-content">{{ $keyi + 1 }}</td>
                                        <td class="text-content">{{ $row['cod_interno'] }}</td>
                                        <td class="text-content">{{ $row['cant_carpetas'] }}</td>
                                        <td class="text-content">{{ date('d/m/Y', strtotime($row['created_at'])) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background: #def5e3;">
                                    <td></td>
                                    <td style="text-align: center"><span style="font-size: 20px; font-weight: bold;">Total Carpetas:</span></td>
                                    <td style="text-align: center"><span style="font-size: 20px; font-weight: bold;">{{ $total_carpetas }}</span></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </tbody>
                </table>
            @endforeach
        </div>
        <div class="car-items">
            <table>
                <tr style="background: #7c7c7c; color: #ffffff; text-align: center; font-weight: bold;">
                    <td colspan="2">TOTALES GENERALES</td>
                </tr>
                <tr style="text-align: center;">
                    <td><span style="font-size: 20px; font-weight: bold;">Total Cajas:</span></td>
                    <td width="23%"><span style="font-size: 20px; font-weight: bold;">{{ $total_cajas }}</span></td>
                </tr>
                <tr style="text-align: center;">
                    <td><span style="font-size: 20px; font-weight: bold;">Total Carpetas:</span></td>
                    <td width="23%"><span style="font-size: 20px; font-weight: bold;">{{ $total_car }}</span></td>
                </tr>
            </table>
        </div>
    </div>
</body>