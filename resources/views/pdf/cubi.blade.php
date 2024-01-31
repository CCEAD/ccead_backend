<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Ubuntu', sans-serif;
        }

        .center {
            width: 60%;
            padding: 10px;
            text-align: center;
            margin: auto;
        }

        .center p {
            font-size: 25px;
            font-weight: bold;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    @foreach($data['cajas'] as $key => $caja)
        <div class="center">
            <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br>
            <center>{!! DNS2D::getBarcodeHTML($caja['detalle'], 'QRCODE',6,6); !!}</center>
            <p>{{ $caja['cubi'] }}</p>
        </div>
        @if(!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>
