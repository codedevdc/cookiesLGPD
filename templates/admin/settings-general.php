<?php defined( 'ABSPATH' ) || exit; ?>
<div class="lgpd-admin wrap">

  <?php include __DIR__ . '/partials/page-header.php'; ?>

  <div class="lgpd-admin__body">
    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="lgpd-form">
      <?php wp_nonce_field( 'lgpd_cc_save_settings' ); ?>
      <input type="hidden" name="action"      value="lgpd_cc_save_settings">
      <input type="hidden" name="current_tab" value="general">

      <div class="lgpd-form__grid">

        <!-- Textos -->
        <div class="lgpd-card">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">Textos do banner</h2>
            <p class="lgpd-card__desc">Personalize as mensagens exibidas para o visitante.</p>
          </div>
          <div class="lgpd-card__body">

            <div class="lgpd-field">
              <label class="lgpd-label" for="lgpd_title">Título do banner</label>
              <input type="text" id="lgpd_title" name="lgpd_settings[title]"
                     value="<?php echo esc_attr( $settings['title'] ); ?>"
                     class="lgpd-input" placeholder="Sua privacidade importa">
            </div>

            <div class="lgpd-field">
              <label class="lgpd-label" for="lgpd_description">Descrição</label>
              <textarea id="lgpd_description" name="lgpd_settings[description]"
                        class="lgpd-textarea" rows="4"
                        placeholder="Use {policy_url} para inserir o link da política automaticamente."><?php echo esc_textarea( $settings['description'] ); ?></textarea>
              <p class="lgpd-help">Use <code>{policy_url}</code> para inserir automaticamente o link para a página de política.</p>
            </div>

            <div class="lgpd-field-row">
              <div class="lgpd-field">
                <label class="lgpd-label" for="lgpd_btn_accept_all">Botão: Aceitar todos</label>
                <input type="text" id="lgpd_btn_accept_all" name="lgpd_settings[btn_accept_all]"
                       value="<?php echo esc_attr( $settings['btn_accept_all'] ); ?>" class="lgpd-input">
              </div>
              <div class="lgpd-field">
                <label class="lgpd-label" for="lgpd_btn_reject_all">Botão: Rejeitar todos</label>
                <input type="text" id="lgpd_btn_reject_all" name="lgpd_settings[btn_reject_all]"
                       value="<?php echo esc_attr( $settings['btn_reject_all'] ); ?>" class="lgpd-input">
              </div>
            </div>

            <div class="lgpd-field-row">
              <div class="lgpd-field">
                <label class="lgpd-label" for="lgpd_btn_customize">Botão: Personalizar</label>
                <input type="text" id="lgpd_btn_customize" name="lgpd_settings[btn_customize]"
                       value="<?php echo esc_attr( $settings['btn_customize'] ); ?>" class="lgpd-input">
              </div>
              <div class="lgpd-field">
                <label class="lgpd-label" for="lgpd_btn_save_prefs">Botão: Salvar preferências</label>
                <input type="text" id="lgpd_btn_save_prefs" name="lgpd_settings[btn_save_prefs]"
                       value="<?php echo esc_attr( $settings['btn_save_prefs'] ); ?>" class="lgpd-input">
              </div>
            </div>

            <div class="lgpd-field-row">
              <div class="lgpd-field">
                <label class="lgpd-label" for="lgpd_modal_title">Título da modal de preferências</label>
                <input type="text" id="lgpd_modal_title" name="lgpd_settings[modal_title]"
                       value="<?php echo esc_attr( $settings['modal_title'] ); ?>" class="lgpd-input">
              </div>
              <div class="lgpd-field">
                <label class="lgpd-label" for="lgpd_withdraw_text">Texto: Revogar consentimento</label>
                <input type="text" id="lgpd_withdraw_text" name="lgpd_settings[withdraw_text]"
                       value="<?php echo esc_attr( $settings['withdraw_text'] ); ?>" class="lgpd-input">
              </div>
            </div>

          </div>
        </div>

        <!-- Comportamento -->
        <div class="lgpd-card">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">Comportamento</h2>
            <p class="lgpd-card__desc">Defina como o banner e os cookies se comportam.</p>
          </div>
          <div class="lgpd-card__body">

            <div class="lgpd-field">
              <label class="lgpd-label" for="lgpd_policy_page">Página de política de privacidade</label>
              <select id="lgpd_policy_page" name="lgpd_settings[policy_page_id]" class="lgpd-select">
                <option value="0">— Selecione —</option>
                <?php foreach ( $pages as $page ) : ?>
                  <option value="<?php echo esc_attr( $page->ID ); ?>"
                    <?php selected( $settings['policy_page_id'], $page->ID ); ?>>
                    <?php echo esc_html( $page->post_title ); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="lgpd-field">
              <label class="lgpd-label" for="lgpd_cookie_lifetime">Validade do consentimento (dias)</label>
              <input type="number" id="lgpd_cookie_lifetime" name="lgpd_settings[cookie_lifetime]"
                     value="<?php echo esc_attr( $settings['cookie_lifetime'] ); ?>"
                     class="lgpd-input lgpd-input--short" min="1" max="730">
              <p class="lgpd-help">Recomendado: 365 dias (1 ano). Após esse período o usuário verá o banner novamente.</p>
            </div>

            <div class="lgpd-toggles-list">
              <?php
              $toggles = [
                [ 'show_reject_btn',    'Exibir botão "Rejeitar todos" no banner' ],
                [ 'show_customize_btn', 'Exibir botão "Personalizar" no banner' ],
                [ 'show_floating_btn',  'Exibir botão flutuante após consentimento' ],
                [ 'force_modal',        'Abrir modal de preferências automaticamente' ],
                [ 'block_on_scroll',    'Aceitar automaticamente ao rolar a página (não recomendado para LGPD)' ],
                [ 'auto_block_scripts', 'Bloquear scripts de rastreamento até consentimento' ],
              ];
              foreach ( $toggles as [$key, $label] ) : ?>
              <div class="lgpd-toggle-row">
                <label class="lgpd-switch" for="lgpd_<?php echo esc_attr( $key ); ?>">
                  <input type="checkbox" id="lgpd_<?php echo esc_attr( $key ); ?>"
                         name="lgpd_settings[<?php echo esc_attr( $key ); ?>]"
                         value="1" <?php checked( ! empty( $settings[ $key ] ) ); ?>>
                  <span class="lgpd-switch__track"></span>
                </label>
                <span class="lgpd-toggle-row__label"><?php echo esc_html( $label ); ?></span>
              </div>
              <?php endforeach; ?>
            </div>

            <div class="lgpd-field" id="lgpd_floating_btn_text_field">
              <label class="lgpd-label" for="lgpd_floating_btn_text">Texto do botão flutuante</label>
              <input type="text" id="lgpd_floating_btn_text" name="lgpd_settings[floating_btn_text]"
                     value="<?php echo esc_attr( $settings['floating_btn_text'] ); ?>"
                     class="lgpd-input lgpd-input--short">
            </div>

          </div>
        </div>

        <!-- Logs -->
        <div class="lgpd-card">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">Registro de consentimentos</h2>
            <p class="lgpd-card__desc">Armazene um histórico para comprovar conformidade com a LGPD.</p>
          </div>
          <div class="lgpd-card__body">
            <div class="lgpd-toggle-row">
              <label class="lgpd-switch" for="lgpd_log_enabled">
                <input type="checkbox" id="lgpd_log_enabled" name="lgpd_settings[log_enabled]"
                       value="1" <?php checked( ! empty( $settings['log_enabled'] ) ); ?>>
                <span class="lgpd-switch__track"></span>
              </label>
              <span class="lgpd-toggle-row__label">Habilitar registro de consentimentos</span>
            </div>
            <div class="lgpd-field" style="margin-top:16px">
              <label class="lgpd-label" for="lgpd_log_retention">Retenção dos logs (dias)</label>
              <input type="number" id="lgpd_log_retention" name="lgpd_settings[log_retention_days]"
                     value="<?php echo esc_attr( $settings['log_retention_days'] ); ?>"
                     class="lgpd-input lgpd-input--short" min="30" max="3650">
              <p class="lgpd-help">Logs mais antigos que este período são removidos automaticamente.</p>
            </div>
          </div>
        </div>

        <!-- Rede avançada -->
        <div class="lgpd-card">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">Rede avançada</h2>
            <p class="lgpd-card__desc">Configurações para ambientes com proxy reverso ou CDN.</p>
          </div>
          <div class="lgpd-card__body">

            <div class="lgpd-toggle-row">
              <label class="lgpd-switch" for="lgpd_trusted_proxy">
                <input type="checkbox" id="lgpd_trusted_proxy" name="lgpd_settings[trusted_proxy]"
                       value="1" <?php checked( ! empty( $settings['trusted_proxy'] ) ); ?>>
                <span class="lgpd-switch__track"></span>
              </label>
              <span class="lgpd-toggle-row__label">O site usa proxy reverso (CloudFlare, nginx, Load Balancer)</span>
            </div>

            <?php if ( ! empty( $settings['trusted_proxy'] ) ) : ?>
            <div class="lgpd-notice lgpd-notice--warning" style="margin-top:14px">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
              <span><strong>Atenção:</strong> com esta opção ativa, o IP registrado nos logs de consentimento vem do cabeçalho <code>X-Forwarded-For</code>, que pode ser forjado por visitantes mal-intencionados. Ative somente se seu servidor realmente estiver atrás de um proxy confiável.</span>
            </div>
            <?php else : ?>
            <p class="lgpd-help" style="margin-top:10px">Quando desativado, o plugin usa sempre o IP direto da conexão (<code>REMOTE_ADDR</code>), que é mais seguro. Ative apenas se o IP registrado nos logs estiver sempre errado (ex.: todos os logs aparecem com o IP do servidor).</p>
            <?php endif; ?>

          </div>
        </div>

        <!-- Shortcodes -->
        <div class="lgpd-card lgpd-card--info">
          <div class="lgpd-card__header">
            <h2 class="lgpd-card__title">Shortcodes disponíveis</h2>
          </div>
          <div class="lgpd-card__body">
            <div class="lgpd-shortcode-list">
              <div class="lgpd-shortcode">
                <code>[lgpd_withdraw_btn]</code>
                <span>Botão para o usuário revogar ou alterar o consentimento.</span>
              </div>
              <div class="lgpd-shortcode">
                <code>[lgpd_withdraw_btn text="Gerenciar Cookies" class="minha-classe"]</code>
                <span>Com texto e classe CSS personalizados.</span>
              </div>
              <div class="lgpd-shortcode">
                <code>[lgpd_policy_link text="Política de Privacidade"]</code>
                <span>Link para a página de política configurada acima.</span>
              </div>
            </div>
          </div>
        </div>

      </div><!-- /.lgpd-form__grid -->

      <div class="lgpd-form__footer">
        <button type="submit" class="lgpd-btn lgpd-btn--primary lgpd-btn--lg">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          Salvar configurações
        </button>
      </div>

    </form>
  </div>
</div>
