/**
 * LGPD Cookie Consent — Admin Script v1.1
 */
(function ($) {
  'use strict';

  // SVGs dos ícones de avatar (mesmo que o frontend)
  const avatarSVGs = {
    smile:  `<svg viewBox="0 0 40 40" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round"><circle cx="14.5" cy="17" r="2.2" fill="white" stroke="none"/><circle cx="25.5" cy="17" r="2.2" fill="white" stroke="none"/><path d="M13 24 Q20 30.5 27 24" stroke="white" stroke-width="2.5" fill="none"/></svg>`,
    shield: `<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4" stroke-width="2.2"/></svg>`,
    cookie: `<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01M16 15.5v.01M12 12v.01" stroke-width="2.8"/></svg>`,
    lock:   `<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>`,
  };

  const Admin = {

    init() {
      this.initColorPickers();
      this.initModeCards();
      this.initIconPicker();
      this.initPresets();
      this.initCustomScripts();
      this.initAvatarFieldsVisibility();
      this.updatePreview();
    },

    // ── Color pickers ──────────────────────────────────────────────────────────
    initColorPickers() {
      $('.lgpd-color-picker').wpColorPicker({
        change: _.debounce(() => Admin.updatePreview(), 80),
        clear:  ()         => Admin.updatePreview(),
      });
    },

    // ── Modo de exibição (avatar / barra / modal) ──────────────────────────────
    initModeCards() {
      $('input[name="lgpd_settings[position]"]').on('change', function () {
        $('.lgpd-mode-card').removeClass('active');
        $(this).closest('.lgpd-mode-card').addClass('active');
        Admin.toggleAvatarFields();
        Admin.updatePreview();
      });
    },

    toggleAvatarFields() {
      const isAvatar = $('input[name="lgpd_settings[position]"]:checked').val() === 'avatar';
      $('#lgpd-avatar-fields, #lgpd-avatar-options').toggle(isAvatar);
    },

    // ── Icon picker ────────────────────────────────────────────────────────────
    initIconPicker() {
      $('.js-icon-pick').on('change', function () {
        $('.lgpd-icon-option').removeClass('active');
        $(this).closest('.lgpd-icon-option').addClass('active');
        Admin.updatePreview();
      });
    },

    // ── Presets de paleta ──────────────────────────────────────────────────────
    initPresets() {
      $(document).on('click', '.js-apply-preset', function () {
        const $btn = $(this);
        const map  = {
          '#lgpd_primary_color':   $btn.data('primary'),
          '#lgpd_secondary_color': $btn.data('secondary'),
          '#lgpd_text_color':      $btn.data('text'),
          '#lgpd_bg_color':        $btn.data('bg'),
        };
        $.each(map, function (selector, color) {
          const $input = $(selector);
          if ($input.length) {
            $input.val(color).trigger('change');
            // Atualiza o wpColorPicker swatch
            try {
              $input.wpColorPicker('color', color);
            } catch(e) {}
          }
        });
        Admin.updatePreview();

        // Feedback visual no botão
        $btn.addClass('lgpd-preset-btn--applied');
        setTimeout(() => $btn.removeClass('lgpd-preset-btn--applied'), 1200);
      });
    },

    // ── Live preview ───────────────────────────────────────────────────────────
    updatePreview() {
      const pc  = $('#lgpd_primary_color').val()   || '#2563eb';
      const sc  = $('#lgpd_secondary_color').val() || '#7c3aed';
      const tc  = $('#lgpd_text_color').val()      || '#1e293b';
      const bg  = $('#lgpd_bg_color').val()        || '#ffffff';
      const gradient = `linear-gradient(135deg, ${pc}, ${sc})`;

      // Popover stripe
      $('#js-preview-stripe').css('background', gradient);

      // Avatar
      $('#js-preview-avatar').css('background', gradient);

      // Popover bg + text
      $('#js-preview-popover').css({ background: bg, color: tc });
      $('#js-preview-title').css('color', tc);

      // Botão primário
      $('#js-preview-btn').css({ background: gradient, borderColor: 'transparent' });

      // Botão outline
      $('#js-preview-btn-outline').css({ borderColor: pc, color: pc, background: 'transparent' });

      // Ícone do avatar
      const iconVal = $('input[name="lgpd_settings[avatar_icon]"]:checked').val() || 'smile';
      $('#js-preview-avatar-icon').html(avatarSVGs[iconVal] || avatarSVGs.smile);

      // Label do avatar
      const label = $('#lgpd_avatar_label').val() || 'Cookies';
      $('#js-preview-avatar-label').text(label);

      // Atualiza também os ícones no icon picker
      const $iconPreviews = $('.lgpd-icon-option__preview');
      $iconPreviews.css('background', gradient);
    },

    // ── Custom scripts ─────────────────────────────────────────────────────────
    initCustomScripts() {
      let scriptIndex = $('#lgpd-custom-scripts-list .lgpd-custom-script').length;

      $('#js-add-custom-script').on('click', function () {
        const tpl = document.getElementById('lgpd-script-template');
        if (!tpl) return;
        const html = $(tpl.content.cloneNode(true)).find('.lgpd-custom-script')
          .prop('outerHTML').replace(/__IDX__/g, scriptIndex);
        $('#lgpd-custom-scripts-list').append(html);
        scriptIndex++;
      });

      $(document).on('click', '.js-remove-script', function () {
        $(this).closest('.lgpd-custom-script').fadeOut(200, function () { $(this).remove(); });
      });
    },

    // ── Mostrar/esconder campos de avatar ──────────────────────────────────────
    initAvatarFieldsVisibility() {
      this.toggleAvatarFields();

      // Label input → preview ao vivo
      $('#lgpd_avatar_label').on('input', _.debounce(() => Admin.updatePreview(), 200));
    },
  };

  $(document).ready(function () {
    Admin.init();
  });

})(jQuery);
