<?php
/**
 * Capital of Business Theme Functions
 *
 * @package Capital_of_Business
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
/**
 * contact form submission
 */
function add_contact_entries_menu() {
    add_menu_page(
        __( 'Contact Entries', 'cob_theme' ),
        __( 'Contact Entries', 'cob_theme' ),
        'manage_options',
        'contact-entries',
        'display_contact_entries_page',
        'dashicons-email',
        6
    );
}
add_action( 'admin_menu', 'add_contact_entries_menu' );

function display_contact_entries_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_entries';
    $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY date DESC" );
    ?>
    <div class="wrap">
        <h1><?php _e( 'Contact Entries', 'cob_theme' ); ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th><?php _e( 'ID', 'cob_theme' ); ?></th>
                <th><?php _e( 'Name', 'cob_theme' ); ?></th>
                <th><?php _e( 'Phone', 'cob_theme' ); ?></th>
                <th><?php _e( 'Email', 'cob_theme' ); ?></th>
                <th><?php _e( 'Message', 'cob_theme' ); ?></th>
                <th><?php _e( 'Date', 'cob_theme' ); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php if ( $results ) : ?>
                <?php foreach ( $results as $entry ) : ?>
                    <tr>
                        <td><?php echo esc_html( $entry->id ); ?></td>
                        <td><?php echo esc_html( $entry->name ); ?></td>
                        <td><?php echo esc_html( $entry->phone ); ?></td>
                        <td><?php echo esc_html( $entry->email ); ?></td>
                        <td><?php echo esc_html( $entry->message ); ?></td>
                        <td><?php echo esc_html( $entry->date ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php _e( 'No entries found', 'cob_theme' ); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
