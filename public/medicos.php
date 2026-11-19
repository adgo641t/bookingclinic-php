
<?php
session_start();

require_once '../config/database.php';

$idEspecialidad = filter_input(INPUT_GET, 'id_especialidad', FILTER_VALIDATE_INT);

if (!$idEspecialidad) {
    header('Location: index.php');
    exit;
}

// Obtener especialidad
$stmt = $pdo->prepare("SELECT * FROM especialidades WHERE id_especialidad = ?");
$stmt->execute([$idEspecialidad]);
$especialidad = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$especialidad) {
    header('Location: index.php');
    exit;
}

// Obtener médicos de esa especialidad
$stmt = $pdo->prepare("
    SELECT * FROM medicos
    WHERE id_especialidad = ?
    ORDER BY apellidos, nombre
");

$stmt->execute([$idEspecialidad]);
$medicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Médicos | BookingClinic</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">

            <a class="navbar-brand" href="index.php">
                <i class="bi bi-heart-pulse-fill me-2"></i>
                BookingClinic
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarContenido">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarContenido">

                <ul class="navbar-nav ms-auto align-items-lg-center">

                    <?php if (isset($_SESSION['id_paciente'])) { ?>

                        <li class="nav-item">
                            <span class="nav-link">
                                Hola, <?= htmlspecialchars($_SESSION['nombre']) ?>
                            </span>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                Cerrar sesión
                            </a>
                        </li>

                    <?php } else { ?>

                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                Iniciar sesión
                            </a>
                        </li>

                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-especialidad" href="registro.php">
                                Crear cuenta
                            </a>
                        </li>

                    <?php } ?>

                </ul>

            </div>
        </div>
    </nav>


    <main class="container">

        <!-- CABECERA -->

        <section class="cabecera-medicos">

            <a href="index.php" class="volver-link">
                <i class="bi bi-arrow-left"></i>
                Volver a especialidades
            </a>

            <span class="hero-label">
                Nuestro equipo médico
            </span>

            <h1>
                <?= htmlspecialchars($especialidad['nombre']) ?>
            </h1>

            <p>
                Consulta los profesionales disponibles en esta especialidad
                y reserva una cita con el médico que prefieras.
            </p>

        </section>


        <!-- LISTADO DE MÉDICOS -->

        <section class="mb-5">

            <div class="mb-4">

                <h2 class="section-title">
                    Nuestros profesionales
                </h2>

                <p class="section-subtitle">
                    <?= count($medicos) ?> profesionales disponibles
                </p>

            </div>


            <div class="row g-4">

                <?php if (count($medicos) > 0) { ?>

                    <?php foreach ($medicos as $medico) { ?>

                        <div class="col-lg-4 col-md-6">

                            <div class="card medico-card h-100">

                                <div class="card-body p-4">

                                    <div class="icono-medico">
                                        <i class="bi bi-person-badge"></i>
                                    </div>

                                    <h5 class="card-title">
                                        Dr./Dra.
                                        <?= htmlspecialchars($medico['nombre'] . ' ' . $medico['apellidos']) ?>
                                    </h5>

                                    <p class="medico-especialidad">
                                        <i class="bi bi-hospital me-2"></i>
                                        <?= htmlspecialchars($especialidad['nombre']) ?>
                                    </p>

                                    <div class="medico-datos">

                                        <p>
                                            <i class="bi bi-telephone me-2"></i>
                                            <?= htmlspecialchars($medico['telefono']) ?>
                                        </p>

                                        <p>
                                            <i class="bi bi-envelope me-2"></i>
                                            <?= htmlspecialchars($medico['email']) ?>
                                        </p>

                                    </div>

                                    <a class="btn btn-especialidad w-100 mt-3"
                                       href="reservar.php?id_medico=<?= (int)$medico['id_medico'] ?>">

                                        Reservar cita
                                        <i class="bi bi-arrow-right ms-2"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="col-12">

                        <div class="alert alert-info">
                            Actualmente no hay médicos disponibles
                            para esta especialidad.
                        </div>

                    </div>

                <?php } ?>

            </div>

        </section>

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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
