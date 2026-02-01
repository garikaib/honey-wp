<?php
/**
 * Custom Meta Boxes for Events
 *
 * @package HoneyScroop_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add Meta Box
 */
function honeyscroop_add_event_meta_box() {
    add_meta_box(
        'honeyscroop_event_details',
        __( 'Event Details', 'honeyscroop' ),
        'honeyscroop_render_event_meta_box',
        'event',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'honeyscroop_add_event_meta_box' );

/**
 * Render Meta Box
 *
 * @param WP_Post $post Current post object.
 */
function honeyscroop_render_event_meta_box( $post ) {
    // Nonce field for verification
    wp_nonce_field( 'honeyscroop_save_event_meta', 'honeyscroop_event_meta_nonce' );

    // Get existing values
    $start_date = get_post_meta( $post->ID, 'event_start_date', true );
    $location   = get_post_meta( $post->ID, 'event_location', true );
    $price      = get_post_meta( $post->ID, 'event_price', true );

    // Convert date for datetime-local input (YYYY-MM-DDTHH:MM)
    $start_date_val = '';
    if ( $start_date ) {
        $start_date_val = date( 'Y-m-d\TH:i', strtotime( $start_date ) );
    }

    ?>
    <div style="margin-bottom: 1em;">
        <label for="event_start_date" style="display:block; margin-bottom: 5px; font-weight: 600;">
            <?php esc_html_e( 'Start Date & Time', 'honeyscroop' ); ?>
        </label>
        <input type="datetime-local" id="event_start_date" name="event_start_date" value="<?php echo esc_attr( $start_date_val ); ?>" style="width: 100%;">
    </div>

    <div style="margin-bottom: 1em;">
        <label for="event_location" style="display:block; margin-bottom: 5px; font-weight: 600;">
            <?php esc_html_e( 'Location', 'honeyscroop' ); ?>
        </label>
        <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr( $location ); ?>" style="width: 100%;" placeholder="e.g. Greendale, Harare">
    </div>

    <div style="margin-bottom: 1em;">
        <label for="event_price" style="display:block; margin-bottom: 5px; font-weight: 600;">
            <?php esc_html_e( 'Price', 'honeyscroop' ); ?>
        </label>
        <input type="text" id="event_price" name="event_price" value="<?php echo esc_attr( $price ); ?>" style="width: 100%;" placeholder="e.g. Free, $10, varies">
    </div>
    <?php
}

/**
 * Save Meta Box Data
 *
 * @param int $post_id Post ID.
 */
function honeyscroop_save_event_meta( $post_id ) {
    // Check nonce
    if ( ! isset( $_POST['honeyscroop_event_meta_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['honeyscroop_event_meta_nonce'], 'honeyscroop_save_event_meta' ) ) {
        return;
    }

    // Auto save check
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Permissions check
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save fields
    if ( isset( $_POST['event_start_date'] ) ) {
        update_post_meta( $post_id, 'event_start_date', sanitize_text_field( $_POST['event_start_date'] ) );
    }

    if ( isset( $_POST['event_location'] ) ) {
        update_post_meta( $post_id, 'event_location', sanitize_text_field( $_POST['event_location'] ) );
    }

    if ( isset( $_POST['event_price'] ) ) {
        update_post_meta( $post_id, 'event_price', sanitize_text_field( $_POST['event_price'] ) );
    }
}
add_action( 'save_post_event', 'honeyscroop_save_event_meta' );
