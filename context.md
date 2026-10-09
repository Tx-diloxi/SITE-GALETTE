# Contexte projet – Site "J'aime la Galette" (version mise à jour)

Ce fichier décrit le contexte fonctionnel et technique du site vitrine "J'aime la Galette", destiné à guider un agent IA (et les développeurs) pour la génération de contenu, de code et d'assets.

---

## 1. Objectifs du site

- Présenter le groupe J'aime la Galette et ses marques (J'aime la Galette, Be Good'n).
- Mettre en avant les produits (crêpes, galettes, chips, caramel, cidre) avec un catalogue piloté par la base de données.
- Valoriser le savoir‑faire industriel et artisanal, ainsi que les engagements RSE.
- Générer des contacts qualifiés (consommateurs, distributeurs, collectivités, fournisseurs, partenaires).
- Attirer des candidats RH via une page Recrutement dynamique.
- Améliorer le SEO via une FAQ indexable et un chatbot connecté à la base de connaissances.
- **Optimiser le référencement naturel (SEO classique + LLMO/GEO)** : titres uniques, meta descriptions riches, données structurées JSON-LD (Organization, BreadcrumbList, FAQPage, Product, LocalBusiness), sitemap.xml, robots.txt, blocs "En résumé" pour IA.

---

## 2. Publics cibles

- Consommateurs finaux / grand public.
- Distributeurs GMS, restauration collective, centrales d'achat.
- Fournisseurs, partenaires éthiques.
- Candidats RH.
- Presse / grand public intéressé par les engagements RSE.

---

## 3. Stack technique & hébergement

- **Front** : HTML5, CSS3, JavaScript (vanilla). SCSS compilé en CSS pour les styles.
- **Back** : PHP 8.x procédural, PDO pour l'accès MySQL. **Pas de framework, pas de MVC.**
- **Base de données** : MySQL 8.0 avec InnoDB, encodage `utf8mb4`, collation `utf8mb4_unicode_ci`.
- **Environnement local** : Docker (PHP 8.2-Apache + MySQL 8.0 + phpMyAdmin), défini dans `docker-compose.yml` à la racine du projet, config via `.env`.
- **Hébergeur cible** : offre mutualisée PHP/MySQL classique (LWS).
- **URL propres** via `.htaccess` et front controller `public/index.php`.
- **Dépendances Composer** : `phpmailer/phpmailer` pour l'envoi d'emails via SMTP (STARTTLS, port 587).

---

## 4. Architecture applicative

### 4.1. Principe général

L'architecture repose sur du **PHP procédural classique**, sans pattern MVC. Toutes les pages sont générées **dynamiquement à partir de la base de données**. Chaque page est construite en assemblant des **partials** (fichiers PHP inclus), chacun représentant une section de la page.

Le mécanisme de construction d'une page fonctionne ainsi :

1. Le **front controller** (`public/index.php`) lit l'URL entrante et détermine quelle page charger.
2. Le fichier de la page interroge la BDD pour récupérer les données de la page.
3. Il **transmet les données dans l'en-tête** de chaque partial (via des variables PHP passées avant l'`include`).
4. Les partials sont inclus séquentiellement pour composer la page complète.
5. Chaque page inclut un fichier CSS spécifique dans `<link>` avant le `<main>`.

> **Exemple de composition d'une page :**
> ```php
> // Récupération des données depuis la BDD
> $footer = $pdo->query("SELECT * FROM footer WHERE id = 1")->fetch();
> $hero   = $pdo->query("SELECT * FROM hero WHERE id = 1")->fetch();
> $apropos = $pdo->query("SELECT * FROM partial_Apropos WHERE id = 1")->fetch();
>
> // Composition de la page par inclusion de partials
> include PARTIALS . 'head.php';
> include PARTIALS . 'header.php';
> // [page-specific CSS link]
> include PARTIALS . 'hero.php';
> include PARTIALS . 'apropos.php';
> include PARTIALS . 'footer.php';
> include PARTIALS . 'chatbot-widget.php';
> ```

---

