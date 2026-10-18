
<?php

session_start();

// Restringir el acceso a los administradores.
$rol_requerido = 'administrador';
require_once __DIR__ . '/verificar_rol.php';

// Conectar con la base de datos.
require_once __DIR__ . '/../config/database.php';

// Permitir únicamente solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: estudiantes.php');
    exit;
}

// Obtener los datos enviados desde el formulario.
$id_estudiante = filter_input(
    INPUT_POST,
    'id_estudiante',
    FILTER_VALIDATE_INT
);

$activo = filter_input(
    INPUT_POST,
    'activo',
    FILTER_VALIDATE_INT
);

// Validar los datos recibidos.
if (
    !$id_estudiante ||
    $id_estudiante < 1 ||
    !in_array($activo, [0, 1], true)
) {
    $_SESSION['mensaje_estado'] = 'Los datos enviados no son válidos.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: estudiantes.php');
    exit;
}

try {
    // Iniciar una transacción para proteger la actualización.
    $pdo->beginTransaction();

    // Consultar y bloquear el estudiante seleccionado.
    $consulta = $pdo->prepare(
        'SELECT id_estudiante, activo
         FROM estudiantes
         WHERE id_estudiante = :id_estudiante
         FOR UPDATE'
    );

    $consulta->execute([
        ':id_estudiante' => $id_estudiante
    ]);

    $estudiante = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        $pdo->rollBack();

        $_SESSION['mensaje_estado'] = 'El estudiante seleccionado no existe.';
        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: estudiantes.php');
        exit;
    }

    // Verificar si el estado solicitado ya está aplicado.
    if ((int) $estudiante['activo'] === $activo) {
        $pdo->rollBack();

        $_SESSION['mensaje_estado'] = $activo === 1
            ? 'El estudiante ya se encuentra activo.'
            : 'El estudiante ya se encuentra desactivado.';

        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: estudiantes.php');
        exit;
    }

    // Actualizar únicamente el estado del estudiante.
    $actualizacion = $pdo->prepare(
        'UPDATE estudiantes
         SET activo = :activo
         WHERE id_estudiante = :id_estudiante'
    );

    $actualizacion->execute([
        ':activo' => $activo,
        ':id_estudiante' => $id_estudiante
    ]);

    // Confirmar la operación.
    $pdo->commit();

    $_SESSION['mensaje_estado'] = $activo === 1
        ? 'El estudiante se activó correctamente.'
        : 'El estudiante se desactivó correctamente.';

    $_SESSION['tipo_mensaje_estado'] = 'exito';

} catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'Error al cambiar el estado del estudiante en RedVital: ' .
        $e->getMessage()
    );

    $_SESSION['mensaje_estado'] =
        'No fue posible cambiar el estado del estudiante. Inténtalo nuevamente.';

    $_SESSION['tipo_mensaje_estado'] = 'error';
}

// Regresar al listado de estudiantes.
header('Location: estudiantes.php');
exit;
