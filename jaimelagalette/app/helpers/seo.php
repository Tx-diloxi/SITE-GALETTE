<?php
// Active le typage strict pour tout le fichier
declare(strict_types=1);

// Convertit une chaîne de caractères en slug SEO (URL-friendly)
function slugify(string $text): string
{
    // Tableau de correspondance des caractères accentués et spéciaux
    $chars = [
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a',
        'Ç' => 'C', 'ç' => 'c',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'Ñ' => 'N', 'ñ' => 'n',
        'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'Š' => 'S', 'š' => 's',
        'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ý' => 'Y', 'ý' => 'y', 'ÿ' => 'y',
        'Ž' => 'Z', 'ž' => 'z',
        '\'' => '-', '"' => '', '&' => '-et-', '@' => '-a-',
    ];
    // Remplace les caractères accentués par leurs équivalents ASCII
    $text = strtr($text, $chars);
    // Supprime tout caractère non alphanumérique ou tiret
    $text = preg_replace('/[^a-z0-9-]/', '-', strtolower(trim($text)));
    // Remplace les tirets multiples par un seul tiret
    $text = preg_replace('/-+/', '-', $text);
    // Supprime les tirets en début et fin de chaîne
    return trim($text, '-');
}

// Génère l'URL SEO d'une page produit à partir de son ID et de son nom
function produitUrl(array $produit): string
{
    return '/produit/' . (int)$produit['id'] . '/' . slugify($produit['nom'] ?: $produit['titre']);
}

// Génère l'URL SEO d'une page atelier à partir de son ID et de son nom
function atelierUrl(array $point): string
{
    return '/atelier/' . (int)$point['id'] . '/' . slugify($point['nom'] ?: $point['ville']);
}
