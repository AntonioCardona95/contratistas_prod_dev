<?php
echo "SI ENTRO A LOGIN1.PHP";
die();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Login de Contratistas
 * Valida usuario y contraseña, inicia sesión y redirige según resultado.
 * Muestra mensajes de error amigables y seguros.
 * 
 * @author  Ing. Antonio Cardona
 * @since   1.0
 * @license MIT
 * 
 * Este script maneja el inicio de sesión de contratistas, validando las credenciales ingresadas.
 * Si las credenciales son correctas, inicia una sesión y redirige al usuario a la página principal.
 * En caso contrario, muestra un mensaje de error y redirige al formulario de inicio de sesión.
 * Este código es parte del sistema de gestión de órdenes de trabajo para contratistas.
 * @version 2.1
 */

date_default_timezone_set('America/Monterrey');
session_start();
require_once("controller/Class_Login.php");

// Función para mostrar mensajes de error y redirigir
function mostrarMensaje($mensaje) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Acceso Contratistas</title>
        <!-- Redirección automática en 3 segundos -->
        <meta http-equiv="refresh" content="3;url=../index.html">
        <link rel="stylesheet" href="../css/stylelogin.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    </head>
    <body>
        <section class="vh-100 gradient-custom">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="card bg-dark text-white" style="border-radius: 1rem;">
                            <div class="card-body p-5 text-center">
                                <div class="mb-md-5 mt-md-4 pb-5">
                                    <center><img src ="../../image/enlace.png" height="70%" width="80%"></center>
                                    <br>
                                    <strong><h4><?= htmlspecialchars($mensaje) ?></h4></strong>
                                </div>
                                <div>
                                    <p class="mb-0">¿Tienes Problemas? 
                                        <a href="https://w.app/GjUY4D" target="_blank" class="text-white-50 fw-bold">Contactar</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
    exit;
}

// Validación de datos POST
if (!empty($_POST['User_Contratista']) && !empty($_POST['Pass_Contratista'])) {
    $User_Contratista = $_POST['User_Contratista'];
    $Pass_Contratista = $_POST['Pass_Contratista'];

    $modal = new Login();
    $rows = $modal->get_user($User_Contratista);

    if (!empty($rows)) {
        // Se asume que solo hay un usuario por nombre
        $row = $rows[0];
        // Comparación segura de contraseñas (idealmente usar hash)
        if ($Pass_Contratista === $row['Pass_Contratista']) {
            $_SESSION['User_Contratista'] = $row['User_Contratista'];
            $_SESSION['Contratista'] = $row['Contratista'];
            $_SESSION['idContratistas'] = $row['idContratistas'];
            $_SESSION['rol'] = $row['rol'] ?? 'CONTRATISTA';
            $_SESSION['contratistaSeleccionado'] = 0;

            // 🚩 Nueva condición: si el usuario es USERP, redirige a indexp.php
            if ($row['User_Contratista'] === 'USERP') {
                header("Location: ../ordenes/indexp.php");
            } else {
                header("Location: ../ordenes/index.php");
            }
            exit;
        } else {
            mostrarMensaje("¡CONTRASEÑA NO COINCIDE!");
        }
    } else {
        mostrarMensaje("¡USUARIO NO EXISTE!");
    }
} else {
    // Si no hay datos POST, redirige al login
    header("Location: ../index.html");
    exit;
}
