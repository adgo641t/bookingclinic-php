<?php

session_start();

require_once '../config/database.php';

//comprobar que el usuario esta loggeado

if(!isset($_SESSION['id_paciente'])) {
    header('Location: login.php');
    exit;
}

if(!isset($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header('Location: mis_citas.php');
    }

//Comprobar que es POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mis_citas.php');
    exit;
}

$id_cita = filter_input(INPUT_POST,'id_cita',FILTER_VALIDATE_INT);

if($id_cita === false || $id_cita === null) {
    header('Location: mis_citas.php');
    exit;
}

$stmt = $pdo->prepare('
    UPDATE citas
    SET estado = \'cancelada\'  
    WHERE id_cita = ?
    AND id_paciente = ?
    AND estado = \'pendiente\'
');

$stmt->execute([$id_cita, $_SESSION['id_paciente']]);

if($stmt->rowCount() == 1) {
    $_SESSION['tipo_mensaje'] = "success";
    $_SESSION['mensaje'] = "La cita se cancelo correctamente";
} else {
    $_SESSION['tipo_mensaje'] = "warning";
    $_SESSION['mensaje'] = "No se pudo cancelar la cita";
}

header('Location: mis_citas.php');
exit;