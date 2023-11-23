$(document).ready(function () {
    function cargarRegiones() {
        $.ajax({
            url: 'procesar.php',
            type: 'POST',
            data: { obtener_regiones: true },
            dataType: 'json',
            success: function (data) {
                var regionSelect = $('#region');

                regionSelect.empty();
                // Llenar el select de regiones
                regionSelect.append('<option value="">Seleccione una región</option>');
                $.each(data.regiones, function (key, value) {
                    regionSelect.append('<option value="' + value.id + '">' + value.nombre + '</option>');
                });

            },
            error: function (xhr, status, error) {
                console.error("XHR status: " + xhr.status);
                console.error("Status: " + status);
                console.error("Error: " + error);
                console.error("Response Text: " + xhr.responseText);
            }
        });
    }

    cargarRegiones(); // Cargar regiones 

    $(document).ready(function () {
        var maxCheckboxes = 2;
        $('.seleccionable').change(function () {
            var checked = $('.seleccionable:checked').length;
            if (checked >= maxCheckboxes) {
                $('.seleccionable:not(:checked)').prop('disabled', true);
            } else {
                $('.seleccionable:not(:checked)').prop('disabled', false);
            }
        });
    });

    $(document).ready(function () {
        $('#rut').on('input', function () {
            let rutSinFormato = $(this).val().replace(/[^0-9kK]/g, ''); // Eliminar todo excepto números y la 'k' (u 'K') final
            let rutFormateado = '';

            // Validar si el RUT tiene formato válido
            if (/^(\d{1,9})-?(\d{0,1}k?)$/.test(rutSinFormato)) {
                let rutLimpio = rutSinFormato.slice(0, -1); // Obtener RUT sin dígito verificador
                let dv = rutSinFormato.slice(-1).toUpperCase(); // Obtener dígito verificador, convertir a mayúscula

                // Formatear el RUT con puntos y guión
                while (rutLimpio.length > 3) {
                    rutFormateado = `.${rutLimpio.slice(-3)}${rutFormateado}`;
                    rutLimpio = rutLimpio.slice(0, -3);
                }

                rutFormateado = rutLimpio + rutFormateado + `-${dv}`;
            }

            $(this).val(rutFormateado); // Establecer el RUT formateado en el campo de entrada

        });

        $('#rut').on('blur', function () {
            let rutSinFormato = $(this).val().replace(/[^0-9kK]/g, ''); // Eliminar todo excepto números y la 'k' (u 'K') final

            // Validar si el RUT tiene el formato correcto de un RUT chileno
            if (/^(\d{1,9})-?(\d{0,1}k?)$/.test(rutSinFormato)) {
               // alert('El RUT ingresado es válido.'); // Mensaje si es un RUT chileno válido
            } else {
                alert('El RUT ingresado no es  válido.'); // Mensaje si no es válido
                $(this).val('');
            }
        });

    });


    // Cambiar las comunas al seleccionar una región
    $('#region').change(function () {
        var selectedRegion = $(this).val();
        $.ajax({
            url: 'procesar.php',
            type: 'POST',
            data: { obtener_comunas_por_region: true, region_id: selectedRegion },
            dataType: 'json',
            success: function (data) {
                var comunaSelect = $('#comuna');
                comunaSelect.empty();

                // Llenar el select de comunas según región seleccionada
                comunaSelect.append('<option value="">Seleccione su comuna</option>');
                $.each(data.comunas, function (key, value) {
                    comunaSelect.append('<option value="' + value.id + '">' + value.nombre + '</option>');
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    $('#comuna').change(function () {
        var selectedComuna = $(this).val();
        $.ajax({
            url: 'procesar.php',
            type: 'POST',
            data: { obtener_candidatos_por_comuna: true, comuna_id: selectedComuna },
            dataType: 'json',
            success: function (data) {
                var candidatoSelect = $('#candidato');
                candidatoSelect.empty();

                // Llenar el select de candidatos con los obtenidos por la comuna seleccionada
                candidatoSelect.append('<option value="">Seleccione su candidato</option>');
                $.each(data.candidatos, function (key, value) {
                    candidatoSelect.append('<option value="' + value.id + '">' + value.nombre + '</option>');
                });
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    });

    document.getElementById('formulario').addEventListener('submit', function(event) {
        var checkboxes = document.querySelectorAll('input[name="entero[]"]:checked');
        
        // Validar que al menos dos checkboxes estén seleccionados
        if (checkboxes.length < 2) {
            event.preventDefault(); // Evitar que el formulario se envíe
            alert("Debe seleccionar al menos dos opciones en 'Como se enteró de Nosotros'.");
        }
    });
});