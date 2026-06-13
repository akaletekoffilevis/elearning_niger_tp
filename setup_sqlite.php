<?php
/**
 * Initialise la base de données SQLite pour le test
 * Usage: DB_DRIVER=sqlite php setup_sqlite.php
 */

// Définir le chemin de la base
$dbPath = __DIR__ . '/app/data/elearning.db';

// Supprimer l'ancienne base si elle existe
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "✓ Ancienne base supprimée\n";
}

// Créer le répertoire data
$dataDir = dirname($dbPath);
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

// Exécuter le schéma SQLite via sqlite3 CLI
$schemaFile = __DIR__ . '/app/migrations/schema_sqlite.sql';
$cmd = sprintf('sqlite3 %s < %s 2>&1', escapeshellarg($dbPath), escapeshellarg($schemaFile));
exec($cmd, $output, $exitCode);

if ($exitCode !== 0) {
    echo "ERREUR lors de l'import du schéma :\n";
    echo implode("\n", $output) . "\n";
    exit(1);
}

echo "✓ Base de données initialisée : {$dbPath}\n";
echo "✓ Schéma importé avec succès\n";
echo "✓ Utilisateur admin créé (admin@elearning.com / 123456)\n";
echo "✓ 5 catégories créées\n";

// Vérifier la base
exec(sprintf('sqlite3 %s "SELECT COUNT(*) FROM users;"', escapeshellarg($dbPath)), $countOut);
echo "✓ Utilisateurs dans la base : " . trim($countOut[0] ?? '0') . "\n";

exec(sprintf('sqlite3 %s "SELECT COUNT(*) FROM categories;"', escapeshellarg($dbPath)), $catCount);
echo "✓ Catégories dans la base : " . trim($catCount[0] ?? '0') . "\n";

echo "\n";
echo "Pour lancer le serveur de test :\n";
echo "  DB_DRIVER=sqlite php -S localhost:8000 -t app/public\n";
echo "\n";
echo "Pour se connecter : admin@elearning.com / 123456\n";
