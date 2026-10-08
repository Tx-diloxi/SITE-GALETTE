-- ============================================================
-- J'aime la Galette - Données initiales (seeds)
-- Fichier : 02_seeds.sql
-- ============================================================

SET NAMES utf8mb4;

SET CHARACTER SET utf8mb4;

-- -----------------------------------------------
-- GLOBAL
-- -----------------------------------------------

INSERT INTO hero (titre, accroche, image, alt, image_fond, alt_fond) VALUES
('J\'aime la Galette', 'l\'ultra-fraîcheur au service du goût et du territoire', '/assets/images/crepe.png', 'Image de la crêpe', '', '');

INSERT INTO footer (logo, alt_logo, description, copyright) VALUES
('/assets/images/logo_jaimelagalette.png', 'J\'aime la Galette', 'Crêpes & galettes artisanales du terroir depuis plus de 35 ans.', '© 2026 J\'aime la Galette. Tous droits réservés.');

INSERT INTO
    footer_lien (
        footer_id,
        categorie,
        label,
        lien,
        ordre
    )
VALUES (
        1,
        'Navigation',
        'Le Groupe',
        '/le-groupe',
        1
    ),
    (
        1,
        'Navigation',
        'Nos produits',
        '/nos-produits',
        2
    ),
    (
        1,
        'Navigation',
        'Notre savoir-faire',
        '/savoir-faire',
        3
    ),
    (
        1,
        'Navigation',
        'Recrutement',
        '/recrutement',
        4
    ),
    (
        1,
        'Services',
        'FAQ',
        '/faq',
        1
    ),
    (
        1,
        'Services',
        'RSE',
        '/rse',
        2
    ),
    (
        1,
        'Services',
        'Contact',
        '/contact',
        3
    ),
    (
        1,
        'Contact',
        'Bretagne : Broons (22)',
        '#',
        1
    ),
    (
        1,
        'Contact',
        'Normandie : Alençon (61)',
        '#',
        2
    ),
    (
        1,
        'Contact',
        'Pays de Loire : Angers (49)',
        '#',
        3
    );

INSERT INTO
    footer_legal (footer_id, label, lien, ordre)
VALUES (
        1,
        'Mentions légales',
        '/mentions-legales',
        1
    ),
    (
        1,
        'Politique de confidentialité',
        '/politique-confidentialite',
        2
    ),
    (
        1,
        'Gestion des cookies',
        '/cookies',
        3
    );

-- -----------------------------------------------
-- MARQUES
-- -----------------------------------------------

INSERT INTO marque (nom, slug, description, couleur_hex, en_ligne, ordre) VALUES
('J\'aime la Galette', 'jaime-la-galette', 'La marque historique de galettes et crêpes artisanales.', '#EE7325', 1, 1),
('Be Good\'n', 'be-goodn', 'L\'univers gourmand et responsable du groupe.', '#4CAF50', 1, 2);

-- -----------------------------------------------
-- PAGE INDEX
-- -----------------------------------------------

INSERT INTO partial_Apropos (sous_titre, titre, contenu, cta_label, cta_lien, image, alt) VALUES
('À propos de nous', 'Notre entreprise', 'J\'aime la Galette, productrice de galettes et crêpes bretonnes artisanales, s\'engage à fournir des produits authentiques et savoureux, fabriqués selon des recettes traditionnelles. Avec une attention particulière portée à la qualité des ingrédients et à la satisfaction de ses clients, l\'entreprise accompagne professionnels et particuliers dans leurs commandes, du petit conditionnement aux volumes industriels.', 'En savoir plus', '/le-groupe', '/assets/images/galette.png', 'Atelier J\'aime la Galette');

INSERT INTO partial_Apropos (sous_titre, titre, contenu, image, alt) VALUES
('Notre terroir a du goût', 'Le Circuit Court au cœur de notre recette', 'Nous privilégions un approvisionnement ultra-local pour toutes nos matières premières majeures (farine de blé noir, beurre, lait, œufs). Cet engagement renforce l\'économie locale et garantit la fraîcheur absolue de nos produits.', '/assets/images/circuit-court.png', 'Circuit Court');

INSERT INTO partial_Chiffre_Groupe (sous_titre, titre, cta_label, cta_lien) VALUES
('Notre Groupe', 'J\'aime la Galette en chiffres', 'En savoir plus', '/le-groupe');

INSERT INTO
    card_Chiffre_Groupe (
        partial_chiffre_groupe_id,
        image,
        alt,
        titre,
        description
    )
VALUES (
        1,
        '/assets/images/icone/calendrier.svg',
        'Icone calendrier',
        '2013',
        'Année de création'
    ),
    (
        1,
        '/assets/images/icone/collaborateurs.svg',
        'Collaborateurs passionnés',
        '+1500',
        'Collaborateurs passionnés'
    ),
    (
        1,
        '/assets/images/icone/ateliers.svg',
        'Ateliers de production',
        '8',
        'Ateliers de production'
    ),
    (
        1,
        '/assets/images/icone/territoire.svg',
        'Ancrage territorial',
        '4 régions',
        'Ancrage territorial'
    ),
    (
        1,
        '/assets/images/icone/livraison.svg',
        'Livraison',
        'Livraison',
        'Avec notre flotte de camionnettes'
    );

INSERT INTO
    partial_Valeur (sous_titre, titre)
VALUES (
        'Nos valeurs',
        'Ce en quoi nous croyons'
    );

INSERT INTO card_Valeur (partial_valeur_id, image, alt, titre, description) VALUES
(1, '/assets/images/icone/fraicheur.svg', 'Ultra-fraîcheur', 'L\'Ultra-FRAîcheur', 'Des dates courtes pour une qualité maximale'),
(1, '/assets/images/icone/qualite.svg', 'Qualité', 'Qualité', 'Les meilleures recettes, ingrédients et procédés'),
(1, '/assets/images/icone/territoire.svg', 'Proximité', 'Proximité', 'Proches de nos clients, fournisseurs et partenaires'),
(1, '/assets/images/icone/france.svg', 'Territoire', 'Territoire', 'Acteur engagé dans le développement local');

INSERT INTO
    partial_Produit (sous_titre, titre)
VALUES (
        'Nos produits',
        'Deux univers, une même exigence'
    );

INSERT INTO card_Produit (partial_produit_id, image, alt, marque, marque_id, tags, description, cta_label, cta_lien) VALUES
(1, '/assets/images/card1.png', 'Galettes & crêpes J\'aime la Galette', 'J\'aime la Galette', 1, 'crêpes & galettes', '100% farine de blé noir · Sans additifs · Recettes traditionnelles', 'Découvrir la gamme', '/nos-produits/jaime-la-galette'),
(1, '/assets/images/card2.png', 'Chips caramel cidre Be Good\'n', 'Be Good\'n', 2, 'chips · caramel · cidre', 'Des produits gourmands et responsables, pour tous les moments de plaisir', 'Découvrir la gamme', '/nos-produits/be-goodn');

