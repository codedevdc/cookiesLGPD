<?php defined( 'ABSPATH' ) || exit;
$ints   = $settings['integrations'] ?? [];
$custom = $ints['custom_scripts'] ?? [];
$is_pro = LGPD_CC_Pro::is_active();
?>
<div class="lgpd-admin wrap">

  <?php include __DIR__ . '/partials/page-header.php'; ?>

  <div class="lgpd-admin__body">

    <?php if ( ! $is_pro ) : ?>
    <?php LGPD_CC_Pro::upsell_banner(
      'Integre suas ferramentas favoritas',
      'GTM, Facebook Pixel, Hotjar e scripts personalizados estão disponíveis no plano PRO.'
    ); ?>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="lgpd-form">
      <?php wp_nonce_field( 'lgpd_cc_save_settings' ); ?>
      <input type="hidden" name="action"      value="lgpd_cc_save_settings">
      <input type="hidden" name="current_tab" value="integrations">

      <div class="lgpd-form__grid">

        <!-- Integrações nativas -->
        <div class="lgpd-card">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">Integrações nativas</h2>
            <p class="lgpd-card__desc">Scripts carregados automaticamente após consentimento da categoria correspondente.</p>
          </div>
          <div class="lgpd-card__body">

            <!-- GA4 — GRÁTIS -->
            <div class="lgpd-integration-item">
              <div class="lgpd-integration-item__logo lgpd-integration-item__logo--ga4">GA4</div>
              <div class="lgpd-integration-item__content">
                <label class="lgpd-label" for="lgpd_ga4_id">Google Analytics 4 — ID de medição</label>
                <input type="text" id="lgpd_ga4_id" name="lgpd_settings[integrations][ga4_id]"
                       value="<?php echo esc_attr( $ints['ga4_id'] ?? '' ); ?>"
                       class="lgpd-input" placeholder="G-XXXXXXXXXX">
                <p class="lgpd-help">Categoria: <strong>Análise e desempenho</strong> — IP anonimizado automaticamente</p>
              </div>
            </div>

            <!-- GTM — PRO -->
            <div class="lgpd-integration-item">
              <div class="lgpd-integration-item__logo lgpd-integration-item__logo--gtm">GTM</div>
              <div class="lgpd-integration-item__content <?php echo ! $is_pro ? 'lgpd-field--locked' : ''; ?>">
                <label class="lgpd-label" for="lgpd_gtm_id">
                  Google Tag Manager — ID do container
                  <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'gtm' ); ?>
                </label>
                <input type="text" id="lgpd_gtm_id" name="lgpd_settings[integrations][gtm_id]"
                       value="<?php echo esc_attr( $ints['gtm_id'] ?? '' ); ?>"
                       class="lgpd-input" placeholder="GTM-XXXXXXX"
                       <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                <p class="lgpd-help">Categoria: <strong>Análise e desempenho</strong></p>
              </div>
            </div>

            <!-- Facebook Pixel — PRO -->
            <div class="lgpd-integration-item">
              <div class="lgpd-integration-item__logo lgpd-integration-item__logo--fb">FB</div>
              <div class="lgpd-integration-item__content <?php echo ! $is_pro ? 'lgpd-field--locked' : ''; ?>">
                <label class="lgpd-label" for="lgpd_fb_pixel_id">
                  Facebook Pixel — ID do pixel
                  <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'fb_pixel' ); ?>
                </label>
                <input type="text" id="lgpd_fb_pixel_id" name="lgpd_settings[integrations][fb_pixel_id]"
                       value="<?php echo esc_attr( $ints['fb_pixel_id'] ?? '' ); ?>"
                       class="lgpd-input" placeholder="123456789012345"
                       <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                <p class="lgpd-help">Categoria: <strong>Marketing e publicidade</strong></p>
              </div>
            </div>

            <!-- Hotjar — PRO -->
            <div class="lgpd-integration-item">
              <div class="lgpd-integration-item__logo lgpd-integration-item__logo--hj">HJ</div>
              <div class="lgpd-integration-item__content <?php echo ! $is_pro ? 'lgpd-field--locked' : ''; ?>">
                <label class="lgpd-label" for="lgpd_hotjar_id">
                  Hotjar — Site ID
                  <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'hotjar' ); ?>
                </label>
                <input type="text" id="lgpd_hotjar_id" name="lgpd_settings[integrations][hotjar_id]"
                       value="<?php echo esc_attr( $ints['hotjar_id'] ?? '' ); ?>"
                       class="lgpd-input" placeholder="1234567"
                       <?php echo ! $is_pro ? 'disabled' : ''; ?>>
                <p class="lgpd-help">Categoria: <strong>Análise e desempenho</strong></p>
              </div>
            </div>

          </div>
        </div>

        <!-- Scripts customizados -->
        <div class="lgpd-card">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">
              Scripts personalizados
              <?php if ( ! $is_pro ) echo LGPD_CC_Pro::badge( 'custom_scripts' ); ?>
            </h2>
            <p class="lgpd-card__desc">Adicione scripts de terceiros vinculados a uma categoria de cookie.</p>
          </div>
          <div class="lgpd-card__body">

            <?php if ( ! $is_pro ) : ?>
            <?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo LGPD_CC_Pro::lock_overlay(
              'custom_scripts',
              'Adicione quantos scripts de terceiros quiser, vinculados a categorias de consentimento.'
            ); ?>

            <?php else : ?>

            <div id="lgpd-custom-scripts-list">
              <?php foreach ( $custom as $i => $cs ) : ?>
              <div class="lgpd-custom-script" data-index="<?php echo esc_attr( $i ); ?>">
                <div class="lgpd-custom-script__header">
                  <input type="text" name="lgpd_settings[integrations][custom_scripts][<?php echo esc_attr( $i ); ?>][name]"
                         value="<?php echo esc_attr( $cs['name'] ); ?>"
                         class="lgpd-input lgpd-input--sm" placeholder="Nome do script (ex: Intercom)">
                  <select name="lgpd_settings[integrations][custom_scripts][<?php echo esc_attr( $i ); ?>][category]" class="lgpd-select lgpd-select--sm">
                    <?php foreach ( $settings['categories'] as $ck => $cc ) : ?>
                    <option value="<?php echo esc_attr( $ck ); ?>" <?php selected( $cs['category'] ?? '', $ck ); ?>><?php echo esc_html( $cc['label'] ); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <select name="lgpd_settings[integrations][custom_scripts][<?php echo esc_attr( $i ); ?>][position]" class="lgpd-select lgpd-select--sm">
                    <option value="head"   <?php selected( $cs['position'] ?? '', 'head' ); ?>>Head</option>
                    <option value="footer" <?php selected( $cs['position'] ?? '', 'footer' ); ?>>Footer</option>
                  </select>
                  <button type="button" class="lgpd-btn lgpd-btn--ghost lgpd-btn--sm js-remove-script" title="Remover">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </div>
                <textarea name="lgpd_settings[integrations][custom_scripts][<?php echo esc_attr( $i ); ?>][code]"
                          class="lgpd-textarea lgpd-textarea--mono lgpd-textarea--sm" rows="4"
                          placeholder="// Código JavaScript do script de terceiro"><?php echo esc_textarea( $cs['code'] ?? '' ); ?></textarea>
              </div>
              <?php endforeach; ?>
            </div>

            <button type="button" class="lgpd-btn lgpd-btn--outline lgpd-btn--sm" id="js-add-custom-script" style="margin-top:16px">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Adicionar script
            </button>

            <!-- Template oculto -->
            <template id="lgpd-script-template">
              <div class="lgpd-custom-script" data-index="__IDX__">
                <div class="lgpd-custom-script__header">
                  <input type="text" name="lgpd_settings[integrations][custom_scripts][__IDX__][name]"
                         value="" class="lgpd-input lgpd-input--sm" placeholder="Nome do script">
                  <select name="lgpd_settings[integrations][custom_scripts][__IDX__][category]" class="lgpd-select lgpd-select--sm">
                    <?php foreach ( $settings['categories'] as $ck => $cc ) : ?>
                    <option value="<?php echo esc_attr( $ck ); ?>"><?php echo esc_html( $cc['label'] ); ?></option>
                    <?php endforeach; ?>
                  </select>
                  <select name="lgpd_settings[integrations][custom_scripts][__IDX__][position]" class="lgpd-select lgpd-select--sm">
                    <option value="footer">Footer</option>
                    <option value="head">Head</option>
                  </select>
                  <button type="button" class="lgpd-btn lgpd-btn--ghost lgpd-btn--sm js-remove-script">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                  </button>
                </div>
                <textarea name="lgpd_settings[integrations][custom_scripts][__IDX__][code]"
                          class="lgpd-textarea lgpd-textarea--mono lgpd-textarea--sm" rows="4"
                          placeholder="// Código JavaScript"></textarea>
              </div>
            </template>

            <?php endif; // $is_pro ?>

          </div>
        </div>

      </div>

      <div class="lgpd-form__footer">
        <button type="submit" class="lgpd-btn lgpd-btn--primary lgpd-btn--lg">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Salvar integrações
        </button>
      </div>

    </form>
  </div>
</div>
