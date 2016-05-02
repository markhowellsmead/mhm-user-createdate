<?php
/*
Plugin Name: User creation date column in WordPress admin
Plugin URI: #
Description: Adds a new column to the WordPress admin user list view, showing the date on which the user was created. The list can be sorted by this column.
Author: Mark Howells-Mead
Version: 1.0
Author URI: https://permanenttourist.ch/
*/

class MHMUserCreatedate {

    public $key     = '';
    public $version = '1.0';
    
    public function __construct(){
        $this->key = basename(__DIR__);
        add_filter('manage_users_columns', array($this, 'custom_column_header'), 20);
        add_action('manage_users_custom_column', array($this, 'custom_column_content'), 10, 3);
        add_filter('manage_users_sortable_columns', array($this, 'column_sortable'), 10, 1);
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
                return strftime('%e.%m.%Y %H:%M:%S', strtotime($user->user_registered));
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