INSERT INTO partial_Carte (sous_titre, titre, image_mascotte, alt_mascotte) VALUES
('Nos sites de production', '8 ateliers, une seule exigence', 'mascotte-carte.png', 'Mascotte J\'aime la Galette sur la carte');

INSERT INTO point_Carte (partial_carte_id, type_site, nom, adresse, code_postal, ville, telephone, email, email_rh, departement, est_ouvert, latitude, longitude) VALUES
(1, 'Atelier', 'La Galette de Broons', '10 Rue de l\'Avenir', '22250', 'Broons', '02 96 84 67 15', 'broons@jaimelagalette.com', 'rh-broons@jaimelagalette.com', 'Côtes-d\'Armor', 1, 48.3148665, -2.2471565),
(1, 'Atelier', 'La Galette d\'Alençon', '35 Rue de Verdun', '61000', 'Alençon', '02 33 80 01 30', 'alencon@jaimelagalette.com', 'rh-alencon@jaimelagalette.com', 'Orne', 1, 48.4338912, 0.1002224),
(1, 'Atelier', 'La Galette du Val de Loire', '12 avenue Jean Joxé', '49109', 'Angers', '02 41 22 98 15', 'angers@jaimelagalette.com', 'rh-angers@jaimelagalette.com', 'Maine-et-Loire', 1, 47.4838937, -0.5432236),
(1, 'Atelier', 'La Galette de Chantonnay', '29 rue des forestis', '85110', 'Chantonnay', '02 72 69 04 97', 'chantonnay@jaimelagalette.com', 'rh-chantonnay@jaimelagalette.com', 'Vendée', 1, 46.701157, -1.044746),
(1, 'Atelier', 'La Galette du Val de Seine', 'Av. Bernard Bicheray', '76000', 'Rouen', '02 78 26 04 94', 'rouen@jaimelagalette.com', 'rh-rouen@jaimelagalette.com', 'Seine-Maritime', 1, 49.45123276394113, 1.0493637327103875),
(1, 'Atelier', 'Les Délices Gildasiens', 'La Croix Daniel', '44530', 'Saint-Gildas-des-Bois', '02 40 01 46 27', 'stgildas@jaimelagalette.com', 'rh-stgildas@jaimelagalette.com', 'Loire-Atlantique', 1, 47.517440144528145, -2.04475054356462),
(1, 'Atelier', 'La Galette de Malansac', '23 rue de la gare', '56220', 'Malansac', NULL, 'malansac@jaimelagalette.com', 'rh-malansac@jaimelagalette.com', 'Morbihan', 1, 47.676617525571665, -2.294210984656153),
(1, 'Atelier', 'Maison d\'Armor', '10 Rue de l\'Avenir', '22250', 'Broons', NULL, NULL, NULL,'Yvelines', 1, 48.3148665, -2.2471565),
(1, 'Siège', 'Siège social', '12 Rue de l\'Avenir', '22250', 'Broons', '02 14 02 51 51', NULL, NULL, 'Côtes-d\'Armor', 1, 48.31443379734153, -2.2466765977515712);

INSERT INTO
    horaire_Site (
        point_carte_id,
        jour,
        ouverture,
        fermeture
    )
VALUES (1, 1, '08:00:00', '18:00:00'),
    (1, 2, '08:00:00', '18:00:00'),
    (1, 3, '08:00:00', '18:00:00'),
    (1, 4, '08:00:00', '18:00:00'),
    (1, 5, '08:00:00', '18:00:00'),
    (2, 1, '08:00:00', '18:00:00'),
    (2, 2, '08:00:00', '18:00:00'),
    (2, 3, '08:00:00', '18:00:00'),
    (2, 4, '08:00:00', '18:00:00'),
    (2, 5, '08:00:00', '18:00:00'),
    (3, 1, '08:00:00', '18:00:00'),
    (3, 2, '08:00:00', '18:00:00'),
    (3, 3, '08:00:00', '18:00:00'),
    (3, 4, '08:00:00', '18:00:00'),
    (3, 5, '08:00:00', '18:00:00'),
    (4, 1, '08:00:00', '18:00:00'),
    (4, 2, '08:00:00', '18:00:00'),
    (4, 3, '08:00:00', '18:00:00'),
    (4, 4, '08:00:00', '18:00:00'),
    (4, 5, '08:00:00', '18:00:00'),
    (5, 1, '08:00:00', '18:00:00'),
    (5, 2, '08:00:00', '18:00:00'),
    (5, 3, '08:00:00', '18:00:00'),
    (5, 4, '08:00:00', '18:00:00'),
    (5, 5, '08:00:00', '18:00:00'),
    (6, 1, '08:00:00', '18:00:00'),
    (6, 2, '08:00:00', '18:00:00'),
    (6, 3, '08:00:00', '18:00:00'),
    (6, 4, '08:00:00', '18:00:00'),
    (6, 5, '08:00:00', '18:00:00'),
    (7, 1, '08:00:00', '18:00:00'),
    (7, 2, '08:00:00', '18:00:00'),
    (7, 3, '08:00:00', '18:00:00'),
    (7, 4, '08:00:00', '18:00:00'),
    (7, 5, '08:00:00', '18:00:00'),
    (8, 1, '08:00:00', '18:00:00'),
    (8, 2, '08:00:00', '18:00:00'),
    (8, 3, '08:00:00', '18:00:00'),
    (8, 4, '08:00:00', '18:00:00'),
    (8, 5, '08:00:00', '18:00:00'),
    (9, 1, '08:00:00', '18:00:00'),
    (9, 2, '08:00:00', '18:00:00'),
    (9, 3, '08:00:00', '18:00:00'),
    (9, 4, '08:00:00', '18:00:00'),
    (9, 5, '08:00:00', '18:00:00');

INSERT INTO
    partial_Partenaire (sous_titre, titre)
VALUES (
        'Acteurs de la vie locale',
        'ILS nous font déjà confiance'
    );

INSERT INTO
    partenaire (
        partial_partenaire_id,
        logo,
        alt,
        lien,
        ordre
    )
VALUES (
        1,
        'assets/images/partenaire/vielle_charrue.png',
        'Logo Moulin de la Ville Huchet',
        'https://www.moulindelavillehuchet.fr/',
        1
    ),
    (
        1,
        'assets/images/partenaire/ligue.png',
        'Logo Ligue contre le cancer',
        'https://www.ligue-cancer.net/',
        2
    ),
    (
        1,
        'assets/images/partenaire/handball.png',
        'Logo Cesson Handball',
        'https://www.cesson-handball.com/',
        3
    );

