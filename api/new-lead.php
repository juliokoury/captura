<?php
header('Content-Type: application/json');
require_once '../config.php';

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Extract fields
$nome = $data['nome'] ?? '';
$como_quer_ser_chamado = $data['como_quer_ser_chamado'] ?? '';
$telefone = $data['telefone'] ?? '';
$idade = $data['idade'] ?? 0;
$local_dor = $data['local_dor'] ?? '';
$tempo_dor = $data['tempo_dor'] ?? '';
$interesse = $data['interesse'] ?? 'Não informado';

// Gemini AI Integration
$geminiApiKey = 'AIzaSyCv77d24ByVvUmTyJ0TMhn3Wt-1OoeUuO0'; // Hardcoded as requested
$aiResult = [
    'urgencia' => 'baixa',
    'tags_ai' => [],
    'resumo' => 'Análise pendente (Erro na API)'
];

if ($geminiApiKey) {
    $prompt = "Você é um assistente de triagem de pacientes para uma clínica de ortopedia e medicina intervencionista da dor. Receba as respostas abaixo e devolva um JSON contendo: urgencia (baixa, média, alta), tags_ai (lista com insights), resumo (descrição curta do quadro do paciente). Responda apenas com JSON puro.\n\nDados do paciente:\nNome: $nome\nIdade: $idade\nLocal da dor: $local_dor\nTempo da dor: $tempo_dor\nInteresse na consulta: $interesse";

    // Updated model to gemini-1.5-flash-001
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-001:generateContent?key=" . $geminiApiKey;

    $payload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);

    if ($response) {
        $responseData = json_decode($response, true);
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'];
            // Clean up markdown code blocks if present
            $aiText = str_replace(['```json', '```'], '', $aiText);
            $aiJson = json_decode($aiText, true);

            if ($aiJson) {
                $aiResult['urgencia'] = strtolower($aiJson['urgencia'] ?? 'baixa');
                $aiResult['tags_ai'] = $aiJson['tags_ai'] ?? [];
                $aiResult['resumo'] = $aiJson['resumo'] ?? '';
            }
        }
    }
    curl_close($ch);
}

// Map urgency to valid enum values just in case
$validUrgency = ['baixa', 'media', 'alta'];
if (!in_array($aiResult['urgencia'], $validUrgency)) {
    // Handle 'média' vs 'media' or other variations
    if ($aiResult['urgencia'] == 'média')
        $aiResult['urgencia'] = 'media';
    else
        $aiResult['urgencia'] = 'baixa';
}

// Prepare Data for DB
$tagsString = is_array($aiResult['tags_ai']) ? implode(', ', $aiResult['tags_ai']) : $aiResult['tags_ai'];

try {
    $stmt = $pdo->prepare("INSERT INTO leads (nome, como_quer_ser_chamado, telefone, idade, local_dor, tempo_dor, urgencia, tags_ai, resumo_ai, status_kanban) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $nome,
        $como_quer_ser_chamado,
        $telefone,
        $idade,
        $local_dor,
        $tempo_dor,
        $aiResult['urgencia'],
        $tagsString,
        $aiResult['resumo'],
        $aiResult['urgencia'] // Initial kanban status matches urgency
    ]);

    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>