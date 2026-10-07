<?php
// Initialise le flag de chargement unique du style du partial chatbot
static $chatbotWidgetStyleLoaded = false;
// Vérifie si le style n'a pas encore été chargé pour éviter les doublons
if (!$chatbotWidgetStyleLoaded): $chatbotWidgetStyleLoaded = true; ?>
<!-- Inclut la feuille de style CSS spécifique au partial chatbot -->
<link rel="stylesheet" href="/assets/css/partials/chatbot-widget/chatbot.css">
<?php endif; ?>

<!-- Widget chatbot intégré à chaque page -->
<div id="chatbot-widget" class="chatbot-widget" data-page="<?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? '/', ENT_QUOTES, 'UTF-8') ?>">
  <!-- Bouton d'ouverture du chatbot -->
  <button id="chatbot-toggle" class="chatbot-toggle" aria-expanded="false" aria-label="Ouvrir l'assistant virtuel">
    <!-- Icône de message -->
    <svg class="chatbot-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      <line x1="8" y1="9" x2="16" y2="9" stroke="currentColor" stroke-width="2"/>
      <line x1="8" y1="13" x2="13" y2="13" stroke="currentColor" stroke-width="2"/>
    </svg>
    <!-- Label du bouton -->
    <span class="chatbot-toggle-label">Une question ?</span>
  </button>

  <!-- Panneau du chatbot -->
  <div id="chatbot-panel" class="chatbot-panel" aria-hidden="true" role="dialog" aria-label="Assistant virtuel J'aime la Galette">
    <!-- En-tête du chatbot -->
    <div class="chatbot-header">
      <div class="chatbot-header-info">
        <!-- Icône de l'assistant -->
        <span class="chatbot-header-icon">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </span>
        <!-- Titre et sous-titre de l'assistant -->
        <div>
          <span class="chatbot-header-title">Assistant</span>
          <span class="chatbot-header-subtitle">J'aime la Galette</span>
        </div>
      </div>
      <!-- Bouton de fermeture -->
      <button id="chatbot-close" class="chatbot-close" aria-label="Fermer">&times;</button>
    </div>

    <!-- Zone des messages -->
    <div id="chatbot-messages" class="chatbot-messages" role="log" aria-live="polite">
      <!-- Message d'accueil du bot -->
      <div class="chatbot-msg chatbot-msg-bot">
        <div class="chatbot-msg-content">
          <p>Bonjour ! Je suis l'assistant virtuel de J'aime la Galette.</p>
          <p>Posez-moi vos questions sur :</p>
          <ul>
            <li>Nos produits et recettes</li>
            <li>Les points de vente et la livraison</li>
            <li>La RSE et nos engagements</li>
            <li>Le recrutement et les offres d'emploi</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Actions rapides (questions prédéfinies) -->
    <div class="chatbot-quick-actions" id="chatbot-quick-actions">
      <button class="chatbot-quick-btn" data-question="Quels additifs utilisez-vous ?">Additifs ?</button>
      <button class="chatbot-quick-btn" data-question="Où trouver vos produits ?">Points de vente</button>
      <button class="chatbot-quick-btn" data-question="Comment postuler ?">Recrutement</button>
      <button class="chatbot-quick-btn" data-question="Livrez-vous partout en France ?">Livraison</button>
    </div>

    <!-- Formulaire de saisie -->
    <form id="chatbot-form" class="chatbot-form" autocomplete="off">
      <input type="text" id="chatbot-input" class="chatbot-input"
             placeholder="Posez votre question..." required
             aria-label="Votre question">
      <!-- Bouton d'envoi -->
      <button type="submit" class="chatbot-send-btn" aria-label="Envoyer">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="22" y1="2" x2="11" y2="13"/>
          <polygon points="22 2 15 22 11 13 2 9 22 2"/>
        </svg>
      </button>
    </form>
  </div>
</div>

<!-- Script de fonctionnement du chatbot -->
<script src="/assets/js/chatbot.js"></script>

</body>
</html>
