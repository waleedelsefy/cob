<?php
if ( ! class_exists( 'Cob_Walker_Nav_Menu' ) ) {
    class Cob_Walker_Nav_Menu extends Walker_Nav_Menu {

        /**
         * Start Level.
         *
         * @param string $output Used to append additional content (passed by reference).
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   An array of arguments.
         */
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat( "\t", $depth );
            $output .= "\n$indent<ul class=\"dropdown\">\n";
        }

        /**
         * End Level.
         *
         * @param string $output Used to append additional content (passed by reference).
         * @param int    $depth  Depth of menu item.
         * @param array  $args   An array of arguments.
         */
        public function end_lvl( &$output, $depth = 0, $args = array() ) {
            $indent = str_repeat( "\t", $depth );
            $output .= "$indent</ul>\n";
        }

        /**
         * Start Element.
         *
         * @param string $output Used to append additional content (passed by reference).
         * @param object $item   Menu item data object.
         * @param int    $depth  Depth of menu item.
         * @param array  $args   An array of arguments.
         * @param int    $id     Current item ID.
         */
        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

            // Get the list of classes for this item.
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;

            // If the item has children, add a specific class.
            if ( in_array( 'menu-item-has-children', $classes ) ) {
                $classes[] = 'dropdown-container';
            }

            $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            $output .= $indent . '<li' . $class_names . '>';

            // Set up the attributes for the <a> element.
            $atts = array();
            $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
            $atts['target'] = ! empty( $item->target ) ? $item->target : '';
            $atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
            $atts['href']   = ! empty( $item->url ) ? $item->url : '';

            // Optionally, if the item has children, you might force the link to "#".
            if ( in_array( 'menu-item-has-children', $item->classes ) ) {
                $atts['href'] = '#';
            }

            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            // Build the output for the menu item.
            $item_output  = $args->before;
            $item_output .= '<a' . $attributes . ' class="mazed">';
            $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

            // If the item has children, append the SVG icon.
            if ( in_array( 'menu-item-has-children', $item->classes ) ) {
                $item_output .= ' <svg class="svg-inline--fa fa-angle-down fa-w-10 fa-lg" aria-hidden="true" data-prefix="fas" data-icon="angle-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z"></path></svg>';
            }
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }

        /**
         * End Element.
         *
         * @param string $output Used to append additional content (passed by reference).
         * @param object $item   Page data object. Not used.
         * @param int    $depth  Depth of page. Not Used.
         * @param array  $args   An array of arguments.
         */
        public function end_el( &$output, $item, $depth = 0, $args = array() ) {
            $output .= "</li>\n";
        }
    }
}
