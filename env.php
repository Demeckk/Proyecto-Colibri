<?php


	// debug - pruebas
	// release - produccion

	define("DEBUG", 1); // sin cache
	define("RELEASE", 0); // con cache

	// Seteo si quiero o no cachear
	define("MODE", DEBUG);

	// Si mode vale 1 entonces le doy a $_ENV[MODE] una variable por GET con un valor aleatorio
	$_ENV["PROJECT_MODE"] = MODE ? "?v=".mt_rand(0, 9999) : "";

	/*< token único para la aplicación */
	$_ENV["PROJECT_TOKEN"] = "mK8dQ766YoEPycd50Kr2x3I6hPgXO2VCLLBhOk8MACUb2yistLQKePKZCPMvXhuo";

	/*< Variables de entorno para el proyecto */
	$_ENV['PROJECT_NAME'] = "AirQuality";
	$_ENV['PROJECT_DESCRIPTION'] = "Aplicación para compartir apuntes.";
	$_ENV['PROJECT_KEYWORDS'] = "Matias Baez,matias baez,mattprofe,MattProfe,Matt Profe,matt profe,programación,programacion,apuntes,notas,textos,información,estudiantes,educación";
	$_ENV['PROJECT_AUTHOR'] = "Matias Leonardo Baez MattProfe" ;
	$_ENV['PROJECT_AUTHOR_CONTACT'] = "elmattprofe@gmail.com";
	$_ENV['PROJECT_URL'] = "http://losapuntes.com.ar";

	/*< Variables de entorno para conectar con la base de datos */
	$_ENV['HOST']= "mattprofe.com.ar";
	$_ENV['USER']= "3901";
	$_ENV['PASS']= "gecko.manzano.auto";
	$_ENV['DB']= "3901";
	$_ENV['PORT'] = 3306;

	//=== Variables de entorno para el envio de correo electrónico

	$_ENV['MAILER_REMITENTE']= 'losapuntes@gmail.com'; // <===
	$_ENV['MAILER_NOMBRE']= 'Los Apuntes'; // <===
	$_ENV['MAILER_PASSWORD']= 'aaaa bbbb cccc dddd'; // <=== token

	$_ENV['MAILER_HOST']= 'smtp.gmail.com';
	$_ENV['MAILER_PORT']= '587';
	$_ENV['MAILER_SMTP_AUTH']= true;
	$_ENV['MAILER_SMTP_SECURE']= 'tls';

 ?>