$(document).ready(function() {
    // botón de logout
    $('#logout').click(function() {
        $.ajax({
            type: 'POST',
            url: '/backoffice/usuarios.php',
            data: { option: 3 },
            success: function(response) {
                
                    window.location.href = 'log.php';
            }
            
            })
        });
    

    // esta funcion sirve para mostrar la descripcion que el usuario dio sobre una imagen
    function obtenerDescripcionImagen(id_archivo) {
        $.ajax({
            type: "POST",
            url: "backoffice/usuarios.php",
            data: {
                option: 22,
                id_archivo: id_archivo
            },
            success: function(response) {
                var descripcionElement = $("#descripcion_" + id_archivo);
                descripcionElement.text(response);
            },
            error: function(error) {
                console.error("Error al obtener la descripción de la imagen:", error);
            }
        });
    }
    // las fotos que sean compartidas de un usuario a otro utiliza esta funcion
    function getFotosCompartidasPorUsuario() {
        $.ajax({
            type: "POST",
            url: "backoffice/usuarios.php", 
            data: {
                option: 20
            },
            success: function (response) {
                $('#fotosCompartidasContainer').html(response);
                // Después de cargar las fotos, obtener la descripción de cada una
                $('.image-container').each(function() {
                    var id_archivo = $(this).attr('id_archivo'); 
                    obtenerDescripcionImagen(id_archivo); 
                });
            },
            error: function (error) {
                console.error(error);
            }
        });
    }

   
    getFotosCompartidasPorUsuario($IDLogin);

});
    
      $("#logout").click(function () {
        $.ajax({
            url: "backoffice/usuarios.php",
            type: "POST",
            data: {
                option: 3
            },
            success: function (a) {
                window.location.href = "index.php";
            }
        });
    });





