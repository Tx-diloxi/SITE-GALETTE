<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// ── Lecture de la configuration (secrets, identifiants, URL) ─────────────────
//
// env('CLE', 'valeur par défaut') cherche la valeur dans cet ordre :
//   1. les variables d'environnement (Docker en local : fichier .env + docker-compose.yml) ;
//   2. le fichier config.local.php à la racine du projet, HORS du dossier web (hébergement
//      mutualisé type LWS, où l'on ne peut pas définir de variables d'environnement).
// Une valeur vide est considérée comme absente. Aucun secret n'est écrit dans le code.

if (!function_exists('env')) {
    function env(string $cle, ?string $defaut = null): ?string
    {
        static $local = null;

        $valeur = getenv($cle);
        if ($valeur !== false && $valeur !== '') {
            return $valeur;
        }

        if ($local === null) {
            $fichier = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config.local.php';
            $contenu = is_file($fichier) ? require $fichier : [];
            $local = is_array($contenu) ? $contenu : [];
        }
        if (isset($local[$cle]) && $local[$cle] !== '') {
            return (string)$local[$cle];
        }

        return $defaut;
    }
}
