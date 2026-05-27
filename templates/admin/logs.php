<?php defined( 'ABSPATH' ) || exit;
$is_pro       = LGPD_CC_Pro::is_active();
$action_labels = [
  'accept_all'      => [ 'label' => 'Aceitar todos',       'class' => 'badge--green' ],
  'accept_selected' => [ 'label' => 'Aceitar selecionados', 'class' => 'badge--blue' ],
  'reject_all'      => [ 'label' => 'Rejeitar todos',      'class' => 'badge--red' ],
  'withdraw'        => [ 'label' => 'Revogar',             'class' => 'badge--orange' ],
];
?>
<div class="lgpd-admin wrap">

  <?php include __DIR__ . '/partials/page-header.php'; ?>

  <div class="lgpd-admin__body">

    <?php if ( ! $is_pro ) : ?>
    <div class="lgpd-info-box" style="margin-bottom:20px">
      <span class="lgpd-info-box__icon">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </span>
      <span>
        Plano <strong>Free</strong>: logs retidos por <strong>30 dias</strong>. Faça upgrade para o
        <a href="<?php echo esc_url( LGPD_CC_Pro::upgrade_url() ); ?>" target="_blank"><strong>plano PRO</strong></a>
        e tenha retenção ilimitada + exportação em CSV.
      </span>
    </div>
    <?php endif; ?>

    <!-- Stats rápidas -->
    <div class="lgpd-stats-mini">
      <?php
      $total = $stats['total'] ?? 0;
      $by    = [];
      foreach ( $stats['by_action'] ?? [] as $row ) $by[ $row['action'] ] = (int) $row['count'];
      $accepts = ( $by['accept_all'] ?? 0 ) + ( $by['accept_selected'] ?? 0 );
      $rejects = $by['reject_all'] ?? 0;
      ?>
      <div class="lgpd-stats-mini__item">
        <span class="lgpd-stats-mini__val"><?php echo number_format_i18n( $total ); ?></span>
        <span class="lgpd-stats-mini__key">Total</span>
      </div>
      <div class="lgpd-stats-mini__item lgpd-stats-mini__item--green">
        <span class="lgpd-stats-mini__val"><?php echo number_format_i18n( $accepts ); ?></span>
        <span class="lgpd-stats-mini__key">Aceitos</span>
      </div>
      <div class="lgpd-stats-mini__item lgpd-stats-mini__item--red">
        <span class="lgpd-stats-mini__val"><?php echo number_format_i18n( $rejects ); ?></span>
        <span class="lgpd-stats-mini__key">Rejeitados</span>
      </div>
      <div class="lgpd-stats-mini__item lgpd-stats-mini__item--blue">
        <span class="lgpd-stats-mini__val"><?php echo $total > 0 ? round( $accepts / $total * 100 ) : 0; ?>%</span>
        <span class="lgpd-stats-mini__key">Taxa de aceite</span>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="lgpd-toolbar">
      <form method="get" class="lgpd-filter-form">
        <input type="hidden" name="page" value="lgpd-cc-logs">
        <div class="lgpd-filter-form__fields">
          <select name="filter_action" class="lgpd-select lgpd-select--sm">
            <option value="">Todas as ações</option>
            <?php foreach ( $action_labels as $val => $al ) : ?>
            <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $_GET['filter_action'] ?? '', $val ); ?>><?php echo esc_html( $al['label'] ); ?></option>
            <?php endforeach; ?>
          </select>
          <input type="date" name="date_from" value="<?php echo esc_attr( $_GET['date_from'] ?? '' ); ?>" class="lgpd-input lgpd-input--sm" placeholder="De">
          <input type="date" name="date_to"   value="<?php echo esc_attr( $_GET['date_to'] ?? '' ); ?>"   class="lgpd-input lgpd-input--sm" placeholder="Até">
          <button type="submit" class="lgpd-btn lgpd-btn--outline lgpd-btn--sm">Filtrar</button>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=lgpd-cc-logs' ) ); ?>" class="lgpd-btn lgpd-btn--ghost lgpd-btn--sm">Limpar</a>
        </div>
      </form>

      <div class="lgpd-toolbar__actions">

        <?php if ( $is_pro ) : ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
          <?php wp_nonce_field( 'lgpd_cc_export_csv' ); ?>
          <input type="hidden" name="action" value="lgpd_cc_export_csv">
          <button type="submit" class="lgpd-btn lgpd-btn--outline lgpd-btn--sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Exportar CSV
          </button>
        </form>
        <?php else : ?>
        <a href="<?php echo esc_url( LGPD_CC_Pro::upgrade_url() ); ?>" target="_blank"
           class="lgpd-btn lgpd-btn--outline lgpd-btn--sm" style="opacity:.6;cursor:not-allowed;pointer-events:none" title="Disponível no plano PRO">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Exportar CSV
          <?php echo LGPD_CC_Pro::badge( 'logs_export' ); ?>
        </a>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
              onsubmit="return confirm('<?php echo esc_js( __( 'Tem certeza? Logs antigos serão removidos.', 'lgpd-cookie-consent' ) ); ?>')">
          <?php wp_nonce_field( 'lgpd_cc_cleanup_logs' ); ?>
          <input type="hidden" name="action" value="lgpd_cc_cleanup_logs">
          <button type="submit" class="lgpd-btn lgpd-btn--danger lgpd-btn--sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            Limpar logs antigos
          </button>
        </form>

      </div>
    </div>

    <!-- Tabela -->
    <div class="lgpd-card">
      <div class="lgpd-card__body lgpd-card__body--no-pad">
        <?php if ( empty( $data['items'] ) ) : ?>
        <div class="lgpd-empty-state">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          <p>Nenhum registro encontrado.</p>
        </div>
        <?php else : ?>
        <table class="lgpd-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Ação</th>
              <th>Categorias aceitas</th>
              <th>IP</th>
              <th>Página</th>
              <th>Data/Hora</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ( $data['items'] as $row ) :
              $al = $action_labels[ $row['action'] ] ?? [ 'label' => $row['action'], 'class' => 'badge--gray' ];
            ?>
            <tr>
              <td class="lgpd-table__id"><?php echo esc_html( $row['id'] ); ?></td>
              <td><span class="lgpd-badge <?php echo esc_attr( $al['class'] ); ?>"><?php echo esc_html( $al['label'] ); ?></span></td>
              <td>
                <?php foreach ( $row['categories'] as $cat_key => $accepted ) :
                  $cat_label = $settings['categories'][ $cat_key ]['label'] ?? $cat_key;
                ?>
                <span class="lgpd-badge <?php echo $accepted ? 'badge--green' : 'badge--gray'; ?>" title="<?php echo esc_attr( $cat_key ); ?>">
                  <?php echo esc_html( $cat_label ); ?>
                </span>
                <?php endforeach; ?>
              </td>
              <td class="lgpd-table__mono"><?php echo esc_html( $row['ip'] ); ?></td>
              <td class="lgpd-table__url" title="<?php echo esc_attr( $row['page_url'] ); ?>">
                <?php echo esc_html( wp_parse_url( $row['page_url'], PHP_URL_PATH ) ?: $row['page_url'] ); ?>
              </td>
              <td class="lgpd-table__date"><?php echo esc_html( wp_date( 'd/m/Y H:i', strtotime( $row['created_at'] ) ) ); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>

    <!-- Paginação -->
    <?php if ( $data['total_pages'] > 1 ) : ?>
    <div class="lgpd-pagination">
      <?php
      $base_url = add_query_arg( [
        'page'          => 'lgpd-cc-logs',
        'filter_action' => $_GET['filter_action'] ?? '',
        'date_from'     => $_GET['date_from'] ?? '',
        'date_to'       => $_GET['date_to'] ?? '',
      ], admin_url( 'admin.php' ) );

      for ( $p = 1; $p <= $data['total_pages']; $p++ ) :
        $url    = add_query_arg( 'paged', $p, $base_url );
        $active = $p === $data['page'];
      ?>
      <a href="<?php echo esc_url( $url ); ?>" class="lgpd-page-btn <?php echo $active ? 'active' : ''; ?>">
        <?php echo esc_html( $p ); ?>
      </a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>

  </div>
</div>
