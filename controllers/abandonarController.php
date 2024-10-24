<?php 

	// pasa el usuario de sesion a una variable
	$usuario = $_SESSION['3901']["usuario"];
	
	// ejecuta el soft delete sobre el usuario	
	$usuario->leaveOut();

	// redirecciona a la landing page
	header("Location: landing");

 ?>