### 4.2. Structure de dossiers

```text
jaimelagalette/
├── .env                          # variables d'environnement (DB, admin, mail)
├── config.php                    # connexion PDO (variables d'environnement)
├── composer.json                 # phpmailer/phpmailer
├── composer.lock
├── public/
│   ├── index.php                 # front controller (routing + dispatch)
│   ├── .htaccess                 # réécriture d'URL vers index.php
│   ├── chatbot.php               # API endpoint pour le chatbot FAQ
│   ├── text.php                  # page textuelle minimale (mentions légales...)
│   ├── sitemap.php               # sitemap XML dynamique
│   ├── robots.txt                # contrôle des crawlers
│   ├── admin/
│   │   ├── index.php             # dashboard backoffice
│   │   ├── login.php             # authentification
│   │   ├── logout.php            # déconnexion
│   │   ├── README-ADMIN.md       # documentation du backoffice
│   │   ├── layout/               # templates header + footer admin
│   │   ├── pages/                # éditeur de pages (list + edit + partials/)
│   │   ├── produits/             # CRUD Produits
│   │   ├── marques/              # CRUD Marques
│   │   ├── sites/                # CRUD Sites de production
│   │   ├── candidatures/         # consultation des candidatures
│   │   ├── contacts/             # consultation des messages contact
│   │   ├── faq/                  # CRUD FAQ (catégories + questions)
│   │   ├── commercial/           # configuration visites mystères
│   │   ├── commerciaux/          # CRUD commerciaux
│   │   ├── livreurs/             # CRUD livreurs
│   │   └── visites-mysteres/     # résultats visites mystères
│   └── assets/
│       ├── css/
│       │   ├── _variables.scss   # variables SCSS globales
│       │   ├── style.scss        # SCSS source principal
│       │   ├── style.css         # SCSS compilé
│       │   ├── style.css.map
│       │   ├── page/             # CSS par page (admin, commercial, contact, faq, le-groupe, nos-produits, recrutement, rse, savoir-faire)
│       │   └── partials/         # CSS par partial (29 dossiers)
│       ├── js/
│       │   ├── admin.js
│       │   ├── chatbot.js
│       │   └── hero.js
│       ├── images/
│       │   ├── hero-bg.jpg
│       │   ├── logo_jaimelagalette.png
│       │   ├── mascotte_*.png
│       │   ├── vague*.svg
│       │   ├── admin-uploads/    # images uploadées via le backoffice
│       │   └── (ingredient/, intro/, label/, partenaire/, produit/, rse/, savoir-faire/)
│       └── uploads/
│           └── cv/               # CV uploadés via le formulaire recrutement
├── app/
│   ├── config/
│   │   ├── config.php            # constantes (APP_ROOT, PARTIALS, HELPERS, BASE_URL)
│   │   ├── database.php          # inclut config.php racine, retourne $pdo
│   │   ├── admin.php             # constantes admin, fonctions auth, upload, CSRF
│   │   ├── mail.php              # configuration SMTP (MAIL_HOST, MAIL_PORT...)
│   │   └── commercial.php        # constantes et auth pour module commercial/visites mystères
│   ├── helpers/
│   │   ├── page.php              # constantes (APP_ROOT, PARTIALS, HELPERS, BASE_URL)
│   │   ├── forms.php             # traitement formulaire contact + candidature
│   │   ├── mailer.php            # envoi d'email via PHPMailer (SMTP)
│   │   ├── horaires.php          # utilitaires de gestion des horaires
│   │   └── seo.php               # fonctions utilitaires SEO (URLs produits, ateliers)
│   └── partials/
│       ├── head.php
│       ├── header.php
│       ├── footer.php
│       ├── chatbot-widget.php
│       ├── hero.php
│       ├── intro.php
│       ├── apropos.php
│       ├── chiffres-groupe.php
│       ├── valeurs.php
│       ├── cta-produits.php
│       ├── carte.php
│       ├── partenaires.php
│       ├── engagements.php
│       ├── contact.php
│       ├── histoire.php
│       ├── actions-rse.php
│       ├── animation.php
│       ├── livraison.php
│       ├── transparence.php
│       ├── label.php
│       ├── carrousel.php
│       ├── essentiel.php
│       ├── circuit-court.php
│       ├── infos-savoir.php
│       ├── formulaire-cta.php
│       ├── pourquoi-nous-rejoindre.php
│       ├── processus-recrutement.php
│       ├── offres-emploi.php
│       └── formulaire-recrutement.php
├── pages/
│   ├── home.php                        # Accueil (/)
│   ├── 404.php                         # Page 404
│   ├── le-groupe/index.php             # Le Groupe (/le-groupe)
│   ├── nos-produits/index.php          # Nos produits (/nos-produits)
│   ├── nos-produits/jaime-la-galette/index.php  # J'aime la Galette (/nos-produits/jaime-la-galette)
│   ├── nos-produits/be-goodn/index.php          # Be Good'n (/nos-produits/be-goodn)
│   ├── savoir-faire/index.php          # Savoir-faire (/savoir-faire)
│   ├── rse/index.php                   # RSE (/rse)
│   ├── recrutement/index.php           # Recrutement (/recrutement)
│   ├── contact/index.php               # Contact (/contact)
│   ├── faq/index.php                   # FAQ (/faq)
│   ├── mentions-legales/index.php      # Mentions légales (/mentions-legales)
│   ├── confidentialite/index.php       # Politique de confidentialité (/politique-confidentialite)
│   ├── cookies/index.php               # Cookies (/cookies)
│   └── produit/index.php               # Fiche produit individuelle (/produit/{id})
└── vendor/                             # Composer autoload (phpmailer)
```

