-- ============================================================
-- Pages légales : mentions légales, cookies, confidentialité
-- Mise à jour d'octobre 2026, alignée sur ce que le site collecte réellement.
--
-- Chargé automatiquement à l'initialisation de la base (après 01_schema et 02_seeds).
-- À exécuter aussi sur la base de production (phpMyAdmin > SQL) : le script est rejouable.
--
-- Contenu à faire valider par le responsable du site avant mise en ligne :
--   hébergeur, e-mail de contact RGPD, durées de conservation, destinataires.
-- ============================================================

SET NAMES utf8mb4;

DELETE FROM legal_Section WHERE partial_legal_id IN (1, 2, 3);

-- ── Mentions légales (partial_legal_id = 1) ─────────────────

INSERT INTO legal_Section (partial_legal_id, ordre, titre, contenu) VALUES
(1, 1, 'Propriétaire / Éditeur', '<p><strong>LA GALETTE DE BROONS</strong><br>
Zone Artisanale du Pilaga<br>
22250 BROONS</p>
<p>Siret : 402557763 00024</p>
<p>N° de TVA intracommunautaire : FR 21 402557763</p>
<p>Directeur de publication et Webmaster : Pascal Enault et Jean-Yves Pierre</p>'),

(1, 2, 'Hébergeur', '<p>Le site est hébergé par :</p>
<p><strong>LWS (Ligne Web Services)</strong><br>
10 rue de Penthièvre<br>
75008 PARIS<br>
Téléphone : 01 77 62 30 03<br>
<a href="https://www.lws.fr" target="_blank" rel="noopener">www.lws.fr</a></p>'),

(1, 3, 'Réalisation graphique et technique', '<p>Communication Internet and Networks Solutions – CINS<br>
Agence Web Caen<br>
9 rue Raymonde Bail<br>
14000 CAEN</p>'),

(1, 4, 'Droit d''auteur – Copyright – Réutilisation des contenus', '<p>Aucune reproduction de texte, visuel, logo ou autre iconographie n''est autorisée, que ce soit sur support électronique ou papier.</p>'),

(1, 5, 'Données personnelles', '<p>Les données personnelles que vous nous transmettez (formulaires de contact et de recrutement, assistant virtuel) ne sont jamais collectées à votre insu ni cédées à des tiers à des fins commerciales.</p>
<p>Le détail des données collectées, de leurs finalités, de leur durée de conservation et de vos droits figure dans notre <a href="/politique-confidentialite">politique de confidentialité</a>.</p>
<p>Pour exercer vos droits : <a href="mailto:siteweb@jaimelagalette.com">siteweb@jaimelagalette.com</a> ou par courrier à « LA GALETTE DE BROONS, Zone Artisanale du Pilaga, 22250 BROONS ».</p>'),

(1, 6, 'Cookies', '<p>Ce site n''utilise aucun cookie publicitaire et aucun outil de mesure d''audience. Seul un cookie technique de session, nécessaire à la sécurité des formulaires, peut être déposé.</p>
<p><a href="/cookies">Voir notre politique de cookies</a></p>'),

(1, 7, 'Formulaires en ligne', '<p>Les champs marqués comme obligatoires dans les formulaires en ligne sont nécessaires au traitement de votre demande. Les informations sont destinées aux services concernés de LA GALETTE DE BROONS (relation client, ressources humaines).</p>
<p>Conformément au règlement (UE) 2016/679 (RGPD) et à la loi « Informatique et Libertés » du 6 janvier 1978 modifiée, vous disposez de droits sur vos données, présentés dans la <a href="/politique-confidentialite">politique de confidentialité</a>.</p>');

-- ── Politique de cookies (partial_legal_id = 2) ─────────────

INSERT INTO legal_Section (partial_legal_id, ordre, titre, contenu) VALUES
(2, 1, 'Qu''est-ce qu''un cookie ?', '<p>Un cookie est un petit fichier enregistré sur votre terminal (ordinateur, tablette, téléphone) lorsque vous consultez un site. Seul l''émetteur d''un cookie peut lire ou modifier les informations qu''il contient.</p>'),

(2, 2, 'Les cookies utilisés sur ce site', '<p>Ce site n''utilise <strong>aucun cookie publicitaire</strong>, <strong>aucun cookie de réseau social</strong> et <strong>aucun outil de mesure d''audience</strong> (type Google Analytics).</p>
<p>Un seul cookie technique peut être déposé :</p>
<ul>
    <li><strong>PHPSESSID</strong> : identifiant de session. Il n''est déposé que sur les pages comportant un formulaire (contact et recrutement). Il sert à protéger les formulaires contre les envois frauduleux (jeton de sécurité). Il ne contient aucune donnée personnelle et expire à la fermeture du navigateur.</li>
</ul>
<p>Ce cookie est strictement nécessaire au fonctionnement du service que vous demandez : il est dispensé de consentement. C''est pourquoi ce site n''affiche pas de bandeau de cookies.</p>'),

(2, 3, 'Services tiers et adresse IP', '<p>Les polices de caractères et les bibliothèques techniques du site sont hébergées sur nos propres serveurs : leur affichage n''entraîne aucune requête vers un service tiers.</p>
<p>Une exception : la carte interactive des sites de production (accueil, Le Groupe, contact et pages des ateliers) charge des fonds de carte auprès de <strong>Stadia Maps</strong> (<a href="https://stadiamaps.com/privacy/" target="_blank" rel="noopener">politique de confidentialité</a>), avec les données cartographiques d''OpenStreetMap. Pour afficher ces fonds de carte, votre navigateur transmet votre adresse IP à ce prestataire, sans dépôt de cookie.</p>'),

(2, 4, 'Vos choix concernant les cookies', '<p>Vous pouvez configurer votre navigateur pour accepter ou refuser les cookies, ou pour être prévenu avant leur enregistrement. Refuser le cookie de session n''empêche pas de consulter le site, mais peut empêcher l''envoi des formulaires.</p>
<ul>
    <li><strong>Chrome :</strong> Paramètres &gt; Confidentialité et sécurité &gt; Cookies et autres données des sites</li>
    <li><strong>Edge :</strong> Paramètres &gt; Cookies et autorisations de site &gt; Gérer et supprimer les cookies</li>
    <li><strong>Firefox :</strong> Paramètres &gt; Vie privée et sécurité &gt; Cookies et données de sites</li>
    <li><strong>Safari :</strong> Réglages &gt; Confidentialité &gt; Gérer les données de sites web</li>
</ul>
<p>Pour en savoir plus sur les cookies : <a href="https://www.cnil.fr/fr/cookies-et-autres-traceurs" target="_blank" rel="noopener">cnil.fr</a>.</p>'),

(2, 5, 'Évolution de cette politique', '<p>Si un outil de mesure d''audience ou tout autre traceur nécessitant votre consentement était ajouté à ce site, un bandeau de choix serait mis en place et cette page serait mise à jour. Dernière mise à jour : octobre 2026.</p>');

-- ── Politique de confidentialité (partial_legal_id = 3) ─────

INSERT INTO legal_Section (partial_legal_id, ordre, titre, contenu) VALUES
(3, 1, 'Responsable du traitement', '<p><strong>LA GALETTE DE BROONS</strong><br>
Zone Artisanale du Pilaga, 22250 BROONS<br>
Contact pour toute question relative à vos données : <a href="mailto:siteweb@jaimelagalette.com">siteweb@jaimelagalette.com</a></p>
<p>Dernière mise à jour : octobre 2026.</p>'),

(3, 2, 'Données collectées, finalités et bases légales', '<p>Nous ne collectons que les données que vous saisissez vous-même :</p>
<ul>
    <li><strong>Formulaire de contact</strong> (grand public et professionnels) : nom, adresse e-mail, objet et contenu du message ; pour les professionnels, également société, secteur d''activité et numéro de téléphone (facultatif). <em>Finalité :</em> répondre à votre demande. <em>Base légale :</em> votre consentement (case à cocher).</li>
    <li><strong>Formulaire de recrutement</strong> : prénom, nom, adresse e-mail, poste ou offre visé, site concerné, message de motivation et CV (fichier PDF ou document). <em>Finalité :</em> examiner votre candidature. <em>Base légale :</em> votre consentement et l''exécution de mesures précontractuelles prises à votre demande.</li>
    <li><strong>Assistant virtuel</strong> : les questions que vous tapez, la page consultée et votre profil de visiteur (si vous l''indiquez). Aucune adresse IP ni identifiant n''est enregistré avec elles. <em>Finalité :</em> améliorer les réponses de l''assistant et la FAQ. <em>Base légale :</em> notre intérêt légitime. Nous vous demandons de ne pas y saisir de données personnelles.</li>
</ul>
<p>Le site ne dépose aucun cookie de suivi (voir la <a href="/cookies">politique de cookies</a>).</p>'),

(3, 3, 'Destinataires des données', '<p>Vos données sont destinées aux seuls services internes concernés de LA GALETTE DE BROONS (relation client, ressources humaines, direction du site concerné). Elles ne sont ni vendues ni cédées à des tiers.</p>
<p>Elles sont traitées pour notre compte par nos prestataires techniques : l''hébergeur du site (LWS, France) et notre service de messagerie électronique, par lequel les messages des formulaires nous sont transmis.</p>
<p>La carte interactive du site fait appel à un prestataire de fonds de carte (Stadia Maps) qui reçoit votre adresse IP lors de l''affichage de la carte, comme indiqué dans la <a href="/cookies">politique de cookies</a>. Ce prestataire peut être établi en dehors de l''Union européenne.</p>'),

(3, 4, 'Durée de conservation', '<ul>
    <li><strong>Demandes de contact :</strong> 3 ans à compter du dernier échange avec vous, puis suppression.</li>
    <li><strong>Candidatures et CV :</strong> 2 ans maximum après le dernier contact, puis suppression ; vous pouvez demander leur suppression à tout moment.</li>
    <li><strong>Questions posées à l''assistant virtuel :</strong> 12 mois.</li>
</ul>'),

(3, 5, 'Vos droits', '<p>Vous disposez, sur vos données, des droits d''accès, de rectification, d''effacement, de limitation du traitement, d''opposition et de portabilité, ainsi que du droit de retirer votre consentement à tout moment et de définir des directives relatives au sort de vos données après votre décès.</p>
<p>Pour les exercer, écrivez à <a href="mailto:siteweb@jaimelagalette.com">siteweb@jaimelagalette.com</a> ou à « LA GALETTE DE BROONS, Zone Artisanale du Pilaga, 22250 BROONS ». Nous pouvons vous demander un justificatif d''identité en cas de doute.</p>
<p>Si vous estimez, après nous avoir contactés, que vos droits ne sont pas respectés, vous pouvez introduire une réclamation auprès de la CNIL : <a href="https://www.cnil.fr/fr/plaintes" target="_blank" rel="noopener">www.cnil.fr/fr/plaintes</a> (Commission nationale de l''informatique et des libertés, 3 place de Fontenoy, TSA 80715, 75334 Paris Cedex 07).</p>'),

(3, 6, 'Sécurité', '<p>Les formulaires sont protégés contre les envois frauduleux, les données sont stockées sur des serveurs situés en France, l''accès à l''espace d''administration est protégé par authentification et les fichiers de CV ne sont accessibles qu''aux personnes habilitées.</p>');
