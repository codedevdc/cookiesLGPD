<?php defined( 'ABSPATH' ) || exit;
$screen   = get_current_screen();
$page_id  = $screen->id ?? '';

$nav_items = [
  'toplevel_page_lgpd-cookie-consent'   => [ 'label' => 'Dashboard',     'page' => 'lgpd-cookie-consent' ],
  'lgpd-cookies_page_lgpd-cc-settings'  => [ 'label' => 'Configurações', 'page' => 'lgpd-cc-settings' ],
  'lgpd-cookies_page_lgpd-cc-appearance'=> [ 'label' => 'Aparência',     'page' => 'lgpd-cc-appearance' ],
  'lgpd-cookies_page_lgpd-cc-categories'=> [ 'label' => 'Categorias',    'page' => 'lgpd-cc-categories' ],
  'lgpd-cookies_page_lgpd-cc-integrations'=> [ 'label' => 'Integrações', 'page' => 'lgpd-cc-integrations' ],
  'lgpd-cookies_page_lgpd-cc-logs'      => [ 'label' => 'Logs',          'page' => 'lgpd-cc-logs' ],
];
?>
<div class="lgpd-admin__header">
  <div class="lgpd-admin__header-inner">
    <div class="lgpd-admin__brand">
      <div class="lgpd-admin__logo-icon">
        <img src="<?php echo esc_url( LGPD_CC_PLUGIN_URL . 'logo.jpg' ); ?>" alt="LGPD Cookie Consent" width="32" height="32" style="display:block;border-radius:6px;">
      </div>
      <div>
        <h1 class="lgpd-admin__title">LGPD Cookie Consent</h1>
      </div>
    </div>
    <div class="lgpd-admin__header-version">v<?php echo esc_html( LGPD_CC_VERSION ); ?></div>
  </div>
  <nav class="lgpd-admin__nav" aria-label="Menu do plugin">
    <?php foreach ( $nav_items as $screen_id => $item ) :
      $is_active = $page_id === $screen_id;
    ?>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $item['page'] ) ); ?>"
       class="lgpd-admin__nav-item <?php echo $is_active ? 'active' : ''; ?>"
       <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
      <?php echo esc_html( $item['label'] ); ?>
    </a>
    <?php endforeach; ?>
  </nav>
</div>
