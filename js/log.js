$(document).ready(function() {
    // Ocultamos las cajas de registro y gestión al inicio
    $("#caja-registro").hide();
    $("#caja-gestion").hide();

    // Al hacer click en el botón de registro, ocultamos la caja de login y mostramos la de registro
    $("#reg").click(function () {
        $("#caja-login").hide();
        $("#caja-registro").show();
    });
    //Ajax de nuevo registro, 
    $("#newreg").click(function () {
        var nombre = $("#nombre").val();
        var apellido1 = $("#apellido1").val();
        var email = $("#email").val();
        var user1 = $("#user1").val();
        var pwd1 = $("#pwd1").val();
        var lenguaje = $("#lenguaje").val();

        $.ajax({
            url: "backoffice/usuarios.php",
            type: "POST",
            data: {
                nombre: nombre,
                apellido1: apellido1,
                email: email,
                user1: user1,
                pwd1: pwd1,
                lenguaje: lenguaje,
                option: 1

            },
            //Despues del nuevo registro
            success: function (response) {
                if (response.error) {
                    alert(response.message);
                } else {
                    $.ajax({
                        url: "backoffice/usuarios.php",
                        type: "POST",
                        data: {
                            user1: user1,
                            pwd1: pwd1,
                            option: 2
                        },
                        success: function () {
                            window.location.href = "index.php";
                        }
                    });
                }
            },
            error: function () {
                alert('Error al procesar la solicitud.');
            }
        });
    });
});