INSERT INTO
    partial_Engagement (
        sous_titre,
        titre,
        cta_label,
        cta_lien
    )
VALUES (
        'Nos engagements',
        'Des promesses concrètes',
        'Découvrir nos engagements RSE',
        '/rse'
    );

INSERT INTO
    card_Engagement (
        partial_engagement_id,
        image,
        alt,
        titre,
        description
    )
VALUES (
        1,
        '/assets/images/icone/france.svg',
        'Origine France',
        'Origine France',
        'Ingrédients sélectionnés localement'
    ),
    (
        1,
        '/assets/images/icone/engagement-additifs.svg',
        'Sans additifs',
        'Sans additifs',
        'Ni conservateurs, ni colorants'
    ),
    (
        1,
        '/assets/images/icone/territoire.svg',
        'Circuit court',
        'Circuit court',
        'Fabrication et distribution locales'
    ),
    (
        1,
        '/assets/images/icone/engagement-rse.svg',
        'RSE',
        'RSE',
        'Engagements environnementaux concrets'
    );

INSERT INTO
    partial_Contact (sous_titre, titre)
VALUES (
        'Vous êtes un professionnel ?',
        'Travaillons Ensemble'
    );

INSERT INTO card_Contact (partial_contact_id, titre, description, cta_label, cta_lien, image, alt) VALUES
(1, 'Envie de nous rejoindre ?', 'Découvrez nos offres d\'emploi et devenez acteur d\'une entreprise humaine et engagée.', 'Rejoindre l\'équipe', '/recrutement', '/assets/images/rejoindre-equipe.png', 'Rejoindre l\'équipe'),
(1, 'Une question, un projet ?', 'Notre équipe est à votre écoute.', 'Nous contacter', '/contact', '/assets/images/nous-contacter.png', 'Nous contacter');

-- -----------------------------------------------
-- MISC PARTIALS (labels)
-- -----------------------------------------------

INSERT INTO
    partial_Label (sous_titre, titre)
VALUES (
        'Nos engagements',
        'Des labels qui Comptent'
    );

INSERT INTO
    card_Label (
        partial_label_id,
        logo,
        alt,
        lien,
        ordre
    )
VALUES (
        1,
        '/assets/images/label/logo-la-nouvelle-agriculture.png',
        'La nouvelle agriculture',
        'https://www.lanouvelleagriculture.coop/',
        1
    ),
    (
        1,
        '/assets/images/label/logo-la-nouvelle-agriculture.png',
        'Sans additifs',
        NULL,
        2
    ),
    (
        1,
        '/assets/images/label/logo-la-nouvelle-agriculture.png',
        'Circuit court',
        NULL,
        3
    ),
    (
        1,
        '/assets/images/label/logo-la-nouvelle-agriculture.png',
        'Engagement RSE',
        NULL,
        4
    );

-- -----------------------------------------------
-- PAGE GROUPE
-- -----------------------------------------------

INSERT INTO
    partial_Histoire (sous_titre, titre)
VALUES (
        'Notre histoire',
        'De Broons à la France entière'
    );

INSERT INTO etape_Histoire (partial_histoire_id, annee, nom, region, description, image, alt, ordre) VALUES
(1, '2013', 'La Galette de Broons', 'Bretagne', 'Tout commence à Broons, au cœur des Côtes-d\'Armor. Pascal Enault et Jean-Yves Pierre, deux passionnés à la tête d\'une solide expérience dans l\'agroalimentaire breton, reprennent un atelier familial qui fabrique des crêpes et galettes depuis plus de 35 ans. Leur ambition : réconcilier tradition artisanale et vision moderne pour offrir des produits d\'une qualité exceptionnelle, sans additif ni conservateur. C\'est le début d\'une aventure qui allait transformer le paysage de la crêperie artisanale en France.', 'assets/images/logo_jaimelagalette.png', 'Atelier de Broons', 1),
(1, '2014', 'La Galette d\'Alençon', 'Normandie', 'Portés par une dynamique de croissance prometteuse, Pascal et Jean-Yves ouvrent un deuxième site de production à Alençon, dans l\'Orne. La Galette d\'Alençon étend l\'ancrage du groupe en Normandie et double sa capacité de production. Un nouveau territoire pour la même obsession : l\'ultra-fraîcheur au service du goût, avec des recettes 100 % naturelles, sans additif ni colorant.', 'assets/images/logo_jaimelagalette.png', 'Atelier d\'Alençon', 2),
(1, '2015', 'La Galette du Val de Loire', 'Pays de Loire', 'Le maillage territorial se renforce avec l\'ouverture du troisième atelier à Angers, au carrefour du Val de Loire. Désormais implanté en Bretagne, Normandie et Pays de Loire, le groupe J\'aime la Galette couvre l\'essentiel du Grand Ouest et pose les fondations de son développement national. Trois régions, une seule signature : des crêpes et galettes artisanales d\'une fraîcheur incomparable.', 'assets/images/logo_jaimelagalette.png', 'Atelier d\'Angers', 3),
(1, '2018', 'Naissance de Be Good\'n', 'France entière', 'Le groupe élargit son horizon avec la création de Be Good\'n, une marque dédiée à la gourmandise responsable. Chips artisanales, caramel au beurre salé, cidre fermier… Chaque recette est développée dans le même esprit d\'exigence : des ingrédients naturels et locaux, zéro additif, un goût authentique. Be Good\'n incarne la conviction du groupe que plaisir et responsabilité peuvent aller de pair.', 'assets/images/logo_jaimelagalette.png', 'Marque Be Good\'n', 4),
(1, '2020', 'Engagements RSE', 'France entière', 'Au-delà du goût, le groupe place la responsabilité au cœur de son ADN. Énergie solaire, écopâturage, tri sélectif, méthanisation, circuit court… Chaque atelier devient un laboratoire d\'initiatives vertueuses. Parce que bien produire, c\'est aussi respecter la terre qui nous nourrit et les hommes qui la cultivent. J\'aime la Galette s\'engage pour un impact positif, mesurable et durable sur ses territoires et la planète.', 'assets/images/logo_jaimelagalette.png', 'Engagements RSE', 5);

INSERT INTO partial_Engagement_Groupe (sous_titre, titre, contenu, cta_label, cta_lien) VALUES
('Nos engagements', 'CE QUI NOUS FAIT AGIR', 'Chez J\'aime la Galette, s\'engager ne se dit pas, ça se fait. De nos champs bretons à nos ateliers, chaque décision reflète notre attachement au territoire et notre responsabilité envers la planète. Voici comment.', 'Découvrir notre histoire', '/le-groupe');

