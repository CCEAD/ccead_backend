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
                      <h1 style="color:#1e1e2d; font-weight:500; margin:0;font-size:32px;font-family:'Rubik',sans-serif;">Nueva Cuenta</h1>
                      <span style="display:inline-block; vertical-align:middle; margin:29px 0 26px; border-bottom:1px solid #cecece; width:100px;"></span>
                      <p style="color:#455056; font-size:15px;line-height:24px; margin:0;">
                        Recibiste este correo electrónico, por que se ha dado de alta tu usuario administrador dentro de nuestra plataforma a continuación te dejamos las credenciales:
                      </p>
                      <hr />
                      <h4>Información del usuario </h4>
                      <hr />
                      <div style="text-align: left;">
                        <div style="margin: 8px;">
                          <span style="font-weight:bold;">Nombre de usuario: </span>{{ $user->name }}
                        </div>
                      </div>

                      <div style="text-align: left;">
                        <div style="margin: 8px;">
                          <span style="font-weight:bold;">Contraseña: </span>{{ $user->temp_password }}
                        </div>
                      </div>
                      <br>
                      <div style="text-align: left;">• Recuerda que éstas credenciales son para tu primer acceso, posteriormente el sistema te pedirá validar tu cuenta y registrar una nueva contraseña.</div>
                      <hr />
                      <div style="text-align: left;">• Para iniciar sesión, pulse en el siguiente enlace:</div>
                      <a href="#" style="background:#007B1A;text-decoration:none!important; font-weight:500; margin-top:35px; color:#fff;text-transform:uppercase; font-size:14px;padding:10px 24px;display:inline-block;border-radius:50px;">Ingresar</a>
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