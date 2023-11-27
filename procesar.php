<?php
$conexion = new mysqli("localhost", "claudio", "cdao3132", "desis");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['obtener_regiones'])) {
    $sqlRegiones = "SELECT id_region, nombre_region FROM regiones";
    $resultadoRegiones = $conexion->query($sqlRegiones);

    if ($resultadoRegiones) {
        $regiones = [];

        while ($row = $resultadoRegiones->fetch_assoc()) {
            $regiones[] = [
                'id' => $row['id_region'],
                'nombre' => $row['nombre_region']
            ];
        }

        $data = [
            'regiones' => $regiones
        ];

        header('Content-type: application/json');
        echo json_encode($data);
    } else {
        echo "Error al obtener datos de la base de datos";
    }
} elseif (isset($_POST['obtener_comunas_por_region'])) {
    $regionId = $_POST['region_id'];

    $sqlComunas = "SELECT id_comuna, nombre_comuna FROM comunas WHERE id_region = $regionId";
    $resultadoComunas = $conexion->query($sqlComunas);

    if ($resultadoComunas) {
        $comunas = [];

        while ($row = $resultadoComunas->fetch_assoc()) {
            $comunas[] = [
                'id' => $row['id_comuna'],
                'nombre' => $row['nombre_comuna']
            ];
        }

        $data = [
            'comunas' => $comunas
        ];

        header('Content-type: application/json');
        echo json_encode($data);
    } else {
        echo "Error al obtener datos de la base de datos";
    }
} elseif (isset($_POST['obtener_candidatos_por_comuna'])) {
    $comunaId = $_POST['comuna_id'];

    $sqlCandidatos = "SELECT id_candidato, nombre FROM candidato WHERE id_comuna = $comunaId";
    $resultadoCandidatos = $conexion->query($sqlCandidatos);

    if ($resultadoCandidatos) {
        $candidatos = [];

        while ($row = $resultadoCandidatos->fetch_assoc()) {
            $candidatos[] = [
                'id' => $row['id_candidato'],
                'nombre' => $row['nombre']
            ];
        }

        $data = [
            'candidatos' => $candidatos
        ];

        header('Content-type: application/json');
        echo json_encode($data);
    } else {
        echo "Error al obtener datos de candidatos por comuna";
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $nombre_apellido = $_POST["nombre_apellido"];
    $alias = $_POST["alias"];
    $rut = $_POST["rut"];
    $email = $_POST["email"];
    $region = $_POST["region"];
    $comuna = $_POST["comuna"];
    $candidato = $_POST["candidato"];
    $comoseentero = implode(", ", $_POST["entero"]); // Convertir el array en una cadena

    // Consultar si el RUT ya existe en la base de datos
    $sql_verificar_rut = "SELECT COUNT(*) AS count FROM votantes WHERE rut = ?";
    $stmt_verificar_rut = $conexion->prepare($sql_verificar_rut);
    $stmt_verificar_rut->bind_param("s", $rut);
    $stmt_verificar_rut->execute();
    $stmt_verificar_rut->bind_result($count);
    $stmt_verificar_rut->fetch();
    $stmt_verificar_rut->close();

    if ($count > 0) {
        echo "El RUT ya existe en la base de datos.";
    } else {
        // Preparar la consulta SQL para insertar los datos
        $sql_insertar = "INSERT INTO votantes (nombre_apellido, alias, rut, email, id_region, id_comuna, id_candidato, comoseentero) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insertar = $conexion->prepare($sql_insertar);

        if ($stmt_insertar) {
            // Vincular los parámetros con la consulta preparada
            $stmt_insertar->bind_param("ssssssss", $nombre_apellido, $alias, $rut, $email, $region, $comuna, $candidato, $comoseentero);

            // Ejecutar la consulta preparada para insertar los datos
            if ($stmt_insertar->execute()) {
                echo "Datos insertados correctamente en la base de datos.";
            } else {
                echo "Error al insertar datos: " . $stmt_insertar->error;
            }

            // Cerrar la consulta preparada para la inserción
            $stmt_insertar->close();
        } else {
            echo "Error en la preparación de la consulta de inserción.";
        }
    }
}




$conexion->close();
?>