INSERT INTO partial_Animation (sous_titre, titre, contenu) VALUES
('Animation Magasin', 'SUR LA ROUTE DES SAVEURS', 'Vivez l\'expérience La Galette de Broons en grandeur nature ! Nos véhicules de collection s\'invitent dans vos rayons pour créer un véritable espace de convivialité, d\'échange et de gourmandise.');

INSERT INTO card_Animation (partial_animation_id, nom, image, alt) VALUES
(1, 'Notre Juvaquatre', 'assets/images/animation-juvaquatre.jpg', 'Juvaquatre J\'aime la Galette'),
(1, 'Notre 2CV', 'assets/images/animation-2cv.jpg', '2CV J\'aime la Galette');

INSERT INTO partial_Livraison (sous_titre, titre, contenu, cta_label, cta_lien, image_fond, alt_fond) VALUES
('Livraison', 'Frais livrés, dès le lendemain', 'Tout ce soin apporté à la fabrication, on le prolonge jusqu\'à votre porte. Nos camions frigorifiques livrent vos galettes et crêpes ultra-fraîches dès le lendemain de leur fabrication, partout en France, sans rupture de la chaîne du froid.', 'Demander un devis', '/contact', 'assets/images/livraison-fond.jpg', 'Camion frigorifique J\'aime la Galette');

INSERT INTO partial_CTA_Produit (titre, cta_label, cta_lien, cta_label2, cta_lien2) VALUES
('ENvie de GOûter ?', 'Découvrir nos produits', '/nos-produits', 'Plonger dans l\'univers de la galette', '/savoir-faire');

-- -----------------------------------------------
-- PAGE NOS-PRODUITS
-- -----------------------------------------------

INSERT INTO
    partial_Transparence (sous_titre, titre)
VALUES (
        'Transparence & pureté',
        'Rien à cacher, tout à montrer'
    );

INSERT INTO
    card_Transparence (
        partial_transparence_id,
        image,
        alt,
        titre,
        description
    )
VALUES (
        1,
        '/assets/images/icone/sans.svg',
        'Sans additifs',
        'Sans additifs',
        'Des recettes pures et minimalistes. La fraîcheur de nos ateliers à votre table.'
    ),
    (
        1,
        '/assets/images/icone/sans.svg',
        'Sans conservateurs',
        'Sans conservateurs',
        'Des recettes pures et minimalistes. La fraîcheur de nos ateliers à votre table.'
    ),
    (
        1,
        '/assets/images/icone/sans.svg',
        'Sans colorants',
        'Sans colorants',
        'La teinte naturelle des céréales bretonnes.'
    ),
    (
        1,
        '/assets/images/icone/sans.svg',
        'Sans arômes art.',
        'Sans arômes artificiels',
        'Le vrai goût authentique du beurre et du sarrasin.'
    );

-- -----------------------------------------------
-- PAGE SAVOIR-FAIRE
-- -----------------------------------------------

INSERT INTO
    partial_SavoirFaire (cta_label, cta_lien)
VALUES (
        'Découvrir nos engagements RSE',
        '/rse'
    );

INSERT INTO card_SavoirFaire (partial_savoirfaire_id, sous_titre, titre, contenu, image_fond, alt_fond, ordre) VALUES
(1, 'Un process artisanal exigeant', 'Fabrication', 'Nos galettes et crêpes sont fabriquées chaque jour selon un savoir-faire traditionnel hérité de plus de 35 ans d\'histoire, depuis La Galette de Broons. Pas d\'additif, de colorant, de conservateur, d\'exhausteur de goût ni d\'injection de gaz neutre — seulement des ingrédients rigoureusement sélectionnés, le moins d\'ingrédients possible, et un procédé de fabrication maîtrisé de bout en bout. Chaque produit est marqué de sa date de fabrication : notre contrat fraîcheur, visible et tenu à chaque livraison.', 'assets/images/savoir-faire/sf-conditionnement.svg', 'Fabrication artisanale', 1),
(1, 'Flexibilité industrielle', 'Des conditionnements adaptés', 'Restaurateurs, distributeurs, GMS, épiceries fines — chaque client a ses contraintes, et nous nous y adaptons. Nos ateliers de production nous permettent de répondre à des volumes variés tout en maintenant la qualité artisanale qui nous définit. Notre gamme s\'étend des galettes 100% farine de blé noir aux crêpes de froment, avec des produits complémentaires sélectionnés et mis au point avec des artisans locaux selon notre propre cahier des charges.', 'assets/images/savoir-faire/sf-conditionnement.svg', 'Packaging produit J\'aime la Galette', 2),
(1, 'Logistique', 'Une couverture régionale efficace', 'Nous livrons rapidement dans tout l\'Ouest grâce à nos 3 unités de production implantées au cœur des régions : en Bretagne à Broons, en Normandie à Alençon et dans les Pays de la Loire à Angers. Ce choix d\'implantation régionale est une décision stratégique forte : produire au plus près de nos clients pour garantir l\'ultra-fraîcheur et une fréquence de livraison élevée. Notre flotte de camionnettes achemine les produits très rapidement vers vous, respectant en toutes circonstances la chaîne du froid et vos délais.', 'assets/images/savoir-faire/sf-conditionnement.svg', 'Flotte de livraison J\'aime la Galette', 3),
(1, 'Circuit court', 'Local et responsable', 'Nous privilégions la fabrication locale et la distribution locale pour réduire notre impact environnemental et soutenir l\'économie de nos territoires d\'implantation. Proximité avec nos fournisseurs, réactivité avec nos partenaires : le circuit court n\'est pas un argument de vente chez nous, c\'est notre façon naturelle de travailler depuis 2013, date à laquelle Pascal Enault et Jean-Yves Pierre ont repris La Galette de Broons.', 'assets/images/savoir-faire/sf-conditionnement.svg', 'Champ breton circuit court', 4),
(1, 'Origine France & filières', 'Des ingrédients de qualité', 'Nos matières premières sont exclusivement françaises : nous n\'utilisons que des farines issues de moulins du Grand Ouest, et nos crêpes de froment sont fabriquées avec une farine provenant d\'un moulin des Côtes-d\'Armor. Le blé noir que nous utilisons est cultivé en France, avec une traçabilité garantie et une attention particulière portée aux filières engagées dans la préservation de cette culture traditionnelle bretonne.', 'assets/images/savoir-faire/sf-conditionnement.svg', 'Ingrédients de qualité J\'aime la Galette', 5);

-- -----------------------------------------------
-- PAGE PRODUIT
-- -----------------------------------------------


