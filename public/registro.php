
<?php
session_start();

require_once '../config/database.php';

// Si ya ha iniciado sesión, volver al inicio
if (isset($_SESSION['id_paciente'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $contraseña = $_POST['contraseña'];

    // Validar campos
    if (
        empty($nombre) ||
        empty($apellidos) ||
        empty($telefono) ||
        empty($email) ||
        empty($contraseña)
    ) {

        $error = "Todos los campos son obligatorios.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Introduce un correo electrónico válido.";

    } elseif (strlen($contraseña) < 8) {

        $error = "La contraseña debe tener al menos 8 caracteres.";

    } else {

        // Encriptar contraseña
        $ps_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        try {

            // Guardar usuario en BD
            $stmt = $pdo->prepare("
                INSERT INTO pacientes
                (nombre, apellidos, telefono, email, password)
                VALUES (?, ?, ?, ?, ?)
            ");

            $check = $stmt->execute([
                $nombre,
                $apellidos,
                $telefono,
                $email,
                $ps_hash
            ]);

            if ($check) {
                $exito = "¡Tu cuenta se ha creado correctamente! Ya puedes iniciar sesión.";
            }

        } catch (PDOException $e) {

            // Comprobar si el email ya existe
            if ($e->getCode() === '23000') {
                $error = "Este correo electrónico ya está registrado.";
            } else {
                $error = "No se ha podido completar el registro. Inténtalo más tarde.";
            }

        }

    }

}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear cuenta | BookingClinic</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar navbar-expand-lg">

        <div class="container">

            <a class="navbar-brand" href="index.php">
                <i class="bi bi-heart-pulse-fill me-2"></i>
                BookingClinic
            </a>

            <div class="ms-auto">

                <a href="login.php" class="nav-link">
                    Iniciar sesión
                </a>

            </div>

        </div>

    </nav>


    <!-- REGISTRO -->

    <main class="container">

        <div class="registro-contenedor">

            <div class="card registro-card">

                <div class="card-body p-4 p-md-5">

                    <div class="login-icono">
                        <i class="bi bi-person-plus"></i>
                    </div>

                    <h2 class="text-center mb-2">
                        Crea tu cuenta
                    </h2>

                    <p class="text-center login-subtitulo mb-4">
                        Regístrate para poder reservar tus citas médicas.
                    </p>


                    <?php if (!empty($error)) { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>

                    <?php } ?>


                    <?php if (!empty($exito)) { ?>

                        <div class="alert alert-success">

                            <?= htmlspecialchars($exito) ?>

                            <div class="mt-2">
                                <a href="login.php" class="alert-link">
                                    Iniciar sesión
                                </a>
                            </div>

                        </div>

                    <?php } ?>


                    <?php if (empty($exito)) { ?>

                        <form action="registro.php" method="POST">

                            <div class="mb-3">

                                <label for="nombre" class="form-label">
                                    Nombre
                                </label>

                                <input
                                    class="form-control campo-reserva"
                                    type="text"
                                    name="nombre"
                                    id="nombre"
                                    required
                                    value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>"
                                    placeholder="Tu nombre"
                                >

                            </div>


                            <div class="mb-3">

                                <label for="apellidos" class="form-label">
                                    Apellidos
                                </label>

                                <input
                                    class="form-control campo-reserva"
                                    type="text"
                                    name="apellidos"
                                    id="apellidos"
                                    required
                                    value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>"
                                    placeholder="Tus apellidos"
                                >

                            </div>


                            <div class="mb-3">

                                <label for="telefono" class="form-label">
                                    Teléfono
                                </label>

                                <input
                                    class="form-control campo-reserva"
                                    type="tel"
                                    name="telefono"
                                    id="telefono"
                                    required
                                    value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                                    placeholder="600 000 000"
                                >

                            </div>


                            <div class="mb-3">

                                <label for="email" class="form-label">
                                    Correo electrónico
                                </label>

                                <input
                                    class="form-control campo-reserva"
                                    type="email"
                                    name="email"
                                    id="email"
                                    required
                                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                    placeholder="ejemplo@correo.com"
                                >

                            </div>


                            <div class="mb-4">

                                <label for="contraseña" class="form-label">
                                    Contraseña
                                </label>

                                <input
                                    class="form-control campo-reserva"
                                    type="password"
                                    name="contraseña"
                                    id="contraseña"
                                    required
                                    minlength="8"
                                    placeholder="Mínimo 8 caracteres"
                                >

                            </div>


                            <button class="btn btn-especialidad w-100" type="submit">

                                <i class="bi bi-person-check me-2"></i>
                                Crear cuenta

                            </button>

                        </form>

                    <?php } ?>


                    <div class="login-registro text-center mt-4">

                        ¿Ya tienes una cuenta?

                        <a href="login.php">
                            Inicia sesión

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </main>
    <!-- FOOTER -->
    <footer>
        <div class="container text-center">
            <h5 class="mb-2">
                <i class="bi bi-heart-pulse-fill me-2"></i>
                BookingClinic
            </h5>
            <p class="mb-0 small">
                Tu salud, nuestra prioridad.
            </p>
        </div>
    </footer>
</body>
</html>
