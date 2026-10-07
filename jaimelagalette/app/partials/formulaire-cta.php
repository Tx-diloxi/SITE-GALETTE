<?php
// Initialise le flag de chargement unique du style du partial formulaire
static $formulaireContactStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$formulaireContactStyleLoaded): $formulaireContactStyleLoaded = true;
?>
<!-- Inclut la feuille de style CSS spécifique au partial formulaire -->
<link rel="stylesheet" href="/assets/css/partials/formulaire/formulaire-base.css">
<?php
endif;

// Récupère le tableau des erreurs de validation du formulaire
$formErreurs = $formResult['errors'] ?? [];
// Récupère le statut de soumission réussi ou non
$formSucces  = $formResult['success'] ?? false;
// Initialise le profil par défaut du formulaire (grand public)
$formProfil  = 'b2c';

// Vérifie si le formulaire a été soumis en méthode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Détermine le profil sélectionné (B2C ou B2B) après soumission avec validation stricte
    $formProfil = in_array($_POST['form_profil'] ?? '', ['b2c', 'b2b'], true)
        ? $_POST['form_profil']
        : 'b2c';
}
?>

<!-- Section principale du formulaire de contact -->
<section id="formulaire-contact">

    <div class="formulaire-wrapper">

        <?php // Affiche le message de succès si le formulaire a été envoyé
        if ($formSucces) : ?>
        <!-- Message de confirmation d'envoi -->
        <div class="formulaire-succes" role="alert">
            <p><?= htmlspecialchars($intro['citation'] ?? 'Votre message a bien été envoyé ! Notre équipe vous répondra dans les plus brefs délais.', ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>

        <?php else : ?>

        <!-- Onglets de sélection du profil (B2C / B2B) -->
        <div class="formulaire-onglets" role="tablist">

            <!-- Onglet Consommateur / Grand public -->
            <button id="onglet-b2c" class="formulaire-onglet <?= ($formProfil === 'b2c') ? 'actif' : '' ?>" role="tab"
                aria-selected="<?= ($formProfil === 'b2c') ? 'true' : 'false' ?>" aria-controls="form-b2c" type="button"
                onclick="switchFormProfil('b2c')">
                <span class="onglet-icone" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="12" cy="7" r="4" />
                    </svg>
                </span>
                Consommateur / Grand public
            </button>

            <!-- Onglet Professionnel -->
            <button id="onglet-b2b" class="formulaire-onglet <?= ($formProfil === 'b2b') ? 'actif' : '' ?>" role="tab"
                aria-selected="<?= ($formProfil === 'b2b') ? 'true' : 'false' ?>" aria-controls="form-b2b" type="button"
                onclick="switchFormProfil('b2b')">
                <span class="onglet-icone" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                    </svg>
                </span>
                Professionnel
            </button>

        </div>

        <!-- Panneaux de formulaires -->
        <div class="formulaire-panneaux">

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

            <!-- Formulaire B2C - Grand public -->
            <div id="form-b2c" class="formulaire-panneau <?= ($formProfil === 'b2c') ? 'actif' : '' ?>" role="tabpanel"
                aria-labelledby="onglet-b2c">

                <form method="post" action="#formulaire-contact" novalidate class="formulaire-form">

                    <!-- Champs cachés : profil et token CSRF -->
                    <input type="hidden" name="form_profil" value="b2c">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                    <div class="formulaire-grille-2">

                        <!-- Champ Nom complet -->
                        <div class="formulaire-champ <?= (!empty($formErreurs['nom'])) ? 'erreur' : '' ?>">
                            <label for="c-nom">Nom complet <span class="obligatoire" aria-hidden="true">*</span></label>
                            <input type="text" id="c-nom" name="nom"
                                value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Jean Dupont" required autocomplete="name">
                            <?php if (!empty($formErreurs['nom'])) : ?>
                            <span class="formulaire-erreur"
                                role="alert"><?= htmlspecialchars($formErreurs['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Champ Adresse e-mail -->
                        <div class="formulaire-champ <?= (!empty($formErreurs['email'])) ? 'erreur' : '' ?>">
                            <label for="c-email">Adresse e-mail <span class="obligatoire"
                                    aria-hidden="true">*</span></label>
                            <input type="email" id="c-email" name="email"
                                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="jean@exemple.com" required autocomplete="email">
                            <?php if (!empty($formErreurs['email'])) : ?>
                            <span class="formulaire-erreur"
                                role="alert"><?= htmlspecialchars($formErreurs['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                    </div>

                    <!-- Champ Objet de la demande -->
                    <div class="formulaire-champ <?= (!empty($formErreurs['sujet'])) ? 'erreur' : '' ?>">
                        <label for="c-sujet">Objet de votre demande <span class="obligatoire"
                                aria-hidden="true">*</span></label>
                        <div class="formulaire-select-wrapper">
                            <select id="c-sujet" name="sujet" required>
                                <option value="" disabled <?= empty($_POST['sujet']) ? 'selected' : '' ?>>Sélectionnez
                                    un motif...</option>
                                <option value="sav" <?= (($_POST['sujet'] ?? '') === 'sav')   ? 'selected' : '' ?>>
                                    Service Après-Vente (SAV)</option>
                                <option value="info" <?= (($_POST['sujet'] ?? '') === 'info')  ? 'selected' : '' ?>>
                                    Informations produit</option>
                                <option value="devis" <?= (($_POST['sujet'] ?? '') === 'devis') ? 'selected' : '' ?>>
                                    Demande de devis</option>
                                <option value="autre" <?= (($_POST['sujet'] ?? '') === 'autre') ? 'selected' : '' ?>>
                                    Autre demande</option>
                            </select>
                            <span class="formulaire-select-icone" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </div>
                        <?php if (!empty($formErreurs['sujet'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['sujet'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Message -->
                    <div class="formulaire-champ <?= (!empty($formErreurs['message'])) ? 'erreur' : '' ?>">
                        <label for="c-message">Votre message <span class="obligatoire"
                                aria-hidden="true">*</span></label>
                        <textarea id="c-message" name="message" rows="5" required
                            placeholder="Comment pouvons-nous vous aider ?"><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (!empty($formErreurs['message'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['message'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Consentement RGPD -->
                    <div class="formulaire-champ formulaire-rgpd <?= (!empty($formErreurs['rgpd'])) ? 'erreur' : '' ?>">
                        <label class="formulaire-rgpd-label">
                            <input type="checkbox" name="rgpd" value="1" required
                                <?= !empty($_POST['rgpd']) ? 'checked' : '' ?>>
                            <span>
                                J'accepte que les données saisies soient utilisées pour me recontacter.
                                Consultez notre <a href="/politique-confidentialite">politique de confidentialité</a>.
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
                            ENVOYER MA DEMANDE
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12" />
                                <polyline points="12 5 19 12 12 19" />
                            </svg>
                        </button>
                    </div>

                </form>
            </div>

            <!-- Formulaire B2B - Professionnel -->
            <div id="form-b2b" class="formulaire-panneau <?= ($formProfil === 'b2b') ? 'actif' : '' ?>" role="tabpanel"
                aria-labelledby="onglet-b2b">

                <form method="post" action="#formulaire-contact" novalidate class="formulaire-form">

                    <!-- Champs cachés : profil et token CSRF -->
                    <input type="hidden" name="form_profil" value="b2b">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                    <div class="formulaire-grille-2">

                        <!-- Champ Société -->
                        <div class="formulaire-champ <?= (!empty($formErreurs['societe'])) ? 'erreur' : '' ?>">
                            <label for="p-societe">Société <span class="obligatoire" aria-hidden="true">*</span></label>
                            <input type="text" id="p-societe" name="societe"
                                value="<?= htmlspecialchars($_POST['societe'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Nom de votre entreprise" required autocomplete="organization">
                            <?php if (!empty($formErreurs['societe'])) : ?>
                            <span class="formulaire-erreur"
                                role="alert"><?= htmlspecialchars($formErreurs['societe'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Champ Nom & Prénom -->
                        <div class="formulaire-champ <?= (!empty($formErreurs['nom'])) ? 'erreur' : '' ?>">
                            <label for="p-nom">Nom & Prénom <span class="obligatoire"
                                    aria-hidden="true">*</span></label>
                            <input type="text" id="p-nom" name="nom"
                                value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="Votre nom complet" required autocomplete="name">
                            <?php if (!empty($formErreurs['nom'])) : ?>
                            <span class="formulaire-erreur"
                                role="alert"><?= htmlspecialchars($formErreurs['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Champ Adresse e-mail professionnelle -->
                        <div class="formulaire-champ <?= (!empty($formErreurs['email'])) ? 'erreur' : '' ?>">
                            <label for="p-email">Adresse e-mail pro <span class="obligatoire"
                                    aria-hidden="true">*</span></label>
                            <input type="email" id="p-email" name="email"
                                value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="contact@entreprise.com" required autocomplete="email">
                            <?php if (!empty($formErreurs['email'])) : ?>
                            <span class="formulaire-erreur"
                                role="alert"><?= htmlspecialchars($formErreurs['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Champ Téléphone (optionnel) -->
                        <div class="formulaire-champ">
                            <label for="p-telephone">Téléphone <span class="optionnel">(optionnel)</span></label>
                            <input type="tel" id="p-telephone" name="telephone"
                                value="<?= htmlspecialchars($_POST['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                placeholder="01 23 45 67 89" autocomplete="tel">
                        </div>

                    </div>

                    <!-- Champ Profil B2B -->
                    <div class="formulaire-champ <?= (!empty($formErreurs['profil_b2b'])) ? 'erreur' : '' ?>">
                        <label for="p-profil">Votre profil <span class="obligatoire" aria-hidden="true">*</span></label>
                        <div class="formulaire-select-wrapper">
                            <select id="p-profil" name="profil_b2b" required>
                                <option value="" disabled <?= empty($_POST['profil_b2b']) ? 'selected' : '' ?>>
                                    Sélectionnez votre secteur...</option>
                                <option value="gms"
                                    <?= (($_POST['profil_b2b'] ?? '') === 'gms')          ? 'selected' : '' ?>>
                                    Distributeur GMS</option>
                                <option value="restauration"
                                    <?= (($_POST['profil_b2b'] ?? '') === 'restauration') ? 'selected' : '' ?>>
                                    Restauration collective</option>
                                <option value="fournisseur"
                                    <?= (($_POST['profil_b2b'] ?? '') === 'fournisseur')  ? 'selected' : '' ?>>
                                    Fournisseur / Partenaire</option>
                                <option value="autre"
                                    <?= (($_POST['profil_b2b'] ?? '') === 'autre')        ? 'selected' : '' ?>>Autre
                                    professionnel</option>
                            </select>
                            <span class="formulaire-select-icone" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </span>
                        </div>
                        <?php if (!empty($formErreurs['profil_b2b'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['profil_b2b'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Objet -->
                    <div class="formulaire-champ <?= (!empty($formErreurs['sujet_b2b'])) ? 'erreur' : '' ?>">
                        <label for="p-sujet">Objet <span class="obligatoire" aria-hidden="true">*</span></label>
                        <input type="text" id="p-sujet" name="sujet_b2b"
                            value="<?= htmlspecialchars($_POST['sujet_b2b'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            placeholder="Sujet de votre message" required>
                        <?php if (!empty($formErreurs['sujet_b2b'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['sujet_b2b'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Champ Message -->
                    <div class="formulaire-champ <?= (!empty($formErreurs['message'])) ? 'erreur' : '' ?>">
                        <label for="p-message">Votre message <span class="obligatoire"
                                aria-hidden="true">*</span></label>
                        <textarea id="p-message" name="message" rows="5" required
                            placeholder="Détaillez votre projet ou votre demande..."><?= htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (!empty($formErreurs['message'])) : ?>
                        <span class="formulaire-erreur"
                            role="alert"><?= htmlspecialchars($formErreurs['message'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Consentement RGPD -->
                    <div class="formulaire-champ formulaire-rgpd <?= (!empty($formErreurs['rgpd'])) ? 'erreur' : '' ?>">
                        <label class="formulaire-rgpd-label">
                            <input type="checkbox" name="rgpd" value="1" required
                                <?= !empty($_POST['rgpd']) ? 'checked' : '' ?>>
                            <span>
                                J'accepte que les données saisies soient utilisées pour me recontacter.
                                Consultez notre <a href="/politique-confidentialite">politique de confidentialité</a>.
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
                            ENVOYER LE MESSAGE
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" aria-hidden="true">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                        </button>
                    </div>

                </form>
            </div>

        </div>
        <?php endif; ?>

    </div>
</section>

<script>
// Bascule entre les formulaires B2C et B2B
(function() {
    function switchFormProfil(profil) {
        // Récupère les éléments des onglets et des panneaux
        var ongletB2C = document.getElementById('onglet-b2c');
        var ongletB2B = document.getElementById('onglet-b2b');
        var panneauB2C = document.getElementById('form-b2c');
        var panneauB2B = document.getElementById('form-b2b');

        // Vérifie que tous les éléments existent
        if (!ongletB2C || !ongletB2B || !panneauB2C || !panneauB2B) {
            return;
        }

        // Active l'onglet et le panneau correspondant au profil choisi
        if (profil === 'b2c') {
            ongletB2C.classList.add('actif');
            ongletB2C.setAttribute('aria-selected', 'true');
            panneauB2C.classList.add('actif');
            ongletB2B.classList.remove('actif');
            ongletB2B.setAttribute('aria-selected', 'false');
            panneauB2B.classList.remove('actif');
        } else {
            ongletB2B.classList.add('actif');
            ongletB2B.setAttribute('aria-selected', 'true');
            panneauB2B.classList.add('actif');
            ongletB2C.classList.remove('actif');
            ongletB2C.setAttribute('aria-selected', 'false');
            panneauB2C.classList.remove('actif');
        }
    }

    // Rend la fonction accessible globalement pour les attributs onclick
    window.switchFormProfil = switchFormProfil;
})();
</script>

<!-- Image décorative de vague en bas de section -->
<img src="/assets/images/vague4.svg" class="vague-formulaire" alt="Image d'une vague stylisée">
