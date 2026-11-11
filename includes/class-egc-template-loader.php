<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EGC_Template_Loader {

    public static function get_template( $template_name, $args = array() ) {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }

        $file = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/steps/' . $template_name;

        if ( file_exists( $file ) ) {
            include $file;
        }
    }
}