---

### 4.3. Routage (front controller)

Le fichier `public/index.php` lit l'URL et fait correspondre chaque route à un fichier dans `pages/` :

```php
$routes = [
    ''                               => 'pages/home.php',
    'le-groupe'                      => 'pages/le-groupe/index.php',
    '404'                            => 'pages/404.php',
    'nos-produits'                   => 'pages/nos-produits/index.php',
    'savoir-faire'                   => 'pages/savoir-faire/index.php',
    'rse'                            => 'pages/rse/index.php',
    'faq'                            => 'pages/faq/index.php',
    'recrutement'                    => 'pages/recrutement/index.php',
    'contact'                        => 'pages/contact/index.php',
    'nos-produits/jaime-la-galette'  => 'pages/nos-produits/jaime-la-galette/index.php',
    'nos-produits/be-goodn'          => 'pages/nos-produits/be-goodn/index.php',
    'sitemap.xml'                    => 'public/sitemap.php',
    'mentions-legales'               => 'pages/mentions-legales/index.php',
    'politique-confidentialite'      => 'pages/confidentialite/index.php',
    'cookies'                        => 'pages/cookies/index.php',
    'admin'                          => 'public/admin/index.php',
];
```

Routes SEO :
- `sitemap.xml` → `public/sitemap.php` (sitemap dynamique avec les pages publiques)
- `robots.txt` → fichier statique `public/robots.txt`

---

### 4.4. Arborescence des URLs du site

```text
jaimelagalette.com/
│
├── /                              → Accueil
├── /le-groupe                     → Le Groupe
├── /nos-produits                  → Nos produits
│   ├── /nos-produits/jaime-la-galette
│   └── /nos-produits/be-goodn
├── /savoir-faire                  → Notre savoir-faire
├── /rse                           → Nos engagements RSE
├── /recrutement                   → Recrutement
├── /contact                       → Contact
├── /faq                           → FAQ (chatbot)
├── /mentions-legales              → Mentions légales
├── /politique-confidentialite     → Politique de confidentialité
├── /cookies                       → Cookies
└── /admin                         → Backoffice
```

---

## 5. Données dynamiques & base MySQL

Le schéma de base de données est défini dans `docker/sql/01_schema.sql`. Il utilise `utf8mb4`, InnoDB, et la collation `utf8mb4_unicode_ci`.

### 5.1. Composants globaux / partagés

