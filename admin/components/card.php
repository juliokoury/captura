<div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-md transition-shadow cursor-move border-l-4 <?php echo $lead['urgencia'] == 'alta' ? 'border-red-400' : ($lead['urgencia'] == 'media' ? 'border-yellow-400' : 'border-green-400'); ?>"
    data-id="<?php echo $lead['id']; ?>">
    <div class="flex justify-between items-start mb-2">
        <h3 class="font-bold text-primary"><?php echo htmlspecialchars($lead['nome']); ?></h3>
        <span class="text-xs text-gray-500"><?php echo date('d/m H:i', strtotime($lead['created_at'])); ?></span>
    </div>
    <p class="text-sm text-gray-600 mb-1"><strong>Dor:</strong> <?php echo htmlspecialchars($lead['local_dor']); ?></p>
    <p class="text-sm text-gray-600 mb-2"><strong>Tempo:</strong> <?php echo htmlspecialchars($lead['tempo_dor']); ?>
    </p>

    <?php if (!empty($lead['tags_ai'])): ?>
        <div class="flex flex-wrap gap-1 mb-3">
            <?php
            $tags = explode(',', $lead['tags_ai']);
            foreach (array_slice($tags, 0, 3) as $tag):
                ?>
                <span
                    class="bg-background text-secondary text-xs px-2 py-1 rounded-full"><?php echo htmlspecialchars(trim($tag)); ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <a href="detalhes.php?id=<?php echo $lead['id']; ?>"
        class="block text-center text-sm text-highlight font-semibold hover:text-primary transition-colors">Ver Detalhes
        &rarr;</a>
</div>