-- Inicialización de base de datos MySQL para CDATTG
-- Este archivo se ejecuta automáticamente al crear el contenedor MySQL

-- Crear usuario de aplicación
CREATE USER IF NOT EXISTS 'cdattg_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON cdattg.* TO 'cdattg_user'@'%';
FLUSH PRIVILEGES;

-- Configurar timezone
SET GLOBAL time_zone = '-05:00';