#### Table `marque`
- `id`, `nom`, `slug` (UNIQUE), `logo`, `alt_logo`, `description`, `couleur_hex`, `en_ligne`, `ordre`

#### Table `hero`
- `id`, `titre`, `accroche`, `image`, `alt`, `image_fond`, `alt_fond`

#### Tables `footer`, `footer_lien`, `footer_legal`
- `footer` : logo, alt_logo, description, copyright.
- `footer_lien` : liens groupés par `categorie`, triés par `ordre`.
- `footer_legal` : liens légaux, triés par `ordre`.

#### Table `partial_Intro`
- `id`, `sous_titre`, `titre`, `citation`, `contenu`, **`nom_page`** (clé de filtrage), `image_fond`, `alt_fond`, `image_mascotte`, `alt_mascotte`, `marque_id` (FK → `marque`)

#### Table `partial_Apropos`
- `id`, `sous_titre`, `titre`, `contenu`, `cta_label`, `cta_lien`, `image`, `alt`

#### Tables `partial_Contact` + `card_Contact`
- `partial_Contact` : sous-titre, titre.
- `card_Contact` : titre, description, CTA, image.

#### Tables `partial_Label` + `card_Label`
- `partial_Label` : sous-titre, titre.
- `card_Label` : logo, alt, lien, ordre.

#### Tables `partial_Carte` + `point_Carte`
- `partial_Carte` : sous-titre, titre, image_mascotte, alt_mascotte.
- `point_Carte` : type_site, nom, adresse, code_postal, ville, telephone, email, **email_rh**, departement, est_ouvert, latitude, longitude.

#### Table `horaire_Site`
- Liée à `point_Carte` (FK `point_carte_id`), stocke les horaires d'ouverture par jour (jour, ouverture, fermeture).

#### Tables `partial_Produit` + `card_Produit`
- `partial_Produit` : sous-titre, titre.
- `card_Produit` : image, marque, marque_id (FK → `marque`), tags, description, CTA.

#### Tables `partial_Valeur` + `card_Valeur`
- `partial_Valeur` : sous-titre, titre, image_fond, alt_fond.
- `card_Valeur` : image, titre, description.

#### Tables `partial_Partenaire` + `partenaire`
- `partial_Partenaire` : sous-titre, titre.
- `partenaire` : logo, alt, lien, ordre.

#### Table `action_RSE`
- `id`, `image`, `alt`, `titre`, `description`, `contenu`, `tags`, `ordre`

---

### 5.2. Page Accueil (`/`)

#### Tables `partial_Chiffre_Groupe` + `card_Chiffre_Groupe`
- `partial_Chiffre_Groupe` : sous-titre, titre, CTA optionnel.
- `card_Chiffre_Groupe` : image, titre, description.

#### Tables `partial_Engagement` + `card_Engagement`
- `partial_Engagement` : sous-titre, titre, CTA optionnel.
- `card_Engagement` : image, titre, description.

---

### 5.3. Page Le Groupe (`/le-groupe`)

#### Tables `partial_Histoire` + `etape_Histoire`
- `partial_Histoire` : sous-titre, titre, CTA.
- `etape_Histoire` : année, nom, région, description, image, ordre.

#### Table `partial_Engagement_Groupe`
- `id`, `sous_titre`, `titre`, `contenu`, `image`, `alt`, `cta_label`, `cta_lien`

#### Tables `partial_Animation` + `card_Animation`
- `partial_Animation` : sous-titre, titre, contenu.
- `card_Animation` : nom, image.

#### Table `partial_Livraison`
- `id`, `sous_titre`, `titre`, `contenu`, `cta_label`, `cta_lien`, `image_fond`, `alt_fond`

#### Table `partial_CTA_Produit`
- `id`, `titre`, `cta_label`, `cta_lien`, `cta_label2`, `cta_lien2`

---

### 5.4. Page Nos Produits (`/nos-produits`)

#### Tables `partial_Transparence` + `card_Transparence`
- `partial_Transparence` : sous-titre, titre, contenu intro.
- `card_Transparence` : image, titre, description.

