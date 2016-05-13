<?php
/*
Plugin Name: User creation date column in WordPress admin
Plugin URI: https://wordpress.org/plugins/mhm-user-createdate/
Text Domain: mhm-user-createdate
Description: Adds a new column to the WordPress admin user list view, showing the date on which the user was created. The list can be sorted by this column.
Author: Mark Howells-Mead
Version: 1.1.3
Author URI: https://permanenttourist.ch/
*/

class MHMUserCreatedate {

    public $key     = '';
    public $version = '1.1.3';
    public $wpversion = '4.3';

    public function __construct(){
        register_activation_hook( __FILE__, array( $this, 'check_version' ) );
        add_action( 'admin_init', array( $this, 'check_version' ) );

        // Don't run anything else in the plugin, if we're on an incompatible WordPress version
        if ( ! $this->compatible_version() ) {
            return;
        }

        $this->key = basename(__DIR__);

        add_action( 'plugins_loaded', array($this, 'load_textdomain') );
        add_filter( 'manage_users_columns', array($this, 'custom_column_header'), 20);
        add_action( 'manage_users_custom_column', array($this, 'custom_column_content'), 10, 3);
        add_filter( 'manage_users_sortable_columns', array($this, 'column_sortable'), 10, 1);
    }

    public function check_version() {
        // Check that this plugin is compatible with the current version of WordPress
        if ( ! $this->compatible_version() ) {
            if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
                deactivate_plugins( plugin_basename( __FILE__ ) );
                add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
                if ( isset( $_GET['activate'] ) ) {
                    unset( $_GET['activate'] );
                }
            }
        }
    }

    public function disabled_notice() {
        echo '<div class="notice notice-error is-dismissible">
            <p>' .sprintf( __('The plugin “%1$s” requires WordPress %2$s or higher!', $this->key),
                _x('User creation date column in WordPress admin', 'Plugin name', $this->key),
                $this->wpversion). '</p>
        </div>';
    }

    private function compatible_version() {
        if ( version_compare( $GLOBALS['wp_version'], $this->wpversion, '<' ) ) {
            return false;
        }
        return true;
    }

	public function load_textdomain(){
    	load_plugin_textdomain( $this->key, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
    }

	public function custom_column_header($cols){
        // Add column and header
		$cols['mhm-user-createdate'] = __('Registration date', $this->key);
		return $cols;
	}

	public function custom_column_content($output, $column_name, $user_id){
        //	show content for each row
		switch($column_name){
			case 'mhm-user-createdate':
			    $user = get_userdata( $user_id );
                return strftime( __('%e.%m.%Y %H:%M:%S', $this->key), strtotime($user->user_registered));
                break;
        }
        return $output;
	}

	public function column_sortable($columns) {
        $custom = array(
            'mhm-user-createdate' => 'user_registered',
        );
        return wp_parse_args($custom, $columns);
    }

}

new MHMUserCreatedate();
