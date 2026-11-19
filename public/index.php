
<?php
session_start();

require_once '../config/database.php';

$query = $pdo->query('SELECT * FROM especialidades');
$especialidades = $query->fetchAll(PDO::FETCH_ASSOC);

// Iconos según la especialidad
$iconos = [
    'Medicina general' => 'bi-clipboard2-pulse',
    'Cardiología' => 'bi-heart-pulse',
    'Dermatología' => 'bi-person-bounding-box',
    'Pediatría' => 'bi-balloon-heart',
    'Traumatología' => 'bi-bandaid',
    'Oftalmología' => 'bi-eye'
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BookingClinic | Centro médico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../public/css/style.css">
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
                            <a class="nav-link" href="mis_citas.php">
                                Citas
                            </a>
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

        <!-- HERO -->

        <section class="hero">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <span class="hero-label">
                        Tu salud es nuestra prioridad
                    </span>

                    <h1>
                        Cuida de tu salud, nosotros te ayudamos.
                    </h1>

                    <p>
                        Encuentra la especialidad médica que necesitas
                        y reserva tu próxima cita de forma sencilla
                        y rápida.
                    </p>

                </div>

                <div class="col-md-4 text-center">
                    <i class="bi bi-hospital hero-icon"></i>
                </div>

            </div>

        </section>


        <!-- ESPECIALIDADES -->

        <section class="mb-5">

            <div class="mb-4">

                <h2 class="section-title">
                    Nuestras especialidades
                </h2>

                <p class="section-subtitle">
                    Selecciona un área médica para consultar nuestros profesionales.
                </p>

            </div>


            <div class="row g-4">

                <?php if (count($especialidades) > 0) { ?>

                    <?php foreach ($especialidades as $val) { ?>

                        <div class="col-lg-4 col-md-6">

                            <div class="card especialidad-card">

                                <div class="card-body p-4">

                                    <div class="icono-especialidad">

                                        <i class="bi <?= $iconos[$val['nombre']] ?? 'bi-hospital' ?>"></i>

                                    </div>

                                    <h5>
                                        <?= htmlspecialchars($val['nombre']) ?>
                                    </h5>

                                    <p>
                                        Consulta nuestros médicos especialistas
                                        y encuentra la atención que necesitas.
                                    </p>

                                    <a class="btn btn-especialidad"
                                       href="medicos.php?id_especialidad=<?= (int)$val['id_especialidad'] ?>">

                                        Ver médicos
                                        <i class="bi bi-arrow-right ms-2"></i>

                                    </a>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="col-12">
                        <div class="alert alert-info">
                            Actualmente no hay especialidades disponibles.
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
