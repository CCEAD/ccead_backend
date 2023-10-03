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
        	<div style="border: 1px solid black; border-bottom: none; text-align: center; font-size: 20px; padding: 20px; font-weight: bold;">LISTA DE INVENTARIO GENERAL</div>
            @php
                $total_cajas = 0;
                $total_car = 0;
            @endphp
            @foreach($data as $key => $item) 
                @php
                    $cajas = collect($item->cajas)->groupBy('gestion');
                @endphp
                <table>
                    <thead class="text-center" style="font-weight: bold;">
                        <tr>
                            <td>
                                <span style="padding-left: 10px; font-size: 20px; float:left;">{{ $item->razon_social }}</span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <table class="detalle">
                            </thead>
                                <tr style="background: #efefef; color: #00000; text-align: center; font-weight: bold;">
                                    <td width="15%">Gestión</td>
                                    <td width="25%">Total Cajas</td>
                                    <td width="25%">Total Carpetas</td>
                                <tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_carpetas = 0;
                                @endphp
                                @foreach($cajas as $keyi => $row)   
                                    @php
                                        $total_cajas += collect($row)->count();
                                        $total_car += collect($row)->sum('cant_carpetas');
                                        $total_carpetas = collect($row)->sum('cant_carpetas');
                                    @endphp 
                                    <tr>
                                        <td class="text-content">{{ $keyi }}</td>
                                        <td class="text-content">{{ collect($row)->count() }}</td>
                                        <td class="text-content">{{ $total_carpetas }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </tbody>
                </table>
            @endforeach
        </div>
        <div class="car-items">
            <table>
                <tr style="background: #7c7c7c; color: #ffffff; text-align: center; font-weight: bold;">
                    <td width="15%"><span style="font-size: 20px; font-weight: bold;">Totales:</span></td>
                    <td width="25%"><span style="font-size: 20px; font-weight: bold;">{{ $total_cajas }}</span></td>
                    <td width="25%"><span style="font-size: 20px; font-weight: bold;">{{ $total_car }}</span></td>
                </tr>
            </table>
        </div>
    </div>
</body>