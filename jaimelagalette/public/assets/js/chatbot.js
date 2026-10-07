(function () {

  var widget = document.getElementById('chatbot-widget');
  if (!widget) return;

  var toggleBtn = document.getElementById('chatbot-toggle');
  var panel = document.getElementById('chatbot-panel');
  var closeBtn = document.getElementById('chatbot-close');
  var messages = document.getElementById('chatbot-messages');
  var form = document.getElementById('chatbot-form');
  var input = document.getElementById('chatbot-input');
  var quickActions = document.getElementById('chatbot-quick-actions');

  var isOpen = false;
  var currentPage = widget.getAttribute('data-page') || '/';
  var profil = null;

  // Open / close
  function openChat() {
    isOpen = true;
    toggleBtn.setAttribute('aria-expanded', 'true');
    panel.setAttribute('aria-hidden', 'false');
    input.focus();
    scrollToBottom();
  }

  function closeChat() {
    isOpen = false;
    toggleBtn.setAttribute('aria-expanded', 'false');
    panel.setAttribute('aria-hidden', 'true');
  }

  toggleBtn.addEventListener('click', function () {
    isOpen ? closeChat() : openChat();
  });

  closeBtn.addEventListener('click', closeChat);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen) closeChat();
  });

  // Scroll to bottom
  function scrollToBottom() {
    if (messages) {
      messages.scrollTop = messages.scrollHeight;
    }
  }

  // Add a message to the chat
  function addMessage(text, type) {
    var div = document.createElement('div');
    div.className = 'chatbot-msg chatbot-msg-' + type;

    var content = document.createElement('div');
    content.className = 'chatbot-msg-content';

    // Support newlines in text
    var paragraphs = text.split('\n');
    paragraphs.forEach(function (p, i) {
      if (i > 0) content.appendChild(document.createElement('br'));
      var paraEl = document.createElement('p');
      paraEl.textContent = p;
      content.appendChild(paraEl);
    });

    div.appendChild(content);
    messages.appendChild(div);
    scrollToBottom();
  }

  // Add a message with HTML links (for bot responses)
  function addBotResponse(text, links) {
    var div = document.createElement('div');
    div.className = 'chatbot-msg chatbot-msg-bot';

    var content = document.createElement('div');
    content.className = 'chatbot-msg-content';

    var paragraphs = text.split('\n');
    paragraphs.forEach(function (p, i) {
      if (i > 0) content.appendChild(document.createElement('br'));
      var paraEl = document.createElement('p');
      paraEl.textContent = p;
      content.appendChild(paraEl);
    });

    // Add CTA links if present
    if (links && links.length > 0) {
      links.forEach(function (link) {
        var linkEl = document.createElement('p');
        var anchor = document.createElement('a');
        anchor.href = link.url;
        anchor.textContent = link.label;
        anchor.target = '_blank';
        linkEl.appendChild(anchor);
        content.appendChild(linkEl);
      });
    }

    div.appendChild(content);
    messages.appendChild(div);
    scrollToBottom();
  }

  // Show typing indicator
  function showTyping() {
    var div = document.createElement('div');
    div.className = 'chatbot-msg chatbot-msg-bot chatbot-msg-typing';
    div.id = 'chatbot-typing';

    var content = document.createElement('div');
    content.className = 'chatbot-msg-content';

    for (var i = 0; i < 3; i++) {
      var dot = document.createElement('span');
      dot.className = 'typing-dot';
      content.appendChild(dot);
    }

    div.appendChild(content);
    messages.appendChild(div);
    scrollToBottom();
  }

  function hideTyping() {
    var typing = document.getElementById('chatbot-typing');
    if (typing) typing.remove();
  }

  // Call the API
  function sendToBot(message) {
    showTyping();

    fetch('/chatbot.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        message: message,
        profil: profil,
        page: currentPage
      })
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
      hideTyping();

      if (data.found) {
        var links = [
          { label: 'Voir dans la FAQ \u2192', url: data.faq_url }
        ];
        addBotResponse(data.answer, links);
      } else {
        var links = [
          { label: 'Consulter la FAQ \u2192', url: '/faq' },
          { label: 'Nous contacter \u2192', url: '/contact' }
        ];
        addBotResponse(data.answer, links);
      }
    })
    .catch(function () {
      hideTyping();
      addMessage('D\u00e9sol\u00e9, une erreur technique est survenue. R\u00e9essayez plus tard.', 'bot');
    });
  }

  // Form submission
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var message = input.value.trim();
    if (!message) return;

    addMessage(message, 'user');
    input.value = '';
    sendToBot(message);
  });

  // Quick action buttons
  if (quickActions) {
    quickActions.addEventListener('click', function (e) {
      var btn = e.target.closest('.chatbot-quick-btn');
      if (!btn) return;

      var question = btn.getAttribute('data-question');
      if (!question) return;

      if (!isOpen) openChat();

      addMessage(question, 'user');
      sendToBot(question);
    });
  }

})();
