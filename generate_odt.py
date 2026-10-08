import os
from odf.opendocument import OpenDocumentText
from odf.style import Style, ParagraphProperties, TextProperties, TableCellProperties
from odf.text import P
from odf.table import Table, TableRow, TableCell

def create_doc():
    doc = OpenDocumentText()

    # ========== STYLES ==========
    s_title = Style(name="s_title", family="paragraph")
    s_title.addElement(ParagraphProperties(margintop="0cm", marginbottom="0.5cm"))
    s_title.addElement(TextProperties(fontsize="22pt", fontweight="bold", fontfamily="Calibri", color="#EE7325"))
    doc.styles.addElement(s_title)

    s_h1 = Style(name="s_h1", family="paragraph")
    s_h1.addElement(ParagraphProperties(margintop="0.6cm", marginbottom="0.3cm"))
    s_h1.addElement(TextProperties(fontsize="16pt", fontweight="bold", fontfamily="Calibri", color="#333333"))
    doc.styles.addElement(s_h1)

    s_h2 = Style(name="s_h2", family="paragraph")
    s_h2.addElement(ParagraphProperties(margintop="0.4cm", marginbottom="0.2cm"))
    s_h2.addElement(TextProperties(fontsize="13pt", fontweight="bold", fontfamily="Calibri", color="#EE7325"))
    doc.styles.addElement(s_h2)

    s_h3 = Style(name="s_h3", family="paragraph")
    s_h3.addElement(ParagraphProperties(margintop="0.3cm", marginbottom="0.15cm"))
    s_h3.addElement(TextProperties(fontsize="11pt", fontweight="bold", fontfamily="Calibri", color="#444444"))
    doc.styles.addElement(s_h3)

    s_normal = Style(name="s_normal", family="paragraph")
    s_normal.addElement(ParagraphProperties(margintop="0.1cm", marginbottom="0.1cm"))
    s_normal.addElement(TextProperties(fontsize="10.5pt", fontfamily="Calibri", color="#222222"))
    doc.styles.addElement(s_normal)

    s_toc = Style(name="s_toc", family="paragraph")
    s_toc.addElement(ParagraphProperties(margintop="0.05cm", marginbottom="0.05cm"))
    s_toc.addElement(TextProperties(fontsize="10pt", fontfamily="Calibri", color="#333333"))
    doc.styles.addElement(s_toc)

    s_sep = Style(name="s_sep", family="paragraph")
    s_sep.addElement(ParagraphProperties(margintop="0.3cm", marginbottom="0.3cm"))
    s_sep.addElement(TextProperties(fontsize="8pt", fontfamily="Calibri", color="#CCCCCC"))
    doc.styles.addElement(s_sep)

    s_etape = Style(name="s_etape", family="paragraph")
    s_etape.addElement(ParagraphProperties(margintop="0.15cm", marginbottom="0.15cm"))
    s_etape.addElement(TextProperties(fontsize="10.5pt", fontfamily="Calibri", color="#222222"))
    doc.styles.addElement(s_etape)

    s_image = Style(name="s_image", family="paragraph")
    s_image.addElement(ParagraphProperties(margintop="0.3cm", marginbottom="0.3cm", padding="0.3cm", border="1pt solid #CCCCCC", backgroundcolor="#F9F9F9"))
    s_image.addElement(TextProperties(fontsize="9pt", fontfamily="Calibri", fontstyle="italic", color="#999999"))
    doc.styles.addElement(s_image)

    s_intro = Style(name="s_intro", family="paragraph")
    s_intro.addElement(ParagraphProperties(margintop="0.2cm", marginbottom="0.3cm"))
    s_intro.addElement(TextProperties(fontsize="11pt", fontfamily="Calibri", fontstyle="italic", color="#555555"))
    doc.styles.addElement(s_intro)

    s_note = Style(name="s_note", family="paragraph")
    s_note.addElement(ParagraphProperties(margintop="0.15cm", marginbottom="0.15cm", marginleft="0.5cm"))
    s_note.addElement(TextProperties(fontsize="9.5pt", fontfamily="Calibri", fontstyle="italic", color="#666666"))
    doc.styles.addElement(s_note)

    s_table_header = Style(name="s_table_header", family="paragraph")
    s_table_header.addElement(ParagraphProperties(margin="0.05cm"))
    s_table_header.addElement(TextProperties(fontsize="9.5pt", fontweight="bold", fontfamily="Calibri", color="#FFFFFF"))
    doc.styles.addElement(s_table_header)

    s_table_cell = Style(name="s_table_cell", family="paragraph")
    s_table_cell.addElement(ParagraphProperties(margin="0.05cm"))
    s_table_cell.addElement(TextProperties(fontsize="9pt", fontfamily="Calibri", color="#222222"))
    doc.styles.addElement(s_table_cell)

    tc_header = Style(name="tc_header", family="table-cell")
    tc_header.addElement(TableCellProperties(backgroundcolor="#EE7325", padding="0.15cm"))
    doc.styles.addElement(tc_header)

    tc_cell = Style(name="tc_cell", family="table-cell")
    tc_cell.addElement(TableCellProperties(padding="0.1cm"))
    doc.styles.addElement(tc_cell)

    def add_para(text, style=s_normal):
        p = P(stylename=style)
        p.addText(text)
        doc.text.addElement(p)

    def add_heading(text, level=1):
        if level == 1:
            add_para(text, s_h1)
        elif level == 2:
            add_para(text, s_h2)
        elif level == 3:
            add_para(text, s_h3)

    def add_title(text):
        add_para(text, s_title)

    def add_sep():
        add_para("=" * 80, s_sep)

    def add_image_placeholder(description):
        add_para("[ INS\u00c9RER IMAGE : " + description + " ]", s_image)

    def add_etape(number, text):
        add_para(str(number) + ". " + text, s_etape)

    def add_note(text):
        add_para("\u24d8 " + text, s_note)

    def add_table(headers, rows):
        tbl = Table()
        tr = TableRow()
        for h in headers:
            tc = TableCell(stylename=tc_header)
            p = P(stylename=s_table_header)
            p.addText(h)
            tc.addElement(p)
            tr.addElement(tc)
        tbl.addElement(tr)
        for row in rows:
            tr = TableRow()
            for cell_text in row:
                tc = TableCell(stylename=tc_cell)
                p = P(stylename=s_table_cell)
                p.addText(str(cell_text))
                tc.addElement(p)
                tr.addElement(tc)
            tbl.addElement(tr)
        doc.text.addElement(tbl)

    # ============================================================
    # CONTENU
    # ============================================================

    add_title("Documentation d'utilisation \u2013 J'aime la Galette")
    add_para("Guide pas \u00e0 pas pour administrer le site vitrine et le backoffice.", s_intro)

    add_heading("Sommaire", 1)
    toc = [
        "1. Pr\u00e9sentation et acc\u00e8s",
        "2. Connexion \u00e0 l'administration",
        "3. Tableau de bord (Dashboard)",
        "4. Gestion des produits",
        "    4.1 Ajouter un produit",
        "    4.2 Modifier un produit",
        "    4.3 Activer / d\u00e9sactiver un produit",
        "    4.4 Supprimer un produit",
        "5. Gestion des marques",
        "    5.1 Ajouter une marque",
        "    5.2 Modifier une marque",
        "6. Gestion des sites de production",
        "    6.1 Ajouter un site",
        "    6.2 Modifier un site",
        "7. Gestion de la FAQ",
        "    7.1 Ajouter une cat\u00e9gorie",
        "    7.2 Ajouter une question",
        "8. Gestion des candidatures",
        "9. Gestion des messages de contact",
        "10. Gestion des livreurs",
        "11. Gestion des commerciaux",
        "12. Visites myst\u00e8res",
        "13. \u00c9diteur de pages (contenu du site)",
        "14. Pages publiques du site",
    ]
    for item in toc:
        add_para(item, s_toc)

    add_sep()

    # ============================================================
    # 1. PRESENTATION
    # ============================================================
    add_heading("1. Pr\u00e9sentation et acc\u00e8s", 1)
    add_para("Le site J'aime la Galette est un site vitrine B2B pour un fabricant de cr\u00eapes et galettes bretonnes. Il se compose de deux parties :")
    add_para("  \u2022 Le site public (accessible \u00e0 tous les visiteurs)")
    add_para("  \u2022 Le backoffice (accessible uniquement aux administrateurs et commerciaux)")
    add_para("")
    add_para("URL du site : https://www.jaimelagalette.com")
    add_para("URL du backoffice : https://www.jaimelagalette.com/admin/")
    add_para("URL de l'espace commercial : https://www.jaimelagalette.com/admin/commercial/visite-mystere/")

    add_image_placeholder("Page d'accueil du site public")

    add_sep()

    # ============================================================
    # 2. CONNEXION
    # ============================================================
    add_heading("2. Connexion \u00e0 l'administration", 1)

    add_heading("Se connecter", 2)
    add_etape(1, "Rendez-vous sur l'URL : /admin/")
    add_etape(2, "Saisissez votre identifiant dans le champ \"Identifiant\"")
    add_etape(3, "Saisissez votre mot de passe dans le champ \"Mot de passe\"")
    add_etape(4, "Cliquez sur le bouton \"Se connecter\"")

    add_image_placeholder("Page de connexion (login.php) avec les champs identifiant et mot de passe")

    add_heading("Identifiants par d\u00e9faut", 2)
    add_table(
        ["R\u00f4le", "Identifiant", "Mot de passe"],
        [
            ["Administrateur", "admin", "admin123"],
            ["Commercial", "plesech", "commercial123"],
            ["Commercial", "smartin", "commercial123"],
        ]
    )

    add_note("En production, les mots de passe doivent \u00eatre modifi\u00e9s via les variables d'environnement ADMIN_USER et ADMIN_PASS_HASH.")

    add_heading("D\u00e9connexion", 2)
    add_para("En haut \u00e0 droite, cliquez sur \"D\u00e9connexion\". La session expire automatiquement apr\u00e8s 2h d'inactivit\u00e9.")

    add_heading("S\u00e9curit\u00e9", 2)
    add_para("  \u2022 Apr\u00e8s 5 tentatives de connexion \u00e9chou\u00e9es, l'acc\u00e8s est bloqu\u00e9 pendant 15 minutes.")
    add_para("  \u2022 Un token CSRF prot\u00e8ge chaque formulaire contre les attaques.")
    add_para("  \u2022 Les mots de passe sont hash\u00e9s avec bcrypt.")

    add_sep()

    # ============================================================
    # 3. DASHBOARD
    # ============================================================
    add_heading("3. Tableau de bord (Dashboard)", 1)

    add_para("Le tableau de bord est la premi\u00e8re page qui s'affiche apr\u00e8s la connexion. Il donne une vue d'ensemble de l'activit\u00e9 du site.")

    add_image_placeholder("Dashboard admin avec les widgets statistiques")

    add_heading("Que voit-on ?", 2)
    add_para("  \u2022 Nombre de candidatures non lues")
    add_para("  \u2022 Nombre de messages de contact non lus")
    add_para("  \u2022 Nombre de produits en ligne / hors ligne")
    add_para("  \u2022 Nombre d'offres d'emploi actives")
    add_para("  \u2022 Nombre d'inspections (visites myst\u00e8res) aujourd'hui et cette semaine")
    add_para("  \u2022 Les 3 derni\u00e8res candidatures re\u00e7ues")
    add_para("  \u2022 Les 3 derniers messages de contact")
    add_para("  \u2022 Les 3 derni\u00e8res visites myst\u00e8res")

    add_note("Chaque \u00e9l\u00e9ment est cliquable pour acc\u00e9der au d\u00e9tail.")

    add_sep()

    # ============================================================
    # 4. PRODUITS
    # ============================================================
    add_heading("4. Gestion des produits", 1)

    add_heading("4.1 Ajouter un produit", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Produits\"")
    add_etape(2, "Cliquez sur le bouton \"Nouveau produit\" en haut \u00e0 droite")

    add_image_placeholder("Liste des produits avec le bouton Nouveau produit")

    add_etape(3, "Remplissez les champs :")
    add_para("     - Nom * (obligatoire) : le nom du produit (ex: Galette)")
    add_para("     - Sous-titre : un texte court (ex: Rencontrer le meilleur)")
    add_para("     - Titre : un titre d\u00e9taill\u00e9 (ex: Nos cr\u00eapes & galettes)")
    add_para("     - Marque : s\u00e9lectionnez la marque associ\u00e9e dans la liste")
    add_para("     - Image : choisissez une image (JPEG, PNG, WebP, SVG max 5 Mo)")
    add_para("     - En ligne : cocher pour publier le produit imm\u00e9diatement")

    add_image_placeholder("Formulaire de cr\u00e9ation d'un nouveau produit")

    add_etape(4, "Cliquez sur \"Cr\u00e9er le produit\"")
    add_etape(5, "Le produit est cr\u00e9\u00e9. Vous \u00eates redirig\u00e9 vers la page d'\u00e9dition pour configurer la section \"\u00c0 propos\"")

    add_note("La section \"\u00c0 propos\" se configure apr\u00e8s la cr\u00e9ation du produit (voir 4.2).")

    add_heading("4.2 Modifier un produit", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Produits\"")
    add_etape(2, "Trouvez le produit dans la liste (utilisez les filtres si besoin)")
    add_etape(3, "Cliquez sur \"Modifier\" dans la colonne Actions")

    add_image_placeholder("Bouton Modifier dans la liste des produits")

    add_etape(4, "Modifiez les champs souhait\u00e9s dans la section \"Fiche produit\"")
    add_etape(5, "D\u00e9roulez la section \"\u00c0 propos\" pour modifier :")
    add_para("     - Sous-titre, Titre, Contenu")
    add_para("     - CTA label (texte du bouton) et CTA lien (URL de destination)")
    add_para("     - Image et texte alternatif (alt)")

    add_image_placeholder("Formulaire d'\u00e9dition avec section A propos d\u00e9pli\u00e9e")

    add_etape(6, "Cliquez sur \"Enregistrer\" en bas du formulaire")

    add_heading("4.3 Activer / d\u00e9sactiver un produit", 2)
    add_etape(1, "Dans la liste des produits, rep\u00e9rez la colonne \"En ligne\"")
    add_etape(2, "Cliquez sur l'interrupteur (toggle) pour basculer le statut")

    add_image_placeholder("Toggle interrupteur marche/arr\u00eat dans la colonne En ligne")

    add_note("Le changement est instantan\u00e9. Un produit hors ligne n'appara\u00eet plus sur le site public.")

    add_heading("4.4 Supprimer un produit", 2)
    add_etape(1, "Dans la liste des produits, cliquez sur \"Supprimer\" dans la colonne Actions")
    add_etape(2, "Une page de confirmation s'affiche avec les informations du produit")
    add_etape(3, "Cliquez sur \"Confirmer la suppression\" pour valider")

    add_image_placeholder("Page de confirmation de suppression avec attention (irr\u00e9versible)")

    add_note("La suppression est irr\u00e9versible et supprime \u00e9galement les donn\u00e9es associ\u00e9es (section \u00c0 propos).")

    add_sep()

    # ============================================================
    # 5. MARQUES
    # ============================================================
    add_heading("5. Gestion des marques", 1)

    add_heading("5.1 Ajouter une marque", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Marques\"")
    add_etape(2, "Cliquez sur \"Nouvelle marque\"")
    add_etape(3, "Remplissez :")
    add_para("     - Nom (ex: J'aime la Galette)")
    add_para("     - Slug (identifiant URL, g\u00e9n\u00e9r\u00e9 automatiquement depuis le nom)")
    add_para("     - Logo (image de la marque)")
    add_para("     - Description")
    add_para("     - Couleur hexad\u00e9cimale (ex: #EE7325)")
    add_para("     - Ordre d'affichage")
    add_para("     - En ligne : cocher pour activer")
    add_etape(4, "Cliquez sur \"Enregistrer\"")

    add_image_placeholder("Formulaire de cr\u00e9ation d'une marque")

    add_heading("5.2 Modifier une marque", 2)
    add_etape(1, "Dans la liste des marques, cliquez sur \"Modifier\"")
    add_etape(2, "Modifiez les champs souhait\u00e9s")
    add_etape(3, "Cliquez sur \"Enregistrer\"")

    add_note("Vous pouvez aussi activer/d\u00e9sactiver une marque depuis la liste avec le toggle interrupteur.")

    add_sep()

    # ============================================================
    # 6. SITES
    # ============================================================
    add_heading("6. Gestion des sites de production", 1)

    add_heading("6.1 Ajouter un site", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Sites\"")

    add_image_placeholder("Menu de gauche avec la rubrique Sites mise en \u00e9vidence")

    add_etape(2, "Cliquez sur \"Nouveau site\"")

    add_image_placeholder("Liste des sites avec le bouton Nouveau site")

    add_etape(3, "Remplissez les informations g\u00e9n\u00e9rales :")
    add_para("     - Type de site * : Atelier / Si\u00e8ge / D\u00e9p\u00f4t / Autre")
    add_para("     - Nom * (ex: La Galette de Broons)")
    add_para("     - Adresse *, Code postal *, Ville *")
    add_para("     - D\u00e9partement, T\u00e9l\u00e9phone, Email, Email RH")

    add_image_placeholder("Formulaire avec les champs d'adresse et le g\u00e9ocodage")

    add_etape(4, "Localisez le site sur la carte :")
    add_para("     - Cliquez sur \"G\u00e9ocoder l'adresse\" pour localiser automatiquement")
    add_para("     - OU cliquez directement sur la carte pour placer le marqueur")
    add_para("     - Vous pouvez glisser-d\u00e9poser le marqueur pour affiner")

    add_image_placeholder("Carte Leaflet avec le marqueur positionn\u00e9")

    add_etape(5, "D\u00e9finissez les horaires d'ouverture :")
    add_para("     - Pour chaque jour (lundi \u00e0 samedi), saisissez les heures d'ouverture et de fermeture")
    add_para("     - Laissez vide si le site est ferm\u00e9 ce jour-l\u00e0")

    add_image_placeholder("Tableau des horaires par jour avec champs heure d'ouverture/fermeture")

    add_etape(6, "Cliquez sur \"Cr\u00e9er le site\"")

    add_heading("6.2 Modifier un site", 2)
    add_etape(1, "Dans la liste des sites, cliquez sur \"Modifier\"")
    add_etape(2, "Modifiez les champs, les horaires ou la position sur la carte")
    add_etape(3, "Le statut \"Ouvert/Ferm\u00e9\" est mis \u00e0 jour automatiquement selon les horaires")

    add_image_placeholder("Page d'\u00e9dition avec le statut temps r\u00e9el affich\u00e9")

    add_note("Le statut en temps r\u00e9el (Ouvert/Ferm\u00e9) se calcule automatiquement depuis les horaires et l'heure actuelle.")

    add_sep()

    # ============================================================
    # 7. FAQ
    # ============================================================
    add_heading("7. Gestion de la FAQ", 1)

    add_heading("7.1 Ajouter une cat\u00e9gorie", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"FAQ\"")
    add_etape(2, "Cliquez sur \"G\u00e9rer les cat\u00e9gories\"")
    add_etape(3, "Saisissez le titre de la cat\u00e9gorie (ex: Nos Produits)")
    add_etape(4, "Cliquez sur \"Enregistrer\"")

    add_image_placeholder("Page de gestion des cat\u00e9gories FAQ")

    add_heading("7.2 Ajouter une question", 2)
    add_etape(1, "Depuis la page FAQ, cliquez sur \"G\u00e9rer les questions\"")
    add_etape(2, "S\u00e9lectionnez la cat\u00e9gorie concern\u00e9e")
    add_etape(3, "Remplissez :")
    add_para("     - Question * : le texte de la question")
    add_para("     - R\u00e9ponse * : la r\u00e9ponse d\u00e9taill\u00e9e")
    add_para("     - Mots-cl\u00e9s : synonymes pour am\u00e9liorer la recherche du chatbot")
    add_para("     - Profil cible : optionnel (grand_public / b2b / rh / presse)")
    add_para("     - Ordre : position d'affichage")
    add_para("     - En ligne : cocher pour publier")
    add_etape(4, "Cliquez sur \"Enregistrer\"")

    add_image_placeholder("Formulaire d'ajout de question avec champ mots-cl\u00e9s")

    add_note("Les mots-cl\u00e9s sont utilis\u00e9s par le chatbot pour trouver la bonne r\u00e9ponse. Plus il y a de mots-cl\u00e9s, meilleure est la pertinence.")

    add_sep()

    # ============================================================
    # 8. CANDIDATURES
    # ============================================================
    add_heading("8. Gestion des candidatures", 1)
    add_para("Les candidatures arrivent automatiquement depuis le formulaire de recrutement du site public.")

    add_heading("Consulter les candidatures", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Candidatures\"")
    add_etape(2, "Utilisez le filtre \"Tous\" ou \"Non lus\" en haut de la page")
    add_etape(3, "Cliquez sur une ligne pour voir le d\u00e9tail complet")

    add_image_placeholder("Liste des candidatures avec les colonnes Nom, Poste, Date, CV, Statut")

    add_etape(4, "Dans le d\u00e9tail, vous pouvez t\u00e9l\u00e9charger le CV")
    add_etape(5, "Pour supprimer une candidature, revenez \u00e0 la liste et utilisez le bouton \"Suppr.\"")

    add_sep()

    # ============================================================
    # 9. CONTACTS
    # ============================================================
    add_heading("9. Gestion des messages de contact", 1)
    add_para("Les messages arrivent depuis le formulaire de contact du site public (profils B2C et B2B).")

    add_heading("Consulter et traiter les messages", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Messages contact\"")
    add_etape(2, "Filtrez par statut (Nouveau / Lu / Trait\u00e9 / Archiv\u00e9) ou par profil (B2C / B2B)")
    add_etape(3, "Changez le statut directement depuis la liste avec le menu d\u00e9roulant")

    add_image_placeholder("Liste des messages avec menu de changement de statut")

    add_etape(4, "Cliquez sur une ligne pour voir le message complet")
    add_etape(5, "Marquez comme Lu, Trait\u00e9 ou Archiv\u00e9 selon l'avancement")

    add_sep()

    # ============================================================
    # 10. LIVREURS
    # ============================================================
    add_heading("10. Gestion des livreurs", 1)
    add_para("Les livreurs sont utilis\u00e9s dans le module Visites myst\u00e8res.")

    add_heading("Ajouter un livreur", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Livreurs\"")
    add_etape(2, "Cliquez sur \"+ Ajouter\"")
    add_etape(3, "Remplissez : nom, pr\u00e9nom, secteur, t\u00e9l\u00e9phone")
    add_etape(4, "Cliquez sur \"Enregistrer\"")

    add_heading("Modifier ou supprimer", 2)
    add_etape(1, "Dans la liste, utilisez \"\u00c9diter\" pour modifier, \"Suppr.\" pour supprimer")

    add_sep()

    # ============================================================
    # 11. COMMERCIAUX
    # ============================================================
    add_heading("11. Gestion des commerciaux", 1)
    add_para("Les commerciaux ont acc\u00e8s \u00e0 l'espace Visites myst\u00e8res.")

    add_heading("Ajouter un commercial", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Commerciaux\"")
    add_etape(2, "Cliquez sur \"+ Ajouter\"")
    add_etape(3, "Remplissez : nom, pr\u00e9nom, email, login, mot de passe, t\u00e9l\u00e9phone")
    add_etape(4, "Cliquez sur \"Enregistrer\"")

    add_image_placeholder("Formulaire d'ajout d'un commercial")

    add_note("Le mot de passe est automatiquement hash\u00e9 avec bcrypt avant d'\u00eatre stock\u00e9.")

    add_sep()

    # ============================================================
    # 12. VISITES MYSTERES
    # ============================================================
    add_heading("12. Visites myst\u00e8res", 1)
    add_para("Deux acc\u00e8s :")
    add_para("  \u2022 Administration (/admin/visites-mysteres/) : consultation et export")
    add_para("  \u2022 Commercial (/admin/commercial/visite-mystere/) : saisie des inspections")

    add_heading("R\u00e9aliser une visite myst\u00e8re (espace commercial)", 2)
    add_etape(1, "Connectez-vous avec un compte commercial")
    add_etape(2, "Vous \u00eates redirig\u00e9 vers le formulaire d'inspection")

    add_image_placeholder("Formulaire de visite myst\u00e8re avec s\u00e9lection du livreur")

    add_etape(3, "S\u00e9lectionnez le livreur concern\u00e9 dans la liste")
    add_etape(4, "R\u00e9pondez aux questions par cat\u00e9gorie :")
    add_para("     - Propret\u00e9 & Hygi\u00e8ne (4 questions)")
    add_para("     - Merchandising & Mise en place (4 questions)")
    add_para("     - Professionnalisme (2 questions)")
    add_para("     Notez chaque crit\u00e8re de 1 \u00e0 5")

    add_image_placeholder("Questions d'inspection avec notation 1-5")

    add_etape(5, "Ajoutez un commentaire libre (optionnel)")
    add_etape(6, "Ajoutez des photos (optionnel, max 10)")
    add_etape(7, "Cliquez sur \"Valider l'inspection\"")
    add_etape(8, "La note g\u00e9n\u00e9rale /20 est calcul\u00e9e automatiquement")

    add_image_placeholder("Page de confirmation avec la note g\u00e9n\u00e9rale")

    add_heading("Consulter les inspections (administration)", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Visites myst\u00e8res\"")
    add_etape(2, "Consultez la liste des inspections r\u00e9alis\u00e9es")
    add_etape(3, "Cliquez sur une inspection pour voir les r\u00e9ponses, notes et photos")
    add_etape(4, "Utilisez \"Export\" pour t\u00e9l\u00e9charger les donn\u00e9es")

    add_sep()

    # ============================================================
    # 13. EDITEUR DE PAGES
    # ============================================================
    add_heading("13. \u00c9diteur de pages (contenu du site)", 1)
    add_para("L'\u00e9diteur permet de modifier le contenu textuel et les images de chaque section du site sans toucher au code.")

    add_heading("Modifier une section", 2)
    add_etape(1, "Dans le menu de gauche, cliquez sur \"Pages\"")
    add_etape(2, "Choisissez la section \u00e0 modifier dans la liste")

    add_image_placeholder("Liste des sections \u00e9ditables (Hero, Intro, A propos, etc.)")

    add_etape(3, "Modifiez les champs (texte, images, CTA)")
    add_etape(4, "Un aper\u00e7u est affich\u00e9 \u00e0 c\u00f4t\u00e9 du formulaire")
    add_etape(5, "Cliquez sur \"Enregistrer\"")

    add_image_placeholder("\u00c9diteur split avec formulaire \u00e0 gauche et aper\u00e7u \u00e0 droite")

    add_heading("Sections modifiables", 2)
    add_para("Hero, Intro, A propos, Carte, Chiffres du groupe, Contact, Engagements, FAQ, Footer, Histoire, Label, Livraison, Offres d'emploi, Partenaires, Processus recrutement, Recrutement, RSE, Savoir-faire, Transparence, Valeurs, Animation magasin")

    add_sep()

    # ============================================================
    # 14. PAGES PUBLIQUES
    # ============================================================
    add_heading("14. Pages publiques du site", 1)
    add_para("Voici l'ensemble des pages accessibles aux visiteurs du site :")

    add_table(
        ["URL", "Page", "Contenu"],
        [
            ["/", "Accueil", "Hero, A propos, Chiffres, Valeurs, CTA, Carte interactive, Partenaires, Engagements, Contact"],
            ["/le-groupe", "Le Groupe", "Histoire, Carte, Valeurs, Engagements, RSE, Animation, Livraison"],
            ["/nos-produits", "Nos produits", "Gammes (JLG + Be Good'n), Transparence, Labels"],
            ["/nos-produits/jaime-la-galette", "Gamme JLG", "Page de marque"],
            ["/nos-produits/be-goodn", "Gamme Be Good'n", "Page de marque"],
            ["/savoir-faire", "Savoir-faire", "Process de fabrication en 5 \u00e9tapes"],
            ["/rse", "RSE", "Actions RSE, Circuit court, Partenaires"],
            ["/faq", "FAQ", "Recherche, Cat\u00e9gories + accord\u00e9on, Chatbot"],
            ["/recrutement", "Recrutement", "Offres, Processus, Formulaire candidature"],
            ["/contact", "Contact", "Formulaire B2C/B2B, Carte des sites"],
            ["/atelier/{id}/{slug}", "Fiche atelier", "Infos, Horaires, Carte, Offres"],
            ["/produit/{id}/{slug}", "Fiche produit", "A propos, Ingr\u00e9dients, Labels"],
            ["/mentions-legales", "Mentions l\u00e9gales", "Page statique"],
            ["/politique-confidentialite", "Confidentialit\u00e9", "Page statique"],
            ["/cookies", "Cookies", "Page statique"],
            ["/sitemap.xml", "Sitemap", "G\u00e9n\u00e9r\u00e9 automatiquement"],
        ]
    )

    add_sep()

    # ============================================================
    # NOTES TECHNIQUES
    # ============================================================
    add_heading("Annexe - Informations techniques", 1)
    add_para("  \u2022 Technologie : PHP 8.x proc\u00e9dural, MySQL, PDO")
    add_para("  \u2022 Frontend : HTML/CSS/JS vanilla, SCSS")
    add_para("  \u2022 Aucun framework PHP")
    add_para("  \u2022 Envoi d'emails : PHPMailer (SMTP)")
    add_para("  \u2022 Carte : Leaflet + API Nominatim")
    add_para("  \u2022 Environnement de dev : Docker (PHP/Apache, MySQL, phpMyAdmin)")
    add_para("  \u2022 Base de donn\u00e9es : sch\u00e9ma dans docker/sql/01_schema.sql")
    add_para("  \u2022 Donn\u00e9es de d\u00e9mo : docker/sql/02_seeds.sql")
    add_para("  \u2022 Variables d'environnement : DB_HOST, DB_NAME, DB_USER, DB_PASS, MAIL_USERNAME, MAIL_PASSWORD, ADMIN_USER, ADMIN_PASS_HASH")

    doc.save(os.path.join(os.path.dirname(os.path.abspath(__file__)), "DOCUMENTATION-UTILISATION.odt"))
    print("Fichier ODT cr\u00e9\u00e9 avec succ\u00e8s !")

create_doc()