INSERT INTO produit (sous_titre, titre, marque, marque_id, nom, image, en_ligne) VALUES
('Rencontrer le meilleur', 'Nos crêpes & galettes', 'J\'aime la Galette', 1, 'Galette', '/assets/images/produit/jaime-la-galette/produit-galette.png', 1),
('Rencontrer le meilleur', 'Nos crêpes & galettes', 'J\'aime la Galette', 1, 'Crêpe', '/assets/images/produit/jaime-la-galette/produit-crepe.png', 1),


('Rencontrer le meilleur', 'Nos chips de sarazin', 'Be good\'n', 2, 'Sel de guérande', '/assets/images/produit/be-goodn/produit-chips-sel.png', 1),
('Rencontrer le meilleur', 'Nos chips de sarazin', 'Be good\'n', 2, 'Tomates bazilic', '/assets/images/produit/be-goodn/produit-chips-tomates.png', 1),
('Rencontrer le meilleur', 'Nos chips de sarazin', 'Be good\'n', 2, 'Oignons roses', '/assets/images/produit/be-goodn/produit-chips-oignons.png', 1),
('Rencontrer le meilleur', 'Notre cidre', 'Be good\'n', 2, 'Cidre brut', '/assets/images/produit/be-goodn/produit-cidre-brut.png', 1),
('Rencontrer le meilleur', 'Notre caramel', 'Be good\'n', 2, 'Caramel', '/assets/images/produit/be-goodn/produit-caramel.png', 1);


INSERT INTO produit_Apropos (produit_id, sous_titre, titre, contenu, cta_label, cta_lien, image, alt) VALUES
(1, 'À propos', 'Galette', 'Nos galettes de blé noir ultra-fraîches incarnent l\'authenticité bretonne, élaborées selon des recettes traditionnelles sans artifice. Fournisseur fiable pour professionnels, nous offrons une texture souple, moelleuse et un goût brut, idéal pour galettes blé noir sans additifs.', 'Demander un devis', '/contact', '/assets/images/produit/jaime-la-galette/produit-galette.png', 'Galette de blé noir J\'aime la Galette'),
(2, 'À propos', 'Crêpe', 'Nos crêpes de froment ultra-fraîches, fabriquées selon les mêmes exigences artisanales que nos galettes. Sans additifs, ni conservateurs, pour un goût authentique à chaque dégustation.', 'Demander un devis', '/contact', '/assets/images/produit/jaime-la-galette/produit-crepe.png', 'Crêpe de froment J\'aime la Galette'),

(3, 'À propos', 'Chips de sarazin au sel de guérande', 'Nos chips de sarazin au sel de guérande sont fabriquées selon les mêmes exigences artisanales que nos galettes. Sans additifs, ni conservateurs, pour un goût authentique à chaque dégustation.', 'Demander un devis', '/contact', '/assets/images/produit/be-goodn/produit-chips-sel.png', 'Chips de sarazin au sel de guérande Be Good\'n'),
(4, 'À propos', 'Chips de sarazin aux tomates bazilic', 'Nos chips de sarazin aux tomates bazilic sont fabriquées selon les mêmes exigences artisanales que nos galettes. Sans additifs, ni conservateurs, pour un goût authentique à chaque dégustation.', 'Demander un devis', '/contact', '/assets/images/produit/be-goodn/produit-chips-tomates.png', 'Chips de sarazin aux tomates bazilic Be Good\'n'),
(5, 'À propos', 'Chips de sarazin aux oignons roses', 'Nos chips de sarazin aux oignons roses sont fabriquées selon les mêmes exigences artisanales que nos galettes. Sans additifs, ni conservateurs, pour un goût authentique à chaque dégustation.', 'Demander un devis', '/contact', '/assets/images/produit/be-goodn/produit-chips-oignons.png', 'Chips de sarazin aux oignons roses Be Good\'n'),
(6, 'À propos', 'Cidre brut', 'Notre cidre brut est fabriqué selon les mêmes exigences artisanales que nos galettes. Sans additifs, ni conservateurs, pour un goût authentique à chaque dégustation.', 'Demander un devis', '/contact', '/assets/images/produit/be-goodn/produit-cidre-brut.png', 'Cidre brut Be Good\'n'),
(7, 'À propos', 'Caramel', 'Notre caramel est fabriqué selon les mêmes exigences artisanales que nos galettes. Sans additifs, ni conservateurs, pour un goût authentique à chaque dégustation.', 'Demander un devis', '/contact', '/assets/images/produit/be-goodn/produit-caramel.png', 'Caramel Be Good\'n');

INSERT INTO partial_Ingredient (produit_id, sous_titre, titre, image_fond, alt_fond) VALUES
(1, 'Ingrédients & qualité', 'L\'essentiel, et rien d\'autre', '/assets/images/ingredient/ingredient-fond.png', 'Fond ingrédients');

INSERT INTO
    card_Ingredient (
        partial_ingredient_id,
        image,
        alt,
        titre,
        description,
        ordre
    )
VALUES (
        1,
        '/assets/images/icone/ingredient-simple.svg',
        'Ingrédient simple',
        'Ingrédient simple',
        'Farine de blé noir, eau, sel, œufs',
        1
    ),
    (
        1,
        '/assets/images/icone/qualite.svg',
        'Qualité',
        'Qualité',
        'Aucun conservateur, additifs, colorants',
        2
    ),
    (
        1,
        '/assets/images/icone/ingredient-savoirfaire.svg',
        'Savoir-faire',
        'Savoir-faire artisanal',
        'Des recettes traditionnelles et un savoir-faire 100% français',
        3
    ),
    (
        1,
        '/assets/images/icone/france.svg',
        'Traçabilité',
        'Traçabilité et garantie',
        'Des filières sélectionnées et un contrôle qualité rigoureux',
        4
    );

-- -----------------------------------------------
-- PAGE RECRUTEMENT
-- -----------------------------------------------

INSERT INTO
    partial_Recrutement (sous_titre, titre)
VALUES (
        'Pourquoi nous rejoindre ?',
        'Pourquoi nous rejoindre ?'
    );

INSERT INTO card_Recrutement (partial_recrutement_id, image, alt, titre, description, ordre) VALUES
(1, '/assets/images/icone/collaborateurs.svg', 'Esprit d\'équipe', 'Esprit d\'équipe', 'Chez nous, l\'humain prime. Nous cultivons une ambiance chaleureuse, bienveillante et solidaire au quotidien.', 1),
(1, '/assets/images/icone/ingredient-savoirfaire.svg', 'Savoir-faire', 'Savoir-faire', 'Transmettre la passion du produit authentique. Vous serez formés à nos techniques traditionnelles.', 2),
(1, '/assets/images/icone/qualite.svg', 'Qualité de vie', 'Qualité de vie', 'Des avantages sociaux concrets, un équilibre vie pro/vie perso respecté, et bien sûr... des galettes !', 3);

