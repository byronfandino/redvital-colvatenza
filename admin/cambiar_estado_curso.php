
<?php

// Iniciar y validar la sesión mediante la protección de roles.
$rol_requerido = 'administrador';
require_once __DIR__ . '/verificar_rol.php';

// Conectar con la base de datos.
require_once __DIR__ . '/../config/database.php';

// Permitir únicamente solicitudes POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['mensaje_estado'] = 'La solicitud no es válida.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: cursos.php');
    exit;
}

// Obtener y validar los datos recibidos.
$id_curso = filter_input(
    INPUT_POST,
    'id_curso',
    FILTER_VALIDATE_INT
);

$activo = filter_input(
    INPUT_POST,
    'activo',
    FILTER_VALIDATE_INT
);

$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

// Validar el identificador del curso.
if ($id_curso === false || $id_curso === null || $id_curso <= 0) {
    $_SESSION['mensaje_estado'] = 'El identificador del curso no es válido.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: cursos.php');
    exit;
}

// Validar el estado solicitado.
if (
    $activo === false ||
    $activo === null ||
    !in_array($activo, [0, 1], true)
) {
    $_SESSION['mensaje_estado'] = 'El estado solicitado no es válido.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: cursos.php');
    exit;
}

// Validar el usuario autenticado.
if ($id_usuario <= 0) {
    $_SESSION['mensaje_estado'] = 'No fue posible identificar al usuario autenticado.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: cursos.php');
    exit;
}

try {
    // Comprobar que el curso exista.
    $sql = 'SELECT id_curso, activo
            FROM cursos
            WHERE id_curso = :id_curso
            LIMIT 1';

    $consulta = $pdo->prepare($sql);
    $consulta->execute([
        'id_curso' => $id_curso
    ]);

    $curso = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        $_SESSION['mensaje_estado'] = 'El curso que intentas modificar no existe.';
        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: cursos.php');
        exit;
    }

    // Evitar actualizar si el curso ya tiene el estado solicitado.
    if ((int) $curso['activo'] === $activo) {
        $_SESSION['mensaje_estado'] = $activo === 1
            ? 'El curso ya se encuentra activo.'
            : 'El curso ya se encuentra desactivado.';

        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: cursos.php');
        exit;
    }

    // Actualizar el estado y registrar quién realizó el cambio.
    $sql = 'UPDATE cursos
            SET activo = :activo,
                actualizado_por = :actualizado_por
            WHERE id_curso = :id_curso';

    $consulta = $pdo->prepare($sql);
    $consulta->execute([
        'activo' => $activo,
        'actualizado_por' => $id_usuario,
        'id_curso' => $id_curso
    ]);

    // Preparar la notificación de confirmación.
    $_SESSION['mensaje_estado'] = $activo === 1
        ? 'El curso se activó correctamente.'
        : 'El curso se desactivó correctamente.';

    $_SESSION['tipo_mensaje_estado'] = 'exito';

} catch (PDOException $e) {
    error_log(
        'Error al cambiar el estado del curso en RedVital Colvatenza: '
        . $e->getMessage()
    );

    $_SESSION['mensaje_estado'] = 'No fue posible cambiar el estado del curso. Inténtalo nuevamente.';
    $_SESSION['tipo_mensaje_estado'] = 'error';
}

// Regresar al listado de cursos.
header('Location: cursos.php');
exit;
