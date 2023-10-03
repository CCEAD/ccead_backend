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
        font-size: 25px;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="center">
        <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br> <br>
        <center>{!! DNS2D::getBarcodeHTML($data['detalle'], 'QRCODE',6,6); !!}</center>
        <p>{{ $data['cubi'] }}</p>
    </div>
</body>
</html>


