-- ============================================================
-- J'aime la Galette - Schéma de base de données complet
-- Fichier : 01_schema.sql
-- Encodage : UTF-8 | Moteur : InnoDB | Collation : utf8mb4
-- ============================================================

SET NAMES utf8mb4;

SET CHARACTER SET utf8mb4;

-- -----------------------------------------------
-- GLOBAL / PARTAGÉ
-- -----------------------------------------------

CREATE TABLE hero (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    accroche VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    image_fond VARCHAR(255),
    alt_fond VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE footer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    logo VARCHAR(255),
    alt_logo VARCHAR(255),
    description TEXT,
    copyright VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE footer_lien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    footer_id INT NOT NULL,
    categorie VARCHAR(100) NOT NULL,
    label VARCHAR(100) NOT NULL,
    lien VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_footer_lien FOREIGN KEY (footer_id) REFERENCES footer (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE footer_legal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    footer_id INT NOT NULL,
    label VARCHAR(100) NOT NULL,
    lien VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_footer_legal FOREIGN KEY (footer_id) REFERENCES footer (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE marque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255),
    alt_logo VARCHAR(255),
    description TEXT,
    couleur_hex VARCHAR(7) DEFAULT '#EE7325',
    en_ligne BOOLEAN NOT NULL DEFAULT TRUE,
    ordre INT DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Intro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    citation TEXT,
    contenu TEXT,
    nom_page VARCHAR(100) NOT NULL,
    image_fond VARCHAR(255),
    alt_fond VARCHAR(255),
    image_mascotte VARCHAR(255),
    alt_mascotte VARCHAR(255),
    marque_id INT DEFAULT NULL,
    KEY fk_intro_marque (marque_id),
    CONSTRAINT fk_intro_marque FOREIGN KEY (marque_id) REFERENCES marque (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Apropos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255),
    image VARCHAR(255),
    alt VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_contact_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    cta_label VARCHAR(100) NOT NULL,
    cta_lien VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    CONSTRAINT fk_card_contact FOREIGN KEY (partial_contact_id) REFERENCES partial_Contact (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Label (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Label (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_label_id INT NOT NULL,
    logo VARCHAR(255) NOT NULL,
    alt VARCHAR(255) NOT NULL,
    lien VARCHAR(255),
    ordre INT DEFAULT 0,
    CONSTRAINT fk_label_partial FOREIGN KEY (partial_label_id) REFERENCES partial_Label (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Carte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    image_mascotte VARCHAR(255),
    alt_mascotte VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE point_Carte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_carte_id INT NOT NULL,
    type_site VARCHAR(100) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    code_postal CHAR(5) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(255),
    email_rh VARCHAR(255),
    departement VARCHAR(100),
    est_ouvert BOOLEAN NOT NULL DEFAULT FALSE,
    latitude DECIMAL(10, 7),
    longitude DECIMAL(10, 7),
    CONSTRAINT fk_card_carte FOREIGN KEY (partial_carte_id) REFERENCES partial_Carte (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE horaire_Site (
    id INT AUTO_INCREMENT PRIMARY KEY,
    point_carte_id INT NOT NULL,
    jour TINYINT NOT NULL COMMENT '0=Dimanche,1=Lundi...6=Samedi',
    ouverture TIME NOT NULL,
    fermeture TIME NOT NULL,
    CONSTRAINT fk_horaire_site FOREIGN KEY (point_carte_id) REFERENCES point_Carte (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_produit_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    marque VARCHAR(255) NOT NULL,
    marque_id INT DEFAULT NULL,
    tags VARCHAR(255),
    description TEXT,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255),
    KEY fk_card_produit_marque (marque_id),
    CONSTRAINT fk_card_produit FOREIGN KEY (partial_produit_id) REFERENCES partial_Produit (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_card_produit_marque FOREIGN KEY (marque_id) REFERENCES marque (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Valeur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    image_fond VARCHAR(255),
    alt_fond VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Valeur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_valeur_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    CONSTRAINT fk_card_valeur FOREIGN KEY (partial_valeur_id) REFERENCES partial_Valeur (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Partenaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partenaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_partenaire_id INT NOT NULL,
    logo VARCHAR(255) NOT NULL,
    alt VARCHAR(255) NOT NULL,
    lien VARCHAR(255),
    ordre INT DEFAULT 0,
    CONSTRAINT fk_partenaire_partial FOREIGN KEY (partial_partenaire_id) REFERENCES partial_Partenaire (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE action_RSE (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description VARCHAR(255),
    contenu TEXT,
    tags VARCHAR(255),
    ordre INT DEFAULT 0
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE INDEX
-- -----------------------------------------------

CREATE TABLE partial_Chiffre_Groupe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Chiffre_Groupe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_chiffre_groupe_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    CONSTRAINT fk_card_partial FOREIGN KEY (partial_chiffre_groupe_id) REFERENCES partial_Chiffre_Groupe (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Engagement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Engagement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_engagement_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    CONSTRAINT fk_card_engagement FOREIGN KEY (partial_engagement_id) REFERENCES partial_Engagement (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE GROUPE
-- -----------------------------------------------

CREATE TABLE partial_Histoire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE etape_Histoire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_histoire_id INT NOT NULL,
    annee VARCHAR(10) NOT NULL,
    nom VARCHAR(255) NOT NULL,
    region VARCHAR(100),
    description TEXT,
    image VARCHAR(255),
    alt VARCHAR(255),
    ordre INT DEFAULT 0,
    CONSTRAINT fk_etape_histoire FOREIGN KEY (partial_histoire_id) REFERENCES partial_Histoire (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Engagement_Groupe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    image VARCHAR(255),
    alt VARCHAR(255),
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Animation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Animation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_animation_id INT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    CONSTRAINT fk_card_animation FOREIGN KEY (partial_animation_id) REFERENCES partial_Animation (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Livraison (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255),
    image_fond VARCHAR(255),
    alt_fond VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_CTA_Produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255),
    cta_label2 VARCHAR(100),
    cta_lien2 VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE NOS-PRODUITS
-- -----------------------------------------------

CREATE TABLE partial_Transparence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Transparence (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_transparence_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    CONSTRAINT fk_card_transparence FOREIGN KEY (partial_transparence_id) REFERENCES partial_Transparence (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE SAVOIR-FAIRE
-- -----------------------------------------------

CREATE TABLE partial_SavoirFaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255),
    titre VARCHAR(255),
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_SavoirFaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_savoirfaire_id INT NOT NULL,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    image_fond VARCHAR(255),
    alt_fond VARCHAR(255),
    image_label VARCHAR(255),
    alt_label VARCHAR(255),
    ordre INT DEFAULT 0,
    CONSTRAINT fk_card_savoirfaire FOREIGN KEY (partial_savoirfaire_id) REFERENCES partial_SavoirFaire (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE PRODUIT
-- -----------------------------------------------

CREATE TABLE produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    marque VARCHAR(255),
    marque_id INT DEFAULT NULL,
    nom VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    en_ligne BOOLEAN NOT NULL DEFAULT FALSE,
    KEY fk_produit_marque (marque_id),
    CONSTRAINT fk_produit_marque FOREIGN KEY (marque_id) REFERENCES marque (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE produit_Apropos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255),
    image VARCHAR(255),
    alt VARCHAR(255),
    CONSTRAINT fk_produit_apropos FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Ingredient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    image_fond VARCHAR(255),
    alt_fond VARCHAR(255),
    CONSTRAINT fk_ingredient_produit FOREIGN KEY (produit_id) REFERENCES produit (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Ingredient (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_ingredient_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_card_ingredient FOREIGN KEY (partial_ingredient_id) REFERENCES partial_Ingredient (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE RECRUTEMENT
-- -----------------------------------------------

CREATE TABLE partial_Recrutement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE card_Recrutement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_recrutement_id INT NOT NULL,
    image VARCHAR(255),
    alt VARCHAR(255),
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_card_recrutement FOREIGN KEY (partial_recrutement_id) REFERENCES partial_Recrutement (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Processus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE etape_Processus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_processus_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_etape_processus FOREIGN KEY (partial_processus_id) REFERENCES partial_Processus (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE partial_Offre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE offre_Emploi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_offre_id INT NOT NULL,
    point_carte_id INT DEFAULT NULL,
    titre VARCHAR(255) NOT NULL,
    contrat VARCHAR(100),
    description TEXT,
    lieu VARCHAR(255),
    cta_label VARCHAR(100),
    date_publication DATE,
    en_ligne BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_offre_emploi FOREIGN KEY (partial_offre_id) REFERENCES partial_Offre (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_offre_point_carte FOREIGN KEY (point_carte_id) REFERENCES point_Carte (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE candidature_spontanee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    cta_label VARCHAR(100),
    cta_lien VARCHAR(255),
    en_ligne BOOLEAN DEFAULT TRUE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- PAGE FAQ
-- -----------------------------------------------

CREATE TABLE partial_FAQ (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sous_titre VARCHAR(255) NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE categorie_FAQ (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_faq_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_categorie_faq FOREIGN KEY (partial_faq_id) REFERENCES partial_FAQ (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE question_FAQ (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_faq_id INT NOT NULL,
    question VARCHAR(500) NOT NULL,
    reponse TEXT NOT NULL,
    mots_cles VARCHAR(500) DEFAULT NULL COMMENT 'Mots-clés / synonymes pour le matching chatbot',
    profil_cible ENUM(
        'grand_public',
        'b2b',
        'rh',
        'presse'
    ) DEFAULT NULL COMMENT 'Filtrage par profil utilisateur',
    ordre INT DEFAULT 0,
    en_ligne BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_question_faq FOREIGN KEY (categorie_faq_id) REFERENCES categorie_FAQ (id) ON DELETE CASCADE ON UPDATE CASCADE,
    FULLTEXT INDEX ft_question_faq (question, mots_cles)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE chatbot_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_brute TEXT NOT NULL COMMENT 'Question posée par l\'utilisateur',
    matched_question_id INT DEFAULT NULL COMMENT 'FK vers question_FAQ si match trouvé',
    profil VARCHAR(50) DEFAULT NULL COMMENT 'Profil utilisateur (grand_public, b2b, rh, presse)',
    page_url VARCHAR(500) DEFAULT NULL COMMENT 'URL de la page où la question a été posée',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date et heure de la question',
    KEY fk_chatbot_log_question (matched_question_id),
    CONSTRAINT fk_chatbot_log_question FOREIGN KEY (matched_question_id) REFERENCES question_FAQ (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

ANALYZE TABLE question_FAQ;

-- -----------------------------------------------
-- PAGES LÉGALES
-- -----------------------------------------------

CREATE TABLE partial_Legal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_page VARCHAR(100) NOT NULL UNIQUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE legal_Section (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partial_legal_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    ordre INT DEFAULT 0,
    CONSTRAINT fk_legal_section FOREIGN KEY (partial_legal_id) REFERENCES partial_Legal (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- FORMULAIRES
-- -----------------------------------------------

CREATE TABLE formulaire_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profil ENUM('b2c', 'b2b') NOT NULL COMMENT 'Grand public ou Professionnel',
    nom VARCHAR(255) NOT NULL COMMENT 'Nom complet de l\'expéditeur',
    email VARCHAR(255) NOT NULL COMMENT 'Adresse e-mail de l\'expéditeur',
    sujet VARCHAR(255) NOT NULL COMMENT 'Objet du message (motif B2C ou objet libre B2B)',
    message TEXT NOT NULL COMMENT 'Corps du message',
    rgpd_accepte TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Consentement RGPD (1 = accepté)',
    societe VARCHAR(255) DEFAULT NULL COMMENT 'Nom de la société (B2B uniquement)',
    telephone VARCHAR(20) DEFAULT NULL COMMENT 'Numéro de téléphone (B2B, optionnel)',
    profil_b2b VARCHAR(100) DEFAULT NULL COMMENT 'Secteur d\'activité : gms, restauration, fournisseur, autre',
    statut ENUM('nouveau', 'lu', 'traite', 'archive') NOT NULL DEFAULT 'nouveau' COMMENT 'Statut de traitement du message',
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date et heure d\'envoi',
    traite_le DATETIME DEFAULT NULL COMMENT 'Date de traitement (renseignée par l\'admin)',
    note_admin TEXT DEFAULT NULL COMMENT 'Note interne de suivi (admin uniquement)'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL COMMENT 'Prénom du candidat',
    nom VARCHAR(100) NOT NULL COMMENT 'Nom du candidat',
    email VARCHAR(255) NOT NULL COMMENT 'Email du candidat',
    poste_souhaite VARCHAR(200) NOT NULL COMMENT 'Poste recherché',
    type_candidature VARCHAR(20) NOT NULL DEFAULT 'spontanee' COMMENT 'Type : offre ou spontanee',
    offre_emploi_id INT DEFAULT NULL COMMENT 'ID de l offre choisie (si candidature sur une offre)',
    message TEXT NOT NULL COMMENT 'Lettre de motivation',
    cv_path VARCHAR(255) DEFAULT NULL COMMENT 'Chemin du fichier CV uploadé',
    rgpd_accepte TINYINT (1) NOT NULL DEFAULT 0 COMMENT 'Consentement RGPD',
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de candidature',
    lue BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -----------------------------------------------
-- VISITES MYSTÈRES
-- -----------------------------------------------

CREATE TABLE commercial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    en_ligne BOOLEAN NOT NULL DEFAULT TRUE,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE livreur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    secteur VARCHAR(100),
    telephone VARCHAR(20),
    en_ligne BOOLEAN NOT NULL DEFAULT TRUE,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE inspection_question (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie VARCHAR(100) NOT NULL,
    question TEXT NOT NULL,
    ordre INT DEFAULT 0,
    actif BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE inspection (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livreur_id INT NOT NULL,
    commercial_id INT NOT NULL,
    note_generale DECIMAL(4, 1) DEFAULT NULL COMMENT 'Note globale /20',
    commentaire TEXT,
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_inspection_livreur FOREIGN KEY (livreur_id) REFERENCES livreur (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inspection_commercial FOREIGN KEY (commercial_id) REFERENCES commercial (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE inspection_reponse (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inspection_id INT NOT NULL,
    question_id INT NOT NULL,
    note INT NOT NULL COMMENT 'Note 1-5',
    commentaire TEXT,
    CONSTRAINT fk_reponse_inspection FOREIGN KEY (inspection_id) REFERENCES inspection (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reponse_question FOREIGN KEY (question_id) REFERENCES inspection_question (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE inspection_photo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inspection_id INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    legende VARCHAR(255),
    cree_le DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_photo_inspection FOREIGN KEY (inspection_id) REFERENCES inspection (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;