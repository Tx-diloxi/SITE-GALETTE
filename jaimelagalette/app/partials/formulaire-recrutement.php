<?php
// Initialise le flag de chargement unique du style du partial formulaire-recrutement
static $formulaireRecrutementStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$formulaireRecrutementStyleLoaded): $formulaireRecrutementStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS specifique au partial formulaire-recrutement -->
<link rel="stylesheet" href="/assets/css/partials/formulaire/formulaire-base.css">
<?php
endif;

// Récupère le tableau des erreurs de validation du formulaire
$formErreurs = $formResult['errors'] ?? [];
// Recupere le statut de soumission reussi ou non
$formSucces  = $formResult['success'] ?? false;
?>

<!-- Section principale du formulaire de recrutement -->
<section id="formulaire-recrutement">
    <div class="formulaire-wrapper">

        <!-- En-tête du formulaire -->
        <div class="formulaire-header">
            <!-- Titre principal du formulaire -->
            <h2 class="formulaire-titre-principal">
                <?= htmlspecialchars($titreFormulaireRecrutement['titre'] ?? 'CANDIDATURE', ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <!-- Sous-titre du formulaire -->
            <p class="formulaire-sous-titre">
                <?= htmlspecialchars($titreFormulaireRecrutement['sous_titre'] ?? 'Laissez-nous vos coordonnées et votre CV', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <!-- Panneaux du formulaire -->
        <div class="formulaire-panneaux">
            <?php // Affiche le message de succès si le formulaire a été envoyé
            if ($formSucces) : ?>
            <!-- Message de confirmation d'envoi -->
            <div class="formulaire-succes" role="alert">
                <p><?= htmlspecialchars($messageSuccesRecrutement ?? 'Votre candidature a bien été envoyée ! Nous vous recontacterons dans les plus brefs délais.', ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
            <?php else : ?>

            <?php // Affiche les erreurs globales si présentes
            if (!empty($formErreurs['global'])) : ?>
            <!-- Erreur globale du formulaire -->
            <div class="formulaire-erreur-global" role="alert">
                <p><?= htmlspecialchars($formErreurs['global'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($formErreurs['mail'])) : ?>
            <!-- Erreur d'envoi par email -->
            <div class="formulaire-erreur-global" role="alert">
                <p><?= htmlspecialchars($formErreurs['mail'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php endif; ?>

            <!-- Formulaire de candidature -->
            <form method="post" action="#formulaire-recrutement" enctype="multipart/form-data" novalidate
                class="formulaire-form">
                <!-- Champs cachés : type de formulaire et token CSRF -->
                <input type="hidden" name="form_recrutement" value="1">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <!-- Grille des champs d'identité -->
                <div class="formulaire-grille-2">
                    <!-- Champ Prénom -->
                    <div class="formulaire-champ <?= !empty($formErreurs['prenom']) ? 'erreur' : '' ?>">
                        <label for="r-prenom">Prénom <span class="obligatoire" aria-hidden="true">*</span></label>
                        <input type="text" id="r-prenom" name="prenom"
                            value="<?= htmlspecialchars($_POST['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Jean" required autocomplete="given-name">
                        <?php if (!empty($formErreurs['prenom'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['prenom'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Nom -->
                    <div class="formulaire-champ <?= !empty($formErreurs['nom']) ? 'erreur' : '' ?>">
                        <label for="r-nom">Nom <span class="obligatoire" aria-hidden="true">*</span></label>
                        <input type="text" id="r-nom" name="nom"
                            value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Dupont" required autocomplete="family-name">
                        <?php if (!empty($formErreurs['nom'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Adresse e-mail -->
                    <div class="formulaire-champ <?= !empty($formErreurs['email']) ? 'erreur' : '' ?>">
                        <label for="r-email">Adresse e-mail <span class="obligatoire"
                                aria-hidden="true">*</span></label>
                        <input type="email" id="r-email" name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="jean@exemple.com" required autocomplete="email">
                        <?php if (!empty($formErreurs['email'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['email'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ choix offre ou candidature spontanée -->
                    <div class="formulaire-champ <?= !empty($formErreurs['type_candidature']) ? 'erreur' : '' ?>">
                        <label for="r-type-candidature">Offre à pourvoir <span class="obligatoire"
                                aria-hidden="true">*</span></label>
                        <select id="r-type-candidature" name="type_candidature" required>
                            <option value="">Sélectionnez une offre</option>
                            <?php if (!empty($offresEmploi)): ?>
                            <?php foreach ($offresEmploi as $offre): ?>
                            <option value="offre_<?= (int)$offre['id'] ?>"
                                <?= ($_POST['type_candidature'] ?? '') === 'offre_' . $offre['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($offre['titre'], ENT_QUOTES, 'UTF-8') ?>
                                — <?= htmlspecialchars($offre['contrat'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                — <?= htmlspecialchars($offre['lieu'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            <option value="spontanee"
                                <?= ($_POST['type_candidature'] ?? '') === 'spontanee' ? 'selected' : '' ?>>
                                Candidature spontanée
                            </option>
                        </select>
                        <?php if (!empty($formErreurs['type_candidature'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['type_candidature'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Poste souhaité (visible uniquement pour candidature spontanée) -->
                    <div class="formulaire-champ <?= !empty($formErreurs['poste']) ? 'erreur' : '' ?>"
                        id="r-poste-spontanee-wrapper"
                        style="<?= ($_POST['type_candidature'] ?? '') === 'spontanee' ? '' : 'display:none;' ?>">
                        <label for="r-poste">Poste souhaité <span class="obligatoire"
                                aria-hidden="true">*</span></label>
                        <input type="text" id="r-poste" name="poste"
                            value="<?= htmlspecialchars($_POST['poste'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Ex: Assistant Marketing, Pâtissier..."
                            <?= ($_POST['type_candidature'] ?? '') === 'spontanee' ? 'required' : '' ?>>
                        <?php if (!empty($formErreurs['poste'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['poste'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Site concerné (visible uniquement pour candidature spontanée) -->
                    <div class="formulaire-champ <?= !empty($formErreurs['site_id']) ? 'erreur' : '' ?>"
                        id="r-site-wrapper"
                        style="<?= ($_POST['type_candidature'] ?? '') === 'spontanee' ? '' : 'display:none;' ?>">
                        <label for="r-site">Site concerné <span class="obligatoire" aria-hidden="true">*</span></label>
                        <select id="r-site" name="site_id" required
                            <?= ($_POST['type_candidature'] ?? '') === 'spontanee' ? '' : 'disabled' ?>>
                            <option value="">Sélectionnez un site</option>
                            <?php if (!empty($sites)): ?>
                            <?php foreach ($sites as $s): ?>
                            <option value="<?= (int)$s['id'] ?>"
                                <?= ($_POST['site_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['nom'], ENT_QUOTES, 'UTF-8') ?>
                                — <?= htmlspecialchars($s['ville'], ENT_QUOTES, 'UTF-8') ?>
                                (<?= htmlspecialchars($s['email_rh'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (!empty($formErreurs['site_id'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['site_id'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Champ Message / Lettre de motivation -->
                <div class="formulaire-champ <?= !empty($formErreurs['message']) ? 'erreur' : '' ?>">
                    <label for="r-message">Votre message / Lettre de motivation <span class="obligatoire"
                            aria-hidden="true">*</span></label>
                    <textarea id="r-message" name="message" rows="5" required
                        placeholder="Dites-nous pourquoi vous souhaitez nous rejoindre..."><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (!empty($formErreurs['message'])) : ?>
                    <span class="formulaire-erreur"
                        role="alert"><?= htmlspecialchars($formErreurs['message'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Champ Pièce jointe (CV) -->
                <div class="formulaire-champ formulaire-fichier <?= !empty($formErreurs['cv']) ? 'erreur' : '' ?>">
                    <label for="r-cv">Pièce jointe (CV) <span class="optionnel">(optionnel)</span></label>
                    <div class="fichier-wrapper">
                        <input type="file" id="r-cv" name="cv" accept=".pdf,.doc,.docx" style="display: none;">
                        <button type="button" class="fichier-btn">Choisir un fichier</button>
                        <span class="fichier-nom">Aucun fichier choisi</span>
                    </div>
                    <!-- Informations sur les formats acceptés -->
                    <p class="formats-info">Formats acceptés : PDF, DOC, DOCX. Max 5Mo.</p>
                    <?php if (!empty($formErreurs['cv'])) : ?>
                    <span class="formulaire-erreur"
                        role="alert"><?= htmlspecialchars($formErreurs['cv'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Consentement RGPD -->
                <div class="formulaire-champ formulaire-rgpd <?= !empty($formErreurs['rgpd']) ? 'erreur' : '' ?>">
                    <label class="formulaire-rgpd-label">
                        <input type="checkbox" name="rgpd" value="1" required
                            <?= !empty($_POST['rgpd']) ? 'checked' : '' ?>>
                        <span>
                            J'accepte que mes données (dont mon CV) soient traitées dans le cadre de ma candidature
                            et conservées 2 ans maximum. Droits et contact : consultez notre
                            <a href="/politique-confidentialite">politique de confidentialité</a>.
                        </span>
                    </label>
                    <?php if (!empty($formErreurs['rgpd'])) : ?>
                    <span class="formulaire-erreur"
                        role="alert"><?= htmlspecialchars($formErreurs['rgpd'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <!-- Bouton d'envoi -->
                <div class="formulaire-submit">
                    <button type="submit" class="formulaire-btn">
                        ENVOYER MA CANDIDATURE
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" aria-hidden="true">
                            <line x1="22" y1="2" x2="11" y2="13" />
                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                        </svg>
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
// Gestion du bouton de sélection de fichier personnalisé
(function() {
    var fileInput = document.getElementById('r-cv');
    var fileNameSpan = document.querySelector('.fichier-nom');
    var fileBtn = document.querySelector('.fichier-btn');

    // Vérifie que tous les éléments existent
    if (fileInput && fileNameSpan && fileBtn) {
        // Ouvre le sélecteur de fichier au clic sur le bouton personnalisé
        fileBtn.addEventListener('click', function() {
            fileInput.click();
        });
        // Met à jour le nom du fichier sélectionné
        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileNameSpan.textContent = fileInput.files[0].name;
                fileNameSpan.style.color = "#F0F0F0";
            } else {
                fileNameSpan.textContent = 'Aucun fichier choisi';
                fileNameSpan.style.color = "#888888";
            }
        });
    }
})();

// Gestion du changement de type de candidature
(function() {
    var select = document.getElementById('r-type-candidature');
    var posteWrapper = document.getElementById('r-poste-spontanee-wrapper');
    var posteInput = document.getElementById('r-poste');
    var siteWrapper = document.getElementById('r-site-wrapper');
    var siteSelect = document.getElementById('r-site');

    if (select && posteWrapper && posteInput && siteWrapper && siteSelect) {
        function toggleSpontaneeFields() {
            if (select.value === 'spontanee') {
                posteWrapper.style.display = '';
                posteInput.setAttribute('required', '');
                siteWrapper.style.display = '';
                siteSelect.removeAttribute('disabled');
                siteSelect.setAttribute('required', '');
            } else {
                posteWrapper.style.display = 'none';
                posteInput.removeAttribute('required');
                siteWrapper.style.display = 'none';
                siteSelect.setAttribute('disabled', '');
                siteSelect.removeAttribute('required');
            }
        }
        select.addEventListener('change', toggleSpontaneeFields);
    }
})();
</script>