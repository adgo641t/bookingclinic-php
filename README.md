# BookingClinic – Medical Appointment Booking System

Aplicación web de reservas médicas desarrollada con **PHP, MySQL y Bootstrap**. Permite a los pacientes registrarse, consultar profesionales médicos por especialidad, reservar citas y gestionar sus propias reservas.

## Tecnologías utilizadas

- **PHP 8** – Lógica del servidor.
- **MySQL** – Gestión de la base de datos.
- **PDO** – Conexión y consultas preparadas.
- **HTML5** – Estructura de la aplicación.
- **CSS3** – Diseño y estilos personalizados.
- **Bootstrap 5** – Interfaz responsive.
- **JavaScript** – Confirmación de acciones.
- **XAMPP** – Entorno de desarrollo local.

## Funcionalidades

- Registro de pacientes.
- Inicio y cierre de sesión.
- Autenticación mediante sesiones PHP.
- Consulta de especialidades médicas.
- Listado de médicos según su especialidad.
- Reserva de citas médicas.
- Consulta del historial de citas personales.
- Cancelación de citas.
- Confirmación antes de cancelar una reserva.
- Mensajes de éxito y error.
- Protección CSRF en el formulario de cancelación.
- Validación y filtrado de datos.
- Consultas preparadas para proteger frente a inyecciones SQL.

## Estructura del proyecto

```text
BookingSystem/
│
├── config/
│   └── database.php
│
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   ├── index.php
│   ├── medicos.php
│   ├── reservar.php
│   ├── registro.php
│   ├── login.php
│   ├── logout.php
│   ├── mis_citas.php
│   └── cancelar_cita.php
│
├── app/
│   ├── controllers/
│   ├── models/
│   └── views/
│
├── database/
│   └── booking_system.sql
│
└── README.md
```

## Instalación y configuración

1. Instalar [XAMPP](https://www.apachefriends.org/).
2. Clonar el repositorio dentro de la carpeta `htdocs`:

```bash
git clone URL_DEL_REPOSITORIO
```

3. Iniciar Apache y MySQL desde el panel de XAMPP.
4. Acceder a phpMyAdmin.
5. Crear una base de datos llamada `booking_system`.
6. Configurar las credenciales de conexión en `config/database.php`.
7. Abrir el proyecto desde el navegador:

```text
http://localhost/BookingSystem/public/
```

## Seguridad

El proyecto incorpora diferentes medidas de seguridad:

- Contraseñas almacenadas mediante `password_hash()`.
- Verificación de contraseñas con `password_verify()`.
- Consultas preparadas mediante PDO.
- Validación de datos recibidos desde formularios.
- Protección de rutas privadas mediante sesiones.
- Verificación de propiedad de las citas.
- Tokens CSRF para proteger la cancelación de reservas.
- Escape de datos HTML mediante `htmlspecialchars()`.

## Estado del proyecto

**Primera versión funcional (MVP).**

El proyecto se ha desarrollado como práctica de aprendizaje de desarrollo web backend con PHP y bases de datos relacionales.

## Autor

Desarrollado como proyecto personal para practicar PHP, MySQL, programación backend y desarrollo de aplicaciones web.
