
<?php
require_once __DIR__ . '/conexion.php';

function e($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('ID de alumno no válido.');
}

$stmt = $conexion->prepare("SELECT * FROM alumnos WHERE id = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch();

if (!$alumno) {
    http_response_code(404);
    exit('Alumno no encontrado.');
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'matricula' => trim($_POST['matricula'] ?? ''),
        'nombre' => trim($_POST['nombre'] ?? ''),
        'carrera' => trim($_POST['carrera'] ?? ''),
        'semestre' => trim($_POST['semestre'] ?? ''),
        'correo' => trim($_POST['correo'] ?? '')
    ];

    $alumno = array_merge($alumno, $datos);

    if ($datos['matricula'] === '' ||
        $datos['nombre'] === '' ||
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
        $errores[] = 'El correo no es válido.';
    }

    if (!$errores) {
        try {
            $stmt = $conexion->prepare(
                "UPDATE alumnos SET
                    matricula = ?,
                    nombre = ?,
                    carrera = ?,
                    semestre = ?,
                    correo = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $datos['matricula'],
                $datos['nombre'],
                $datos['carrera'],
                $semestre,
                $datos['correo'] ?: null,
                $id
            ]);

            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errores[] = 'La matrícula ya existe.';
            } else {
                error_log($e->getMessage());
                $errores[] = 'Error al actualizar el alumno.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar alumno</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
<main class="contenedor">
    <header class="encabezado">
        <div>
            <h1>✏️ Editar alumno</h1>
            <p>Actualiza los datos del estudiante</p>
        </div>
        <a href="index.php" class="btn btn-secundario">
            Volver
        </a>
    </header>

    <section class="tarjeta formulario">
        <?php foreach ($errores as $error): ?>
            <p class="mensaje-error"><?= e($error) ?></p>
        <?php endforeach; ?>

        <form method="POST">
            <label>Matrícula *</label>
            <input name="matricula" required maxlength="20"
                   value="<?= e($alumno['matricula']) ?>">

            <label>Nombre completo *</label>
            <input name="nombre" required maxlength="100"
                   value="<?= e($alumno['nombre']) ?>">

            <label>Carrera *</label>
            <input name="carrera" required maxlength="100"
                   value="<?= e($alumno['carrera']) ?>">

            <label>Semestre *</label>
            <input name="semestre" type="number"
                   min="1" max="12" required
                   value="<?= e($alumno['semestre']) ?>">

            <label>Correo electrónico</label>
            <input name="correo" type="email" maxlength="150"
                   value="<?= e($alumno['correo']) ?>">

            <button type="submit" class="btn btn-primary">
                Guardar cambios
            </button>
        </form>
    </section>
</main>
</body>
</html>
