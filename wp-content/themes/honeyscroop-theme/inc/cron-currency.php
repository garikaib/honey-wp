<?php
/**
 * Currency Cron Job
 * 
 * Handles background syncing of currency rates.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Schedule the cron event if not already scheduled, or unschedule it if disabled.
 */
function honeyscroop_manage_currency_cron( $auto_update ) {
    $hook = 'honeyscroop_sync_rates_event';

    if ( $auto_update && 'true' === $auto_update ) {
        if ( ! wp_next_scheduled( $hook ) ) {
            wp_schedule_event( time(), 'hourly', $hook );
        }
    } else {
        $timestamp = wp_next_scheduled( $hook );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, $hook );
        }
    }
}

/**
 * The Cron Callback Function
 * Fetches rates from APIs and updates the option.
 */
function honeyscroop_sync_rates_job() {
    $current_options = get_option( 'honeyscroop_option_currency', array() );
    
    // Ensure we have a rates array
    if ( ! isset( $current_options['rates'] ) ) {
        $current_options['rates'] = array( 'USD' => 1 );
    }

    $new_rates = $current_options['rates'];
    $updated = false;

    // 1. Fetch ZWG Rate (ZiG_BMBuy)
    $zwg_res = wp_remote_get( 'https://api.clientemails.xyz/api/rates/fx-rates' );
    if ( ! is_wp_error( $zwg_res ) ) {
        $body = wp_remote_retrieve_body( $zwg_res );
        $data = json_decode( $body, true );
        
        if ( isset( $data['success'] ) && $data['success'] && isset( $data['rates']['ZiG_BMBuy'] ) ) {
            $new_rates['ZWG'] = (float) $data['rates']['ZiG_BMBuy'];
            $updated = true;
        }
    }

    // 2. Fetch Other Rates
    $oe_res = wp_remote_get( 'https://api.clientemails.xyz/api/rates/oe-rates/raw' );
    if ( ! is_wp_error( $oe_res ) ) {
        $body = wp_remote_retrieve_body( $oe_res );
        $data = json_decode( $body, true );

        if ( isset( $data['success'] ) && $data['success'] && isset( $data['rates']['rates'] ) && is_array( $data['rates']['rates'] ) ) {
            
            // Map the array of objects: [{"ZAR": 18}, ...] -> "ZAR" => 18
            $remote_map = array();
            foreach ( $data['rates']['rates'] as $rate_obj ) {
                foreach ( $rate_obj as $code => $rate ) {
                    $remote_map[ $code ] = $rate;
                }
            }

            $targets = array( 'ZAR', 'GBP', 'BWP', 'AUD', 'NZD' );
            foreach ( $targets as $target ) {
                if ( isset( $remote_map[ $target ] ) ) {
                    $new_rates[ $target ] = (float) $remote_map[ $target ];
                    $updated = true;
                }
            }
        }
    }

    // Save if changed
    if ( $updated ) {
        $current_options['rates'] = $new_rates;
        $current_options['last_synced'] = time(); // Track last sync
        update_option( 'honeyscroop_option_currency', $current_options );
    }
}
add_action( 'honeyscroop_sync_rates_event', 'honeyscroop_sync_rates_job' );