---

### 5.5. Page Savoir-Faire (`/savoir-faire`)

#### Tables `partial_SavoirFaire` + `card_SavoirFaire`
- `partial_SavoirFaire` : sous-titre, titre, CTA.
- `card_SavoirFaire` : sous-titre, titre, contenu, image_fond, image_label, ordre.

---

### 5.6. Fiches Produits (`/nos-produits/jaime-la-galette`, `/nos-produits/be-goodn`, `/produit/{id}`)

#### Table `produit`
- `id`, `sous_titre`, `titre`, `marque`, `marque_id` (FK → `marque`), `nom`, `cta_label`, `cta_lien`, `image`, `en_ligne`

#### Table `produit_Apropos`
- `id`, `produit_id` (FK → `produit`), `sous_titre`, `titre`, `contenu`, `cta_label`, `cta_lien`, `image`, `alt`

#### Tables `partial_Ingredient` + `card_Ingredient`
- `partial_Ingredient` : `produit_id` (FK → `produit`), sous-titre, titre, image_fond.
- `card_Ingredient` : image, titre, description, ordre.

---

### 5.7. Page Recrutement (`/recrutement`)

#### Tables `partial_Recrutement` + `card_Recrutement`
- `partial_Recrutement` : sous-titre, titre.
- `card_Recrutement` : image, titre, description, ordre.

#### Tables `partial_Processus` + `etape_Processus`
- `partial_Processus` : sous-titre, titre.
- `etape_Processus` : titre, description, ordre.

#### Tables `partial_Offre` + `offre_Emploi`
- `partial_Offre` : sous-titre, titre.
- `offre_Emploi` : titre, contrat, description, lieu, cta_label, cta_lien, date_publication, en_ligne, **point_carte_id** (FK → `point_Carte`).

#### Table `candidature_spontanee`
- `id`, `titre`, `description`, `cta_label`, `cta_lien`, `en_ligne`

---

### 5.8. Page FAQ (`/faq`)

#### Tables `partial_FAQ` + `categorie_FAQ` + `question_FAQ`
- `partial_FAQ` : sous-titre, titre, contenu intro.
- `categorie_FAQ` : titre, ordre — lié à `partial_FAQ`.
- `question_FAQ` : question, réponse, mots_cles, profil_cible, ordre, en_ligne — lié à `categorie_FAQ`. Index FULLTEXT sur `(question, mots_cles)`.

#### Table `chatbot_log`
- Stocke l'historique des questions posées au chatbot, avec la question brute, la question FAQ matchée (FK → `question_FAQ`), le profil, l'URL de la page, et la date.

---

### 5.9. Tables de formulaires

#### Table `formulaire_contact`
Stocke les messages du formulaire de contact (B2C et B2B).
- `id`, `profil` (ENUM b2c/b2b), `nom`, `email`, `sujet`, `message`, `rgpd_accepte`, `societe`, `telephone`, `profil_b2b`, `statut` (ENUM nouveau/lu/traite/archive), `cree_le`, `traite_le`, `note_admin`

#### Table `applications`
Stocke les candidatures du formulaire recrutement (offres + spontanées).
- `id`, `prenom`, `nom`, `email`, `poste_souhaite`, `type_candidature`, `offre_emploi_id` (FK → `offre_Emploi`), `message`, `cv_path`, `rgpd_accepte`, `cree_le`, `lue`

---

### 5.10. Tables module commercial / visites mystères

#### Table `commercial`
- `id`, `nom`, `email`, `telephone`, `statut`, `cree_le`

#### Table `livreur`
- `id`, `nom`, `email`, `telephone`, `statut`, `cree_le`

#### Tables `inspection_question`, `inspection`, `inspection_reponse`, `inspection_photo`
- Questionnaire d'inspection / visites mystères, avec questions, réponses et photos.

---

### 5.11. Conventions du schéma

