<!DOCTYPE html>
<html lang="es-ES">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Notificación de Registro</title>
    <style type="text/css">
      a:hover {text-decoration: underline !important;}
    </style>
  </head>
  <body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #007B1A;" leftmargin="0">
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8" style="@import url(https://fonts.googleapis.com/css?family=Rubik:300,400,500,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
      <tr>
        <td>
          <table style="background-color: #007B1A; max-width:670px;  margin:0 auto;" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
            <tr>
              <td style="text-align:center;">
                <a href="#" title="ccead" target="_blank">
                  <img src="http://www.ccead.com.bo/images/logo.png" style="width:250px;">
                </a>
              </td>
            </tr>
            <tr>
              <td>
                <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" style="max-width:670px;background:#fff; border-radius:3px; text-align:center;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                  <tr>
                    <td style="height:40px;">&nbsp;</td>
                  </tr>
                  <tr>
                    <td style="padding:0 35px;">
                      <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">Código de Verificación</h1>
                      <hr />
                      <h4>Utilice el siguiente código de verificación para poder restablecer su contraseña:  </h4>
                      <hr />
                      <h2 style="background: #007B1A;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">
                        {{ $otp->otp }}
                      </h2>
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