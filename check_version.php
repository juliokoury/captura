<?php
header('Content-Type: text/plain');

$file = 'api/reanalyze.php';
if (file_exists($file)) {
    echo "--- Conteúdo do arquivo $file no servidor ---\n\n";
    echo file_get_contents($file);
} else {
    echo "Arquivo $file não encontrado.";
}
?>