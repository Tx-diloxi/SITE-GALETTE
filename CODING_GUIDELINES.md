# Guide de Codage - J'aime la Galette

## 1. Règles générales (indépendantes du langage)

- **Clarté avant tout** : le code doit se lire comme une phrase simple.
- **Éviter les abréviations** sauf les très connues (ex. `id`, `url`, `db`).
- **Fonctions courtes** (max 30 lignes, une seule responsabilité).
- **Pas de duplication** : mutualiser les traitements dans les helpers.
- **Sécurité** : échappement systématique (`htmlspecialchars`), requêtes préparées, validation des entrées.
- **Encodage** : UTF-8 sans BOM.

---

## 2. Structure des dossiers et fichiers

| Type               | Convention                                                                 |
|--------------------|----------------------------------------------------------------------------|
| Fichiers PHP       | `snake_case.php` (ex. `config.php`, `handle-contact.php`)                 |
| Pages (routes)     | `home.php`, `groupe.php`, `jaime-la-galette.php` (kebab-case pour l'URL)   |
| Partials           | `header.php`, `hero.php`, `form-contact.php` (nom explicite)              |
| Helpers (librairies)| `products.php`, `faq.php`, `forms.php`                                    |
| Assets CSS/JS      | `style.css`, `carousel.js`, `chatbot.js` (kebab-case si plusieurs mots)   |
| Images             | `galette-bretagne.jpg`, `logo-be-goodn.png` (bas latin, tirets)           |

---

## 3. Nommage des variables, fonctions, constantes

### 3.1 Variables PHP

- **snake_case** pour toutes les variables locales.
- **Préfixe indicatif** : `$isConnected`, `$hasError` (booléens), `$userList` (tableau), `$dbConnection` (ressource).
- Éviter `$data`, `$temp`, `$result` sans contexte.

```php
$productId = 42;
$isProductOnline = true;
$productList = $db->query(...);
$errorMessage = '';
```

### 3.2 Variables superglobales

Pas de renommage inutile. Utiliser directement `$_POST`, `$_GET`, `$_SESSION`.

### 3.3 Fonctions

- **snake_case**, verbe ou verbe+nom.
- Commencer par un verbe d'action : `get`, `set`, `save`, `delete`, `update`, `validate`, `send`, `render`, `redirect`.

```php
function getProductById($id) { ... }
function saveContactMessage($data) { ... }
function renderHeroSection($heroData) { ... }
```

### 3.4 Constantes

- **MAJUSCULES_SOULIGNEES**.
- Définies dans le fichier de configuration propre à chaque module :
  - `app/helpers/page.php` pour les constantes générales du site public (APP_ROOT, BASE_URL, PARTIALS, HELPERS)
  - `app/config/admin.php` pour les constantes du back-office admin (session, upload)
  - `app/config/commercial.php` pour les constantes du module commercial

```php
define('BASE_URL', 'https://jaimelagalette.com');
define('UPLOAD_MAX_SIZE', 5242880);
define('DB_HOST', 'localhost');
```

### 3.5 Noms de classes (même si projet procédural)

Le jour où un autoloader ou une petite classe utilitaire apparaît : **PascalCase** (ex. `DatabaseConnection`, `FormValidator`).

---

## 4. Commentaires en français

### 4.1 Principes

- **Commenter le « pourquoi »**, pas le « quoi » (le code dit déjà quoi).
- **Français clair**, sans jargon excessif.
- Pas de blocs décoratifs (pas de `/**********/`).

### 4.2 En-tête de fichier (optionnel mais utile)

```php
<?php
/**
 * Fichier : partials/hero.php
 * Rôle : Affiche la section héroïque (titre, accroche, image) pour chaque page.
 * Données attendues : $heroData (array avec 'titre', 'accroche', 'image', etc.)
 */
```

### 4.3 Commentaires de fonction

```php
/**
 * Récupère la liste des offres d'emploi actives, triées par date de publication.
 * @param PDO $db      Connexion à la base de données
 * @param int $limit   Nombre maximum d'offres à récupérer (0 = toutes)
 * @return array       Tableau associatif des offres (vide si aucune)
 */
function getActiveJobOffers(PDO $db, int $limit = 0): array {
    ...
}
```

### 4.4 Commentaires internes

```php
// On vérifie d'abord si le fichier a bien été uploadé
if ($_FILES['cv']['error'] !== UPLOAD_ERR_OK) {
    $errors[] = 'Le fichier CV n'a pas pu être reçu.';
}

// Si la requête est AJAX, on retourne du JSON, sinon on redirige
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    ...
}
```

### 4.5 TODO / FIXME

```php
// TODO : Implémenter la pagination des offres d'emploi
// FIXME : Corriger le filtre par catégorie sur la FAQ
```

---

## 5. Bonnes pratiques pour la base de données

### 5.1 Nommage des tables et colonnes

- **Tables** : `snake_case`, singulier de préférence (ex. `produit`, `offre_emploi`, `contact_message`).
- **Colonnes** : `snake_case`.
- **Clé primaire** : toujours `id INT AUTO_INCREMENT`.
- **Clé étrangère** : `table_parent_id` (ex. `produit_id`, `categorie_faq_id`).
- **Colonnes booléennes** : préfixe `is_`, `has_`, `en_ligne`.
- **Colonnes de date** : `created_at`, `updated_at` (type `DATETIME`).

```sql
CREATE TABLE offre_emploi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    contrat VARCHAR(50),
    lieu VARCHAR(100),
    is_published BOOLEAN DEFAULT TRUE,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### 5.2 Requêtes préparées – toujours

```php
$stmt = $pdo->prepare('SELECT * FROM produit WHERE id = :id AND en_ligne = 1');
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch();
```

### 5.3 Gestion des erreurs SQL

Logguer l'erreur (fichier ou table `error_log`), mais ne jamais afficher les détails techniques à l'utilisateur.

```php
try {
    $stmt->execute();
} catch (PDOException $e) {
    error_log('SQL Error: ' . $e->getMessage());
    // Renvoyer une erreur générique à l'utilisateur
    return false;
}
```

---

## 6. PHP procédural – organisation et flux

### 6.1 Fichier d'entrée (`public/index.php`)

- Ne contient **que le routage et le dispatch**.
- Inclut `config.php`, `database.php`, les helpers nécessaires.

### 6.2 Helpers (fichiers dans `app/helpers/`)

- Regroupement par domaine métier (ex. `products.php`, `faq.php`).
- Une seule responsabilité par fonction.
- Pas de sortie directe (`echo`, `print`) sauf si la fonction le précise (ex. `renderSomething()`).

### 6.3 Partials (`app/partials/`)

- Contiennent **uniquement du HTML** + quelques variables PHP échappées.
- Si une boucle ou un `if` est long, le commenter.
- Ne jamais faire de requête SQL dans un partial.

```php
<!-- partials/hero.php -->
<section class="hero" style="background-image: url('<?= htmlspecialchars($heroData['image_fond']) ?>')">
    <h1><?= htmlspecialchars($heroData['titre']) ?></h1>
    <p><?= nl2br(htmlspecialchars($heroData['accroche'])) ?></p>
</section>
```

### 6.4 Inclusion des partials dans les pages

```php
// pages/jaime-la-galette.php
$heroData = getHeroById(5);   // ID correspondant à la page
include PARTIALS . 'header.php';
include PARTIALS . 'hero.php';
include PARTIALS . 'produits-carousel.php';
include PARTIALS . 'fiche-produit.php';
include PARTIALS . 'footer.php';
```

---

## 7. JavaScript – interactions et carrousels

- **Fichiers séparés** par fonctionnalité : `carousel.js`, `chatbot.js`, `forms.js`.
- **Code vanilla** (pas de jQuery).
- **Événements** attachés après le DOMContentLoaded.
- **Nommage** : fonctions `camelCase`, variables `camelCase`, constantes `UPPER_SNAKE_CASE`.

```javascript
// carousel.js
const initProductCarousel = (containerId) => { ... };
const scrollToProductDetail = (productId) => { ... };

document.addEventListener('DOMContentLoaded', () => {
    initProductCarousel('carousel-crepes');
    initProductCarousel('carousel-be-goodn');
});
```

- **Commentaires en français** sur les parties complexes (gestion du swipe, animations).

---

## 8. Formulaires et validation (côté serveur)

- **Toujours valider et assainir** (`filter_var`, `trim`, `strip_tags` sur les champs texte).
- **Vérification CSRF** (jeton stocké en session) pour les formulaires de BO uniquement.
- **Messages d'erreur** stockés dans un tableau `$errors` puis affichés dans le formulaire.

```php
$name = trim($_POST['name'] ?? '');
if (mb_strlen($name) < 2) {
    $errors['name'] = 'Veuillez entrer un nom d'au moins 2 caractères.';
}
```

- **Après succès** : redirection (PRG – Post/Redirect/Get) sauf pour les requêtes AJAX.

---

## 9. Sécurité transversale

| Pratique | Exemple |
|----------|---------|
| Échappement HTML | `htmlspecialchars($text, ENT_QUOTES, 'UTF-8')` |
| Protection XSS | Jamais de `echo $_GET['var']` sans échappement |
| Protection SQL injection | Requêtes préparées |
| Upload de fichiers | Vérifier type MIME réel, limiter taille, renommer |
| Session | `session_start()`, régénérer ID après login |
| .htaccess | Désactiver l'affichage des erreurs en prod, bloquer l'accès aux dossiers `app/` |

Exemple pour `.htaccess` à la racine `public/` :
```apache
Options -Indexes
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]
```

---

## 10. Exemples concrets – commentaires et nommage dans votre contexte

### Exemple 1 – Helper produit

```php
// app/helpers/products.php