INSERT INTO
    partial_Processus (sous_titre, titre)
VALUES (
        'Simple et transparent',
        'Notre processus'
    );

INSERT INTO etape_Processus (partial_processus_id, titre, description, ordre) VALUES
(1, 'Candidature', 'Envoyez-nous votre CV et dites-nous pourquoi vous !', 1),
(1, 'Premier échange', 'Un appel téléphonique court pour faire connaissance.', 2),
(1, 'Rencontre', 'Entretien sur site et découverte de l\'atelier ou des bureaux.', 3),
(1, 'Intégration', 'Bienvenue ! Un parcours d\'accueil sur-mesure vous attend.', 4);

INSERT INTO partial_Offre (sous_titre, titre) VALUES
('Nous rejoindre', 'Nos offres d\'emploi');

INSERT INTO candidature_spontanee (titre, description, cta_label, cta_lien, en_ligne) VALUES
('Pas d\'offre en ce moment qui correspond à votre profil ?', 'On aime rencontrer des gens passionnés. Envoyez-nous votre candidature spontanée !', 'Envoyer ma candidature spontanée', '/recrutement/candidature-spontanee', 1);

-- -----------------------------------------------
-- PAGE FAQ
-- -----------------------------------------------

INSERT INTO
    partial_FAQ (sous_titre, titre, contenu)
VALUES (
        'On vous dit tout',
        'FOIRE AUX QUESTIONS',
        'Vous vous posez des questions sur nos produits, notre distribution ou nos engagements ? Trouvez vos réponses ici ou posez-les directement à notre assistant virtuel en bas de page !'
    );

INSERT INTO
    categorie_FAQ (partial_faq_id, titre, ordre)
VALUES (1, 'Nos Produits', 1),
    (
        1,
        'Distribution & Points de vente',
        2
    ),
    (
        1,
        'Devis, RSE & Recrutement',
        3
    );

INSERT INTO question_FAQ (categorie_faq_id, question, reponse, mots_cles, profil_cible, ordre, en_ligne) VALUES
(1, 'Quels additifs utilisez-vous dans vos recettes ?', 'Nous n\'utilisons aucun additif dans nos recettes. Nos galettes et crêpes sont fabriquées uniquement à partir d\'ingrédients naturels : farine de blé noir, eau, sel et œufs.', 'additifs, conservateurs, colorants, ingrédients, composition, naturels', NULL, 1, 1),
(1, 'Quelle est la différence entre une galette et une crêpe chez vous ?', 'La galette est fabriquée à base de farine de blé noir (sarrasin), sans gluten, avec un goût rustique et authentique. La crêpe est fabriquée à base de farine de froment, plus douce et moelleuse.', 'différence, galette, crêpe, blé noir, sarrasin, froment, gluten', NULL, 2, 1),
(1, 'Vos produits sont-ils certifiés bio (AB) ?', 'Nos produits ne sont pas certifiés bio, mais nous sélectionnons rigoureusement nos filières pour garantir une qualité maximale et une traçabilité complète de nos ingrédients.', 'bio, certification, biologique, AB, label, agriculture biologique', NULL, 3, 1),
(2, 'Où trouver vos produits J\'aime la Galette ?', 'Nos produits sont disponibles en GMS (grandes et moyennes surfaces) dans tout l\'Ouest de la France, ainsi qu\'en commande directe pour les professionnels.', 'trouver, acheter, points de vente, magasins, GMS, distribution, commande', NULL, 1, 1),
(2, 'Quelles enseignes GMS distribuent vos gammes ?', 'Nos produits sont référencés dans plusieurs grandes enseignes de la distribution alimentaire. Contactez-nous pour connaître les points de vente les plus proches de chez vous.', 'enseignes, GMS, grandes surfaces, magasins, distribution, référencement', NULL, 2, 1),
(2, 'Livrez-vous hors de votre région historique ?', 'Oui, nous livrons partout en France grâce à notre flotte de camions frigorifiques au départ de nos trois ateliers de production en Bretagne, Normandie et Pays de la Loire.', 'livraison, France, transport, camion frigorifique, fraîcheur, nationale', NULL, 3, 1),
(3, 'Professionnels : comment commander et quels sont les délais ?', 'Pour toute commande professionnelle, contactez-nous via le formulaire de notre page Contact en sélectionnant le profil \"Professionnel\". Nos équipes vous répondent sous 24h.', 'commande, professionnel, devis, délai, contact, b2b', 'b2b', 1, 1),
(3, 'Qu\'est-ce que l\'écopâturage mis en place autour de vos ateliers ?', 'Nos espaces verts sont entretenus par des animaux (moutons) et non par des machines thermiques. Cette pratique respecte les sols, préserve la faune locale et réduit notre empreinte carbone.', 'écopâturage, moutons, biodiversité, RSE, environnement, espaces verts', NULL, 2, 1),
(3, 'Quels types de postes recrutez-vous et comment postuler ?', 'Nous recrutons principalement des crêpiers, maîtres galettiers, commerciaux et profils logistiques. Consultez nos offres sur la page Recrutement et candidatez directement en ligne.', 'recrutement, emploi, postuler, candidature, offres, métiers, crêpier', 'rh', 3, 1);

-- -----------------------------------------------
-- RSE
-- -----------------------------------------------

INSERT INTO action_RSE (image, alt, titre, description, contenu, tags, ordre) VALUES
(NULL, NULL, 'Circuit court', 'Fabriqué ici, distribué ici', 'Tout commence par un choix : rester ici. De la farine bretonne à la livraison, chaque étape se passe au plus près de chez nous, avec des partenaires locaux, pour une filière qui a vraiment du sens.', NULL, 1),
('assets/images/rse/rse-energie.png', 'Panneaux solaires', 'Énergie renouvelable', 'On mise sur le soleil breton', 'Nos panneaux photovoltaïques produisent une partie de l\'énergie de nos ateliers, moins de fossiles, plus d\'autonomie, un impact positif mesurable.', 'Énergie solaire · Production autonome · Zéro fossile · Impact positif', 2),
('assets/images/rse/rse-biodiversite.png', 'Mouton écopâturage', 'Biodiversité', 'La nature, notre jardinière', 'On va encore plus loin autour de nos ateliers. Nos espaces verts sont entretenus par des animaux, pas des machines. L\'écopâturage respecte les sols, préserve la faune locale et crée un cadre agréable pour nos équipes au quotidien.', 'Écopâturage · Zéro tondeuse thermique · Biodiversité préservée', 3),
('assets/images/rse/rse-recyclage.png', 'Mascotte tri sélectif', 'Recyclage', 'Ici, rien ne se perd', 'Et ce qui sort de nos ateliers a toujours une deuxième vie. Tri sélectif, méthanisation, compostage, redistribution aux élevages locaux : chaque déchet devient une ressource. Ici, gaspiller n\'est tout simplement pas une option.', 'Tri sélectif · Méthanisation · Composteur', 4);

