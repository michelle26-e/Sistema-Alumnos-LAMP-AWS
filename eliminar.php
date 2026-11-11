
<?php
require_once __DIR__ . '/conexion.php';

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID no válido.');
}

$stmt = $conexion->prepare(
    "SELECT id, matricula, nombre FROM alumnos WHERE id = ?"
);
$stmt->execute([$id]);
$alumno = $stmt->fetch();

if (!$alumno) {
    http_response_code(404);
    exit('Alumno no encontrado.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Solicitud no válida.');
    }

    try {
        $stmt = $conexion->prepare(
            "DELETE FROM alumnos WHERE id = ?"
        );
        $stmt->execute([$id]);

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $error = 'No fue posible eliminar el alumno.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar alumno</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<main class="contenedor">
    <header class="encabezado">
        <div>
            <h1>🗑️ Eliminar alumno</h1>
            <p>Confirma la eliminación del registro</p>
        </div>
        <a href="index.php" class="btn btn-secundario">
            Cancelar
        </a>
    </header>

    <section class="tarjeta formulario">
        <h2>¿Deseas eliminar este alumno?</h2>

        <p>
            <strong>Matrícula:</strong>
            <?= e($alumno['matricula']) ?>
        </p>

        <p>
            <strong>Nombre:</strong>
            <?= e($alumno['nombre']) ?>
        </p>

        <?php if ($error): ?>
            <p class="mensaje-error"><?= e($error) ?></p>
        <?php endif; ?>

        <p>Esta acción no se puede deshacer.</p>

        <form method="POST">
            <input type="hidden" name="csrf_token"
                   value="<?= e($_SESSION['csrf_token']) ?>">

            <button type="submit" class="btn btn-eliminar">
                Sí, eliminar alumno
            </button>
            <a href="index.php" class="btn btn-secundario">
                Cancelar
            </a>
        </form>
    </section>
</main>
</body>
</html>