/**
 * Retourne les produits actifs pour une marque donnée, triés par ordre.
 * @param PDO    $db     Connexion
 * @param string $brand  'J'aime la Galette' ou 'Be Good'n'
 * @return array
 */
function getProductsByBrand(PDO $db, string $brand): array {
    $sql = 'SELECT * FROM produit WHERE marque = :brand AND en_ligne = 1 ORDER BY ordre ASC';
    $stmt = $db->prepare($sql);
    $stmt->execute(['brand' => $brand]);
    return $stmt->fetchAll();
}
```

### Exemple 2 – Partial de la FAQ

```php
<!-- partials/faq-liste.php -->
<?php
// Données attendues : $categoriesFaq (tableau de catégories contenant leurs questions)
if (empty($categoriesFaq)): ?>
    <p>Aucune question fréquente pour le moment.</p>
<?php else: ?>
    <div class="faq-accordion">
        <?php foreach ($categoriesFaq as $categorie): ?>
            <h3><?= htmlspecialchars($categorie['titre']) ?></h3>
            <?php foreach ($categorie['questions'] as $question): ?>
                <div class="faq-item">
                    <button class="faq-question"><?= htmlspecialchars($question['question']) ?></button>
                    <div class="faq-reponse"><?= nl2br(htmlspecialchars($question['reponse'])) ?></div>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