-- -----------------------------------------------
-- PAGES INTRO (partial_Intro)
-- -----------------------------------------------

INSERT INTO partial_Intro (sous_titre, titre, citation, contenu, nom_page, image_fond, alt_fond, image_mascotte, alt_mascotte) VALUES
('Fabrication artisanale · Depuis toujours', 'Le Groupe', 'Les meilleurs ingrédients, de la passion, un vrai savoir-faire traditionnel, l\'obsession de la fraîcheur', 'Né en 2013 de la vision de Pascal et Jean-Yves, J\'aime la Galette est aujourd\'hui un groupe artisanal reconnu, avec trois ateliers de production implantés au cœur des terroirs bretons, normands et ligériens.', 'le-groupe', NULL, NULL, '/assets/images/intro/mascotte-groupe.png', 'Photo des 2 patrons'),
('Le goût de l\'authentique', 'NOS DEUX UNIVERS PRODUITS', NULL, 'Notre philosophie ? Des recettes simples, saines et profondément ancrées dans nos terroirs. Pour répondre à toutes vos envies, nous avons façonné deux gammes uniques : le respect absolu de la tradition bretonne d\'un côté, et une approche moderne et créative de l\'autre.', 'nos-produits', NULL, NULL, '/assets/images/mascotte-produits.png', 'Mascotte avec galette et cidre'),
('De la farine bretonne à votre assiette', 'Notre Savoir-faire', NULL, 'Un savoir-faire traditionnel breton, une obsession de l\'ultra-fraîcheur et une exigence de chaque instant — pour des galettes et crêpes savoureuses, locales et livrées au plus près de vous.', 'savoir-faire', NULL, NULL, '/assets/images/mascotte-savoirfaire.png', 'Mascotte avec galette et cidre'),
('Rencontrer le meilleur', 'NoS crêpes & galettes', NULL, NULL, 'produits', NULL, NULL, NULL, NULL),
('Des actes plutôt que des mots', 'NOS ENGAGEMENTS RSE ET VALEURS', '', 'Parce que produire de bonnes galettes implique de respecter la terre qui nous fournit nos ingrédients. Nous agissons au quotidien pour réduire notre empreinte et soutenir nos territoires.', 'rse', NULL, '', '/assets/images/mascotte-rse.png', 'Mascotte qui fait du tri sélectif'),
('Restons en contact', 'TRAVAILLONS ENSEMBLE', 'Votre message a bien été envoyé ! Notre équipe vous répondra dans les plus brefs délais.', 'Une question, une remarque ou un projet de partenariat ? Sélectionnez votre profil ci-dessous et remplissez le formulaire, notre équipe vous répondra dans les plus brefs délais.', 'contact', NULL, NULL, '/assets/images/mascotte-contact.png', 'Mascotte avec galette et cidre'),
('Rejoignez l\'aventure', 'VENEZ FAIRE DES CRÊPES', NULL, 'Plus qu\'une entreprise, une véritable famille de passionnés du goût et du savoir-faire breton. Découvrez nos métiers et construisons ensemble l\'avenir de la galette.', 'recrutement', NULL, NULL, '/assets/images/mascotte-savoirfaire.png', 'Mascotte qui fait des crêpes'),
('On vous dit tout', 'FOIRE AUX QUESTIONS', NULL, 'Vous vous posez des questions sur nos produits, notre distribution ou nos engagements ? Trouvez vos réponses ici ou posez-les directement à notre assistant virtuel en bas de page !', 'faq', NULL, NULL, NULL, NULL),
('Informations légales', 'MENTIONS LÉGALES', NULL, NULL, 'mentions-legales', NULL, NULL, NULL, NULL),
('Gestion des cookies', 'POLITIQUE DE COOKIES', NULL, NULL, 'cookies', NULL, NULL, NULL, NULL),
('Protection des données', 'POLITIQUE DE CONFIDENTIALITÉ', NULL, NULL, 'confidentialite', NULL, NULL, NULL, NULL);

-- -----------------------------------------------
-- PAGES LÉGALES (partial_Legal + legal_Section)
-- -----------------------------------------------

INSERT INTO
    partial_Legal (nom_page)
VALUES ('mentions-legales'),
    ('cookies'),
    ('confidentialite');

