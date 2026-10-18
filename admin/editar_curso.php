<?php

$pagina_activa = 'cursos'; 

// Restringir el acceso a los administradores.
$rol_requerido = 'administrador';
require_once __DIR__ . '/verificar_rol.php';

// Conectar con la base de datos.
require_once __DIR__ . '/../config/database.php';

// Obtener y validar el identificador del curso.
$id_curso = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id_curso === false || $id_curso === null || $id_curso <= 0) {
    $_SESSION['mensaje_estado'] = 'El identificador del curso no es válido.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: cursos.php');
    exit;
}

// Inicializar variables del formulario.
$grado = '';
$grupo = '';
$mensaje_error = '';

// Consultar el curso seleccionado.
try {
    $sql = 'SELECT id_curso, grado, grupo
            FROM cursos
            WHERE id_curso = :id_curso
            LIMIT 1';

    $consulta = $pdo->prepare($sql);
    $consulta->execute([
        'id_curso' => $id_curso
    ]);

    $curso = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$curso) {
        $_SESSION['mensaje_estado'] = 'El curso que intentas editar no existe.';
        $_SESSION['tipo_mensaje_estado'] = 'error';

        header('Location: cursos.php');
        exit;
    }

    // Cargar los datos actuales del curso.
    $grado = (string) $curso['grado'];
    $grupo = (string) $curso['grupo'];

} catch (PDOException $e) {
    error_log(
        'Error al consultar curso para edición en RedVital Colvatenza: '
        . $e->getMessage()
    );

    $_SESSION['mensaje_estado'] = 'No fue posible consultar el curso.';
    $_SESSION['tipo_mensaje_estado'] = 'error';

    header('Location: cursos.php');
    exit;
}

// Obtener el identificador del usuario autenticado.
$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $grado = trim($_POST['grado'] ?? '');
    $grupo = trim($_POST['grupo'] ?? '');

    // Validar que los campos contengan números enteros.
    if (
        $grado === '' ||
        $grupo === '' ||
        filter_var($grado, FILTER_VALIDATE_INT) === false ||
        filter_var($grupo, FILTER_VALIDATE_INT) === false
    ) {
        $mensaje_error = 'El grado y el grupo deben ser números enteros.';

    } elseif ((int) $grado < 0 || (int) $grado > 20) {
        $mensaje_error = 'El grado debe estar entre 0 y 20.';

    } elseif ((int) $grupo < 1 || (int) $grupo > 99) {
        $mensaje_error = 'El grupo debe estar entre 1 y 99.';

    } elseif ($id_usuario <= 0) {
        $mensaje_error = 'No fue posible identificar al usuario autenticado.';

    } else {

        try {
            // Comprobar que no exista otro curso con el mismo grado y grupo.
            $sql = 'SELECT COUNT(*)
                    FROM cursos
                    WHERE grado = :grado
                      AND grupo = :grupo
                      AND id_curso <> :id_curso';

            $consulta = $pdo->prepare($sql);
            $consulta->execute([
                'grado' => $grado,
                'grupo' => $grupo,
                'id_curso' => $id_curso
            ]);

            $curso_duplicado = (int) $consulta->fetchColumn();

            if ($curso_duplicado > 0) {
                $mensaje_error = 'Ya existe un curso registrado con ese grado y grupo.';

            } else {
                // Actualizar el curso y registrar quién realizó el cambio.
                $sql = 'UPDATE cursos
                        SET grado = :grado,
                            grupo = :grupo,
                            actualizado_por = :actualizado_por
                        WHERE id_curso = :id_curso';

                $consulta = $pdo->prepare($sql);
                $consulta->execute([
                    'grado' => $grado,
                    'grupo' => $grupo,
                    'actualizado_por' => $id_usuario,
                    'id_curso' => $id_curso
                ]);

                // Preparar la notificación para el listado.
                $_SESSION['mensaje_estado'] = 'El curso se actualizó correctamente.';
                $_SESSION['tipo_mensaje_estado'] = 'exito';

                header('Location: cursos.php');
                exit;
            }

        } catch (PDOException $e) {
            error_log(
                'Error al actualizar curso en RedVital Colvatenza: '
                . $e->getMessage()
            );

            if ($e->getCode() === '23000') {
                $mensaje_error = 'Ya existe un curso con ese grado y grupo.';
            } else {
                $mensaje_error = 'No fue posible actualizar el curso. Inténtalo nuevamente.';
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

    <title>Editar curso | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_curso.css">
</head>

<body>
    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <section class="formulario-cursos">

                <header class="encabezado-cursos">
                    <div>
                        <h1>Editar curso</h1>

                        <p>
                            Modifica el grado o el grupo del curso seleccionado.
                        </p>
                    </div>
                </header>

                <div class="contenido-formulario">

                    <div class="introduccion-formulario">
                        <h2>Datos del curso</h2>

                        <p>
                            Actualiza la información y guarda los cambios.
                        </p>
                    </div>

                    <?php if ($mensaje_error !== ''): ?>

                        <div class="mensaje-formulario mensaje-error" role="alert">
                            <?= htmlspecialchars(
                                $mensaje_error,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                    <form action="editar_curso.php?id=<?= (int) $id_curso ?>" method="POST">

                        <div class="campos-formulario">

                            <div class="campo-formulario">
                                <label for="grado">Grado</label>

                                <input
                                    type="number"
                                    id="grado"
                                    name="grado"
                                    min="0"
                                    max="20"
                                    step="1"
                                    placeholder="Ej. 10"
                                    value="<?= htmlspecialchars(
                                        $grado,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    required
                                >
                            </div>

                            <div class="campo-formulario">
                                <label for="grupo">Grupo</label>

                                <input
                                    type="number"
                                    id="grupo"
                                    name="grupo"
                                    min="1"
                                    max="99"
                                    step="1"
                                    placeholder="Ej. 1"
                                    value="<?= htmlspecialchars(
                                        $grupo,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    required
                                >
                            </div>

                        </div>

                        <div class="acciones-formulario">

                            <a href="cursos.php" class="boton-secundario">
                                Cancelar
                            </a>

                            <button type="submit" class="boton-principal">
                                Guardar cambios
                            </button>

                        </div>

                    </form>

                </div>

            </section>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>

</body>

</html>
