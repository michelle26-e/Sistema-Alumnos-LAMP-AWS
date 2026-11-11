
<?php
require_once __DIR__ . '/conexion.php';

$errores = [];
$datos = [
    'matricula' => '',
    'nombre' => '',
    'carrera' => '',
    'semestre' => '',
    'correo' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($datos as $campo => $valor) {
        $datos[$campo] = trim($_POST[$campo] ?? '');
    }

    if ($datos['matricula'] === '' || $datos['nombre'] === '' ||
        $datos['carrera'] === '') {
        $errores[] = 'Completa los campos obligatorios.';
    }

    $semestre = filter_var(
        $datos['semestre'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 12]]
    );

    if ($semestre === false) {
        $errores[] = 'El semestre debe estar entre 1 y 12.';
    }

    if ($datos['correo'] !== '' &&
        !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo electrónico no es válido.';
    }

    if (!$errores) {
        try {
            $stmt = $conexion->prepare(
                "INSERT INTO alumnos
                (matricula, nombre, carrera, semestre, correo)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $datos['matricula'],
                $datos['nombre'],
                $datos['carrera'],
                $semestre,
                $datos['correo'] ?: null
            ]);

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'La matrícula ya está registrada.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'No se pudo guardar el alumno.';
            }
        }
    }
}

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar alumno</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<main class="contenedor">
    <header class="encabezado">
        <div>
            <h1>Registrar alumno</h1>
            <p>Ingresa los datos del estudiante</p>
        </div>
        <a class="btn btn-secundario" href="index.php">Volver</a>
    </header>

    <section class="tarjeta formulario">
        <?php foreach ($errores as $error): ?>
            <p class="mensaje-error"><?= e($error) ?></p>
        <?php endforeach; ?>

        <form method="POST">
            <label>Matrícula *</label>
            <input name="matricula" required maxlength="20"
                   value="<?= e($datos['matricula']) ?>">

            <label>Nombre completo *</label>
            <input name="nombre" required maxlength="100"
                   value="<?= e($datos['nombre']) ?>">

            <label>Carrera *</label>
            <input name="carrera" required maxlength="100"
                   value="<?= e($datos['carrera']) ?>">

            <label>Semestre *</label>
            <input name="semestre" type="number"
                   min="1" max="12" required
                   value="<?= e($datos['semestre']) ?>">

            <label>Correo electrónico</label>
            <input name="correo" type="email" maxlength="150"
                   value="<?= e($datos['correo']) ?>">

            <button class="btn btn-primary" type="submit">
                Guardar alumno
            </button>
        </form>
    </section>
</main>
</body>
</html>
