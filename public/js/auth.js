(function () {
  function qs(sel, root) {
    return (root || document).querySelector(sel);
  }

  function qsa(sel, root) {
    return Array.from((root || document).querySelectorAll(sel));
  }

  function clearErrors(form) {
    qsa('[data-error-for]', form).forEach(function (node) {
      node.textContent = '';
    });

    qsa('[data-field]', form).forEach(function (field) {
      field.classList.remove('input-error');
    });
  }

  function showFieldError(form, field, message) {
    var input = qs('[data-field="' + field + '"]', form);
    var errorNode = qs('[data-error-for="' + field + '"]', form);

    if (input) {
      input.classList.add('input-error');
    }

    if (errorNode) {
      errorNode.textContent = message || '';
    }
  }

  function bindPasswordToggle() {
    qsa('[data-toggle-password]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var wrap = btn.closest('.password-wrap');
        if (!wrap) {
          return;
        }

        var input = qs('input', wrap);
        if (!input) {
          return;
        }

        var isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.textContent = isHidden ? 'Masquer' : 'Voir';
      });
    });
  }

  function bindSingleCheckbox() {
    qsa('[data-single-check-group]').forEach(function (group) {
      qsa('[data-single-check]', group).forEach(function (box) {
        box.addEventListener('change', function () {
          if (!box.checked) {
            return;
          }

          qsa('[data-single-check]', group).forEach(function (other) {
            if (other !== box) {
              other.checked = false;
            }
          });
        });
      });
    });
  }

  function bindAjaxForms() {
    qsa('[data-ajax-form]').forEach(function (form) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearErrors(form);

        var submit = qs('[data-submit-btn]', form);
        if (submit) {
          submit.disabled = true;
        }

        var payload = new FormData(form);

        fetch(form.action, {
          method: form.method || 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          },
          body: payload
        })
          .then(function (res) {
            return res.json().then(function (json) {
              return { status: res.status, ok: res.ok, data: json };
            });
          })
          .then(function (result) {
            if (result.ok && result.data && result.data.redirect) {
              window.location.href = result.data.redirect;
              return;
            }

            var errors = (result.data && result.data.errors) || {};
            Object.keys(errors).forEach(function (key) {
              showFieldError(form, key, errors[key]);
            });
          })
          .catch(function () {
            console.error('Impossible de contacter le serveur.');
          })
          .finally(function () {
            if (submit) {
              submit.disabled = false;
            }
          });
      });

      qsa('[data-field]', form).forEach(function (input) {
        input.addEventListener('input', function () {
          input.classList.remove('input-error');
          var name = input.getAttribute('data-field');
          var errorNode = qs('[data-error-for="' + name + '"]', form);
          if (errorNode) {
            errorNode.textContent = '';
          }
        });
      });
    });
  }

  bindPasswordToggle();
  bindSingleCheckbox();
  bindAjaxForms();
})();
