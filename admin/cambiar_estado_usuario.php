<?php

// Restringir el acceso a los administradores.
$rol_requerido = 'administrador';
require_once __DIR__ . '/verificar_rol.php';

// Conectar con la base de datos.
require_once __DIR__ . '/../config/database.php';

// Permitir únicamente solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios.php');
    exit;
}

// Obtener los datos enviados desde el formulario.
$id_usuario = filter_input(
    INPUT_POST,
    'id_usuario',
    FILTER_VALIDATE_INT
);

$activo = filter_input(
    INPUT_POST,
    'activo',
    FILTER_VALIDATE_INT
);

// Validar los datos recibidos.
if (
    !$id_usuario ||
    $id_usuario < 1 ||
    !in_array($activo, [0, 1], true)
) {
    $_SESSION['mensaje_estado'] = 'Los datos enviados no son válidos.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: usuarios.php');
    exit;
}

// Impedir que el administrador desactive su propia cuenta.
if (
    $activo === 0 &&
    $id_usuario === (int) $_SESSION['id_usuario']
) {
    $_SESSION['mensaje_estado'] = 'No puedes desactivar tu propia cuenta.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: usuarios.php');
    exit;
}

try {
    // Iniciar una transacción para proteger la actualización.
    $pdo->beginTransaction();

    // Consultar y bloquear la cuenta seleccionada.
    $consulta = $pdo->prepare(
        'SELECT id_usuario, rol, activo
         FROM usuarios
         WHERE id_usuario = :id_usuario
         FOR UPDATE'
    );

    $consulta->execute([
        ':id_usuario' => $id_usuario
    ]);

    $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $pdo->rollBack();

        $_SESSION['mensaje_estado'] = 'El usuario seleccionado no existe.';
        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: usuarios.php');
        exit;
    }

    // Verificar si el estado solicitado ya está aplicado.
    if ((int) $usuario['activo'] === $activo) {
        $pdo->rollBack();

        $_SESSION['mensaje_estado'] = $activo === 1
            ? 'La cuenta ya se encuentra activa.'
            : 'La cuenta ya se encuentra desactivada.';

        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: usuarios.php');
        exit;
    }

    // Evitar desactivar al último administrador activo.
    if (
        $activo === 0 &&
        $usuario['rol'] === 'administrador' &&
        (int) $usuario['activo'] === 1
    ) {
        $consulta = $pdo->query(
            "SELECT COUNT(*)
             FROM usuarios
             WHERE rol = 'administrador'
               AND activo = 1
             FOR UPDATE"
        );

        $administradores_activos = (int) $consulta->fetchColumn();

        if ($administradores_activos <= 1) {
            $pdo->rollBack();

            $_SESSION['mensaje_estado'] =
                'No puedes desactivar al último administrador activo.';

            $_SESSION['tipo_mensaje_estado'] = 'error';

            header('Location: usuarios.php');
            exit;
        }
    }

    // Actualizar el estado de la cuenta.
    $actualizacion = $pdo->prepare(
        'UPDATE usuarios
         SET activo = :activo
         WHERE id_usuario = :id_usuario'
    );

    $actualizacion->execute([
        ':activo' => $activo,
        ':id_usuario' => $id_usuario
    ]);

    // Confirmar la operación.
    $pdo->commit();

    $_SESSION['mensaje_estado'] = $activo === 1
        ? 'El usuario se activó correctamente.'
        : 'El usuario se desactivó correctamente.';

    $_SESSION['tipo_mensaje_estado'] = 'exito';

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log(
        'Error al cambiar el estado del usuario en RedVital: ' .
        $e->getMessage()
    );

    $_SESSION['mensaje_estado'] =
        'No fue posible cambiar el estado del usuario. Inténtalo nuevamente.';

    $_SESSION['tipo_mensaje_estado'] = 'error';
}

// Regresar al listado de usuarios.
header('Location: usuarios.php');
exit;
