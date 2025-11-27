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
$geminiApiKey = 'AIzaSyCv77d24ByVvUmTyJ0TMhn3Wt-1OoeUuO0'; // Hardcoded
$aiResult = [
    'urgencia' => 'baixa',
    'tags_ai' => [],
    'resumo' => 'Análise pendente (Erro na API)'
];

if ($geminiApiKey) {
    $prompt = "Você é um assistente de triagem de pacientes para uma clínica de ortopedia e medicina intervencionista da dor. Receba as respostas abaixo e devolva um JSON contendo: urgencia (baixa, média, alta), tags_ai (lista com insights), resumo (descrição curta do quadro do paciente). Responda apenas com JSON puro.\n\nDados do paciente:\nNome: $nome\nIdade: $idade\nLocal da dor: $local_dor\nTempo da dor: $tempo_dor\nInteresse na consulta: $interesse";

    // Using gemini-2.0-flash as confirmed in user's model list
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $geminiApiKey;

    $payload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

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