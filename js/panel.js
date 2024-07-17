// Declaramos un array para almacenar los IDs
var arrayDeId = [];


$("#botonborrar").click(function () {

    // Iteramos sobre cada checkbox
    $(".cb").each(function () {
        // Si el checkbox está marcado, añadimos su ID al array
        if ($(this).prop("checked")) {
            arrayDeId.push($(this).attr("idid"));
        };
    });

    console.log(arrayDeId);

    $.ajax({
        type: "POST",
        url: "./usuarios.php",
        datatype: "text",
        data: {
            arrayDeId: arrayDeId,
            option: 5

        },
        success: function (respuesta) {
            alert (respuesta)
            location.reload();

        },
        error: function (xhr, status, error) {
            console.error("Error en la petición AJAX: " + error);
        }
    });

});


$(".botonmodificar").click(function () {
    var idbotonmodificar = $(this).attr("idboton");

    var iid = $("#id_" + idbotonmodificar).val();
    var nnombre = $("#nombre_" + idbotonmodificar).val();
    var aapellidos = $("#apellidos_" + idbotonmodificar).val();
    var eemail = $("#email_" + idbotonmodificar).val();
    var uusuario = $("#usuario_" + idbotonmodificar).val();
    var ccontraseña = $("#contraseña_" + idbotonmodificar).val();
    var aadministrador = $("#admin_" + idbotonmodificar).prop("checked");

    if (aadministrador == true) {
        aadministrador = 1;
    } else {
        aadministrador = 0;
    }

    $.ajax({
        type: "post",
        url: "./usuarios.php",
        datatype: "text",
        data: {
            iid: iid,
            nnombre: nnombre,
            aapellidos: aapellidos,
            eemail: eemail,
            uusuario: uusuario,
            ccontraseña: ccontraseña,
            aadministrador: aadministrador,
            option: 4
        },
        success: function () {
            alert("Los datos fueron modificados");
            location.reload();
        }
    });
});


$("#botonAñadirUsuario").click(function () {
    var nombre = $("#nombre").val();
    var apellido1 = $("#apellido1").val();
    var email = $("#email").val();
    var user1 = $("#user1").val();
    var pwd1 = $("#pwd1").val();

    $.ajax({
        url: "usuarios.php",
        type: "POST",
        data: {
            nombre: nombre,
            apellido1: apellido1,
            email: email,
            user1: user1,
            pwd1: pwd1,
            option: 6
        },
        success: function () {
            alert("El nuevo usuario fue añadido correctamente");
            location.reload();
        },
    });
    
});
// Controlador de eventos clic para el botón de logout en index.php
$('#logout').click(function() {
    // Realizar una solicitud al servidor para llamar a la función logoutUser
    $.ajax({
        type: 'POST',
        url: '/backoffice/usuarios.php',
        data: { option: 3 },
        success: function(response) {
            window.location.href = 'log.php';
        }
    });
});


