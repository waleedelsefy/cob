<?php
/**
 * Link Unit to Project
 *
 * هذا الملف يضيف صندوق ميتا (Meta Box) في شاشة تحرير منشورات النوع "properties"
 * للسماح للمستخدم باختيار مشروع أب (Compound) من قائمة منسدلة.
 * عند حفظ الوحدة يتم تعيين معرف المشروع المحدد كـ post_parent للوحدة.
 *
 * @package Capital_of_Business
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* =====================================================
   إضافة صندوق الميتا (Meta Box) في شاشة تحرير الوحدة
   ===================================================== */

/**
 * يُضيف صندوق ميتا لاختيار المشروع الأب.
 */
function cob_add_propertie_project_metabox() {
    add_meta_box(
        'propertie_project_metabox',
        __( 'Parent Project', 'cob_theme' ),
        'cob_render_propertie_project_metabox',
        'properties',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'cob_add_propertie_project_metabox' );

/**
 * يعرض صندوق الميتا لاختيار المشروع الأب.
 *
 * @param WP_Post $post كائن المنشور الحالي.
 */
function cob_render_propertie_project_metabox( $post ) {
    // الحصول على معرف المشروع الأب الحالي إن وجد.
    $parent_project_id = $post->post_parent;

    // الحصول على جميع المشاريع المنشورة.
    $projects = get_posts( array(
        'post_type'      => 'projects',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    // إضافة حقل nonce للأمان.
    wp_nonce_field( 'cob_save_propertie_project', 'cob_propertie_project_nonce' );

    // عرض القائمة المنسدلة لاختيار المشروع الأب.
    echo '<label for="cob_parent_project">' . esc_html__( 'Select Parent Project:', 'cob_theme' ) . '</label>';
    echo '<select name="cob_parent_project" id="cob_parent_project" style="width:100%;">';
    echo '<option value="0">' . esc_html__( '-- None --', 'cob_theme' ) . '</option>';
    foreach ( $projects as $project ) {
        printf(
            '<option value="%d" %s>%s</option>',
            esc_attr( $project->ID ),
            selected( $parent_project_id, $project->ID, false ),
            esc_html( $project->post_title )
        );
    }
    echo '</select>';
}

/* =====================================================
   إضافة عمود وحقول تحرير سريع / جماعي (Bulk / Quick Edit)
   ===================================================== */

/**
 * إضافة عمود مخصص في قائمة الوحدات لعرض المشروع الأب (Compound).
 *
 * @param array $columns الأعمدة الحالية.
 * @return array الأعمدة بعد التعديل.
 */
function cob_properties_columns( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        // إدراج العمود بعد عمود العنوان.
        if ( 'title' === $key ) {
            $new_columns['parent_project'] = __( 'Compound', 'cob_theme' );
        }
    }
    return $new_columns;
}
add_filter( 'manage_properties_posts_columns', 'cob_properties_columns' );

/**
 * إخراج محتوى العمود المخصص للمشروع الأب.
 *
 * @param string $column  اسم العمود.
 * @param int    $post_id معرف المنشور.
 */
function cob_properties_custom_column( $column, $post_id ) {
    if ( 'parent_project' === $column ) {
        $parent_id = (int) get_post_field( 'post_parent', $post_id );
        if ( $parent_id ) {
            echo esc_html( get_the_title( $parent_id ) );
            // إخراج عنصر مخفي يحتوي على معرف المشروع للأستخدام في التحرير السريع.
            echo '<span class="hidden quick-parent-id" style="display:none;">' . esc_html( $parent_id ) . '</span>';
        } else {
            echo __( 'None', 'cob_theme' );
            echo '<span class="hidden quick-parent-id" style="display:none;">0</span>';
        }
    }
}
add_action( 'manage_properties_posts_custom_column', 'cob_properties_custom_column', 10, 2 );

/**
 * إضافة حقل تخصيص التحرير الجماعي (Bulk Edit) لاختيار المشروع الأب.
 *
 * @param string $column_name اسم العمود.
 * @param string $post_type   نوع المنشور.
 */
function cob_bulk_edit_custom_box( $column_name, $post_type ) {
    if ( 'parent_project' !== $column_name || 'properties' !== $post_type ) {
        return;
    }
    // الحصول على جميع المشاريع المنشورة.
    $projects = get_posts( array(
        'post_type'      => 'projects',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <fieldset class="inline-edit-col-right">
        <div class="inline-edit-col">
            <label>
                <span class="title"><?php esc_html_e( 'Parent Project', 'cob_theme' ); ?></span>
                <span class="input-text-wrap">
                    <select name="bulk_parent_project">
                        <option value=""><?php esc_html_e( '-- No Change --', 'cob_theme' ); ?></option>
                        <?php foreach ( $projects as $project ) : ?>
                            <option value="<?php echo esc_attr( $project->ID ); ?>">
                                <?php echo esc_html( $project->post_title ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </span>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action( 'bulk_edit_custom_box', 'cob_bulk_edit_custom_box', 10, 2 );

/**
 * إضافة حقل التحرير السريع (Quick Edit) لاختيار المشروع الأب.
 *
 * @param string $column_name اسم العمود.
 * @param string $post_type   نوع المنشور.
 */
function cob_quick_edit_custom_box( $column_name, $post_type ) {
    if ( 'parent_project' !== $column_name || 'properties' !== $post_type ) {
        return;
    }
    // الحصول على جميع المشاريع المنشورة.
    $projects = get_posts( array(
        'post_type'      => 'projects',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
    ?>
    <fieldset class="inline-edit-col-left inline-edit-custom">
        <div class="inline-edit-col">
            <label class="alignleft">
                <span class="title"><?php esc_html_e( 'Parent Project', 'cob_theme' ); ?></span>
                <span class="input-text-wrap">
                    <select name="quick_parent_project">
                        <option value=""><?php esc_html_e( '-- No Change --', 'cob_theme' ); ?></option>
                        <?php foreach ( $projects as $project ) : ?>
                            <option value="<?php echo esc_attr( $project->ID ); ?>">
                                <?php echo esc_html( $project->post_title ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </span>
            </label>
        </div>
    </fieldset>
    <?php
}
add_action( 'quick_edit_custom_box', 'cob_quick_edit_custom_box', 10, 2 );

/* =====================================================
   وظيفة الحفظ الموحدة لتحديث parent (المشروع الأب) للوحدة
   ===================================================== */

/**
 * دالة حفظ موحدة لتحديث حقل post_parent للوحدة.
 *
 * تُعالج الحقول المرسلة من شاشة تحرير الوحدة (الميتا بوكس) أو من التحرير الجماعي/السريع.
 *
 * @param int $post_id معرف المنشور (الوحدة).
 */
function cob_save_propertie_parent( $post_id ) {
    // التأكد من أن نوع المنشور هو "properties".
    if ( 'properties' !== get_post_type( $post_id ) ) {
        return;
    }
    // عدم التنفيذ أثناء الحفظ التلقائي.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // التحقق من صلاحيات المستخدم.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // استخدام متغير ثابت لمنع التكرار (حلقة لا نهائية).
    static $is_updating = false;
    if ( $is_updating ) {
        return;
    }
    $is_updating = true;

    $current_parent = (int) get_post_field( 'post_parent', $post_id );
    $new_parent     = null;

    // التحقق من وجود حقل التحرير الجماعي.
    if ( isset( $_REQUEST['bulk_parent_project'] ) && '' !== $_REQUEST['bulk_parent_project'] ) {
        $new_parent = intval( sanitize_text_field( $_REQUEST['bulk_parent_project'] ) );
    }
    // التحقق من حقل التحرير السريع.
    elseif ( isset( $_REQUEST['quick_parent_project'] ) && '' !== $_REQUEST['quick_parent_project'] ) {
        $new_parent = intval( sanitize_text_field( $_REQUEST['quick_parent_project'] ) );
    }
    // وإلا التحقق من حقل الميتا (من شاشة تحرير الوحدة المفردة).
    elseif ( isset( $_POST['cob_propertie_project_nonce'] ) ) {
        // التحقق من صحة nonce.
        if ( ! wp_verify_nonce( $_POST['cob_propertie_project_nonce'], 'cob_save_propertie_project' ) ) {
            $is_updating = false;
            return;
        }
        $new_parent = isset( $_POST['cob_parent_project'] ) ? intval( $_POST['cob_parent_project'] ) : 0;
    }

    // إذا كان هناك تغيير في قيمة المشروع الأب، نقوم بتحديث الحقل.
    if ( ! is_null( $new_parent ) && $new_parent !== $current_parent ) {
        wp_update_post( array(
            'ID'          => $post_id,
            'post_parent' => $new_parent,
        ) );
    }

    $is_updating = false;
}
add_action( 'save_post_properties', 'cob_save_propertie_parent' );

/* =====================================================
   تحميل سكريبتات وإطارات CSS الخاصة بـ Select2 في شاشة إدارة "properties"
   ===================================================== */

/**
 * تحميل سكريبتات Select2 والسكريبتات المخصصة لشاشة تحرير الوحدات (Bulk/Quick Edit).
 *
 * @param string $hook صفحة الإدارة الحالية.
 */
function cob_enqueue_admin_scripts_bulk_and_quick_edit( $hook ) {
    global $pagenow;
    if ( $pagenow === 'edit.php' && isset( $_GET['post_type'] ) && 'properties' === $_GET['post_type'] ) {
        wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), '4.0.13', true );
        wp_enqueue_style( 'select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', array(), '4.0.13' );
        wp_enqueue_script( 'cob-bulk-quick-edit', get_template_directory_uri() . '/admin/js/cob-bulk-quick-edit.js', array( 'jquery', 'select2' ), '1.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'cob_enqueue_admin_scripts_bulk_and_quick_edit' );
?>
