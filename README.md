
# Sistema de Gestión de Alumnos

Proyecto desarrollado como práctica de instalación y configuración de un servidor LAMP en Amazon EC2.

## Tecnologías utilizadas

- Amazon Linux 2023
- Apache HTTP Server
- PHP 8.5
- MariaDB 10.5
- HTML y CSS
- Git y GitHub

## Funcionalidades

El sistema permite realizar operaciones CRUD:

- Registrar alumnos
- Consultar alumnos registrados
- Buscar por nombre o matrícula
- Editar información
- Eliminar registros

## Base de datos

Nombre: `practica_lamp`

Tabla: `alumnos`

El archivo `database.sql` contiene la estructura necesaria para crear la base de datos.

## Instalación

1. Instalar Apache, PHP y MariaDB.
2. Descargar o clonar el repositorio.
3. Importar `database.sql` en MariaDB.
4. Crear un usuario de base de datos con los permisos necesarios.
5. Copiar `conexion.example.php` como `conexion.php`.
6. Configurar las credenciales locales en `conexion.php`.
7. Acceder al proyecto mediante Apache.

## Seguridad

El archivo `conexion.php` está excluido de GitHub para evitar publicar credenciales.

Este proyecto es una demostración académica y requiere autenticación de usuarios y medidas de seguridad adicionales antes de utilizarse en producción.

## Autoría

Práctica académica de Ingeniería en Sistemas Computacionales.
