jQuery(document).ready(function($) {
    // Initialize Select2 for the Bulk Edit form when it is opened.
    $(document).on('click', '#bulk_edit', function() {
        setTimeout(function() {
            $('select[name="bulk_parent_project"]').select2({
                placeholder: '-- No Change --',
                allowClear: true
            });
        }, 100);
    });

    // Extend the inline edit (Quick Edit) functionality.
    var $wp_inline_edit = inlineEditPost.edit;
    inlineEditPost.edit = function( id ) {
        $wp_inline_edit.apply( this, arguments );
        var post_id = 0;
        if ( typeof(id) === 'object' ) {
            post_id = parseInt( this.getId( id ) );
        }
        if ( post_id > 0 ) {
            var $edit_row = $('#edit-' + post_id);
            // Retrieve the current parent project ID from the hidden span in the row.
            var parent_id = $('.quick-parent-id', '#post-' + post_id).text();
            $edit_row.find('select[name="quick_parent_project"]').val( parent_id );
            // Initialize Select2 on the quick edit dropdown.
            if ( $.fn.select2 ) {
                $edit_row.find('select[name="quick_parent_project"]').select2({
                    placeholder: '-- No Change --',
                    allowClear: true
                });
            }
        }
    };
});
