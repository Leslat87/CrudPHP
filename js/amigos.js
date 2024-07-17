$(document).ready(function () {
    $("#btnEnviarSolicitud").click(function () {
        var nombreUsuarioAmigo = $("#nombreUsuarioAmigo").val();
        enviarSolicitudAmistad(nombreUsuarioAmigo);
    });

    cargarSolicitudesPendientes();
    // envia solicitud de amistad a un usuario existente en la bd
    function enviarSolicitudAmistad(nombreUsuarioAmigo) {
        $.ajax({
            type: "POST",
            url: "/backoffice/usuarios.php",
            data: {
                option: 14,
                nombreUsuarioAmigo: nombreUsuarioAmigo
            },
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición AJAX: " + error);
            }
        });
    }
    // una vez creada la solicitud la mostramos para aceptarla o rechazarla
    function cargarSolicitudesPendientes() {
        $.ajax({
            type: "POST",
            url: "/backoffice/usuarios.php",
            data: {
                option: 16
            },
            success: function (response) {
                
                mostrarSolicitudesPendientes(response);
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición AJAX: " + error);
            }
        });
    }
    // a la hora de mostrarla creamos una lista con los botones para aceptar o rechazar la solicitud, por eso creamos esta funcion
    function mostrarSolicitudesPendientes(solicitudesTexto) {
        var listaSolicitudes = $("#lista-solicitudes");
        listaSolicitudes.empty();

        try {
            var solicitudes = JSON.parse(solicitudesTexto);

            if (Array.isArray(solicitudes) && solicitudes.length > 0) {
                solicitudes.forEach(function (solicitud) {
                    var idAmistad = solicitud.id_amistad;
                    var nombreUsuario = solicitud.nombre_usuario;

                    var listItem = $("<li>").appendTo(listaSolicitudes);
                    listItem.text("Usuario: " + nombreUsuario);

                    var btnAceptar = $("<button>").text("Aceptar").click(function () {
                        aceptarSolicitudAmistad(idAmistad);
                        window.location.href = "amigos.php";
                    });
                    var btnRechazar = $("<button>").text("Rechazar").click(function () {
                        rechazarSolicitudAmistad(idAmistad);
                        window.location.href = "amigos.php";
                    });
                    listItem.append(btnAceptar, btnRechazar);
                });
            } else {
                listaSolicitudes.append($("<li>").text("No tienes solicitudes de amistad pendientes."));
            }
        } catch (error) {
            listaSolicitudes.append($("<li>").text("Error al cargar las solicitudes de amistad."));
        }
    }
    function aceptarSolicitudAmistad(idAmistad) {
        $.ajax({
            type: "POST",
            url: "/backoffice/usuarios.php",
            data: {
                option: 15,
                idAmistad: idAmistad
            },
            success: function (response) {
                alert(response);
                cargarSolicitudesPendientes();
                window.location.href = "amigos.php";
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición AJAX: " + error);
            }
        });
    }

    function rechazarSolicitudAmistad(idAmistad) {
        $.ajax({
            type: "POST",
            url: "/backoffice/usuarios.php",
            data: {
                option: 17,
                idAmistad: idAmistad
            },
            success: function (response) {
                alert(response);
                cargarSolicitudesPendientes();
                window.location.href = "amigos.php";
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición AJAX: " + error);
            }
        });
    }
    // los amigos aceptado son mostrados con esta funcion
    function obtenerListaAmigos() {
        $.ajax({
            type: "POST",
            url: "/backoffice/usuarios.php",
            datatype: "text",
            data: {
                option: 18
            },
            success: function (response) {
                
            },
            error: function (xhr, status, error) {
                console.error("Error en la petición AJAX: " + error);
            }
        });
    }

    $("#logout").click(function () {
        $.ajax({
            url: "/backoffice/usuarios.php",
            type: "POST",
            data: {
                option: 3
            },
            success: function (a) {
                window.location.href = "index.php";
            }
        });
    });

    obtenerListaAmigos();
});
