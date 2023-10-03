<!DOCTYPE html>
<html lang="es-ES">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Solicitud de Ingreso</title>
    <style type="text/css">
      #Table-1 {
        font-family:"Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
        font-size:12px;
        width:100%;
        text-align:center;
        border-collapse:collapse;
      }

      #Table-1 th{
        font-size:14px;
        font-weight:bold;
        background:#969696;
        border-top:4px solid green;
        border-bottom:1px solid #fff;
        color:#fff;padding:8px;
      }
   
      #Table-1 td{
        background:#eeeeee;
        border-bottom:1px solid #fff;
        color:#4c4c4c;
        border-top:1px solid transparent;
        padding:8px;
        font-size:17px;
      }
    </style>
  </head>
  <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #007B1A;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
      <tr>
        <td>
          <table style="background-color:#1E692D; max-width:800px;  margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td style="text-align:center; padding: 15px">
                <a href="#" title="ccead" target="_blank">
                  <img src="http://www.ccead.com.bo/images/logo.png" style="width:250px;">
                </a>
              </td>
            </tr>
            <tr>
              <td>
                <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:800px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                  <tr>
                    <td style="height:40px;">&nbsp;</td>
                  </tr>
                  <tr>
                    <td style="padding:0 35px;">
                      <h1 style="margin: 0px;padding-bottom: 25px; text-transform: uppercase;">Solicitud de Almacenamiento de Cajas</h1>
                      <h2 style="margin: 0px;padding-bottom: 25px; text-transform: uppercase; color: #7c7c7c;">{{ $ingreso->agencia->razon_social }}</h2>
                      <hr />
                      <h4>FECHA DE SOLICITUD:  {{ date('d/m/Y', strtotime($ingreso->fecha_solicitud)) }}</h4>
                      <h4>TOTAL CAJAS: {{ $ingreso->cajas()->count() }} </h4>
                      <table id="Table-1">
                        <thead>
                          <tr>
                            <th scope="col">N°</th>
                            <th scope="col">Gestión</th>
                            <th scope="col">Total Carpetas</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($ingreso->cajas as $key => $caja)
                            <tr>
                              <td>{{ $key+1 }}</td>
                              <td>{{ $caja->gestion }}</td>
                              <td>{{ $caja->cant_carpetas }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </td>
                  </tr>
                  <tr>
                    <td style="height:40px;">&nbsp;</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
  	        <td style="height:20px;">&nbsp;</td>
  	      </tr>
  	      <tr>
  	        <td style="text-align:center;">
  	          <p style="font-size:14px; color:#F2F3F8; line-height:18px; margin:0 0 0;">
  	          	<strong>© {{date('Y')}} CCEAD S.A. Todos los derechos reservados.</strong>
  	          </p>
  	        </td>
  	      </tr>
  	      <tr>
  	        <td style="height:80px;">&nbsp;</td>
  	      </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>