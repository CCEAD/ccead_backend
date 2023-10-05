<!DOCTYPE html>
<html>
<head>
<style>
    .center {
        margin: auto;
        width: 60%;
        padding: 10px;
        text-align: center;
        font-family: 'Ubuntu', sans-serif;
        page-break-before:always;
    }

    .center p {
        margin-top: 8px;
        font-size: 25px;
        font-weight: bold;
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
    @endforeach
</body>
</html>


