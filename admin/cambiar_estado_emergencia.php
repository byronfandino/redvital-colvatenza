
<?php

session_start();

$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Validar que la solicitud se envíe mediante POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: emergencias.php');
    exit;
}

// Obtener y validar los datos recibidos.
$id_emergencia = filter_input(
    INPUT_POST,
    'id_emergencia',
    FILTER_VALIDATE_INT
);

$activo = filter_input(
    INPUT_POST,
    'activo',
    FILTER_VALIDATE_INT
);

if (
    !$id_emergencia ||
    !in_array($activo, [0, 1], true)
) {
    $_SESSION['mensaje_estado'] = 'Los datos enviados no son válidos.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: emergencias.php');
    exit;
}

try {
    // Actualizar el estado y registrar quién realizó el cambio.
    $sql = "
        UPDATE emergencia
        SET
            activo = :activo,
            actualizado_por = :actualizado_por,
            actualizado_en = CURRENT_TIMESTAMP
        WHERE id_emergencia = :id_emergencia
    ";

    $consulta = $pdo->prepare($sql);

    $consulta->execute([
        'activo' => $activo,
        'actualizado_por' => $_SESSION['id_usuario'],
        'id_emergencia' => $id_emergencia
    ]);

    if ($consulta->rowCount() > 0) {
        $_SESSION['mensaje_estado'] = $activo === 1
            ? 'La emergencia fue activada correctamente.'
            : 'La emergencia fue desactivada correctamente.';

        $_SESSION['tipo_mensaje_estado'] = 'exito';
    } else {
        // Verificar si el registro existe cuando no hubo cambios.
        $verificar = $pdo->prepare(
            'SELECT id_emergencia, activo
             FROM emergencia
             WHERE id_emergencia = :id_emergencia'
        );

        $verificar->execute([
            'id_emergencia' => $id_emergencia
        ]);

        $registro = $verificar->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            $_SESSION['mensaje_estado'] =
                'No se encontró la emergencia solicitada.';
            $_SESSION['tipo_mensaje_estado'] = 'error';
        } elseif ((int) $registro['activo'] === $activo) {
            $_SESSION['mensaje_estado'] =
                'La emergencia ya tenía el estado solicitado.';
            $_SESSION['tipo_mensaje_estado'] = 'exito';
        } else {
            $_SESSION['mensaje_estado'] =
                'No fue posible actualizar el estado de la emergencia.';
            $_SESSION['tipo_mensaje_estado'] = 'error';
        }
    }

} catch (PDOException $e) {
    error_log(
        'Error al cambiar el estado de una emergencia en RedVital Colvatenza: '
        . $e->getMessage()
    );

    $_SESSION['mensaje_estado'] =
        'Ocurrió un error al actualizar el estado de la emergencia.';
    $_SESSION['tipo_mensaje_estado'] = 'error';
}

header('Location: emergencias.php');
exit;
