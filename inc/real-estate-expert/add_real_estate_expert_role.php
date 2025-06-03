<?php
function add_real_estate_expert_role() {
    if ( ! get_role( 'real_estate_expert' ) ) {
        add_role(
            'real_estate_expert',
            __( 'Real Estate Expert', 'cob_theme' ),
            array(
                'read'         => true,
                'edit_posts'   => false,
                'delete_posts' => false,
            )
        );
    }
}
add_action( 'init', 'add_real_estate_expert_role' );
