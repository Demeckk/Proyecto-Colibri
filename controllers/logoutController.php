<?php 

	// Ejecuta el metodo logout del objeto user
	$_SESSION['3901']["usuario"]->logout();

	// Redirecciona a la landing page
	header("Location: landing");
 ?>