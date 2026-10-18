<?php

// Identificar la página activa.
$pagina_activa = 'cursos';

// Restringir el acceso a los administradores.
$rol_requerido = 'administrador';
require_once __DIR__ . '/verificar_rol.php';

// Conectar con la base de datos.
require_once __DIR__ . '/../config/database.php';

// Obtener el ID del usuario autenticado.
$id_usuario = (int) ($_SESSION['id_usuario'] ?? 0);

// Inicializar las variables del formulario.
$grado = '';
$grupo = '';
$mensaje_error = '';

// Procesar el formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtener y limpiar los datos enviados.
    $grado = trim($_POST['grado'] ?? '');
    $grupo = trim($_POST['grupo'] ?? '');

    // Validar los campos obligatorios.
    if ($grado === '' || $grupo === '') {

        $mensaje_error = 'Debes completar todos los campos.';

    } elseif (mb_strlen($grado) > 20 || mb_strlen($grupo) > 10) {

        $mensaje_error = 'El grado o el grupo superan la longitud permitida.';

    } elseif ($id_usuario <= 0) {

        $mensaje_error = 'No se pudo identificar al usuario autenticado.';

    } else {

        try {

            // Verificar si el grado y el grupo ya existen.
            $sql = 'SELECT COUNT(*)
                    FROM cursos
                    WHERE grado = :grado
                    AND grupo = :grupo';

            $consulta = $pdo->prepare($sql);

            $consulta->execute([
                'grado' => $grado,
                'grupo' => $grupo
            ]);

            $curso_existente = (int) $consulta->fetchColumn();

            if ($curso_existente > 0) {

                $mensaje_error = 'Ya existe un curso con ese grado y grupo.';

            } else {

                // Registrar el curso y el usuario que lo creó.
                $sql = 'INSERT INTO cursos (
                            grado,
                            grupo,
                            creado_por
                        ) VALUES (
                            :grado,
                            :grupo,
                            :creado_por
                        )';

                $consulta = $pdo->prepare($sql);

                $consulta->execute([
                    'grado' => $grado,
                    'grupo' => $grupo,
                    'creado_por' => $id_usuario
                ]);

                // Guardar el mensaje y regresar al listado.
                $_SESSION['mensaje_estado'] = 'El curso se registró correctamente.';
                $_SESSION['tipo_mensaje_estado'] = 'exito';

                header('Location: cursos.php');
                exit;
            }

        } catch (PDOException $e) {

            error_log(
                'Error al registrar un curso en RedVital Colvatenza: ' .
                $e->getMessage()
            );

            if ($e->getCode() === '23000') {
                $mensaje_error = 'Ya existe un curso con ese grado y grupo.';
            } else {
                $mensaje_error = 'No fue posible registrar el curso. Inténtalo nuevamente.';
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

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/crear_curso.css">

    <title>Registrar curso | RedVital Colvatenza</title>
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <div class="formulario-cursos">

                <header class="encabezado-cursos">
                    <h1>Registrar curso</h1>
                    <p>
                        Registra un nuevo grado y grupo de la sede campestre.
                    </p>
                </header>

                <section class="contenido-formulario">

                    <div class="titulo-formulario">
                        <h2>Información del curso</h2>
                        <p>
                            Completa los campos para registrar el curso.
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

                    <form action="crear_curso.php" method="POST">

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
                                    value="<?= htmlspecialchars($grado) ?>"
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
                                    value="<?= htmlspecialchars($grupo) ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="acciones-formulario">

                            <a href="cursos.php" class="boton-secundario">
                                Cancelar
                            </a>

                            <button type="submit" class="boton-principal">
                                Registrar curso
                            </button>

                        </div>

                    </form>

                </section>

            </div>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>

</body>
</html>
