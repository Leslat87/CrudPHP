// Función para obtener las imágenes del amigo desde el backend
function obtenerImagenesDeAmigo(idAmigo) {
    // Realizar una petición AJAX para obtener los datos JSON de las imágenes
    $.ajax({
        url: 'obtener_imagenes_amigo.php?id=' + idAmigo,
        method: 'GET',
        success: function(response) {
            // Llamar a la función para mostrar las imágenes cuando la respuesta sea exitosa
            mostrarImagenes(response);
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener imágenes del amigo:', error);
        }
    });
}

// Función para mostrar las imágenes en el frontend
function mostrarImagenes(imagesData) {
    // Aquí puedes usar JavaScript para mostrar las imágenes en el frontend
    // Por ejemplo, puedes iterar sobre imagesData y agregar las imágenes al DOM
}

// Obtener el ID del amigo de alguna manera (por ejemplo, desde PHP)
var idAmigo = obtenerIdAmigoDesdePHP(); // Esta función debe ser definida en tu PHP para obtener el ID del amigo

if (idAmigo) {
    obtenerImagenesDeAmigo(idAmigo);
} else {
    console.error('ID de amigo no válido');
}
