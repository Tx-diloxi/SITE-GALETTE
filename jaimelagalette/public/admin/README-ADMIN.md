# Backoffice J'aime la Galette

## Accès

| Champ | Valeur |
|---|---|
| URL | `http://localhost:8080/admin/` |
| Identifiant | `admin` |
| Mot de passe | `admin123` |

## Changer le mot de passe

1. Générer un hash bcrypt :
   ```bash
   php -r "echo password_hash('mon-nouveau-mot-de-passe', PASSWORD_BCRYPT);"
   ```
2. Copier le hash dans le fichier `.env` à la racine du projet :
   ```
   ADMIN_PASS_HASH=$2y$10$...
   ```
3. Redémarrer les conteneurs Docker :
   ```bash
   docker compose down -v && docker compose up -d
   ```

## Structure du backoffice

```
public/admin/
├── index.php                    # Dashboard
├── login.php                    # Connexion
├── logout.php                   # Déconnexion
├── layout/                      # Templates réutilisables (header + footer)
├── pages/                       # Éditeur de pages
│   ├── index.php                # Liste des pages
│   ├── edit.php                 # Éditeur split (preview + formulaires)
│   └── partials/                # 20 formulaires de section
├── produits/                    # CRUD Produits
├── marques/                     # CRUD Marques
├── sites/                       # CRUD Sites de production
├── candidatures/                # Vue des candidatures
├── contacts/                    # Vue des messages contact
└── faq/                         # CRUD FAQ (catégories + questions)
```

## Sécurité

- Authentification par session avec timeout de 2h
- Blocage 15 minutes après 5 tentatives de connexion échouées
- Token CSRF sur tous les formulaires POST
- Upload d'images limité à 5 Mo (types autorisés : JPEG, PNG, WebP, SVG)
- Toutes les requêtes BDD en requêtes préparées PDO

## Dépendances

- PHP 8.2+
- PDO MySQL
- Pas de framework — PHP procédural uniquement
- Mêmes tokens SCSS que le site public (`_variables.scss`)
