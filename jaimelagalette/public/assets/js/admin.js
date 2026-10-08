(function() {
  'use strict';

  function toSlug(str) {
    return str.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-|-$/g, '');
  }

  document.addEventListener('click', function(e) {
    var header = e.target.closest('.admin-section-header');
    if (!header) return;
    var section = header.closest('.admin-section');
    if (!section) return;
    var body = section.querySelector('.admin-section-body');
    if (!body) return;
    var expanded = header.getAttribute('aria-expanded') === 'true';
    header.setAttribute('aria-expanded', String(!expanded));
    body.classList.toggle('admin-section-body--open');

    if (!expanded) {
      var anchor = header.getAttribute('data-anchor');
      var base = header.closest('.admin-split').querySelector('#preview-url');
      var iframe = document.getElementById('preview-frame');
      if (base && iframe) {
        var url = base.value.split('#')[0];
        if (anchor) {
          url += '#' + anchor;
        }
        base.value = url;
        iframe.src = url;
      }
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.matches('input[type="file"]')) {
      var file = e.target.files[0];
      if (!file) return;
      var reader = new FileReader();
      reader.onload = function(evt) {
        var container = e.target.closest('.admin-image-field') || e.target.closest('.admin-field');
        if (!container) return;
        var preview = container.querySelector('.admin-image-preview');
        if (!preview) {
          preview = document.createElement('img');
          preview.className = 'admin-image-preview';
          container.appendChild(preview);
        }
        preview.src = evt.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
    }
  });

  var slugSource = document.getElementById('slug-source');
  var slugTarget = document.getElementById('slug-target');
  if (slugSource && slugTarget) {
    slugSource.addEventListener('input', function() {
      slugTarget.value = toSlug(this.value);
    });
  }

  var toggleForms = document.querySelectorAll('.admin-toggle-form');
  toggleForms.forEach(function(form) {
    var checkbox = form.querySelector('input[type="checkbox"]');
    if (checkbox) {
      checkbox.addEventListener('change', function() {
        form.submit();
      });
    }
  });

  var deleteLinks = document.querySelectorAll('[data-confirm]');
  deleteLinks.forEach(function(link) {
    link.addEventListener('click', function(e) {
      if (!confirm(this.getAttribute('data-confirm') || 'Confirmer la suppression ?')) {
        e.preventDefault();
      }
    });
  });

  var deleteForms = document.querySelectorAll('[data-confirm-form]');
  deleteForms.forEach(function(form) {
    form.addEventListener('submit', function(e) {
      if (!confirm(this.getAttribute('data-confirm-form') || 'Confirmer la suppression ?')) {
        e.preventDefault();
      }
    });
  });
})();
