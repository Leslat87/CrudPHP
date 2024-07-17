
    $(document).ready(function () {
        //hacer favoritos
        $(".formFavoritos").submit(function (event) {
            event.preventDefault();
            var form = $(this);
            enviarFormulario(form);
        });
        // deshacer favoritos
        $(".formFavoritosn").submit(function (event) {
            event.preventDefault();
            var form = $(this);
            enviarFormulario(form);
        });
        //mandar a papelera
        $(".formPapelera").submit(function (event) {
            event.preventDefault();
            var form = $(this);
            enviarFormulario(form);
        });
        //Restaurar de papelera
        $(".formRestaurar").submit(function (event) {
            event.preventDefault();
            var form = $(this);
            enviarFormulario(form);
        });
        // compartir imagen con un amigo
        $("#formCompartir").submit(function (event) {
            event.preventDefault();
            var form = $(this);
            var idArchivo = $("#idArchivo").val();
            var idUsuarioOrigen = $("#idUsuarioOrigen").val();
            var amigoDestino = $("#amigoDestino").val();
            $.ajax({
                type: "POST",
                url: "usuarios.php",
                data: {
                    id_archivo: idArchivo,
                    amigoDestino: amigoDestino,
                    idUsuarioOrigen: idUsuarioOrigen,
                    option: 21
                },
                success: function (response) {
                    console.log(response);
                },
                error: function (error) {
                    console.error(error);
                }
            });
        });
        // esta funcion ejecutara las opciones aplicadas sobre la imagen
        function enviarFormulario(form) {
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    alert("Operación exitosa");
                    window.location.href = "galeria.php";
                },
                error: function () {
                    alert("Error en la operación");
                }
            });
        }
    });
    // con esta funcion obtendremos esas fotos compartidas por amigo hacia nosotros
    function obtenerFotosCompartidasPorAmigo(nombreAmigo) {
        $.ajax({
            type: "POST",
            url: "usuarios.php",
            data: {
                option: 20,
                nombreAmigo: nombreAmigo
            },
            success: function (response) {
                console.log(response);
            },
            error: function (error) {
                console.error(error);
            }
        });
    }

   
    $(document).ready(function() {
        
        $('#logout').click(function() {
           
            $.ajax({
                type: 'POST',
                url: 'usuarios.php', 
                data: { option: 3 },
                success: function(response) {
                  
                        window.location.href = '/log.php'; 
                },
                error: function() {
                    alert('Error al realizar la solicitud al servidor');
                }
            });
        });
    });
    
    
    