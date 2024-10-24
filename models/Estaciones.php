<?php 
	
	/**
	* @file Estaciones.php
	* @brief Declaraciones de la clase Estaciones para la conexión con la base de datos. de la aplicación de estaciones metereologicas
	* @author Matias Leonardo Baez
	* @date 2024
	* @contact elmattprofe@gmail.com
	*/

	// incluye la libreria para conectar con la db

	include_once 'DBAbstract.php';

	/*< declaración de la clase Estaciones*/
	class Estaciones extends DBAbstract{


		/**
		 * 
		 * constructor de la clase
		 * @brief constructor de la clase
		 * 
		 * */
		function __construct(){

		}

		/**
		 * 
		 * Retorna una matriz con información de todas las estaciones metereologicas
		 * @brief Recupera todos los datos de cada estación metereologica
		 * @return array [ASSOC]
		 * 
		 * */
		function getAll(){

			/*< consulta para recuperar todos los datos de todas las estaciones metereologicas*/
			$sql = "SELECT * FROM estaciones";

			/*< ejecuta la consulta*/
			$response = $this->consultar($sql);

			/*< retorna la matriz con los datos*/
			return $response->fetch_all(MYSQLI_ASSOC);
		}

		/**
		 * 
		 * Retorna la información de una estación metereologica correspondiente al chipid
		 * @brief Retorna los datos de una estación por medio de su chipid
		 * @param array $params [id] id de la estación metereologica
		 * @return array [ASSOC]
		 * 
		 * */
		function getByChipId($params){

			/*< recupera el id del form*/
			$chipid = $params["id"];

			/*< consulta para buscar los datos de una estación metereologica*/
			$sql = "SELECT * FROM estaciones WHERE chipId = $chipid";

			/*< ejecuta la consulta*/
			$response = $this->consultar($sql);

			/*< retorna la matriz con los datos de la consulta*/
			return $response->fetch_all(MYSQLI_ASSOC);
		}
	}


 ?>