- Toutes les tables : `ENGINE = InnoDB`, `CHARSET = utf8mb4`, `COLLATE = utf8mb4_unicode_ci`.
- Clés primaires : `INT AUTO_INCREMENT PRIMARY KEY`.
- Clés étrangères : `ON DELETE CASCADE ON UPDATE CASCADE` (sauf `SET NULL` quand approprié).
- Champs `ordre INT DEFAULT 0` pour le tri.
- Champs `en_ligne` / `est_ouvert` : `BOOLEAN NOT NULL DEFAULT FALSE/TRUE`.
- Chemins d'images : relatifs vers `public/assets/images/`.

---

## 6. Structure des pages & contenus

### 6.1. `/` — Accueil (`pages/home.php`)

Sections & partials associés :
- Hero : `hero`
- À propos : `partial_Apropos`
- Chiffres clés : `partial_Chiffre_Groupe` + `card_Chiffre_Groupe`
- Valeurs : `partial_Valeur` + `card_Valeur`
- Nos produits : `partial_Produit` + `card_Produit` (avec jointure sur `marque`)
- Carte : `partial_Carte` + `point_Carte`
- Partenaires : `partial_Partenaire` + `partenaire`
- Engagements : `partial_Engagement` + `card_Engagement`
- Contact : `partial_Contact` + `card_Contact`

### 6.2. `/le-groupe` — Le Groupe (`pages/le-groupe/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (`nom_page = 'le-groupe'`)
- Histoire : `partial_Histoire` + `etape_Histoire`
- Carte : `partial_Carte` + `point_Carte`
- Valeurs : `partial_Valeur` + `card_Valeur`
- Engagements : `partial_Engagement` + `card_Engagement`
- Actions RSE : `action_RSE`
- Animation : `partial_Animation` + `card_Animation`
- Livraison : `partial_Livraison`
- Contact : `partial_Contact` + `card_Contact`

CSS spécifique : `assets/css/page/le-groupe/le-groupe.css`

### 6.3. `/nos-produits` — Nos produits (`pages/nos-produits/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (`nom_page = 'nos-produits'`)
- Cartes produits : `partial_Produit` + `card_Produit`
- Transparence : `partial_Transparence` + `card_Transparence`
- Labels : `partial_Label` + `card_Label`
- Contact : `partial_Contact` + `card_Contact`

CSS spécifique : `assets/css/page/nos-produits/nos-produits.css`

### 6.4. `/nos-produits/jaime-la-galette` et `/nos-produits/be-goodn`

Sections & partials associés :
- Carrousel produits : `produit` (filtrés par marque / `en_ligne = TRUE`)
- À propos du produit : `produit_Apropos`
- Essentiel / Ingrédients : `partial_Ingredient` + `card_Ingredient`
- Labels : `partial_Label` + `card_Label`
- Contact : `partial_Contact` + `card_Contact`

CSS spécifique : `assets/css/page/nos-produits/marque/marque.css`

### 6.5. `/savoir-faire` — Savoir-faire (`pages/savoir-faire/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (`nom_page = 'savoir-faire'`)
- Étapes savoir-faire : `partial_SavoirFaire` + `card_SavoirFaire`
- Contact : `partial_Contact` + `card_Contact`

CSS spécifique : `assets/css/page/savoir-faire/savoir-faire.css`

### 6.6. `/rse` — RSE (`pages/rse/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (`nom_page = 'rse'`)
- Actions RSE : `action_RSE`
- À propos : `partial_Apropos` (id = 2)
- Partenaires : `partial_Partenaire` + `partenaire`
- Contact : `partial_Contact` + `card_Contact`

CSS spécifique : `assets/css/page/rse/rse.css`

### 6.7. `/recrutement` — Recrutement (`pages/recrutement/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (`nom_page = 'recrutement'`)
- Pourquoi nous rejoindre : `partial_Recrutement` + `card_Recrutement`
- Processus : `partial_Processus` + `etape_Processus`
- Offres d'emploi : `partial_Offre` + `offre_Emploi`
- Formulaire candidature : `candidature_spontanee`

CSS spécifique : `assets/css/page/recrutement/recrutement.css`

