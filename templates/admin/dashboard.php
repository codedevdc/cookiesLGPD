<?php defined( 'ABSPATH' ) || exit; ?>
<div class="lgpd-admin wrap">

  <div class="lgpd-admin__header">
    <div class="lgpd-admin__header-inner">
      <div class="lgpd-admin__brand">
        <div class="lgpd-admin__logo-icon">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div>
          <h1 class="lgpd-admin__title">LGPD Cookie Consent</h1>
          <p class="lgpd-admin__subtitle">Conformidade com a Lei Geral de Proteção de Dados</p>
        </div>
      </div>
      <div class="lgpd-admin__header-actions">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" class="lgpd-btn lgpd-btn--outline lgpd-btn--sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Ver site
        </a>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=lgpd-cc-settings' ) ); ?>" class="lgpd-btn lgpd-btn--primary lgpd-btn--sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
          Configurar
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="lgpd-admin__body">

    <div class="lgpd-stats-grid">
      <?php
      $total      = $stats['total'] ?? 0;
      $by_action  = [];
      foreach ( ( $stats['by_action'] ?? [] ) as $row ) {
        $by_action[ $row['action'] ] = (int) $row['count'];
      }
      $accept_all = $by_action['accept_all'] ?? 0;
      $reject_all = $by_action['reject_all'] ?? 0;
      $selected   = $by_action['accept_selected'] ?? 0;
      $rate       = $total > 0 ? round( ( $accept_all + $selected ) / $total * 100 ) : 0;
      ?>
      <div class="lgpd-stat-card lgpd-stat-card--blue">
        <div class="lgpd-stat-card__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="lgpd-stat-card__content">
          <div class="lgpd-stat-card__value"><?php echo number_format_i18n( $total ); ?></div>
          <div class="lgpd-stat-card__label">Total de registros</div>
        </div>
      </div>
      <div class="lgpd-stat-card lgpd-stat-card--green">
        <div class="lgpd-stat-card__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div class="lgpd-stat-card__content">
          <div class="lgpd-stat-card__value"><?php echo number_format_i18n( $accept_all + $selected ); ?></div>
          <div class="lgpd-stat-card__label">Consentimentos aceitos</div>
        </div>
      </div>
      <div class="lgpd-stat-card lgpd-stat-card--red">
        <div class="lgpd-stat-card__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </div>
        <div class="lgpd-stat-card__content">
          <div class="lgpd-stat-card__value"><?php echo number_format_i18n( $reject_all ); ?></div>
          <div class="lgpd-stat-card__label">Cookies rejeitados</div>
        </div>
      </div>
      <div class="lgpd-stat-card lgpd-stat-card--purple">
        <div class="lgpd-stat-card__icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        </div>
        <div class="lgpd-stat-card__content">
          <div class="lgpd-stat-card__value"><?php echo $rate; ?>%</div>
          <div class="lgpd-stat-card__label">Taxa de aceitação</div>
        </div>
      </div>
    </div>

    <!-- Quick nav -->
    <div class="lgpd-quick-nav">
      <h2 class="lgpd-section-title">Acesso rápido</h2>
      <div class="lgpd-quick-nav__grid">
        <?php
        $nav_items = [
          [ 'page' => 'lgpd-cc-settings',      'icon' => 'M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z', 'title' => 'Configurações gerais', 'desc' => 'Textos, comportamento e política' ],
          [ 'page' => 'lgpd-cc-appearance',     'icon' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z', 'title' => 'Aparência', 'desc' => 'Cores, posição e animações' ],
          [ 'page' => 'lgpd-cc-categories',     'icon' => 'M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z', 'title' => 'Categorias', 'desc' => 'Gerencie grupos de cookies' ],
          [ 'page' => 'lgpd-cc-integrations',   'icon' => 'M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71', 'title' => 'Integrações', 'desc' => 'GTM, GA4, Facebook Pixel' ],
          [ 'page' => 'lgpd-cc-logs',           'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8 M10 9H8', 'title' => 'Logs de consentimento', 'desc' => 'Histórico e exportação CSV' ],
        ];
        foreach ( $nav_items as $item ) : ?>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $item['page'] ) ); ?>" class="lgpd-nav-card">
          <div class="lgpd-nav-card__icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="<?php echo esc_attr( $item['icon'] ); ?>"/></svg>
          </div>
          <div>
            <div class="lgpd-nav-card__title"><?php echo esc_html( $item['title'] ); ?></div>
            <div class="lgpd-nav-card__desc"><?php echo esc_html( $item['desc'] ); ?></div>
          </div>
          <svg class="lgpd-nav-card__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Plugin info -->
    <div class="lgpd-info-box">
      <div class="lgpd-info-box__icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div>
        <strong>LGPD — Lei nº 13.709/2018</strong><br>
        Este plugin auxilia na conformidade com a LGPD. Certifique-se de também manter sua Política de Privacidade atualizada e nomear um DPO (Encarregado pelo Tratamento de Dados).
      </div>
    </div>

  </div>
</div>
