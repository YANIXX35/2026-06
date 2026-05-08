<?php
/**
 * Script de setup InfinityFree — À SUPPRIMER après déploiement
 * Accès : https://ton-site.infinityfreeapp.com/setup.php?key=antigaspi2026
 */

$SECRET_KEY = 'antigaspi2026';

if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    die('<h2 style="color:red;font-family:sans-serif;">❌ Accès refusé. Ajoutez ?key=antigaspi2026 à l\'URL.</h2>');
}

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo '<html><head><meta charset="UTF-8"><style>
body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:20px;}
.ok{color:#16a34a;font-weight:bold;}
.err{color:#dc2626;font-weight:bold;}
pre{background:#f0f0f0;padding:12px;border-radius:8px;font-size:.82rem;overflow:auto;}
h2{color:#1a3a5c;}
</style></head><body>';

echo '<h2>🚀 AntiGaspi CI — Setup production</h2>';

$steps = [
    ['cmd' => 'key:generate', 'args' => ['--force' => true], 'label' => 'Génération APP_KEY'],
    ['cmd' => 'migrate',      'args' => ['--force' => true], 'label' => 'Migration base de données'],
    ['cmd' => 'db:seed',      'args' => ['--force' => true, '--class' => 'DatabaseSeeder'], 'label' => 'Seeding (rôles, catégories, admin)'],
    ['cmd' => 'storage:link', 'args' => [],                  'label' => 'Lien symbolique storage'],
    ['cmd' => 'config:cache', 'args' => [],                  'label' => 'Cache config'],
    ['cmd' => 'route:cache',  'args' => [],                  'label' => 'Cache routes'],
    ['cmd' => 'view:cache',   'args' => [],                  'label' => 'Cache vues'],
];

foreach ($steps as $step) {
    echo '<p>▶ ' . $step['label'] . '...</p>';
    ob_start();
    try {
        $exitCode = $kernel->call($step['cmd'], $step['args']);
        $output   = ob_get_clean();
        if ($exitCode === 0) {
            echo '<p class="ok">✅ ' . $step['label'] . ' — OK</p>';
        } else {
            echo '<p class="err">⚠️ ' . $step['label'] . ' — code ' . $exitCode . '</p>';
        }
        if ($output) echo '<pre>' . htmlspecialchars($output) . '</pre>';
    } catch (\Throwable $e) {
        ob_end_clean();
        echo '<p class="err">❌ Erreur : ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
}

echo '<hr><h3 class="ok">✅ Setup terminé !</h3>';
echo '<p style="color:#dc2626;font-weight:bold;">⚠️ SUPPRIMEZ ce fichier (setup.php) maintenant via le File Manager InfinityFree !</p>';
echo '</body></html>';
