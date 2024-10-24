<?php 
	
	/**
	* @file User.php
	* @brief Declaraciones de la clase User para la conexión con la base de datos.
	* @author Matias Leonardo Baez
	* @date 2024
	* @contact elmattprofe@gmail.com
	*/

	// incluye la libreria para conectar con la db
	include_once 'DBAbstract.php';

	/*< Se incluye la clase para el envio de correo electrónico*/
	include_once 'Mailer.php';

	/**
	 * 
	 * Clase para conectar con la tabla de Usuarios
	 * 
	 * */
	class User extends DBAbstract{

		public $attributes = array();

		/**
		 * 
		 * Constructor de la clase, ejecuta el constructor de DBAbstract
		 * 
		 * */
		function __construct(){
			parent::__construct();

			$result = $this->consultar('DESCRIBE `usuarios`; ');

			foreach ($result as $key => $value) {

				// guarda el nombre de la columna en una variable
				$attrib = $value['Field']; 

				// Guarda los nombres de las columnas en un vector
				$this->attributes[] = $attrib;

				// crea el atributo con el nombre de la columna
				$this->$attrib = "";
			}
		}

		/**
		 * 
		 * Actualiza los datos del usuario
		 * @param array $form arreglo asociativo con los datos actualizar
		 * @return array arreglo de errores
		 * 
		 * */
		function update($form){
			$this->nombre = $form["txt_nombre"];
			$this->apellido = $form["txt_apellido"];

			$sql = "CALL updateUser('{$this->nombre}', '{$this->apellido}', '{$this->email}')";


			$this->consultar($sql);

			return ["error" => "Actualizacion exitosa", "errno" => 200];
		}

		/**
		 * 
		 * loguea un usuario a la aplicacion si existe y esta activo
		 * 
		 * @param array $form_login arreglo asociativo con txt_email y txt_pass
		 * @return array arreglo asociativo error y errno
		 * 
		 * */
		function login($form_login){

			if($_SERVER["REQUEST_METHOD"]!="GET"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			$email = $form_login["txt_email"];
			// cifra la contraseña
			$pass = md5($form_login["txt_pass"]);

			$response = $this->consultar("CALL `login`('$email')");

			$response = $response->fetch_all(MYSQLI_ASSOC);

			// no encontre el email
			if(count($response)==0){
				return ["error" => "Usuario no registrado", "errno" => 404];
			}

			// si la contraseña es correcta
			if($response[0]["pass"]==$pass){

				// autocarga de valores en los atributos
				foreach ($this->attributes as $key => $attribute) {
					// menos la contraseña
					if($attribute!="pass"){
						$this->$attribute = $response[0][$attribute];
					}
				}

				$_SESSION['3901']['usuario'] = $this;

				return ["error" => "Logueo valido", "errno" => 200];
			}

			return ["error" => "Contraseña invalida", "errno" => 405];
		}

		/**
		 * 
		 * Registra un nuevo usuario en la tabla de usuarios
		 * 
		 * @param array $form arreglo asociativo con datos de usuario
		 * @return array arreglo con los valores de error
		 * 
		 * */
		function register($form){
			$email = $form["txt_email"];
			$pass = md5($form["txt_pass"]);

			$response = $this->consultar("SELECT * FROM usuarios WHERE email='$email'");

			$response = $response->fetch_all(MYSQLI_ASSOC);
			//var_dump($response);

			// no encontre el email entonces puedo registrarme
			if(count($response)==0){

				/*< crea un avatar para el usuario*/
				$avatar = "https://robohash.org/".$email.".png?set=set4";

				/*< genera el token de email para validarlo*/
				$email_token = md5($_ENV["PROJECT_TOKEN"] . date("YmdHis" . mt_rand(0,1000)));

				/*< agrega el usuario a la base de datos*/
				$this->consultar("CALL userRegister('$email', '$pass', '$avatar', '$email_token')");

				
				/*< se crea el correo electrónico*/
				$object_email = new Mailer();

				/*< información que contendrá el correo*/
				$motivo = "Validación de correo";
				$contenido = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Registro - '.$_ENV['PROJECT_NAME'].'</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
        }
        .header h1 {
            margin: 0;
            color: #FF9800;
        }
        .content {
            padding: 20px;
        }
        .content p {
            line-height: 1.6;
        }
        .button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 0;
            background-color: #FF9800;
            color: #ffffff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #999999;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>'.$_ENV['PROJECT_NAME'].'</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Gracias por registrarte en <strong>'.$_ENV['PROJECT_NAME'].'</strong>. Para completar tu registro y comenzar a organizar tus notas de manera eficiente, por favor verifica tu dirección de correo electrónico haciendo clic en el botón de abajo:</p>
            <a href="'.$_ENV['PROJECT_URL'].'/verify?token='.$email_token.'" class="button">Verificar Email</a>
            <p>Si no te registraste en <strong>'.$_ENV['PROJECT_NAME'].'</strong>, por favor ignora este correo electrónico.</p>
        </div>
        <div class="footer">
            <p>&copy; 2024 '.$_ENV['PROJECT_NAME'].'. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
';
				/*< vector asociativo para el método send de email*/
				$email_form = ["destinatario"=>$email, "motivo" => $motivo, "contenido" => $contenido];

				/*< envia el correo electrónico*/
				$object_email->send($email_form);

				/*< aviso de registro exitoso*/
				return ["error" => "Usuario registrado correctamente", "errno" => 200];

			}else{ // Se encontro el email

				// Si el usuario es uno que abandono la app
				if($response[0]["delete_at"]!="0000-00-00 00:00:00"){

					$id = $response[0]["id"];

					$sql = "CALL userComeBack($id)";

					//var_dump($sql);

					$this->consultar($sql);

					return ["error" => "Usuario que abandono y volvio", "errno" => 202];

				}else{ // el usuario solo esta registrado

					return ["error" => "Usuario ya registrado", "errno" => 201];
				}
			}

			return ["error" => "Usuario ya registrado", "errno" => 201];
		}

		/**
		 * 
		 * Retorna el id del usuario
		 * 
		 * @return int id del usuario
		 * 
		 * */
		function getId(){
			return $this->id;
		}


		/**
		 * 
		 * Desloguea al usuario
		 * 
		 * */
		function logout(){

			session_unset();
			session_destroy();

		}


		/**
		 * 
		 * Soft Delete del usuario en la tabla de usuarios
		 * @return bool true
		 * 
		 * */
		function leaveOut(){

			$fecha_hora = date("Y-m-d h:i:s");
			$id = $this->id;

			$sql = "UPDATE usuarios SET nombre= '', apellido = '', delete_at = '$fecha_hora' WHERE id=$id";

			$this->consultar($sql);

			$this->logout();

			return true;
		}

		/**
		 * 
		 * Retorna la cantidad de usuarios
		 * 
		 * @return int cantidad de usuarios
		 * 
		 * */
		function getCantUser(){

			$response = $this->consultar("CALL `getCantUser`();");

			return $this->db->affected_rows;

		}

		/**
		 * 
		 * Retorna todos datos de un usuario por medio de su id
		 * 
		 * @param int $id id de usuario
		 * @return array datos del usuario
		 * 
		 * */
		function getById($id){
			$response = $this->consultar("SELECT * FROM usuarios WHERE id='$id'");

			return $response;
		}

		/**
		 * 
		 * @brief Lista de usuarios limitada GET
		 * @param array $params [inicio]int [cantidad]int
		 * @return array listado de usuarios
		 *
		 * */
		function getAll($params){

			if($_SERVER["REQUEST_METHOD"]!="GET"){
				return ["errno" => 405, "error" => "Metodo incorrecto"];
			}

			$inicio = $params["inicio"];
			$cantidad = $params["cantidad"];

			$result = $this->consultar("SELECT * FROM usuarios LIMIT  $inicio,$cantidad")->fetch_all(MYSQLI_ASSOC);

			$result = ["errno" => 200, "error" => "Listado correctamente", "info" => $result];

			return $result;
		}

		/**
		 * 
		 * Valida un token de email y activa el usuario si fuera correspondiente
		 * @brief Valida token de email y activa el usuario
		 * @param array [token] token que llega por GET
		 * @return array [errno, error] resultado de la validacion
		 * 
		 * */
		function verify($params){

			/*< recupera el token del form*/
			$token = $params["token"];

			/*< consulta para buscar el usuario por email_token*/
			$sql = "SELECT * FROM usuarios WHERE email_token = '$token'";

			/*< ejecuta la consulta*/
			$response = $this->consultar($sql);

			/*< recupera los datos del usuario en una matriz*/
			$matriz = $response->fetch_all(MYSQLI_ASSOC);

			/*< si no se encontro usuario por medio de ese email_token*/
			if(count($matriz)>0){

				/*< consulta para activar el usuario y borrar su email_token*/
				$sql = "UPDATE usuarios SET active = 1, email_token = '' WHERE email_token = '$token'";

				/*< ejecuta la consulta*/
				$this->consultar($sql);

				return ["errno" => 200, "error" => "Usuario activado correctamente"];

			}else{ // no encontre el token
				return ["errno" => 404, "error" => "El token no es valido"];
			}
		}
	}

 ?>






