<?php defined( 'ABSPATH' ) || exit;
$is_pro = LGPD_CC_Pro::is_active();
?>
<div class="lgpd-admin wrap">

  <?php include __DIR__ . '/partials/page-header.php'; ?>

  <div class="lgpd-admin__body">

    <?php LGPD_CC_Pro::upsell_banner(
      'Desbloqueie a aparência completa com o PRO',
      'Gradiente, tema escuro, todos os ícones, presets de paleta e muito mais.'
    ); ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="lgpd-form">
      <?php wp_nonce_field( 'lgpd_cc_save_settings' ); ?>
      <input type="hidden" name="action"      value="lgpd_cc_save_settings">
      <input type="hidden" name="current_tab" value="appearance">

      <div class="lgpd-form__grid lgpd-form__grid--with-preview">
        <div class="lgpd-form__fields">

          <!-- ── AVATAR ─────────────────────────────────── -->
          <div class="lgpd-card lgpd-card--featured">
            <div class="lgpd-card__header">
              <h2 class="lgpd-card__title">
                <span class="lgpd-card__title-badge">Novo</span>
                Avatar — botão redondo
              </h2>
              <p class="lgpd-card__desc">Bolinha flutuante estilo AdOpt. Aparece discretamente após o consentimento.</p>
            </div>
            <div class="lgpd-card__body">

              <!-- Modo de exibição -->
              <div class="lgpd-field">
                <label class="lgpd-label">Modo de exibição</label>
                <div class="lgpd-mode-grid">
                  <?php
                  $modes = [
                    'avatar'       => [ 'label' => 'Avatar', 'desc' => 'Bolinha + modal',   'icon' => 'M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z', 'pro' => false ],
                    'bottom_bar'   => [ 'label' => 'Barra inferior', 'desc' => 'Banner no rodapé', 'icon' => 'M3 18h18M3 6h18', 'pro' => false ],
                    'top_bar'      => [ 'label' => 'Barra superior', 'desc' => 'Banner no topo',   'icon' => 'M3 6h18M3 18h18', 'pro' => true ],
                    'modal_center' => [ 'label' => 'Modal central',  'desc' => 'Caixa centralizada','icon' => 'M4 4h16v16H4z',  'pro' => true ],
                  ];
                  foreach ( $modes as $val => $mode ) :
                    $locked = $mode['pro'] && ! $is_pro;
                  ?>
                  <label class="lgpd-mode-card <?php echo $settings['position'] === $val ? 'active' : ''; ?> <?php echo $locked ? 'lgpd-mode-card--locked' : ''; ?>">
                    <input type="radio" name="lgpd_settings[position]" value="<?php echo esc_attr( $val ); ?>"
                           <?php checked( $settings['position'], $val ); ?>
                           <?php disabled( $locked, true ); ?>>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="<?php echo esc_attr( $mode['icon'] ); ?>"/>
                    </svg>
                    <strong><?php echo esc_html( $mode['label'] ); ?></strong>
                    <span><?php echo esc_html( $mode['desc'] ); ?></span>
                    <?php if ( $locked ) : ?>
                      <?php echo LGPD_CC_Pro::badge( 'position_advanced' ); // phpcs:ignore ?>
                    <?php endif; ?>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Ícone do avatar -->
              <div class="lgpd-field">
                <label class="lgpd-label">
                  Ícone do avatar
                  <?php if ( ! $is_pro ) : ?>
                    <span class="lgpd-label--hint">— ícones adicionais disponíveis no PRO</span>
                  <?php endif; ?>
                </label>
                <div class="lgpd-icon-picker">
                  <?php
                  $av_icons = [
                    'shield' => [ 'label' => 'Escudo',    'pro' => false, 'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4" stroke-width="2.2"/></svg>' ],
                    'smile'  => [ 'label' => 'Amigável',  'pro' => true,  'svg' => '<svg viewBox="0 0 40 40" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round"><circle cx="14.5" cy="17" r="2.2" fill="white" stroke="none"/><circle cx="25.5" cy="17" r="2.2" fill="white" stroke="none"/><path d="M13 24 Q20 30.5 27 24" stroke="white" stroke-width="2.5" fill="none"/></svg>' ],
                    'cookie' => [ 'label' => 'Cookie',    'pro' => true,  'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01M16 15.5v.01M12 12v.01" stroke-width="2.8"/></svg>' ],
                    'lock'   => [ 'label' => 'Cadeado',   'pro' => true,  'svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' ],
                  ];
                  foreach ( $av_icons as $val => $ico ) :
                    $locked = $ico['pro'] && ! $is_pro;
                  ?>
                  <label class="lgpd-icon-option <?php echo $settings['avatar_icon'] === $val ? 'active' : ''; ?> <?php echo $locked ? 'lgpd-icon-option--locked' : ''; ?>">
                    <input type="radio" name="lgpd_settings[avatar_icon]" value="<?php echo esc_attr( $val ); ?>"
                           class="js-icon-pick"
                           <?php checked( $settings['avatar_icon'], $val ); ?>
                           <?php disabled( $locked, true ); ?>>
                    <div class="lgpd-icon-option__preview js-avatar-mini">
                      <?php echo $ico['svg']; // phpcs:ignore ?>
                      <?php if ( $locked ) : ?><span class="lgpd-icon-lock">🔒</span><?php endif; ?>
                    </div>
                    <span><?php echo esc_html( $ico['label'] ); ?></span>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Tamanho + canto + label -->
              <div class="lgpd-field-row">
                <div class="lgpd-field">
                  <label class="lgpd-label">Tamanho</label>
                  <div class="lgpd-radio-group">
                    <?php foreach ( [ '44' => 'P', '52' => 'M', '60' => 'G', '68' => 'GG' ] as $val => $lbl ) : ?>
                    <label class="lgpd-radio">
                      <input type="radio" name="lgpd_settings[avatar_size]" value="<?php echo esc_attr( $val ); ?>"
                             <?php checked( $settings['avatar_size'], $val ); ?>>
                      <?php echo esc_html( $lbl ); ?>
                    </label>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="lgpd-field">
                  <label class="lgpd-label">Posição na tela</label>
                  <div class="lgpd-radio-group">
                    <?php foreach ( [ 'right' => 'Inferior direita', 'left' => 'Inferior esquerda' ] as $val => $lbl ) : ?>
                    <label class="lgpd-radio">
                      <input type="radio" name="lgpd_settings[avatar_corner]" value="<?php echo esc_attr( $val ); ?>"
                             <?php checked( $settings['avatar_corner'], $val ); ?>>
                      <?php echo esc_html( $lbl ); ?>
                    </label>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="lgpd-field">
                  <label class="lgpd-label" for="lgpd_avatar_label">Etiqueta</label>
                  <input type="text" id="lgpd_avatar_label" name="lgpd_settings[avatar_label]"
                         value="<?php echo esc_attr( $settings['avatar_label'] ); ?>"
                         class="lgpd-input" placeholder="Cookies">
                </div>
              </div>

              <!-- Pulse — PRO -->
              <div class="lgpd-toggle-row">
                <label class="lgpd-switch <?php echo ! $is_pro ? 'lgpd-switch--locked' : ''; ?>" for="lgpd_avatar_pulse">
                  <input type="checkbox" id="lgpd_avatar_pulse" name="lgpd_settings[avatar_pulse]"
                         value="1" <?php checked( ! empty( $settings['avatar_pulse'] ) ); ?>
                         <?php disabled( ! $is_pro, true ); ?>>
                  <span class="lgpd-switch__track"></span>
                </label>
                <span class="lgpd-toggle-row__label">
                  Animação de pulso
                  <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'avatar_pulse' ); // phpcs:ignore ?>
                </span>
              </div>

            </div>
          </div>

          <!-- ── CORES ────────────────────────────────────── -->
          <div class="lgpd-card">
            <div class="lgpd-card__header">
              <h2 class="lgpd-card__title">Paleta de cores</h2>
            </div>
            <div class="lgpd-card__body">
              <div class="lgpd-color-palette">

                <div class="lgpd-color-swatch-field">
                  <label class="lgpd-label">Cor primária <span class="lgpd-label--hint">Botões e destaques</span></label>
                  <input type="text" name="lgpd_settings[primary_color]"
                         value="<?php echo esc_attr( $settings['primary_color'] ); ?>"
                         class="lgpd-color-picker" data-default-color="#2563eb">
                </div>

                <!-- Cor secundária — PRO -->
                <div class="lgpd-color-swatch-field <?php echo ! $is_pro ? 'lgpd-field--locked' : ''; ?>">
                  <label class="lgpd-label">
                    Cor secundária
                    <span class="lgpd-label--hint">Gradiente do avatar</span>
                    <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'secondary_color' ); // phpcs:ignore ?>
                  </label>
                  <input type="text" name="lgpd_settings[secondary_color]"
                         value="<?php echo esc_attr( $settings['secondary_color'] ); ?>"
                         class="lgpd-color-picker" data-default-color="#7c3aed"
                         <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                </div>

                <div class="lgpd-color-swatch-field">
                  <label class="lgpd-label">Cor do texto</label>
                  <input type="text" name="lgpd_settings[text_color]"
                         value="<?php echo esc_attr( $settings['text_color'] ); ?>"
                         class="lgpd-color-picker" data-default-color="#1e293b">
                </div>

                <div class="lgpd-color-swatch-field">
                  <label class="lgpd-label">Cor de fundo</label>
                  <input type="text" name="lgpd_settings[bg_color]"
                         value="<?php echo esc_attr( $settings['bg_color'] ); ?>"
                         class="lgpd-color-picker" data-default-color="#ffffff">
                </div>

              </div>

              <!-- Presets — PRO -->
              <?php if ( $is_pro ) : ?>
              <div class="lgpd-field" style="margin-top:20px">
                <label class="lgpd-label">Presets de paleta</label>
                <div class="lgpd-theme-presets">
                  <?php
                  $presets = [
                    [ 'name' => 'Azul & Violeta',  'primary' => '#2563eb', 'secondary' => '#7c3aed', 'text' => '#1e293b', 'bg' => '#ffffff' ],
                    [ 'name' => 'Verde & Teal',    'primary' => '#059669', 'secondary' => '#0891b2', 'text' => '#064e3b', 'bg' => '#ffffff' ],
                    [ 'name' => 'Laranja & Rosa',  'primary' => '#ea580c', 'secondary' => '#db2777', 'text' => '#1e293b', 'bg' => '#ffffff' ],
                    [ 'name' => 'Dark pro',        'primary' => '#6366f1', 'secondary' => '#a855f7', 'text' => '#f1f5f9', 'bg' => '#0f172a' ],
                    [ 'name' => 'Neutro elegante', 'primary' => '#334155', 'secondary' => '#64748b', 'text' => '#1e293b', 'bg' => '#ffffff' ],
                    [ 'name' => 'Coral & Âmbar',   'primary' => '#f43f5e', 'secondary' => '#f59e0b', 'text' => '#1e293b', 'bg' => '#ffffff' ],
                  ];
                  foreach ( $presets as $preset ) : ?>
                  <button type="button" class="lgpd-preset-btn js-apply-preset"
                          data-primary="<?php echo esc_attr( $preset['primary'] ); ?>"
                          data-secondary="<?php echo esc_attr( $preset['secondary'] ); ?>"
                          data-text="<?php echo esc_attr( $preset['text'] ); ?>"
                          data-bg="<?php echo esc_attr( $preset['bg'] ); ?>">
                    <span class="lgpd-preset-btn__swatch" style="background: linear-gradient(135deg, <?php echo esc_attr( $preset['primary'] ); ?>, <?php echo esc_attr( $preset['secondary'] ); ?>);"></span>
                    <span><?php echo esc_html( $preset['name'] ); ?></span>
                  </button>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php else : ?>
              <div class="lgpd-pro-feature-row" style="margin-top:16px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Presets de paleta disponíveis no PRO
                <a href="<?php echo esc_url( LGPD_CC_Pro::upgrade_url() ); ?>" class="lgpd-pro-badge" target="_blank">Fazer upgrade</a>
              </div>
              <?php endif; ?>

            </div>
          </div>

          <!-- ── TEMA ─────────────────────────────────────── -->
          <div class="lgpd-card">
            <div class="lgpd-card__header">
              <h2 class="lgpd-card__title">Tema e tipografia</h2>
            </div>
            <div class="lgpd-card__body">

              <div class="lgpd-field">
                <label class="lgpd-label">
                  Modo de cor
                  <?php if ( ! $is_pro ) : ?>
                    <span class="lgpd-label--hint">— dark e automático disponíveis no</span>
                    <?php echo LGPD_CC_Pro::badge( 'theme_dark' ); // phpcs:ignore ?>
                  <?php endif; ?>
                </label>
                <div class="lgpd-radio-group">
                  <?php foreach ( [ 'light' => [ 'label' => 'Claro', 'pro' => false ], 'dark' => [ 'label' => 'Escuro', 'pro' => true ], 'auto' => [ 'label' => 'Automático (sistema)', 'pro' => true ] ] as $val => $opt ) :
                    $locked_theme = $opt['pro'] && ! $is_pro;
                  ?>
                  <label class="lgpd-radio <?php echo $locked_theme ? 'lgpd-radio--locked' : ''; ?>">
                    <input type="radio" name="lgpd_settings[theme]" value="<?php echo esc_attr( $val ); ?>"
                           <?php checked( $settings['theme'], $val ); ?>
                           <?php disabled( $locked_theme, true ); ?>>
                    <?php echo esc_html( $opt['label'] ); ?>
                    <?php if ( $locked_theme ) echo LGPD_CC_Pro::badge(); // phpcs:ignore ?>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="lgpd-field">
                <label class="lgpd-label">Animação de entrada</label>
                <div class="lgpd-radio-group">
                  <?php foreach ( [ 'slide' => 'Deslizar', 'fade' => 'Desvanecer', 'none' => 'Sem animação' ] as $val => $lbl ) : ?>
                  <label class="lgpd-radio">
                    <input type="radio" name="lgpd_settings[animation]" value="<?php echo esc_attr( $val ); ?>"
                           <?php checked( $settings['animation'], $val ); ?>>
                    <?php echo esc_html( $lbl ); ?>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>

              <div class="lgpd-field-row">
                <div class="lgpd-field">
                  <label class="lgpd-label" for="lgpd_font_size">Tamanho da fonte (px)</label>
                  <input type="number" id="lgpd_font_size" name="lgpd_settings[font_size]"
                         value="<?php echo esc_attr( $settings['font_size'] ); ?>"
                         class="lgpd-input lgpd-input--short" min="11" max="20">
                </div>
                <div class="lgpd-field">
                  <label class="lgpd-label" for="lgpd_border_radius">Arredondamento (px)</label>
                  <input type="number" id="lgpd_border_radius" name="lgpd_settings[border_radius]"
                         value="<?php echo esc_attr( $settings['border_radius'] ); ?>"
                         class="lgpd-input lgpd-input--short" min="0" max="32">
                </div>
              </div>

            </div>
          </div>

          <!-- ── LOGO — PRO ────────────────────────────────── -->
          <div class="lgpd-card" style="position:relative">
            <div class="lgpd-card__header">
              <h2 class="lgpd-card__title">
                Logo personalizada
                <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'logo' ); // phpcs:ignore ?>
              </h2>
            </div>
            <?php if ( $is_pro ) : ?>
            <div class="lgpd-card__body">
              <div class="lgpd-toggle-row">
                <label class="lgpd-switch" for="lgpd_show_logo">
                  <input type="checkbox" id="lgpd_show_logo" name="lgpd_settings[show_logo]"
                         value="1" <?php checked( ! empty( $settings['show_logo'] ) ); ?>>
                  <span class="lgpd-switch__track"></span>
                </label>
                <span class="lgpd-toggle-row__label">Exibir logo no popover/banner</span>
              </div>
              <div class="lgpd-field" style="margin-top:14px">
                <label class="lgpd-label" for="lgpd_logo_url">URL da logo</label>
                <input type="url" id="lgpd_logo_url" name="lgpd_settings[logo_url]"
                       value="<?php echo esc_attr( $settings['logo_url'] ); ?>"
                       class="lgpd-input" placeholder="https://exemplo.com/logo.png">
              </div>
            </div>
            <?php else : ?>
            <div class="lgpd-card__body" style="padding:0">
              <?php $feature = 'logo'; $description = 'Exiba a logo da sua empresa no banner de cookies.';
              include __DIR__ . '/partials/pro-gate.php'; ?>
            </div>
            <?php endif; ?>
          </div>

        </div><!-- /.lgpd-form__fields -->

        <!-- Preview ao vivo -->
        <div class="lgpd-form__preview">
          <div class="lgpd-preview-box">
            <div class="lgpd-preview-box__label">Pré-visualização</div>
            <div class="lgpd-preview-screen" id="js-preview-screen">
              <div class="lgpd-preview-popover" id="js-preview-popover">
                <div class="lgpd-preview-popover__stripe" id="js-preview-stripe"></div>
                <div class="lgpd-preview-popover__title" id="js-preview-title">Sua privacidade importa</div>
                <div class="lgpd-preview-popover__desc">Usamos cookies para melhorar sua experiência.</div>
                <div class="lgpd-preview-popover__btns">
                  <span class="lgpd-preview-btn lgpd-preview-btn--primary" id="js-preview-btn">Aceitar todos</span>
                  <span class="lgpd-preview-btn lgpd-preview-btn--outline" id="js-preview-btn-outline">Recusar</span>
                </div>
              </div>
              <div class="lgpd-preview-avatar" id="js-preview-avatar">
                <div class="lgpd-preview-avatar__icon" id="js-preview-avatar-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4" stroke-width="2.2"/></svg>
                </div>
                <div class="lgpd-preview-avatar__label" id="js-preview-avatar-label"><?php echo esc_html( $settings['avatar_label'] ); ?></div>
              </div>
            </div>
            <p class="lgpd-preview-box__note">Salve e visite o site para o resultado real.</p>
          </div>
        </div>

      </div>

      <div class="lgpd-form__footer">
        <button type="submit" class="lgpd-btn lgpd-btn--primary lgpd-btn--lg">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Salvar aparência
        </button>
      </div>

    </form>
  </div>
</div>
