
<?php
session_start();

require_once '../config/database.php';

// Si ya está autenticado, volver al inicio
if (isset($_SESSION['id_paciente'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE email = ?');
    $stmt->execute([$email]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($pass, $usuario['password'])) {

        $_SESSION['id_paciente'] = $usuario['id_paciente'];
        $_SESSION['nombre'] = $usuario['nombre'];

        header('Location: index.php');
        exit;

    } else {

        $error = "El usuario no existe o la contraseña es incorrecta";

    }

}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | BookingClinic</title>

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
                <a href="registro.php" class="nav-link">
                    Crear cuenta
                </a>
            </div>

        </div>

    </nav>


    <!-- LOGIN -->

    <main class="container">

        <div class="login-contenedor">

            <div class="card login-card">

                <div class="card-body p-4 p-md-5">

                    <div class="login-icono">
                        <i class="bi bi-person-circle"></i>
                    </div>

                    <h2 class="text-center mb-2">
                        Bienvenido de nuevo
                    </h2>

                    <p class="text-center login-subtitulo mb-4">
                        Accede a tu cuenta para gestionar tus citas médicas.
                    </p>


                    <?php if (!empty($error)) { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>

                    <?php } ?>


                    <form action="login.php" method="POST">

                        <div class="mb-3">

                            <label for="email" class="form-label">
                                Correo electrónico
                            </label>

                            <input
                                type="email"
                                class="form-control campo-reserva"
                                id="email"
                                name="email"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                placeholder="ejemplo@correo.com"
                                required
                            >

                        </div>


                        <div class="mb-4">

                            <label for="password" class="form-label">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control campo-reserva"
                                id="password"
                                name="password"
                                placeholder="Introduce tu contraseña"
                                required
                            >

                        </div>


                        <button type="submit" class="btn btn-especialidad w-100">

                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Iniciar sesión

                        </button>

                    </form>


                    <div class="login-registro text-center mt-4">

                        ¿Todavía no tienes cuenta?

                        <a href="registro.php">
                            Regístrate aquí
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
