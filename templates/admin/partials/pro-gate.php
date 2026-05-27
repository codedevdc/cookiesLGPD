<?php
/**
 * Partial: overlay de feature bloqueada.
 * Variáveis esperadas: $feature (string), $description (string opcional)
 */
defined( 'ABSPATH' ) || exit;
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo LGPD_CC_Pro::lock_overlay( $feature ?? '', $description ?? '' );
