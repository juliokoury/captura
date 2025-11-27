<?php
require_once 'auth_check.php';
require_once '../config.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: kanban.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
$stmt->execute([$id]);
$lead = $stmt->fetch();

if (!$lead) {
    echo "Lead não encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Paciente - Clínica Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#564549',
                        secondary: '#767964',
                        background: '#e9eae2',
                        highlight: '#d4af37',
                    },
                    fontFamily: {
                        serif: ['"DM Serif Display"', 'serif'],
                        sans: ['"Raleway"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Raleway:wght@300;400;600&display=swap"
        rel="stylesheet">
</head>

<body class="bg-background min-h-screen font-sans">

    <!-- Navbar -->
    <nav class="bg-primary text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <a href="kanban.php" class="font-serif text-xl hover:text-highlight transition-colors">&larr; Voltar ao
                Kanban</a>
            <h1 class="font-serif text-2xl">Detalhes do Paciente</h1>
        </div>
    </nav>

    <div class="container mx-auto p-6 max-w-4xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">

            <!-- Header -->
            <div class="bg-primary p-6 text-white flex justify-between items-center">
                <div>
                    <h2 class="font-serif text-3xl"><?php echo htmlspecialchars($lead['nome']); ?></h2>
                    <p class="text-secondary text-lg opacity-80">
                        "<?php echo htmlspecialchars($lead['como_quer_ser_chamado']); ?>"</p>
                </div>
                <div class="text-right">
                    <span
                        class="inline-block px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wide 
                        <?php echo $lead['urgencia'] == 'alta' ? 'bg-red-500 text-white' : ($lead['urgencia'] == 'media' ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white'); ?>">
                        Urgência: <?php echo ucfirst($lead['urgencia']); ?>
                    </span>
                    <p class="text-sm mt-2 opacity-75"><?php echo date('d/m/Y H:i', strtotime($lead['created_at'])); ?>
                    </p>
                </div>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Patient Data -->
                <div>
                    <h3 class="font-serif text-2xl text-primary mb-4 border-b border-gray-200 pb-2">Dados Pessoais</h3>
                    <ul class="space-y-3 text-lg">
                        <li><strong class="text-secondary">Idade:</strong>
                            <?php echo htmlspecialchars($lead['idade']); ?> anos</li>
                        <li><strong class="text-secondary">Whatsapp:</strong> <a
                                href="https://wa.me/55<?php echo preg_replace('/\D/', '', $lead['telefone']); ?>"
                                target="_blank"
                                class="text-green-600 hover:underline"><?php echo htmlspecialchars($lead['telefone']); ?></a>
                        </li>
                        <li><strong class="text-secondary">Local da Dor:</strong>
                            <?php echo htmlspecialchars($lead['local_dor']); ?></li>
                        <li><strong class="text-secondary">Tempo da Dor:</strong>
                            <?php echo htmlspecialchars($lead['tempo_dor']); ?></li>
                    </ul>
                </div>

                <!-- AI Analysis -->
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-serif text-2xl text-primary">Análise IA</h3>
                        <button onclick="reanalyzeAI(<?php echo $lead['id']; ?>)" id="btn-reanalyze"
                            class="text-sm bg-highlight text-white px-3 py-1 rounded hover:bg-yellow-600 transition-colors">
                            Reanalisar IA
                        </button>
                    </div>

                    <div class="mb-4">
                        <strong class="block text-secondary mb-1">Resumo do Quadro:</strong>
                        <p class="text-gray-700 italic leading-relaxed">
                            "<?php echo nl2br(htmlspecialchars($lead['resumo_ai'] ?? '')); ?>"</p>
                    </div>

                    <div>
                        <strong class="block text-secondary mb-2">Tags Sugeridas:</strong>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            if (!empty($lead['tags_ai'])):
                                $tags = explode(',', $lead['tags_ai']);
                                foreach ($tags as $tag):
                                    ?>
                                    <span
                                        class="bg-white border border-secondary text-secondary px-3 py-1 rounded-full text-sm"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php
                                endforeach;
                            else:
                                echo "<span class='text-gray-400 text-sm'>Nenhuma tag gerada.</span>";
                            endif;
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function reanalyzeAI(id) {
            const btn = document.getElementById('btn-reanalyze');
            const originalText = btn.innerText;
            btn.innerText = 'Analisando...';
            btn.disabled = true;
            btn.classList.add('opacity-50');

            fetch('../api/reanalyze.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Erro na reanálise: ' + (data.error || 'Desconhecido'));
                        btn.innerText = originalText;
                        btn.disabled = false;
                        btn.classList.remove('opacity-50');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Erro de conexão.');
                    btn.innerText = originalText;
                    btn.disabled = false;
                    btn.classList.remove('opacity-50');
                });
        }
    </script>
</body>

</html>