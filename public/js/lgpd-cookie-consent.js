/**
 * LGPD Cookie Consent — Frontend Script v1.1
 * Design: Avatar redondo + Popover (estilo AdOpt)
 */
(function () {
  'use strict';

  const CFG         = window.lgpdCC || {};
  const COOKIE_NAME = 'lgpd_consent';
  const CAT_COOKIE  = 'lgpd_categories';
  const av          = CFG.avatar || {};
  const corner      = av.corner || 'right';

  // Fallbacks para textos — garante que nunca apareçam vazios
  const _cfg18 = CFG.i18n || {};
  const i18n = {
    accept_all : _cfg18.accept_all  || 'Aceitar todos',
    reject_all : _cfg18.reject_all  || 'Rejeitar tudo',
    customize  : _cfg18.customize   || 'Personalizar',
    save_prefs : _cfg18.save_prefs  || 'Salvar preferências',
    withdraw   : _cfg18.withdraw    || 'Gerenciar cookies',
  };

  // ─────────────────────────────────────────────────────────────────────────────
  // SVG Icons
  // ─────────────────────────────────────────────────────────────────────────────
  const icons = {

    /* Avatar icons — ficam dentro da bolinha */
    smile: `<svg width="28" height="28" viewBox="0 0 40 40" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <circle cx="14.5" cy="17" r="2.2" fill="white" stroke="none"/>
      <circle cx="25.5" cy="17" r="2.2" fill="white" stroke="none"/>
      <path d="M13 24 Q20 30.5 27 24" stroke="white" stroke-width="2.5" fill="none"/>
    </svg>`,

    shield: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
      <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2.2"/>
    </svg>`,

    cookie: `<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/>
      <path d="M8.5 8.5v.01M16 15.5v.01M12 12v.01" stroke="white" stroke-width="2.8"/>
    </svg>`,

    lock: `<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
      <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>`,

    /* UI icons — usados nos botões e no modal */
    check: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`,
    x:     `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    chevron:`<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>`,
    shieldSm:`<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
    chartSm: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`,
    megSm:   `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>`,
    settSm:  `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>`,
    checkCircle:`<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>`,
  };

  const catIcons = { shield: 'shieldSm', chart: 'chartSm', megaphone: 'megSm', settings: 'settSm' };

  // ─────────────────────────────────────────────────────────────────────────────
  // Cookies
  // ─────────────────────────────────────────────────────────────────────────────
  const Cookies = {
    set(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + days * 864e5);
      document.cookie = `${name}=${encodeURIComponent(value)};expires=${d.toUTCString()};path=/;SameSite=Lax`;
    },
    get(name) {
      const c = document.cookie.split(';').find(s => s.trim().startsWith(name + '='));
      return c ? decodeURIComponent(c.trim().split('=')[1]) : null;
    },
    remove(name) {
      document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
    },
  };

  // ─────────────────────────────────────────────────────────────────────────────
  // State
  // ─────────────────────────────────────────────────────────────────────────────
  const state = {
    consented:      false,
    categories:     {},
    popoverOpen:    false,
    modalOpen:      false,
  };

  // DOM refs
  let $avatar, $popover, $modal, $overlay, $toast, $banner;

  // ─────────────────────────────────────────────────────────────────────────────
  // Build: Avatar (a bolinha)
  // ─────────────────────────────────────────────────────────────────────────────
  function buildAvatar() {
    const iconKey  = av.icon || 'smile';
    const showLabel = parseInt(av.size || 56) >= 52;

    const el = document.createElement('button');
    el.id = 'lgpd-avatar';
    el.className = `corner-${corner}`;
    el.setAttribute('aria-label', av.label || 'Cookies');
    el.setAttribute('aria-haspopup', 'dialog');
    el.innerHTML = `
      <span class="lgpd-avatar__icon">${icons[iconKey] || icons.smile}</span>
      ${showLabel ? `<span class="lgpd-avatar__label">${esc(av.label || 'Cookies')}</span>` : ''}
    `;
    return el;
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Build: Popover (balão de fala)
  // ─────────────────────────────────────────────────────────────────────────────
  function buildPopover() {
    const rejectBtn = (CFG.showReject && CFG.isPro)
      ? `<button class="lgpd-btn lgpd-btn--ghost lgpd-btn--sm js-lgpd-reject">${esc(i18n.reject_all)}</button>` : '';
    const customBtn = (CFG.showCustom && CFG.isPro)
      ? `<button class="lgpd-btn lgpd-btn--outline lgpd-btn--sm lgpd-btn--full js-lgpd-customize">${esc(i18n.customize)}</button>` : '';
    const logoHtml = (CFG.showLogo && CFG.logoUrl)
      ? `<div class="lgpd-popover__logo"><img src="${esc(CFG.logoUrl)}" alt="Logo"></div>` : '';

    const el = document.createElement('div');
    el.id   = 'lgpd-popover';
    el.className = `corner-${corner}`;
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'false');
    el.setAttribute('aria-label', 'Aviso de cookies');
    el.innerHTML = `
      <div class="lgpd-popover__stripe"></div>
      <div class="lgpd-popover__body">
        ${logoHtml}
        <div class="lgpd-popover__title">
          <span class="lgpd-popover__title-icon">${icons.shieldSm}</span>
          ${esc(CFG.title || 'Sua privacidade importa')}
        </div>
        <div class="lgpd-popover__desc">${CFG.description || ''}</div>
      </div>
      <div class="lgpd-popover__actions">
        <button class="lgpd-btn lgpd-btn--primary lgpd-btn--full js-lgpd-accept-all">
          ${icons.check} ${esc(i18n.accept_all)}
        </button>
        <div class="lgpd-popover__actions-row">
          ${rejectBtn}
          ${customBtn}
        </div>
      </div>
      <button class="lgpd-popover__close js-lgpd-popover-close" aria-label="Fechar">${icons.x}</button>
    `;
    return el;
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Build: Banner barra (modo não-avatar)
  // ─────────────────────────────────────────────────────────────────────────────
  function buildBanner() {
    const pos  = CFG.position || 'bottom_bar';
    const anim = CFG.animation || 'slide';

    const rejectBtn = CFG.showReject
      ? `<button class="lgpd-btn lgpd-btn--ghost lgpd-btn--full js-lgpd-reject">${esc(i18n.reject_all)}</button>` : '';
    const customBtn = CFG.showCustom
      ? `<button class="lgpd-btn lgpd-btn--outline lgpd-btn--full js-lgpd-customize">${esc(i18n.customize)}</button>` : '';

    const el = document.createElement('div');
    el.id        = 'lgpd-cc-banner';
    el.className = `pos-${pos} anim-${anim}`;
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');
    el.innerHTML = `
      <div class="lgpd-cc-banner__inner">
        <div class="lgpd-cc-banner__content">
          <div class="lgpd-cc-banner__title">
            ${icons.shieldSm}
            ${esc(CFG.title || 'Sua privacidade importa')}
          </div>
          <div class="lgpd-cc-banner__desc">${CFG.description || ''}</div>
        </div>
        <div class="lgpd-cc-banner__actions">
          <button class="lgpd-btn lgpd-btn--primary lgpd-btn--full js-lgpd-accept-all">
            ${icons.check} ${esc(i18n.accept_all)}
          </button>
          ${customBtn}
          ${rejectBtn}
        </div>
      </div>`;
    return el;
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Build: Modal — funciona como UI principal de consentimento
  // ─────────────────────────────────────────────────────────────────────────────
  function buildModal() {
    const cats = CFG.categories || {};

    const catsHtml = Object.entries(cats).map(([key, cat]) => {
      const iconKey    = catIcons[cat.icon] || 'settSm';
      const checked    = cat.enabled ? 'checked'  : '';
      const disabled   = cat.locked  ? 'disabled' : '';
      const lockedBadge = cat.locked
        ? `<span class="lgpd-cat__badge lgpd-cat__badge--locked">Sempre ativo</span>` : '';
      const cookieTags = (cat.cookies || []).length
        ? `<div class="lgpd-cat__cookies">${cat.cookies.map(c => `<span class="lgpd-cat__cookie-tag">${esc(c)}</span>`).join('')}</div>` : '';

      return `
        <div class="lgpd-cat" data-category="${esc(key)}">
          <div class="lgpd-cat__header js-cat-header">
            <div class="lgpd-cat__icon">${icons[iconKey] || icons.settSm}</div>
            <div class="lgpd-cat__info">
              <div class="lgpd-cat__name">${esc(cat.label)} ${lockedBadge}</div>
            </div>
            <label class="lgpd-toggle" onclick="event.stopPropagation()">
              <input type="checkbox" class="lgpd-cat__toggle" data-cat="${esc(key)}" ${checked} ${disabled}>
              <span class="lgpd-toggle__track"></span>
            </label>
            <span class="lgpd-cat__expand">${icons.chevron}</span>
          </div>
          <div class="lgpd-cat__body">
            <div class="lgpd-cat__desc">${esc(cat.description)}${cookieTags}</div>
          </div>
        </div>`;
    }).join('');

    const el = document.createElement('div');
    el.id   = 'lgpd-cc-modal';
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');
    el.setAttribute('aria-labelledby', 'lgpd-modal-title');
    el.innerHTML = `
      <div class="lgpd-modal__stripe"></div>

      <div class="lgpd-modal__header">
        <div class="lgpd-modal__title" id="lgpd-modal-title">
          ${icons.shieldSm}
          ${esc(CFG.modalTitle || 'Central de privacidade')}
        </div>
        <button class="lgpd-modal__close js-lgpd-modal-close" aria-label="Fechar">${icons.x}</button>
      </div>

      <div class="lgpd-modal__body">
        <p class="lgpd-modal__desc">${CFG.description || ''}</p>

        <div class="lgpd-modal__quick-actions">
          <button class="lgpd-btn lgpd-btn--primary lgpd-btn--full js-lgpd-modal-accept-all">
            ${icons.check} ${esc(i18n.accept_all)}
          </button>
          ${CFG.isPro ? `
          <button class="lgpd-btn lgpd-btn--outline lgpd-btn--full js-lgpd-modal-reject-all">
            ${esc(i18n.reject_all)}
          </button>` : ''}
        </div>

        ${CFG.isPro ? `
          <div class="lgpd-modal__divider">ou personalize abaixo</div>
          <div class="lgpd-cat-list">${catsHtml}</div>
        ` : ''}
      </div>

      <div class="lgpd-modal__footer">
        <button class="lgpd-btn lgpd-btn--ghost lgpd-btn--sm js-lgpd-modal-close">Cancelar</button>
        ${CFG.isPro ? `
        <div class="lgpd-modal__footer-right">
          <button class="lgpd-btn lgpd-btn--primary js-lgpd-save-prefs">
            ${icons.check} ${esc(i18n.save_prefs)}
          </button>
        </div>` : ''}
      </div>`;
    return el;
  }

  function buildToast() {
    const el = document.createElement('div');
    el.id        = 'lgpd-cc-toast';
    el.className = `corner-${corner}`;
    el.innerHTML = `<span class="lgpd-toast__icon">${icons.checkCircle}</span><span class="lgpd-toast__msg"></span>`;
    return el;
  }

  function buildOverlay() {
    const el = document.createElement('div');
    el.id = 'lgpd-cc-overlay';
    return el;
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Visibility helpers
  // ─────────────────────────────────────────────────────────────────────────────
  function showAvatar(pulse, consented) {
    if (!$avatar) return;
    $avatar.classList.toggle('pulse', !!pulse);
    $avatar.classList.toggle('consented', !!consented);
    requestAnimationFrame(() => {
      requestAnimationFrame(() => $avatar.classList.add('visible'));
    });
  }

  function hideAvatar() {
    if ($avatar) $avatar.classList.remove('visible', 'pulse');
  }

  function showPopover() {
    if (!$popover) return;
    state.popoverOpen = true;
    $avatar && $avatar.classList.remove('pulse');
    requestAnimationFrame(() => $popover.classList.add('visible'));
    $popover.querySelector('.lgpd-popover__close')?.focus();
  }

  function hidePopover() {
    if (!$popover) return;
    state.popoverOpen = false;
    $popover.classList.remove('visible');
  }

  function showBanner() {
    if (!$banner) return;
    requestAnimationFrame(() => {
      $banner.classList.remove('hidden');
      requestAnimationFrame(() => $banner.classList.add('active'));
    });
  }

  function hideBanner() {
    if (!$banner) return;
    $banner.classList.remove('active');
    setTimeout(() => $banner.classList.add('hidden'), 320);
  }

  function showModal() {
    syncModalToggles();
    $overlay && $overlay.classList.add('active');
    requestAnimationFrame(() => {
      $modal && $modal.classList.add('active');
    });
    state.modalOpen = true;
    hidePopover();
    trapFocus($modal);
  }

  function hideModal() {
    $modal   && $modal.classList.remove('active');
    $overlay && $overlay.classList.remove('active');
    state.modalOpen = false;
    // Só devolve foco ao avatar se ele estiver visível (pós-consentimento)
    if (state.consented && $avatar) $avatar.focus();
  }

  function showToast(msg) {
    if (!$toast) return;
    $toast.querySelector('.lgpd-toast__msg').textContent = msg;
    $toast.classList.add('show');
    setTimeout(() => $toast.classList.remove('show'), 3200);
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Consentimento
  // ─────────────────────────────────────────────────────────────────────────────
  function getStoredConsent() {
    const raw = Cookies.get(COOKIE_NAME);
    if (!raw) return null;
    try { return JSON.parse(raw); } catch { return null; }
  }

  function saveConsent(categories, action) {
    Cookies.set(COOKIE_NAME,  JSON.stringify({ version: '1.0', action, categories, timestamp: new Date().toISOString() }), CFG.cookieLifetime || 365);
    Cookies.set(CAT_COOKIE,   JSON.stringify(categories), CFG.cookieLifetime || 365);

    state.consented  = true;
    state.categories = categories;

    if (CFG.restUrl) {
      fetch(CFG.restUrl + 'consent', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': CFG.nonce || '' },
        body:    JSON.stringify({ categories: Object.keys(categories).filter(k => categories[k]), action }),
      }).catch(() => {});
    }

    document.dispatchEvent(new CustomEvent('lgpd:consent', { detail: { categories, action }, bubbles: true }));
    loadIntegrations(categories);
  }

  function acceptAll() {
    const cats = {};
    Object.keys(CFG.categories || {}).forEach(k => cats[k] = true);
    saveConsent(cats, 'accept_all');
    afterConsent('Todos os cookies aceitos!');
  }

  function rejectAll() {
    const cats = {};
    Object.keys(CFG.categories || {}).forEach(k => {
      cats[k] = !!(CFG.categories[k].locked);
    });
    saveConsent(cats, 'reject_all');
    afterConsent('Apenas cookies essenciais ativados.');
  }

  function saveSelected() {
    const cats = {};
    document.querySelectorAll('.lgpd-cat__toggle').forEach(t => { cats[t.dataset.cat] = t.checked; });
    saveConsent(cats, 'accept_selected');
    afterConsent('Preferências salvas com sucesso!');
  }

  function afterConsent(msg) {
    hideModal();
    hideBanner();
    hidePopover();
    showToast(msg);
    showAvatar(false, true); // consented=true → fica discreto
  }

  function syncModalToggles() {
    document.querySelectorAll('.lgpd-cat__toggle').forEach(t => {
      const k = t.dataset.cat;
      t.checked = state.categories[k] !== undefined
        ? !!state.categories[k]
        : !!(CFG.categories?.[k]?.enabled);
    });
    // Atualiza borda
    document.querySelectorAll('.lgpd-cat').forEach(el => {
      const inp = el.querySelector('.lgpd-cat__toggle');
      el.classList.toggle('checked', inp ? inp.checked : false);
    });
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Integrações
  // ─────────────────────────────────────────────────────────────────────────────
  function loadIntegrations(categories) {
    (CFG.integrations || []).forEach(int => {
      if (!categories[int.category]) return;
      if      (int.id === 'gtm'     && int.gtm_id)   loadGTM(int.gtm_id);
      else if (int.id === 'ga4'     && int.ga4_id)   loadGA4(int.ga4_id);
      else if (int.id === 'fbpixel' && int.pixel_id) loadFBPixel(int.pixel_id);
      else if (int.id === 'hotjar'  && int.hjid)     loadHotjar(int.hjid);
      else if (int.id.startsWith('custom_') && int.code) injectCode(int.code, int.position);
    });
  }

  function loadGTM(id) {
    if (window.dataLayer) return;
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({ 'gtm.start': Date.now(), event: 'gtm.js' });
    appendScript(`https://www.googletagmanager.com/gtm.js?id=${id}`, true);
  }

  function loadGA4(id) {
    if (document.querySelector(`script[src*="${id}"]`)) return;
    appendScript(`https://www.googletagmanager.com/gtag/js?id=${id}`, true);
    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function() { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', id, { anonymize_ip: true });
  }

  function loadFBPixel(id) {
    if (window.fbq) return;
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
    window.fbq('init', id);
    window.fbq('track', 'PageView');
  }

  function loadHotjar(id) {
    if (window.hj) return;
    (function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};h._hjSettings={hjid:id,hjsv:6};a=o.getElementsByTagName('head')[0];r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r)})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
  }

  function appendScript(src, async) {
    const s = document.createElement('script');
    s.src   = src;
    s.async = !!async;
    document.head.appendChild(s);
  }

  function injectCode(code, position) {
    const s = document.createElement('script');
    s.textContent = code;
    (position === 'head' ? document.head : document.body).appendChild(s);
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Eventos
  // ─────────────────────────────────────────────────────────────────────────────
  function bindEvents() {
    // Avatar sempre abre o modal (só aparece após consentimento)
    $avatar?.addEventListener('click', showModal);

    // Popover actions
    delegate(document, '.js-lgpd-accept-all',     'click', acceptAll);
    delegate(document, '.js-lgpd-reject',          'click', rejectAll);
    delegate(document, '.js-lgpd-customize',       'click', showModal);
    delegate(document, '.js-lgpd-popover-close',   'click', hidePopover);

    // Banner actions
    delegate(document, '.js-lgpd-banner-accept',   'click', acceptAll);
    delegate(document, '.js-lgpd-banner-reject',   'click', rejectAll);
    delegate(document, '.js-lgpd-banner-customize','click', showModal);

    // Modal actions
    delegate(document, '.js-lgpd-modal-close',      'click', hideModal);
    delegate(document, '.js-lgpd-modal-accept-all', 'click', acceptAll);
    delegate(document, '.js-lgpd-modal-reject-all', 'click', rejectAll);
    delegate(document, '.js-lgpd-save-prefs',       'click', saveSelected);

    // Accordion categorias
    delegate(document, '.js-cat-header', 'click', function(e) {
      if (e.target.closest('.lgpd-toggle')) return;
      const cat = e.target.closest('.lgpd-cat');
      if (!cat) return;
      cat.classList.toggle('open');
    });

    // Toggle muda borda do card
    delegate(document, '.lgpd-cat__toggle', 'change', function(e) {
      const cat = e.target.closest('.lgpd-cat');
      if (cat) cat.classList.toggle('checked', e.target.checked);
    });

    // Overlay
    $overlay?.addEventListener('click', hideModal);

    // Escape
    document.addEventListener('keydown', e => {
      if (e.key !== 'Escape') return;
      if (state.modalOpen)   hideModal();
      else if (state.popoverOpen) hidePopover();
    });

    // Block on scroll
    if (CFG.blockOnScroll && !state.consented) {
      let fired = false;
      window.addEventListener('scroll', function onScroll() {
        if (fired) return;
        fired = true;
        window.removeEventListener('scroll', onScroll);
        acceptAll();
      }, { passive: true });
    }
  }

  function delegate(parent, selector, event, handler) {
    parent.addEventListener(event, function(e) {
      const target = e.target.closest(selector);
      if (target) handler.call(target, e);
    });
  }

  function trapFocus(el) {
    if (!el) return;
    const sel = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    const focusable = [...el.querySelectorAll(sel)];
    if (!focusable.length) return;
    focusable[0].focus();
    el.addEventListener('keydown', function(e) {
      if (e.key !== 'Tab') return;
      const first = focusable[0];
      const last  = focusable[focusable.length - 1];
      if (e.shiftKey) { if (document.activeElement === first) { e.preventDefault(); last.focus(); } }
      else             { if (document.activeElement === last)  { e.preventDefault(); first.focus(); } }
    });
  }

  function esc(str) {
    return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // Tema
  // ─────────────────────────────────────────────────────────────────────────────
  function applyTheme() {
    document.documentElement.dataset.lgpdTheme = CFG.theme || 'light';
  }

  // ─────────────────────────────────────────────────────────────────────────────
  // API pública
  // ─────────────────────────────────────────────────────────────────────────────
  window.lgpdCC = {
    openPreferences: showModal,
    acceptAll,
    rejectAll,
    getConsent:  () => ({ ...state.categories }),
    hasConsent:  (cat) => !!state.categories[cat],
    withdraw() {
      Cookies.remove(COOKIE_NAME);
      Cookies.remove(CAT_COOKIE);
      state.consented  = false;
      state.categories = {};
      document.dispatchEvent(new CustomEvent('lgpd:withdraw', { bubbles: true }));
      // Esconde o avatar e mostra o modal para novo consentimento
      hideAvatar();
      showModal();
    },
  };

  // ─────────────────────────────────────────────────────────────────────────────
  // Init
  // ─────────────────────────────────────────────────────────────────────────────
  function init() {
    applyTheme();

    const useAvatar = !CFG.position || CFG.position === 'avatar';

    // Monta DOM
    $overlay = buildOverlay();
    $modal   = buildModal();
    $toast   = buildToast();
    document.body.appendChild($overlay);
    document.body.appendChild($modal);
    document.body.appendChild($toast);

    if (useAvatar) {
      $avatar  = buildAvatar();
      $popover = buildPopover();
      document.body.appendChild($popover);
      document.body.appendChild($avatar);
    } else {
      $banner = buildBanner();
      document.body.appendChild($banner);
    }

    bindEvents();

    // ── Lógica principal ──────────────────────────────────────────────────────
    const stored = getStoredConsent();

    if (stored) {
      // JÁ consentiu: avatar aparece para gerenciar preferências
      state.consented  = true;
      state.categories = stored.categories || {};
      loadIntegrations(state.categories);
      showAvatar(false, true);
    } else {
      // PRIMEIRA VISITA: abre o modal (mais visível que popover no canto)
      if (useAvatar) {
        setTimeout(showModal, 350);
      } else {
        showBanner();
      }
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
