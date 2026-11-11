<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class EGC_Assets {

    public function enqueue_assets() {
        if ( ! function_exists( 'is_product' ) || ! is_product() ) {
            return;
        }

        $url = plugin_dir_url( dirname( __FILE__ ) );
        $ver = time();

        // Enqueue CSS files
        wp_enqueue_style( 'egc-global-customizer', $url . 'assets/css/global-customizer.css', array(), $ver );
        wp_enqueue_style( 'egc-rx-type', $url . 'assets/css/rx-type.css', array( 'egc-global-customizer' ), $ver );
        wp_enqueue_style( 'egc-prescription', $url . 'assets/css/prescription.css', array( 'egc-global-customizer' ), $ver );
        wp_enqueue_style( 'egc-material', $url . 'assets/css/material.css', array( 'egc-global-customizer' ), $ver );
        wp_enqueue_style( 'egc-coating', $url . 'assets/css/coating.css', array( 'egc-global-customizer' ), $ver );
        wp_enqueue_style( 'egc-features', $url . 'assets/css/features.css', array( 'egc-global-customizer' ), $ver );
        wp_enqueue_style( 'egc-summary', $url . 'assets/css/summary.css', array( 'egc-global-customizer' ), $ver );

        // Enqueue JS files
        wp_enqueue_script( 'egc-global-customizer', $url . 'assets/js/global-customizer.js', array( 'jquery' ), $ver, true );
        wp_enqueue_script( 'egc-rx-type', $url . 'assets/js/rx-type.js', array( 'jquery', 'egc-global-customizer' ), $ver, true );
        wp_enqueue_script( 'egc-prescription', $url . 'assets/js/prescription.js', array( 'jquery', 'egc-global-customizer' ), $ver, true );
        wp_enqueue_script( 'egc-material', $url . 'assets/js/material.js', array( 'jquery', 'egc-global-customizer' ), $ver, true );
        wp_enqueue_script( 'egc-coating', $url . 'assets/js/coating.js', array( 'jquery', 'egc-global-customizer' ), $ver, true );
        wp_enqueue_script( 'egc-features', $url . 'assets/js/features.js', array( 'jquery', 'egc-global-customizer' ), $ver, true );
        wp_enqueue_script( 'egc-summary', $url . 'assets/js/summary.js', array( 'jquery', 'egc-global-customizer' ), $ver, true );
    }
}