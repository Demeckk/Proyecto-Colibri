<?php 

	$tpl = new Tini("verify");

	$token_verify = (str_replace("/verify?token=", "" ,$_SERVER['REQUEST_URI']));


	$usuario = new User();

	$response = $usuario->verify(["token" => $token_verify]);

	$vars = ["MSG_VERIFY" => $response["error"]];

	// reemplazamos las variables en la plantilla
	$tpl->setVars($vars);

	// imprime la plantilla
	$tpl->print();

 ?>