
<?php
session_start();

require_once '../config/database.php';

// Comprobar sesión
if (!isset($_SESSION['id_paciente'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id_medico'] ?? $_POST['id_medico'] ?? null;

// Obtener médico
$stmt = $pdo->prepare("
    SELECT m.*, e.nombre AS especialidad
    FROM medicos m
    INNER JOIN especialidades e
    ON m.id_especialidad = e.id_especialidad
    WHERE m.id_medico = ?
");

$stmt->execute([$id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$res) {
    header('Location: index.php');
    exit;
}

// Variables para mensajes
$mensajeReserva = '';
$tipoMensaje = '';


// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idMedico = $_POST['id_medico'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $mensaje = $_POST['descripcion'];
    $id_paciente = $_SESSION['id_paciente'];

    if (empty($fecha) || empty($hora) || $fecha < date("Y-m-d")) {

        $mensajeReserva = "Debes indicar una fecha válida y una hora.";
        $tipoMensaje = "danger";

    } else {

        // Comprobar disponibilidad
        $stmt = $pdo->prepare("
            SELECT * FROM citas
            WHERE id_medico = ?
            AND fecha = ?
            AND hora = ?
        ");

        $stmt->execute([$idMedico, $fecha, $hora]);
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($check) {

            $mensajeReserva = "Ya hay una cita a esta hora. Prueba otra diferente.";
            $tipoMensaje = "warning";

        } else {

            try {

                // Insertar cita
                $stmt = $pdo->prepare("
                    INSERT INTO citas
                    (id_medico, id_paciente, fecha, hora, estado)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $check = $stmt->execute([
                    $idMedico,
                    $id_paciente,
                    $fecha,
                    $hora,
                    "pendiente"
                ]);

                if ($check) {
                    $mensajeReserva = "¡Tu cita previa ha sido reservada correctamente!";
                    $tipoMensaje = "success";
                }

            } catch (PDOException $e) {

                $mensajeReserva = "No ha sido posible completar la reserva. Prueba otro horario.";
                $tipoMensaje = "danger";

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

    <title>Reservar cita | BookingClinic</title>

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


    <main class="container">

        <!-- CABECERA -->

        <section class="cabecera-medicos">

            <a href="medicos.php?id_especialidad=<?= (int)$res['id_especialidad'] ?>"
               class="volver-link">

                <i class="bi bi-arrow-left"></i>
                Volver a médicos

            </a>

            <span class="hero-label">
                Reserva online
            </span>

            <h1>Reserva tu cita médica</h1>

            <p>
                Selecciona el día y la hora que mejor se adapten a ti
                para solicitar tu próxima consulta.
            </p>

        </section>


        <!-- CONTENIDO -->

        <div class="row g-4 mb-5">

            <!-- FICHA DEL MÉDICO -->

            <div class="col-lg-4">

                <div class="card medico-card h-100">

                    <div class="card-body p-4">

                        <div class="icono-medico">
                            <i class="bi bi-person-badge"></i>
                        </div>

                        <span class="hero-label">
                            Profesional seleccionado
                        </span>

                        <h4 class="card-title">
                            <?= htmlspecialchars($res['nombre'] . ' ' . $res['apellidos']) ?>
                        </h4>

                        <p class="medico-especialidad">
                            <i class="bi bi-hospital me-2"></i>
                            <?= htmlspecialchars($res['especialidad']) ?>
                        </p>

                        <div class="medico-datos">

                            <p>
                                <i class="bi bi-telephone me-2"></i>
                                <?= htmlspecialchars($res['telefono']) ?>
                            </p>

                            <p>
                                <i class="bi bi-envelope me-2"></i>
                                <?= htmlspecialchars($res['email']) ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- FORMULARIO -->

            <div class="col-lg-8">

                <div class="card formulario-reserva">

                    <div class="card-body p-4 p-md-5">

                        <h3 class="section-title mb-2">
                            Datos de la consulta
                        </h3>

                        <p class="section-subtitle mb-4">
                            Completa los siguientes campos para solicitar tu cita.
                        </p>

                        <?php if (!empty($mensajeReserva)) { ?>

                            <div class="alert alert-<?= $tipoMensaje ?>">
                                <?= htmlspecialchars($mensajeReserva) ?>
                            </div>

                        <?php } ?>


                        <form action="reservar.php" method="POST">

                            <input type="hidden"
                                   name="id_medico"
                                   value="<?= (int)$id ?>">

                            <div class="mb-4">

                                <label for="fecha" class="form-label">
                                    Fecha de la consulta
                                </label>

                                <input
                                    class="form-control campo-reserva"
                                    required
                                    type="date"
                                    name="fecha"
                                    id="fecha"
                                    min="<?= date('Y-m-d') ?>"
                                    value="<?= htmlspecialchars($_POST['fecha'] ?? '') ?>"
                                >

                            </div>


                            <div class="mb-4">

                                <label for="hora" class="form-label">
                                    Hora de la consulta
                                </label>

                                <select
                                    required
                                    class="form-select campo-reserva"
                                    name="hora"
                                    id="hora">

                                    <option value="" disabled
                                        <?= empty($_POST['hora']) ? 'selected' : '' ?>>
                                        Selecciona una hora
                                    </option>

                                    <?php foreach (['09:00', '10:00', '11:00'] as $h) { ?>

                                        <option value="<?= $h ?>"
                                            <?= ($_POST['hora'] ?? '') === $h ? 'selected' : '' ?>>

                                            <?= $h ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>


                            <div class="mb-4">

                                <label for="descripcion" class="form-label">
                                    Motivo de la consulta
                                </label>

                                <textarea
                                    class="form-control campo-reserva"
                                    required
                                    name="descripcion"
                                    id="descripcion"
                                    rows="4"
                                    placeholder="Describe brevemente el motivo de tu consulta..."
                                ><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>

                            </div>


                            <button class="btn btn-especialidad w-100" type="submit">

                                <i class="bi bi-calendar-check me-2"></i>
                                Confirmar reserva

                            </button>

                        </form>

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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
