<?php defined( 'ABSPATH' ) || exit; ?>
<div class="lgpd-admin wrap">

  <?php include __DIR__ . '/partials/page-header.php'; ?>

  <div class="lgpd-admin__body">
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="lgpd-form">
      <?php wp_nonce_field( 'lgpd_cc_save_settings' ); ?>
      <input type="hidden" name="action"      value="lgpd_cc_save_settings">
      <input type="hidden" name="current_tab" value="categories">

      <div class="lgpd-info-box" style="margin-bottom:24px">
        <div class="lgpd-info-box__icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div>
          As <strong>categorias de cookies</strong> são exibidas na modal de preferências. Cookies marcados como <em>bloqueados</em> ficam sempre ativos e o usuário não pode desativá-los. Os cookies listados são apenas informativos.
        </div>
      </div>

      <div id="lgpd-categories-list" class="lgpd-categories-editor">
        <?php foreach ( $settings['categories'] as $key => $cat ) : ?>
        <div class="lgpd-cat-editor" data-key="<?php echo esc_attr( $key ); ?>">
          <div class="lgpd-cat-editor__header">
            <div class="lgpd-cat-editor__drag" title="Arrastar">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="5" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="19" r="1"/><circle cx="15" cy="5" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="19" r="1"/></svg>
            </div>
            <div class="lgpd-cat-editor__icon-selector">
              <select name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][icon]" class="lgpd-select lgpd-select--sm">
                <?php foreach ( [ 'shield' => 'Escudo', 'chart' => 'Gráfico', 'megaphone' => 'Megafone', 'settings' => 'Engrenagem' ] as $iv => $il ) : ?>
                <option value="<?php echo esc_attr( $iv ); ?>" <?php selected( $cat['icon'], $iv ); ?>><?php echo esc_html( $il ); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <input type="text" name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][label]"
                   value="<?php echo esc_attr( $cat['label'] ); ?>"
                   class="lgpd-input lgpd-cat-editor__label-input"
                   placeholder="Nome da categoria">
            <div class="lgpd-cat-editor__toggles">
              <label class="lgpd-checkbox-label" title="Habilitado por padrão">
                <input type="checkbox" name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][enabled]"
                       value="1" <?php checked( ! empty( $cat['enabled'] ) ); ?>>
                Padrão: ativo
              </label>
              <label class="lgpd-checkbox-label <?php echo ! empty( $cat['locked'] ) ? 'lgpd-checkbox-label--locked' : ''; ?>" title="Sempre ativo (necessário)">
                <input type="checkbox" name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][locked]"
                       value="1" <?php checked( ! empty( $cat['locked'] ) ); ?>>
                Sempre ativo
              </label>
            </div>
          </div>
          <div class="lgpd-cat-editor__body">
            <div class="lgpd-field">
              <label class="lgpd-label">Descrição</label>
              <textarea name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][description]"
                        class="lgpd-textarea lgpd-textarea--sm" rows="2"><?php echo esc_textarea( $cat['description'] ); ?></textarea>
            </div>
            <div class="lgpd-field">
              <label class="lgpd-label">Cookies desta categoria <span class="lgpd-label--hint">(um por linha)</span></label>
              <textarea name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][cookies][]"
                        class="lgpd-textarea lgpd-textarea--sm lgpd-textarea--mono" rows="3"
                        placeholder="_ga&#10;_gid&#10;_gat"><?php echo esc_textarea( implode( "\n", $cat['cookies'] ?? [] ) ); ?></textarea>
            </div>
            <input type="hidden" name="lgpd_settings[categories][<?php echo esc_attr( $key ); ?>][key_hidden]"
                   value="<?php echo esc_attr( $key ); ?>">
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="lgpd-form__footer">
        <button type="submit" class="lgpd-btn lgpd-btn--primary lgpd-btn--lg">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Salvar categorias
        </button>
      </div>

    </form>
  </div>
</div>
