<?php
header('Content-Type: application/json; charset=utf-8');

// Incluir configuraciones
require_once '../Config/api_keys.php';
require_once '../Config/Liquour_bdd.php';

// Leer datos del POST
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$userMessage = $data['message'] ?? '';

if (empty($userMessage)) {
    echo json_encode(['error' => 'Mensaje vacío']);
    exit;
}

try {
    // 1. Obtener contexto del sistema desde la base de datos
    $bdd = new BDD();
    $conexion = $bdd->conectar();
    
    // Obtener algunos productos para dar contexto
    $stmt = $conexion->prepare("SELECT nombre FROM productos LIMIT 50");
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $listaProductos = implode(", ", $productos);
    
    $bdd->desconectar();
} catch (Exception $e) {
    $listaProductos = "No se pudo cargar el inventario actual.";
}

// 2. Construir el System Prompt para Gemini
$systemPrompt = "Eres un amable, profesional y experto asistente virtual de 'Liquour', una tienda especializada en la venta de licores premium, vinos reserva y destilados de colección.
Tu tono debe ser elegante pero accesible.
Horario de atención: Lunes a Sábado de 10:00 AM a 10:00 PM.
Ubicación: Avenida Principal #123, esquina con Calle 4 (centro de la ciudad).
Contacto: 555-0192 o contacto@liquour.com.
Productos actualmente en inventario (algunos de ellos): $listaProductos.
Responde de manera concisa a menos que te pidan detalles. No uses formato Markdown complejo en tus respuestas a menos que sea necesario, usa párrafos simples.";

// 3. Llamar a la API de Gemini
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=' . GEMINI_API_KEY;

$requestData = [
    'system_instruction' => [
        'parts' => [
            ['text' => $systemPrompt]
        ]
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.7,
        'maxOutputTokens' => 800,
    ]
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($requestData),
    ],
];

$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode(['error' => 'Error al comunicarse con la IA. Asegúrate de configurar una API Key válida.']);
    exit;
}

$response = json_decode($result, true);
$botText = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Lo siento, tuve un problema procesando tu solicitud.';

echo json_encode(['response' => $botText]);
?>
