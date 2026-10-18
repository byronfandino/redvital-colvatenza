<?php

$pagina_activa = 'usuarios';
$rol_requerido = 'administrador';

require_once __DIR__ . '/verificar_rol.php';
require_once __DIR__ . '/../config/database.php';

// Consultar los usuarios registrados.
try {
    $sql = "
        SELECT
            id_usuario,
            nombres,
            apellidos,
            correo_electronico,
            rol,
            activo,
            creado_en,
            actualizado_en,
            ultimo_acceso
        FROM usuarios
        ORDER BY id_usuario ASC
    ";

    $consulta = $pdo->query($sql);
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $error_consulta = false;

} catch (PDOException $e) {
    error_log(
        'Error al consultar usuarios en RedVital Colvatenza: ' . $e->getMessage()
    );

    $usuarios = [];
    $error_consulta = true;
}

// Recuperar el mensaje de estado.
$mensaje_estado = $_SESSION['mensaje_estado'] ?? '';
$tipo_mensaje_estado = $_SESSION['tipo_mensaje_estado'] ?? 'exito';

unset($_SESSION['mensaje_estado']);
unset($_SESSION['tipo_mensaje_estado']);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de usuarios | RedVital Colvatenza</title>

    <link rel="stylesheet" href="../assets/css/admin/menu.css">
    <link rel="stylesheet" href="../assets/css/admin/usuarios.css">
</head>

<body>

    <div class="estructura-administrativa">

        <?php require_once __DIR__ . '/includes/menu.php'; ?>

        <main class="contenido-administrativo">

            <section class="gestion-usuarios">

                <header class="encabezado-usuarios">
                    <div>
                        <h1>Gestión de usuarios</h1>
                        <p>
                            Consulta y administra las cuentas de acceso
                            a RedVital Colvatenza.
                        </p>
                    </div>

                    <a href="crear_usuario.php" class="boton-principal">
                        Registrar usuario
                    </a>
                </header>

                <div class="contenido-usuarios">

                    <div class="resumen-usuarios">
                        <div>
                            <h2>Usuarios registrados</h2>
                            <p>
                                Total registros:
                                <strong><?= count($usuarios) ?></strong>
                            </p>
                        </div>
                    </div>

                    <?php if ($mensaje_estado !== ''): ?>

                        <div
                            class="mensaje-formulario <?= $tipo_mensaje_estado === 'exito'
                                ? 'mensaje-exito'
                                : 'mensaje-error' ?>"
                            role="alert"
                        >
                            <?= htmlspecialchars(
                                $mensaje_estado,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </div>

                    <?php endif; ?>

                    <?php if ($error_consulta): ?>

                        <div class="mensaje-formulario mensaje-error" role="alert">
                            No fue posible consultar los usuarios.
                            Inténtalo nuevamente.
                        </div>

                    <?php elseif (empty($usuarios)): ?>

                        <div class="mensaje-vacio">
                            <h3>No hay usuarios registrados</h3>
                            <p>
                                Todavía no existen cuentas para mostrar.
                            </p>
                        </div>

                    <?php else: ?>

                        <div class="tabla-contenedor">

                            <table class="tabla-usuarios">

                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre completo</th>
                                        <th>Correo electrónico</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Fecha de creación</th>
                                        <th>Fecha de actualización</th>
                                        <th>Último acceso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($usuarios as $usuario): ?>

                                        <tr>
                                            <td>
                                                <?= (int) $usuario['id_usuario'] ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $usuario['nombres'] . ' ' . $usuario['apellidos'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars(
                                                    $usuario['correo_electronico'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>
                                            </td>

                                            <td>
                                                <span class="etiqueta-rol">
                                                    <?= $usuario['rol'] === 'administrador'
                                                        ? 'Administrador'
                                                        : 'Lector' ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ((int) $usuario['activo'] === 1): ?>

                                                    <span class="etiqueta-estado estado-activo">
                                                        Activo
                                                    </span>

                                                <?php else: ?>

                                                    <span class="etiqueta-estado estado-inactivo">
                                                        Inactivo
                                                    </span>

                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($usuario['creado_en'])): ?>
                                                    <?= htmlspecialchars(
                                                        date('d/m/Y H:i', strtotime($usuario['creado_en'])),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                <?php else: ?>
                                                    <span class="dato-sin-registro">
                                                        Sin registro
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($usuario['actualizado_en'])): ?>
                                                    <?= htmlspecialchars(
                                                        date('d/m/Y H:i', strtotime($usuario['actualizado_en'])),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                <?php else: ?>
                                                    <span class="dato-sin-registro">
                                                        Sin actualizar
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($usuario['ultimo_acceso'])): ?>
                                                    <?= htmlspecialchars(
                                                        date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>
                                                <?php else: ?>
                                                    <span class="dato-sin-registro">
                                                        Sin acceso registrado
                                                    </span>
                                                <?php endif; ?>
                                            </td>

                                            <td>
                                                <div class="acciones-usuario">

                                                    <a
                                                        href="editar_usuario.php?id=<?= (int) $usuario['id_usuario'] ?>"
                                                        class="boton-accion boton-editar"
                                                    >
                                                        Editar
                                                    </a>

                                                    <form
                                                        action="cambiar_estado_usuario.php"
                                                        method="POST"
                                                        class="formulario-accion"
                                                    >
                                                        <input
                                                            type="hidden"
                                                            name="id_usuario"
                                                            value="<?= (int) $usuario['id_usuario'] ?>"
                                                        >

                                                        <?php if ((int) $usuario['activo'] === 1): ?>

                                                            <input
                                                                type="hidden"
                                                                name="activo"
                                                                value="0"
                                                            >

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-desactivar"
                                                            >
                                                                Desactivar
                                                            </button>

                                                        <?php else: ?>

                                                            <input
                                                                type="hidden"
                                                                name="activo"
                                                                value="1"
                                                            >

                                                            <button
                                                                type="submit"
                                                                class="boton-accion boton-activar"
                                                            >
                                                                Activar
                                                            </button>

                                                        <?php endif; ?>

                                                    </form>

                                                </div>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php endif; ?>

                </div>

            </section>

        </main>

    </div>

    <script src="../assets/js/admin/menu.js" defer></script>

</body>

</html>