<?php
$conexion = new mysqli("localhost", "claudio", "cdao3132", "desis");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (isset($_POST['obtener_regiones'])) {
    $sqlRegiones = "SELECT id_region, nombre_region FROM regiones";
    $resultadoRegiones = $conexion->query($sqlRegiones);

    if ($resultadoRegiones) {
        $regiones = [];

        // Agregar el option por defecto
        $regiones[] = [
            'id' => '',
            'nombre' => 'Seleccione su región'
        ];

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
}

if (isset($_POST['obtener_comunas_por_region'])) {
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
}

if (isset($_POST['obtener_candidatos_por_comuna'])) {
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
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establecer la conexión a la base de datos

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener los valores del formulario y validarlos
    $nombre_apellido = validar($_POST["nombre_apellido"]);
    $alias = validar($_POST["alias"]);
    $rut = validar($_POST["rut"]);
    $email = validar($_POST["email"]);
    $region = validar($_POST["region"]);
    $comuna = validar($_POST["comuna"]);
    $candidato = validar($_POST["candidato"]);
    $comoseentero = validarComoseEntero($_POST["entero"]); // Esta función se encarga de validar los checkboxes

    // Función para validar los datos
    function validar($datos) {
        $datos = trim($datos); // Eliminar espacios en blanco al inicio y al final
        $datos = stripslashes($datos); // Eliminar barras invertidas
        $datos = htmlspecialchars($datos); // Convertir caracteres especiales a entidades HTML
        return $datos;
    }

    // Función para validar los datos de los checkboxes
    function validarComoseEntero($checkboxes) {
        // Verificar que al menos dos opciones hayan sido seleccionadas
        if (count($checkboxes) >= 2) {
            return implode(", ", $checkboxes); // Devolver un string con las opciones seleccionadas separadas por coma
        } else {
            // Si no se han seleccionado al menos dos opciones, puedes manejar la validación de acuerdo a tu lógica
            // Por ejemplo, podrías mostrar un mensaje de error o tomar otra acción requerida.
            return "Menos de dos opciones seleccionadas";
        }
    }

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO nombre_tabla (nombre_apellido, alias, rut, email, region, comuna, candidato, comoseentero) 
            VALUES ('$nombre_apellido', '$alias', '$rut', '$email', '$region', '$comuna', '$candidato', '$comoseentero')";

    if ($conexion->query($sql) === TRUE) {
        echo "Datos insertados correctamente en la base de datos.";
    } else {
        echo "Error al insertar datos: " . $conexion->error;
    }

    // Cerrar la conexión a la base de datos
    //$conexion->close();
}




$conexion->close();
?>