-- Mentions légales
INSERT INTO legal_Section (partial_legal_id, titre, contenu, ordre) VALUES
(1, 'Propriétaire / Éditeur',
'<p><strong>LA GALETTE DE BROONS</strong><br>
Zone Artisanale du Pilaga<br>
22250 BROONS</p>
<p>Siret : 402557763 00024</p>
<p>N° de TVA intracommunautaire : FR 21 402557763</p>
<p>Directeur de publication et Webmaster : Pascal Enault et Jean-Yves Pierre</p>', 1),
(1, 'Réalisation graphique et technique',
'<p>Communication Internet and Networks Solutions – CINS<br>
Agence Web Caen<br>
9 rue Raymonde Bail<br>
14000 CAEN</p>', 2),
(1, 'Droit d\'auteur – Copyright – Réutilisation des contenus',
'<p>Aucune reproduction de texte, visuel, logo ou autre iconographie n\'est autorisée, que ce soit sur support électronique ou papier.</p>', 3),
(1, 'Données personnelles',
'<p>Aucune information personnelle n\'est collectée à votre insu et/ou cédée à des tiers.</p>
<p>Vous disposez d\'un droit d\'accès, de rectification et d\'opposition aux données vous concernant que vous pouvez exercer en contactant le Webmaster du site.</p>', 4),
(1, 'Cookies et statistiques',
'<p>En vue d\'améliorer l\'accessibilité et l\'ergonomie du site au besoin des internautes, nous mesurons le nombre de visites, le nombre de pages vues ainsi que l\'activité des visiteurs sur le site et leur fréquence de retour.</p>
<p><a href="/cookies">Voir notre politique de cookies</a></p>', 5),
(1, 'Formulaires en ligne',
'<p>La plupart des informations fournies dans les formulaires en ligne sont obligatoires. Elles font l\'objet d\'un traitement informatisé par le gestionnaire du site et sont destinées aux membres et services du propriétaire/éditeur, ainsi qu\'au public désireux de s\'informer de l\'existence d\'un fichier dans les conditions prévues à l\'article 31 de la loi du 6 janvier 1978 modifiée.</p>
<p>Vous pouvez exercer votre droit d\'accès et de rectification aux informations vous concernant en écrivant à la « LA GALETTE DE BROONS, Zone Artisanale du Pilaga, 22250 BROONS » ou par téléphone.</p>', 6);

-- Cookies
INSERT INTO legal_Section (partial_legal_id, titre, contenu, ordre) VALUES
(2, 'À quoi servent les cookies émis sur notre site ?',
'<p>Seul l\'émetteur d\'un cookie est susceptible de lire ou de modifier des informations qui y sont contenues.</p>
<p>Les cookies que nous émettons nous permettent :</p>
<ul>
    <li>d\'établir des statistiques et volumes de fréquentation et d\'utilisation des diverses éléments composant notre Site (rubriques et contenus visités, parcours), nous permettant d\'améliorer l\'intérêt et l\'ergonomie de nos Services ;</li>
    <li>d\'adapter la présentation de notre Site aux préférences d\'affichage de votre Terminal (langue utilisée, résolution d\'affichage, système d\'exploitation utilisé, etc.) lors de vos visites sur notre Site, selon les matériels et les logiciels de visualisation ou de lecture que votre Terminal comporte ;</li>
    <li>de mémoriser des informations relatives à un formulaire que vous avez rempli sur notre Site ;</li>
    <li>de mettre en œuvre des mesures de sécurité.</li>
</ul>', 1),
(2, 'Vos choix concernant les cookies',
'<p>Vous pouvez configurer votre logiciel de navigation de manière à ce que des cookies soient enregistrés dans votre Terminal ou, au contraire, qu\'ils soient rejetés, soit systématiquement, soit selon leur émetteur.</p>
<p>Pour la gestion des cookies et de vos choix, la configuration de chaque navigateur est différente :</p>
<ul>
    <li><strong>Chrome :</strong> Menu &gt; Paramètres &gt; Confidentialité &gt; Paramètres de contenu &gt; Cookies</li>
    <li><strong>Firefox :</strong> Outils &gt; Options &gt; Vie Privée &gt; Afficher les cookies</li>
    <li><strong>Internet Explorer :</strong> Outils &gt; Options Internet &gt; Général &gt; Supprimer &gt; Cookies</li>
    <li><strong>Safari :</strong> Menu &gt; Préférences &gt; Sécurité &gt; Afficher les cookies</li>
</ul>', 2),
(2, 'Désactiver Google Analytics',
'<p>Nom du cookie utilisé : <strong>_ga</strong><br>
Type de cookie : Cookies d\'audience</p>
<p>Vous pouvez empêcher l\'utilisation et le dépôt de ce cookie sur votre poste en vous rendant sur <a href="https://tools.google.com/dlpage/gaoptout?hl=fr" target="_blank" rel="noopener">https://tools.google.com/dlpage/gaoptout?hl=fr</a>.</p>', 3);

-- Confidentialité
INSERT INTO legal_Section (partial_legal_id, titre, contenu, ordre) VALUES
(3, 'Collecte des données personnelles',
'<p>Les informations recueillies via les formulaires de contact et de recrutement font l\'objet d\'un traitement informatisé destiné au gestionnaire du site. Conformément à la loi « Informatique et Libertés » du 6 janvier 1978 modifiée et au RGPD, vous disposez d\'un droit d\'accès, de rectification, d\'opposition et de suppression des données vous concernant.</p>
<p>Pour exercer ces droits, écrivez à : <strong>LA GALETTE DE BROONS, Zone Artisanale du Pilaga, 22250 BROONS</strong>.</p>', 1),
(3, 'Destinataires des données',
'<p>Les données collectées sont destinées aux services internes du propriétaire/éditeur du site. Elles ne sont en aucun cas cédées à des tiers sans votre accord explicite.</p>', 2),
(3, 'Durée de conservation',
'<p>Les données personnelles sont conservées pendant la durée nécessaire à la finalité du traitement et conformément aux obligations légales.</p>', 3),
(3, 'Sécurité',
'<p>Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour garantir la sécurité et la confidentialité de vos données personnelles.</p>', 4);

-- -----------------------------------------------
-- VISITES MYSTÈRES
-- -----------------------------------------------

-- Commerciaux (mot de passe : commercial123)
INSERT INTO
    commercial (
        nom,
        prenom,
        email,
        login,
        mot_de_passe,
        telephone
    )
VALUES (
        'LE SECH',
        'Paulin',
        'p.lesech@jaimelagalette.com',
        'plesech',
        '$2y$10$AaaSYDzb4t1G..AwC9vPb.whb/MF6/s1Y/J2v07AQUEa7R3oFy/gy',
        '0612345678'
    ),
    (
        'Martin',
        'Sophie',
        'sophie.martin@jaimelagalette.fr',
        'smartin',
        '$2y$10$AaaSYDzb4t1G..AwC9vPb.whb/MF6/s1Y/J2v07AQUEa7R3oFy/gy',
        '0687654321'
    );

-- Livreurs
INSERT INTO
    livreur (
        nom,
        prenom,
        secteur,
        telephone
    )
VALUES (
        'Le Goff',
        'Yann',
        'Bretagne Nord',
        '0611111111'
    ),
    (
        'Dubois',
        'Pierre',
        'Bretagne Sud',
        '0622222222'
    ),
    (
        'Moreau',
        'Julie',
        'Normandie',
        '0633333333'
    ),
    (
        'Petit',
        'Lucas',
        'Pays de la Loire',
        '0644444444'
    ),
    (
        'Roux',
        'Camille',
        'Île-de-France',
        '0655555555'
    );

-- Questions d'inspection
INSERT INTO
    inspection_question (categorie, question, ordre)
VALUES (
        'Propreté & Hygiène',
        'Le stand est-il propre et bien entretenu ?',
        1
    ),
    (
        'Propreté & Hygiène',
        'Les vitrines et surfaces de présentation sont-elles propres ?',
        2
    ),
    (
        'Propreté & Hygiène',
        'La tenue du livreur est-elle propre et conforme ?',
        3
    ),
    (
        'Propreté & Hygiène',
        'L''espace de vente est-il rangé et organisé ?',
        4
    ),
    (
        'Merchandising & Mise en place',
        'Les produits sont-ils présentés de manière appétissante ?',
        5
    ),
    (
        'Merchandising & Mise en place',
        'Les étiquettes et prix sont-ils visibles et à jour ?',
        6
    ),
    (
        'Merchandising & Mise en place',
        'L''approvisionnement en produits est-il suffisant ?',
        7
    ),
    (
        'Merchandising & Mise en place',
        'Le logo et les couleurs de la marque sont-ils respectés ?',
        8
    ),
    (
        'Professionnalisme',
        'Le livreur est-il courtois, souriant et professionnel ?',
        9
    ),
    (
        'Professionnalisme',
        'Le livreur porte-t-il les équipements requis ?',
        10
    );