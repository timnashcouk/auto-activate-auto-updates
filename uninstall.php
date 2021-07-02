<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
// Delete our option
delete_option( 'auto_update_deactivated_plugins' );
