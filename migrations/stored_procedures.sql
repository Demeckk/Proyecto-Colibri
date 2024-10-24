-- Función que retorna todos los datos de un usuario en base a su email

DELIMITER $$
CREATE DEFINER=`mbcorp`@`%` PROCEDURE `getUserByEmail`(IN `param_email` TEXT)
SELECT * FROM users WHERE email=param_email$$
DELIMITER ;