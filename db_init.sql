
-- Paso 1: Crear la base de datos
CREATE DATABASE album_photo
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos recién creada
USE album_photo;

-- Paso 2: Crear el usuario con contraseña segura
-- Cambia 'xxxx' por tu contraseña
-- Cambia 'xxx.nnn' por tu usuario
CREATE USER 'xxx.nnn'@'localhost' IDENTIFIED BY 'xxxx';

-- Paso 3: Dar permisos al usuario sobre la base de datos
GRANT ALL PRIVILEGES ON album_photo.* TO 'xxx.nnn'@'localhost';
FLUSH PRIVILEGES;

show databases;



CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  apellido VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  user VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fotos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  titulo VARCHAR(255),
  descripcion TEXT,
  ruta VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);