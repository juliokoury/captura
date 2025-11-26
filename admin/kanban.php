<?php
require_once 'auth_check.php';
require_once '../config.php';

// Fetch leads grouped by status
$statuses = ['baixa', 'media', 'alta'];
$leads = [];

foreach ($statuses as $status) {
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE status_kanban = ? ORDER BY created_at DESC");
    $stmt->execute([$status]);
    $leads[$status] = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban - Clínica Premium</title>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        .kanban-col {
            min-height: 500px;
        }

        .card-ghost {
            opacity: 0.5;
            background: #f0f0f0;
        }
    </style>
</head>

<body class="bg-background min-h-screen font-sans">

    <!-- Navbar -->
    <nav class="bg-primary text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="font-serif text-2xl">Gestão de Pacientes</h1>
            <div>
                <span class="mr-4">Olá, Admin</span>
                <a href="../logout.php"
                    class="bg-secondary hover:bg-highlight text-white px-4 py-2 rounded transition-colors">Sair</a>
            </div>
        </div>
    </nav>

    <!-- Kanban Board -->
    <div class="container mx-auto p-6 overflow-x-auto">
        <div class="flex gap-6 min-w-[1000px]">

            <!-- Column: Baixa Urgência -->
            <div class="flex-1 bg-white rounded-xl shadow-md p-4">
                <h2 class="font-serif text-xl text-primary mb-4 border-b-2 border-green-200 pb-2">Baixa Urgência</h2>
                <div id="baixa" class="kanban-col space-y-4" data-status="baixa">
                    <?php foreach ($leads['baixa'] as $lead): ?>
                        <?php include 'components/card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column: Média Urgência -->
            <div class="flex-1 bg-white rounded-xl shadow-md p-4">
                <h2 class="font-serif text-xl text-primary mb-4 border-b-2 border-yellow-200 pb-2">Média Urgência</h2>
                <div id="media" class="kanban-col space-y-4" data-status="media">
                    <?php foreach ($leads['media'] as $lead): ?>
                        <?php include 'components/card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Column: Alta Urgência -->
            <div class="flex-1 bg-white rounded-xl shadow-md p-4">
                <h2 class="font-serif text-xl text-primary mb-4 border-b-2 border-red-200 pb-2">Alta Urgência</h2>
                <div id="alta" class="kanban-col space-y-4" data-status="alta">
                    <?php foreach ($leads['alta'] as $lead): ?>
                        <?php include 'components/card.php'; ?>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        const columns = document.querySelectorAll('.kanban-col');

        columns.forEach(col => {
            new Sortable(col, {
                group: 'kanban',
                animation: 150,
                ghostClass: 'card-ghost',
                onEnd: function (evt) {
                    const itemEl = evt.item;
                    const newStatus = evt.to.getAttribute('data-status');
                    const leadId = itemEl.getAttribute('data-id');

                    // Update status via API
                    fetch('../api/update-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            id: leadId,
                            status: newStatus
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log('Status updated');
                            } else {
                                alert('Erro ao atualizar status');
                            }
                        });
                }
            });
        });
    </script>
</body>

</html>