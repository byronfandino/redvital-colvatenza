
<?php

// Restringir el acceso a los administradores.
$rol_requerido = 'administrador';
require_once __DIR__ . '/verificar_rol.php';

// Conectar con la base de datos.
require_once __DIR__ . '/../config/database.php';

// Permitir únicamente solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: brigadas.php');
    exit;
}

// Obtener los datos enviados desde el formulario.
$id_brigada = filter_input(
    INPUT_POST,
    'id_brigada',
    FILTER_VALIDATE_INT
);

$activo = filter_input(
    INPUT_POST,
    'activo',
    FILTER_VALIDATE_INT
);

// Validar los datos recibidos.
if (
    !$id_brigada ||
    $id_brigada < 1 ||
    !in_array($activo, [0, 1], true)
) {
    $_SESSION['mensaje_estado'] = 'Los datos enviados no son válidos.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: brigadas.php');
    exit;
}

try {
    // Iniciar una transacción para proteger la actualización.
    $pdo->beginTransaction();

    // Consultar y bloquear la brigada seleccionada.
    $consulta = $pdo->prepare(
        'SELECT id_brigada, activo
         FROM brigadas
         WHERE id_brigada = :id_brigada
         FOR UPDATE'
    );

    $consulta->execute([
        ':id_brigada' => $id_brigada
    ]);

    $brigada = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$brigada) {
        $pdo->rollBack();

        $_SESSION['mensaje_estado'] = 'La brigada seleccionada no existe.';
        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: brigadas.php');
        exit;
    }

    // Verificar si el estado solicitado ya está aplicado.
    if ((int) $brigada['activo'] === $activo) {
        $pdo->rollBack();

        $_SESSION['mensaje_estado'] = $activo === 1
            ? 'La brigada ya se encuentra activa.'
            : 'La brigada ya se encuentra desactivada.';

        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: brigadas.php');
        exit;
    }

    // Actualizar el estado de la brigada.
    $actualizacion = $pdo->prepare(
        'UPDATE brigadas
         SET activo = :activo,
             actualizado_por = :actualizado_por
         WHERE id_brigada = :id_brigada'
    );

    $actualizacion->execute([
        ':activo' => $activo,
        ':actualizado_por' => $_SESSION['id_usuario'],
        ':id_brigada' => $id_brigada
    ]);

    // Confirmar la operación.
    $pdo->commit();

    $_SESSION['mensaje_estado'] = $activo === 1
        ? 'La brigada se activó correctamente.'
        : 'La brigada se desactivó correctamente.';

    $_SESSION['tipo_mensaje_estado'] = 'exito';

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'Error al cambiar el estado de la brigada en RedVital: ' .
        $e->getMessage()
    );

    $_SESSION['mensaje_estado'] =
        'No fue posible cambiar el estado de la brigada. Inténtalo nuevamente.';

    $_SESSION['tipo_mensaje_estado'] = 'error';
}

// Regresar al listado de brigadas.
header('Location: brigadas.php');
exit;