### 6.8. `/contact` — Contact (`pages/contact/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (`nom_page = 'contact'`)
- Formulaire CTA : `formulaire-cta.php` (POST vers `index.php` pour traitement)
- Carte : `partial_Carte` + `point_Carte`

CSS spécifique : `assets/css/page/contact/contact.css`

### 6.9. `/faq` — FAQ (`pages/faq/index.php`)

Sections & partials associés :
- Intro : `partial_Intro` (sans filtre `nom_page`)
- FAQ structurée : `partial_FAQ` + `categorie_FAQ` + `question_FAQ` (accordéon + recherche JS)

CSS spécifique : `assets/css/page/faq/faq.css`

### 6.10. Pages légales

- `/mentions-legales` (`pages/mentions-legales/index.php`)
- `/politique-confidentialite` (`pages/confidentialite/index.php`)
- `/cookies` (`pages/cookies/index.php`)

---

## 7. Comportements transverses

### 7.1. Navigation

- Menu principal : Accueil, Le Groupe, Nos produits, Notre savoir‑faire, RSE, FAQ, Recrutement, Contact.

### 7.2. Chatbot global

- Widget flottant inclus sur toutes les pages via `chatbot-widget.php` (dernier include avant `</body>`).
- Le chatbot interroge l'API via `public/chatbot.php` (endpoint POST) qui match la question utilisateur sur `question_FAQ` via FULLTEXT + scoring.
- Logging des questions dans `chatbot_log`.

### 7.3. RGPD

- Case de consentement obligatoire sur tous les formulaires (Contact, Recrutement).
- Pages Mentions légales, Politique de confidentialité et Cookies routées et accessibles.

### 7.4. Styles et assets

- **SCSS** : source dans `assets/css/style.scss`, compilé vers `style.css`. Variables globales dans `_variables.scss`.
- **CSS par page** : fichiers dans `assets/css/page/{page}/` chargés via `<link>` dans chaque page.
- **CSS par partial** : fichiers dans `assets/css/partials/{partial}/` chargés via le mécanisme `static $styleLoaded` dans chaque partial.
- **JS** : fichiers séparés (`hero.js`, `chatbot.js`, `admin.js`), JS vanilla uniquement.

---

## 8. Backoffice (admin)

- Accès `/admin` → routé vers `public/admin/index.php` via le front controller.
- Authentification par session avec timeout de 2h et blocage 15 min après 5 tentatives échouées.
- Token CSRF sur tous les formulaires POST.
- CRUD complet : pages (21 formulaires de section), produits, marques, sites de production, FAQ (catégories + questions), consultation des candidatures et messages contact.
- Module commercial : gestion des commerciaux, livreurs, visites mystères et résultats d'inspection.
- Upload d'images limité à 5 Mo (JPEG, PNG, WebP, SVG).
- Les constantes et fonctions d'authentification sont dans `app/config/admin.php`.
- Identifiants par défaut : `admin` / `admin123` (hash bcrypt dans `.env`).

---

## 9. Fichiers de référence

- `context.md` — présent document.
- `AGENTS.md` — instructions pour l'agent IA.
- `CODING_GUIDELINES.md` — guide de codage détaillé (nommage, conventions, sécurité).
- `.github/copilot-instructions.md` — instructions de refactorisation pour GitHub Copilot.
- `command.md` — commandes utiles (Docker, Composer).
- `docker/sql/01_schema.sql` — schéma complet de la base de données.
- `docker/sql/02_seeds.sql` — données de démonstration.
- `maquette/` — maquettes de référence desktop (fichiers `.txt`).

---

## 10. Développement local

```bash
# Démarrer l'environnement
docker compose up -d

# Arrêter et réinitialiser (perte des données)
docker compose down -v

# Installation des dépendances Composer (dans le conteneur)
docker compose exec php composer install

# URL locales
# Site : http://localhost:8080
# phpMyAdmin : http://localhost:8081
```

