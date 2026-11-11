
<?php
require_once __DIR__ . '/conexion.php';

$busqueda = trim($_GET['buscar'] ?? '');

if ($busqueda !== '') {
    $stmt = $conexion->prepare(
        "SELECT * FROM alumnos
         WHERE nombre LIKE :nombre OR matricula LIKE :matricula
         ORDER BY id DESC"
    );
    $stmt->execute([
        'nombre' => "%$busqueda%",
        'matricula' => "%$busqueda%"
    ]);
} else {
    $stmt = $conexion->query(
        "SELECT * FROM alumnos ORDER BY id DESC"
    );
}

$alumnos = $stmt->fetchAll();

function escapar($valor) {
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Alumnos</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <main class="contenedor">
        <header class="encabezado">
            <div>
                <h1>🎓 Gestión de Alumnos</h1>
                <p>Sistema desarrollado con PHP y MariaDB</p>
            </div>
            <a href="crear.php" class="btn btn-primary">
                + Nuevo alumno
            </a>
        </header>

        <section class="tarjeta">
            <div class="barra">
                <h2>Alumnos registrados</h2>
                <span class="contador">
                    <?= count($alumnos) ?> resultados
                </span>
            </div>

            <form method="GET" class="buscador">
                <input
                    type="text"
                    name="buscar"
                    placeholder="Buscar por nombre o matrícula..."
                    value="<?= escapar($busqueda) ?>"
                >
                <button class="btn btn-primary" type="submit">
                    Buscar
                </button>
                <a href="index.php" class="btn btn-secundario">
                    Limpiar
                </a>
            </form>

            <div class="tabla-contenedor">
                <table>
                    <thead>
                        <tr>
                            <th>Matrícula</th>
                            <th>Nombre</th>
                            <th>Carrera</th>
                            <th>Semestre</th>
                            <th>Correo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($alumnos as $alumno): ?>
                        <tr>
                            <td><?= escapar($alumno['matricula']) ?></td>
                            <td><?= escapar($alumno['nombre']) ?></td>
                            <td><?= escapar($alumno['carrera']) ?></td>
                            <td><?= escapar($alumno['semestre']) ?></td>
                            <td><?= escapar($alumno['correo']) ?></td>
                            <td>
                                <a class="btn btn-editar"
                                   href="editar.php?id=<?= (int)$alumno['id'] ?>">
                                    Editar
                                </a>
                                <a class="btn btn-eliminar"
                                   href="eliminar.php?id=<?= (int)$alumno['id'] ?>">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($alumnos)): ?>
                        <tr>
                            <td colspan="6" class="vacio">
                                No se encontraron alumnos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <footer>Práctica AWS EC2 · PHP + MariaDB</footer>
    </main>
</body>
</html>
