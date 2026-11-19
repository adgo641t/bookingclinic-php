<?php
require_once '../config/database.php';

session_start();

if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if (!isset($_SESSION['id_paciente'])) {
    header('Location: login.php');
    exit;
}

$mensaje = $_SESSION['mensaje'] ?? null;
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? null;

unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);

$stmt = $pdo->prepare('
    SELECT 
        c.id_cita,
        c.fecha,
        c.hora,
        c.estado,
        m.nombre,
        m.apellidos,
        e.nombre AS especialidad
    FROM citas c
    INNER JOIN medicos m
        ON m.id_medico = c.id_medico
    INNER JOIN especialidades e
        ON e.id_especialidad = m.id_especialidad
    WHERE c.id_paciente = ?
    ORDER BY c.fecha DESC, c.hora DESC
');

$stmt->execute([$_SESSION['id_paciente']]);

$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mis citas | BookingClinic</title>

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

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarContenido">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarContenido">

                <ul class="navbar-nav ms-auto align-items-lg-center">

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

                </ul>

            </div>

        </div>

    </nav>


    <!-- CONTENIDO -->

    <main class="container">

        <section class="cabecera-medicos">

            <span class="hero-label">Área del paciente</span>

            <h1>Mis citas médicas</h1>

            <p>
                Consulta y gestiona tus próximas citas médicas
                desde un único lugar.
            </p>

        </section>
        <?php if ($mensaje): ?>
            <div class="alert alert-<?= $tipo_mensaje ?>">
                <strong><?= $mensaje ?> </strong>     
            </div>
        <?php endif; ?>
        <section class="mb-5">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

                <div>
                    <h3 class="section-title mb-1">Historial de citas</h3>

                    <p class="section-subtitle mb-0">
                        Aquí encontrarás todas tus reservas.
                    </p>
                </div>

                <a href="index.php" class="btn btn-especialidad">
                    <i class="bi bi-plus-lg me-2"></i>
                    Nueva cita
                </a>

            </div>


            <?php if (count($citas) > 0) { ?>

                <div class="row g-4">

                    <?php foreach ($citas as $cita) { ?>

                        <div class="col-lg-6">

                            <div class="card cita-card h-100">

                                <div class="card-body p-4">

                                    <div class="d-flex justify-content-between align-items-start mb-3">

                                        <div class="icono-medico">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>

                                        <?php

                                        $estado = strtolower($cita['estado']);

                                        $claseEstado = match($estado) {
                                            'pendiente' => 'bg-warning-subtle text-warning-emphasis',
                                            'confirmada' => 'bg-success-subtle text-success-emphasis',
                                            'cancelada' => 'bg-danger-subtle text-danger-emphasis',
                                            default => 'bg-secondary-subtle text-secondary-emphasis'
                                        };

                                        ?>

                                        <span class="badge <?= $claseEstado ?>">
                                            <?= htmlspecialchars(ucfirst($estado)) ?>
                                        </span>

                                    </div>


                                    <h5 class="mb-1">
                                        <?= htmlspecialchars($cita['nombre'] . ' ' . $cita['apellidos']) ?>
                                    </h5>

                                    <p class="medico-especialidad">
                                        <i class="bi bi-hospital me-2"></i>
                                        <?= htmlspecialchars($cita['especialidad']) ?>
                                    </p>


                                    <div class="cita-datos mt-4">

                                        <p>
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <?= date('d/m/Y', strtotime($cita['fecha'])) ?>
                                        </p>

                                        <p>
                                            <i class="bi bi-clock me-2"></i>
                                            <?= htmlspecialchars(substr($cita['hora'], 0, 5)) ?>
                                        </p>

                                    </div>


                                    <?php if ($estado === 'pendiente') { ?>

                                        <div class="mt-3">
                                            <!--cancelar citas-->
                                            <form action="cancelar_cita.php" method="post">
                                                <!--id del paciente-->
                                                <input type="hidden" name="id_cita" value="<?= (int)$cita['id_cita']?>">
                                                <!--CSRF-->
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                <button onclick="return confirm('Seguro que quieres borrarlo?')" class="btn btn-outline-danger btn-sm">
                                                    Cancelar cita
                                                </button>
                                            </form>
                                        
                                        </div>

                                    <?php } ?>

                                </div>

                            </div>

                        </div>

                    <?php } ?>

                </div>

            <?php } else { ?>

                <div class="sin-citas text-center">

                    <i class="bi bi-calendar-x"></i>

                    <h4>No tienes citas todavía</h4>

                    <p>
                        Cuando reserves una consulta médica,
                        aparecerá aquí.
                    </p>

                    <a href="index.php" class="btn btn-especialidad">
                        Reservar una cita
                    </a>

                </div>

            <?php } ?>

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