```

### Exemple 3 – Traitement de formulaire (contact)

```php
// app/helpers/forms.php

/**
 * Traite le formulaire de contact et enregistre le message en base.
 * @param PDO   $db
 * @param array $post Données $_POST
 * @return array ['success' => bool, 'errors' => array]
 */
function handleContactForm(PDO $db, array $post): array {
    $errors = [];
    
    // Validation
    $name = trim($post['name'] ?? '');
    if (empty($name)) $errors['name'] = 'Le nom est requis.';
    
    $email = filter_var($post['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) $errors['email'] = 'Email invalide.';
    
    // ... autres champs
    
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Insertion
    $sql = 'INSERT INTO contact_messages (name, email, message, consent_rgpd, created_at)
            VALUES (:name, :email, :message, :consent, NOW())';
    $stmt = $db->prepare($sql);
    $stmt->execute([
        'name'    => $name,
        'email'   => $email,
        'message' => $post['message'],
        'consent' => (int)($post['rgpd'] ?? 0)
    ]);
    
    return ['success' => true, 'errors' => []];
}
```

---

## 11. Checklist à fournir à l'IA (pour générer ou refactorer le code)

> **Consignes pour l'IA** :  
> - Utilise exclusivement les conventions décrites ci-dessus.  
> - Produis du code PHP procédural, sans classes sauf exceptions justifiées.  
> - Tous les commentaires doivent être en français.  
> - Toute sortie HTML doit passer par `htmlspecialchars()`.  
> - Toute requête SQL doit être préparée.  
> - Respecte la structure de dossiers donnée dans `context.md`.  
> - Pour les interactions JS, utilise du vanilla JS (pas de bibliothèque externe).  
> - Pour le back-office, ajoute une protection CSRF basique (jeton en session).  

Ce guide garantira la **cohérence et la maintenabilité** de l'ensemble du site, même généré ou modifié par une IA.
