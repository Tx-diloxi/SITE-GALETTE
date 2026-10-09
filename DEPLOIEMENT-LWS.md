# Mise en ligne sur LWS : pas à pas

Ce guide décrit comment publier le site « J'aime la Galette » sur un hébergement mutualisé LWS (PHP + MySQL).
Comptez environ 45 minutes la première fois.

## 0. Ce qu'il faut avoir

- Un hébergement LWS avec **PHP 8.1 ou plus** (panel LWS > *Configuration PHP*), les extensions `pdo_mysql`, `mbstring`, `intl`, `fileinfo`, et **mod_rewrite** (activé par défaut).
- Un accès **FTP** (FileZilla ou autre) et le panel LWS.
- Le **nom de domaine** définitif relié à l'hébergement, avec le **certificat HTTPS** actif (panel LWS > *SSL*).
- Une **boîte e-mail** (par exemple `siteweb@jaimelagalette.com`) pour l'envoi des formulaires : son mot de passe est nécessaire.

## 1. Préparer les fichiers (sur votre PC)

À la racine du dépôt :

```bash
bash deploy-lws.sh
```

Le script crée le dossier `lws-ready/` (et `lws-ready.zip`) contenant exactement ce qu'il faut envoyer. Il est régénéré à chaque exécution et n'est pas versionné.

Contenu de `lws-ready/` :

| Dossier / fichier | Rôle | À envoyer ? |
|---|---|---|
| `htdocs/` | site public (pages, images, CSS, JS, administration) | oui |
| `app/`, `pages/`, `vendor/` | code de l'application et PHPMailer | oui |
| `config.php`, `config.local.example.php` | connexion à la base et modèle de configuration | oui |
| `_sql_a_importer/` | `01_schema.sql` et `02_seeds.sql` | **non**, à importer dans phpMyAdmin |
| `LISEZMOI-DEPLOIEMENT.md` | ce guide | non |

## 2. Créer et remplir la base de données

1. Panel LWS > *MySQL & phpMyAdmin* > **créer une base** et son utilisateur. Notez : nom de la base, utilisateur, mot de passe, serveur (souvent `localhost`).
2. Ouvrez **phpMyAdmin**, sélectionnez la base, onglet *Importer* :
   1. importez `01_schema.sql` (création des tables) ;
   2. puis `02_seeds.sql` (contenu du site, pages légales comprises).
3. Importez ces deux fichiers **une seule fois**. Pour repartir de zéro : videz la base (*Opérations > Supprimer*), puis recommencez.

### À faire juste après l'import (important)

`02_seeds.sql` contient des **données de démonstration**. Dans phpMyAdmin, onglet *SQL* :

```sql
-- Comptes « commerciaux » de démonstration : leur mot de passe de test est écrit dans le fichier de seeds
DELETE FROM commercial;
```

Créez ensuite les vrais comptes depuis l'administration (menu *Commerciaux*). Passez aussi en revue, dans l'administration, les livreurs, sites et textes de démonstration.

## 3. Envoyer les fichiers

À la racine de votre FTP LWS (là où se trouve déjà le dossier `htdocs/`), envoyez :

```
/htdocs/                  <- le contenu de lws-ready/htdocs/
/app/
/pages/
/vendor/
/config.php
/config.local.example.php
```

