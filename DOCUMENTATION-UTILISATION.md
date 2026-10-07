# Documentation d'utilisation – J'aime la Galette

Guide pas à pas pour administrer le site vitrine et le backoffice.

---

## Sommaire

1. [Présentation et accès](#1-présentation-et-accès)
2. [Connexion à l'administration](#2-connexion-à-ladministration)
3. [Tableau de bord (Dashboard)](#3-tableau-de-bord-dashboard)
4. [Gestion des produits](#4-gestion-des-produits)
   - 4.1 [Ajouter un produit](#41-ajouter-un-produit)
   - 4.2 [Modifier un produit](#42-modifier-un-produit)
   - 4.3 [Activer / désactiver un produit](#43-activer--désactiver-un-produit)
   - 4.4 [Supprimer un produit](#44-supprimer-un-produit)
5. [Gestion des marques](#5-gestion-des-marques)
   - 5.1 [Ajouter une marque](#51-ajouter-une-marque)
   - 5.2 [Modifier une marque](#52-modifier-une-marque)
6. [Gestion des sites de production](#6-gestion-des-sites-de-production)
   - 6.1 [Ajouter un site](#61-ajouter-un-site)
   - 6.2 [Modifier un site](#62-modifier-un-site)
7. [Gestion de la FAQ](#7-gestion-de-la-faq)
   - 7.1 [Ajouter une catégorie](#71-ajouter-une-catégorie)
   - 7.2 [Ajouter une question](#72-ajouter-une-question)
8. [Gestion des candidatures](#8-gestion-des-candidatures)
9. [Gestion des messages de contact](#9-gestion-des-messages-de-contact)
10. [Gestion des livreurs](#10-gestion-des-livreurs)
11. [Gestion des commerciaux](#11-gestion-des-commerciaux)
12. [Visites mystères](#12-visites-mystères)
13. [Éditeur de pages (contenu du site)](#13-éditeur-de-pages-contenu-du-site)
14. [Offres d'emploi](#14-offres-demploi)
15. [Pages publiques du site](#15-pages-publiques-du-site)

---

## 1. Présentation et accès

Le site **J'aime la Galette** est un site vitrine pour un fabricant de crêpes et galettes bretonnes. Il se compose de deux parties :

- **Le site public** (accessible à tous les visiteurs)
- **Le backoffice** (accessible uniquement aux administrateurs et commerciaux)

**URL du site** : `https://www.jaimelagalette.com`  
**URL du backoffice** : `https://www.jaimelagalette.com/admin/`

---

## 2. Connexion à l'administration

### Se connecter

1. Rendez-vous sur l'URL : `https://www.jaimelagalette.com/admin/`
2. Saisissez votre identifiant dans le champ **"Identifiant"**
3. Saisissez votre mot de passe dans le champ **"Mot de passe"**
4. Cliquez sur le bouton **"Se connecter"**

### Identifiants par défaut

| Rôle | Identifiant | Mot de passe |
|------|------------|--------------|
| Administrateur | `la-galette-admin` | `9?UqSACao3UcG#g!` |

### Déconnexion

En haut à droite, cliquez sur **"Déconnexion"**. La session expire automatiquement après **2h d'inactivité**.

### Sécurité

- Après **5 tentatives** de connexion échouées, l'accès est bloqué pendant **15 minutes**
- Un **token CSRF** protège chaque formulaire contre les attaques
- Les mots de passe sont hashés avec **bcrypt**

---

## 3. Tableau de bord (Dashboard)

Le tableau de bord est la première page qui s'affiche après la connexion. Il donne une vue d'ensemble de l'activité du site.

### Que voit-on ?

- Nombre de **candidatures non lues**
- Nombre de **messages de contact non lus**
- Nombre de **produits en ligne / hors ligne**
- Nombre d'**offres d'emploi actives**
- Nombre d'**inspections** (visites mystères) aujourd'hui et cette semaine
- Les **3 dernières candidatures** reçues
- Les **3 derniers messages** de contact
- Les **3 dernières visites mystères**

---

## 4. Gestion des produits

### 4.1 Ajouter un produit

1. Dans le menu de gauche, cliquez sur **"Produits"**
2. Cliquez sur le bouton **"Nouveau produit"** en haut à droite
3. Remplissez les champs :
   - **Nom** \* (obligatoire) : le nom du produit (ex: Galette)
   - **Sous-titre** : un texte court (ex: Rencontrer le meilleur)
   - **Titre** : un titre détaillé (ex: Nos crêpes & galettes)
   - **Marque** : sélectionnez la marque associée dans la liste
   - **Image** : choisissez une image (JPEG, PNG, WebP, SVG max 5 Mo)
   - **En ligne** : cocher pour publier le produit immédiatement
4. Cliquez sur **"Créer le produit"**
5. Le produit est créé. Vous êtes redirigé vers la page d'édition pour configurer la section **"À propos"**

### 4.2 Modifier un produit

1. Dans le menu de gauche, cliquez sur **"Produits"**
2. Trouvez le produit dans la liste (utilisez les filtres si besoin)
3. Cliquez sur **"Modifier"** dans la colonne Actions
4. Modifiez les champs souhaités dans la section **"Fiche produit"**
5. Déroulez la section **"À propos"** pour modifier :
   - Sous-titre, Titre, Contenu
   - CTA label (texte du bouton) et CTA lien (URL de destination)
   - Image et texte alternatif (alt)
6. Cliquez sur **"Enregistrer"** en bas du formulaire

### 4.3 Activer / désactiver un produit

1. Dans la liste des produits, repérez la colonne **"En ligne"**
2. Cliquez sur l'interrupteur (toggle) pour basculer le statut

> Le changement est instantané. Un produit hors ligne n'apparaît plus sur le site public.

### 4.4 Supprimer un produit

1. Dans la liste des produits, cliquez sur **"Supprimer"** dans la colonne Actions
2. Une page de confirmation s'affiche avec les informations du produit
3. Cliquez sur **"Confirmer la suppression"** pour valider

> La suppression est irréversible et supprime également les données associées (section À propos).

---

## 5. Gestion des marques

### 5.1 Ajouter une marque

1. Dans le menu de gauche, cliquez sur **"Marques"**
2. Cliquez sur **"Nouvelle marque"**
3. Remplissez :
   - **Nom** (ex: J'aime la Galette)
   - **Slug** (identifiant URL, généré automatiquement depuis le nom)
   - **Logo** (image de la marque)
   - **Description**
   - **Couleur hexadécimale** (ex: `#EE7325`)
   - **Ordre d'affichage**
   - **En ligne** : cocher pour activer
4. Cliquez sur **"Enregistrer"**

### 5.2 Modifier une marque

1. Dans la liste des marques, cliquez sur **"Modifier"**
2. Modifiez les champs souhaités
3. Cliquez sur **"Enregistrer"**

> Vous pouvez aussi activer/désactiver une marque depuis la liste avec le toggle interrupteur.

---

## 6. Gestion des sites de production

### 6.1 Ajouter un site

1. Dans le menu de gauche, cliquez sur **"Sites"**
2. Cliquez en haut à gauche sur **"Nouveau site"**
3. Remplissez les informations générales :
   - **Type de site** \* : Atelier / Siège / Dépôt / Autre
   - **Nom** \* (ex: La Galette de Broons)
   - **Adresse** \*, **Code postal** \*, **Ville** \*
   - **Département**, **Téléphone**, **Email**, **Email RH**
4. Localisez le site sur la carte :
   - Cliquez sur **"Géocoder l'adresse"** pour localiser automatiquement
   - OU cliquez directement sur la carte pour placer le marqueur
   - Vous pouvez glisser-déposer le marqueur pour affiner
5. Définissez les horaires d'ouverture :
   - Pour chaque jour (lundi à samedi), saisissez les heures d'ouverture et de fermeture
   - Laissez vide si le site est fermé ce jour-là
6. Cliquez sur **"Créer le site"**

### 6.2 Modifier un site

1. Dans la liste des sites, cliquez sur **"Modifier"**
2. Modifiez les champs, les horaires ou la position sur la carte
3. Le statut **"Ouvert/Fermé"** est mis à jour automatiquement selon les horaires

---

## 7. Gestion de la FAQ

### 7.1 Ajouter une catégorie

1. Dans le menu de gauche, cliquez sur **"FAQ"**
2. Cliquez sur **"Gérer les catégories"**
3. Saisissez le titre de la catégorie (ex: Nos Produits)
4. Cliquez sur **"Ajouter"**

### 7.2 Ajouter une question

1. Depuis la page FAQ, cliquez sur **"Gérer les questions"**
2. Sélectionnez la catégorie concernée
3. Remplissez :
   - **Question** \* : le texte de la question
   - **Réponse** \* : la réponse détaillée
   - **Mots-clés** : synonymes pour améliorer la recherche du chatbot
   - **Profil cible** : optionnel (grand_public / b2b / rh / presse)
   - **Ordre** : position d'affichage
   - **En ligne** : cocher pour publier
4. Cliquez sur **"Ajouter"**

> Les mots-clés sont utilisés par le chatbot pour trouver la bonne réponse. Plus il y a de mots-clés, meilleure est la pertinence.

---

## 8. Gestion des candidatures

Les candidatures arrivent automatiquement depuis le formulaire de recrutement du site public.

### Consulter les candidatures

1. Dans le menu de gauche, cliquez sur **"Candidatures"**
2. Utilisez le filtre **"Tous"** ou **"Non lus"** en haut de la page
3. Cliquez sur une ligne pour voir le détail complet
4. Dans le détail, vous pouvez télécharger le **CV**
5. Pour supprimer une candidature, revenez à la liste et utilisez le bouton **"Suppr."**

---

## 9. Gestion des messages de contact

Les messages arrivent depuis le formulaire de contact du site public (profils B2C et B2B).

### Consulter et traiter les messages

1. Dans le menu de gauche, cliquez sur **"Messages contact"**
2. Filtrez par statut (**Nouveau / Lu / Traité / Archivé**) ou par profil (**B2C / B2B**)
3. Changez le statut directement depuis la liste avec le menu déroulant
4. Cliquez sur une ligne pour voir le message complet
5. Marquez comme **Lu**, **Traité** ou **Archivé** selon l'avancement

---

## 10. Gestion des livreurs

Les livreurs sont utilisés dans le module **Visites mystères**.

### Ajouter un livreur

1. Dans le menu de gauche, cliquez sur **"Livreurs"**
2. Cliquez sur **"+ Ajouter"**
3. Remplissez : nom, prénom, secteur, téléphone
4. Cliquez sur **"Enregistrer"**

### Modifier ou supprimer

Dans la liste, utilisez **"Éditer"** pour modifier, **"Suppr."** pour supprimer.

---

## 11. Gestion des commerciaux

Les commerciaux ont accès à l'espace **Visites mystères**.

### Ajouter un commercial

1. Dans le menu de gauche, cliquez sur **"Commerciaux"**
2. Cliquez sur **"+ Ajouter"**
3. Remplissez : nom, prénom, email, login, mot de passe, téléphone
4. Cliquez sur **"Enregistrer"**

> Le mot de passe est automatiquement hashé avec **bcrypt** avant d'être stocké.

---

## 12. Visites mystères

**Deux accès :**

- **Administration** (`/admin/visites-mysteres/`) : consultation et export
- **Commercial** (`/admin/commercial/visite-mystere/`) : saisie des inspections

### Réaliser une visite mystère (espace commercial)

1. Connectez-vous avec un **compte commercial**
2. Vous êtes redirigé vers le formulaire d'inspection
3. Sélectionnez le **livreur** concerné dans la liste
4. Répondez aux questions par catégorie :
   - **Propreté & Hygiène** (4 questions)
   - **Merchandising & Mise en place** (4 questions)
   - **Professionnalisme** (2 questions)
   - Notez chaque critère **de 1 à 5**
5. Ajoutez un **commentaire libre** (optionnel)
6. Ajoutez des **photos** (optionnel, max 10)
7. Cliquez sur **"Valider l'inspection"**
8. La **note générale /20** est calculée automatiquement

### Consulter les inspections (administration)

1. Dans le menu de gauche, cliquez sur **"Visites mystères"**
2. Consultez la liste des inspections réalisées
3. Cliquez sur une inspection pour voir les réponses, notes et photos
4. Utilisez **"Export"** pour télécharger les données

---

## 13. Éditeur de pages (contenu du site)

L'éditeur permet de modifier le contenu textuel et les images de chaque section du site sans toucher au code.

### Modifier une section

1. Dans le menu de gauche, cliquez sur **"Pages"**
2. Choisissez la page à modifier dans la liste
3. Cliquez sur **"Modifier"**
4. Modifiez les champs (texte, images, CTA)
5. Un **aperçu** est affiché à côté du formulaire
6. Cliquez sur **"Enregistrer"**

### Sections modifiables

Hero, Intro, À propos, Carte, Chiffres du groupe, Contact, Engagements, FAQ, Footer, Histoire, Label, Livraison, **Offres d'emploi**, Partenaires, Processus recrutement, Recrutement, RSE, Savoir-faire, Transparence, Valeurs, Animation magasin

---

## 14. Offres d'emploi

Les offres d'emploi sont gérées depuis l'éditeur de pages, dans la section **"Offres d'emploi"** de la page Recrutement. Elles sont affichées sur le site public sous forme de cartes avec une modale de détail.

### Ajouter une offre d'emploi

1. Dans le menu de gauche, cliquez sur **"Pages"**
2. Choisissez **"Recrutement"** dans la liste
3. Cliquez sur **"Modifier"**
4. Déroulez la section **"Offres d'emploi"**
5. Dans le bloc **"Ajouter une offre"**, remplissez :
   - **Titre** \* : intitulé du poste (ex: Conducteur de ligne)
   - **Contrat** \* : type de contrat (CDI, CDD, Stage, Alternance...)
   - **Description** \* : détail du poste et du profil recherché
   - **Lieu** \* : sélectionnez la ville dans la liste
   - **Site lié** : sélectionnez le site de production concerné (permet de router les candidatures vers le bon email RH)
   - **CTA label** \* : texte du bouton (ex: Découvrir)
   - **Date de publication** \* : date à partir de laquelle l'offre est active
   - **En ligne** : cocher pour publier l'offre immédiatement
6. Cliquez sur **"Ajouter"**

### Modifier une offre

1. Dans la liste des offres, repérez celle à modifier
2. Cliquez sur **"Modifier"** (le bouton s'affiche dans la colonne Actions)
3. Un formulaire inline apparaît : modifiez les champs souhaités
4. Cliquez sur **"Sauvegarder"**

### Activer / désactiver une offre

1. Dans la liste des offres, repérez la colonne **"En ligne"**
2. Cliquez sur l'interrupteur (toggle) pour basculer le statut

> Une offre désactivée n'apparaît plus sur le site public (page Recrutement et fiches Atelier).

### Supprimer une offre

1. Dans la liste des offres, cliquez sur **"Modifier"**
2. En bas du formulaire inline, cliquez sur **"Supprimer"**
3. Confirmez la suppression dans la boîte de dialogue

### Candidature spontanée

La section **candidature spontanée** est également éditable depuis le même écran :
- **Titre**, **description**, **CTA label** et **CTA lien**
- **En ligne** : cocher pour afficher le bloc sur le site

> Les candidatures reçues (sur offre ou spontanées) sont consultables dans le menu **"Candidatures"**.

---

## 15. Pages publiques du site

Voici l'ensemble des pages accessibles aux visiteurs du site :

| URL | Page |
|-----|------|
| `/` | Accueil |
| `/le-groupe` | Le Groupe |
| `/nos-produits` | Nos produits |
| `/nos-produits/jaime-la-galette` | Gamme J'aime la Galette |
| `/nos-produits/be-goodn` | Gamme Be Good'n |
| `/savoir-faire` | Savoir-faire |
| `/rse` | RSE |
| `/faq` | FAQ |
| `/recrutement` | Recrutement |
| `/contact` | Contact |
| `/atelier/{id}/{slug}` | Fiche atelier |
| `/produit/{id}/{slug}` | Fiche produit |
| `/mentions-legales` | Mentions légales |
| `/politique-confidentialite` | Politique de confidentialité |
| `/cookies` | Cookies |
| `/sitemap.xml` | Sitemap XML |