Fichier `.env` (à la racine du dépôt) :
```
DB_NAME=jaimelagalette
DB_USER=jalg_user
DB_PASS=jalg_secret
DB_ROOT_PASS=root_secret
MAIL_USERNAME=siteweb@jaimelagalette.com
MAIL_PASSWORD=
MAIL_DEBUG=0
ADMIN_USER=admin
ADMIN_PASS_HASH=
```

---

## 11. Envoi d'emails

- **Bibliothèque** : PHPMailer 7.x via Composer.
- **Serveur SMTP** : `mail.jaimelagalette.com` (LWS), port 587, STARTTLS.
- **Authentification** : utilisateur + mot de passe depuis `.env` (`MAIL_USERNAME`, `MAIL_PASSWORD`).
- **Expéditeur** : `siteweb@jaimelagalette.com` (« J'aime la Galette »).
- **Destinataire par défaut** : `siteweb@jaimelagalette.com` (fallback si pas de destinataire spécifique).
- **Configuration** : `app/config/mail.php` définit les constantes SMTP depuis `$_ENV`.
- **Envoi** : `app/helpers/mailer.php` (`sendMail()`) gère l'envoi SMTP avec PHPMailer, charset UTF-8, pièces jointes optionnelles.
- **Routage recrutement** : les candidatures partent vers `email_rh` du site concerné (via `offre_Emploi.point_carte_id` ou sélecteur de site en candidature spontanée).
- **Destinataire contact** : les messages du formulaire de contact partent vers `MAIL_TO` (`siteweb@jaimelagalette.com`), avec Reply-To de l'expéditeur.

---

## 12. Positionnement SEO & LLMO/GEO

### 12.1. Stratégie SEO classique

- **Titres uniques** : chaque page a un `<title>` formaté `[Mot-clé principal] | J'aime la Galette`.
- **Meta descriptions** : phrases de 150-160 caractères répondant à une intention de recherche.
- **Balises `<h1>`** : unique par page, reprend la requête principale avec la marque.
- **Balises `<h2>`** : formulées en questions pour le GEO.
- **Maillage interne** : liens contextuels entre les pages.
- **Données structurées** (JSON-LD) :
  - `Organization` sur toutes les pages.
  - `BreadcrumbList` sur toutes les pages.
  - `FAQPage` sur la page FAQ.
  - `Product` sur les pages marque.
  - `LocalBusiness` sur la page Le Groupe.
- **Performance** : CSS minifié par page, images optimisées, Core Web Vitals visés ≥ 80 mobile.

### 12.2. Optimisation LLMO / Generative Engine Optimization (GEO)

- **Titres en mode question** : certains H2 sont formulés comme des questions pour être capturés par les LLM.
- **Contenu factuel** : langage clair avec données précises (années, lieux, chiffres).
- **Context unique (context.md)** : document de référence aligné avec le contenu du site, utilisable par les agents IA.

### 12.3. Fichiers SEO

| Fichier | Rôle | Emplacement |
|---------|------|-------------|
| `head.php` | Balises meta, OG, Twitter Cards, canonical, JSON-LD | `app/partials/head.php` |
| `robots.txt` | Contrôle des crawlers | `public/robots.txt` |
| `sitemap.php` | Sitemap XML dynamique | `public/sitemap.php`, routé via `index.php` |
| `llms.txt` | Résumé factuel du site pour les assistants IA (GEO) : marque, ateliers, pages clés | `public/llms.txt` |
| `paths.php` | `SITE_URL` : domaine public unique (canonical, Open Graph, sitemap, JSON-LD). Surchargeable par la variable d'environnement `SITE_URL` | `app/config/paths.php` |
| `seo.php` | `siteUrl()`, `jsonLd()`, `atelierSchema()` (LocalBusiness avec GPS) | `app/helpers/seo.php` |

**À vérifier à la mise en ligne** : `SITE_URL` (défaut `https://www.jaimelagalette.com`) et la ligne `Sitemap:` de `robots.txt` doivent correspondre au domaine définitif.

Ce document doit servir de base unique de référence pour tout agent IA chargé de générer code, contenu ou documentation autour du projet "J'aime la Galette".