- Supprimez `default_index.html` dans `/htdocs/` s'il est présent.
- **Seul `htdocs/` est public.** Tout le reste est hors du site web, c'est voulu : le code et les mots de passe ne sont pas accessibles depuis Internet.
- Les dossiers suivants doivent être **inscriptibles** (droits 755, ou 775 si l'envoi échoue) : `htdocs/assets/uploads/cv/`, `htdocs/assets/uploads/inspections/` (créé à la première photo), `htdocs/assets/images/admin-uploads/`.

## 4. Configurer le site (`config.local.php`)

Sur le serveur, à la racine (au même niveau que `app/`) :

1. dupliquez `config.local.example.php` en **`config.local.php`** ;
2. remplacez chaque valeur `A_REMPLACER` :

| Clé | Valeur |
|---|---|
| `SITE_URL` | adresse définitive, sans slash final, par exemple `https://www.jaimelagalette.com` |
| `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` | informations de la base créée à l'étape 2 |
| `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` | serveur et identifiants de la boîte e-mail d'envoi |
| `ADMIN_USER` | identifiant de l'administration |
| `ADMIN_PASS_HASH` | **hash** du mot de passe administrateur (voir ci-dessous) |
| `APP_DEBUG` | laissez `0` en production |

**Générer le hash du mot de passe administrateur** (sur votre PC, avec le mot de passe de votre choix, long et unique) :

```bash
php -r "echo password_hash('VOTRE_MOT_DE_PASSE', PASSWORD_BCRYPT), PHP_EOL;"
```

Collez le résultat (il commence par `$2y$`) dans `ADMIN_PASS_HASH`, entre apostrophes. **Il n'existe aucun mot de passe par défaut** : sans hash, personne ne peut se connecter à l'administration.

> Changez le mot de passe qui servait en développement : il figure dans l'historique du dépôt et dans `DOCUMENTATION-UTILISATION.md`.

## 5. Tâche planifiée : purge des données personnelles (RGPD)

La politique de confidentialité annonce des durées de conservation (contacts 3 ans, candidatures 2 ans, chatbot 12 mois). Le script `app/cli/purge_donnees.php` les applique.

Panel LWS > *Tâches Cron* : une fois par mois, commande :

```
php /chemin/absolu/vers/app/cli/purge_donnees.php
```

Le chemin absolu est visible dans le gestionnaire de fichiers du panel (sous la forme `/home/<compte>/…`).

## 6. Vérifications après la mise en ligne

Parcourez cette liste sur le vrai domaine :

- [ ] `http://` redirige bien vers `https://`, et le cadenas est vert.
- [ ] Accueil, Le Groupe, Nos produits, FAQ, Contact, Recrutement s'affichent sans erreur.
- [ ] La **carte** (accueil, Le Groupe) charge ses fonds. Si elle reste grise, le fournisseur de fonds de carte (Stadia Maps) demande peut-être d'enregistrer votre domaine ou une clé : voir leur tableau de bord.
- [ ] **Envoyez un message** par le formulaire de contact **et** une candidature avec CV : vous devez recevoir les deux e-mails.
- [ ] Connexion à `/admin/login.php`, ouverture d'un CV depuis *Candidatures*, enregistrement d'une section de page.
- [ ] `/robots.txt`, `/sitemap.xml` et `/llms.txt` répondent et citent le bon domaine.
- [ ] Dans le code source d'une page : `<link rel="canonical">` pointe vers votre domaine (sinon, `SITE_URL` est faux).
- [ ] `https://votre-domaine/config.local.php` et `https://votre-domaine/app/` donnent une erreur 404 (ils ne doivent pas être accessibles).
- [ ] Aucune erreur PHP visible à l'écran.

## 7. Référencement (après la mise en ligne)

1. **Ligne `Sitemap:` de `robots.txt`** : elle contient `https://www.jaimelagalette.com/sitemap.xml`. Si votre domaine est différent, modifiez `htdocs/robots.txt` sur le serveur.
2. **Google Search Console** : ajoutez le site, validez-le, puis envoyez `sitemap.xml`.
3. **Google Business Profile** : créez ou revendiquez une fiche pour chaque atelier. C'est le levier principal pour apparaître sur « galette de Broons » ou « galette bretonne près de moi ».
4. Faites relire les pages légales (mentions, cookies, confidentialité) : hébergeur, e-mail RGPD, durées et destinataires sont à valider.

## 8. Mettre à jour le site plus tard

1. Modifiez le code ou les images dans le dépôt (`optimiser_images.py` après avoir ajouté des images).
2. `bash deploy-lws.sh`.
3. Envoyez par FTP uniquement ce qui a changé. **N'écrasez jamais `config.local.php`** (il n'existe pas dans `lws-ready/`, vous ne risquez donc rien tant que vous n'envoyez pas un fichier de ce nom).
4. Ne réimportez pas `02_seeds.sql` sur une base déjà en service : cela remplacerait les contenus modifiés dans l'administration.

**Sauvegardes** : exportez régulièrement la base (phpMyAdmin > *Exporter*) et téléchargez `htdocs/assets/uploads/` et `htdocs/assets/images/admin-uploads/` (CV et images envoyées par l'administration).

## 9. Dépannage

| Symptôme | À vérifier |
|---|---|
| Page blanche ou erreur 500 | version PHP (8.1 minimum), présence de `config.local.php` et de `vendor/`, journaux d'erreurs dans le panel LWS |
| « Erreur technique » | identifiants `DB_*` de `config.local.php`, base importée |
| Impossible de se connecter à l'administration | `ADMIN_PASS_HASH` bien renseigné (60 caractères, commence par `$2y$`), identifiant `ADMIN_USER` |
| Les e-mails n'arrivent pas | `MAIL_USERNAME` / `MAIL_PASSWORD`, port 587 ouvert ; passer `MAIL_DEBUG` à `2` quelques minutes pour voir le détail dans les journaux |
| Envoi de CV ou d'image refusé | droits d'écriture des dossiers d'envoi (étape 3), taille limite de 5 Mo |
| Redirections en boucle | HTTPS non actif sur le domaine, ou le certificat n'est pas encore émis |
| Les modifications de CSS/JS n'apparaissent pas | le navigateur garde CSS et JS 1 semaine : `Ctrl+F5`, ou videz le cache |
