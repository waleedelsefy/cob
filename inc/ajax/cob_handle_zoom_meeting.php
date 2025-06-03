<?php
use Firebase\JWT\JWT;

// إنشاء الجدول عند تفعيل الثيم
add_action('after_switch_theme','cob_create_zoom_table');
function cob_create_zoom_table(){
    global $wpdb;
    $table = $wpdb->prefix . 'zoom_meetings';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE {$table} (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      meeting_id VARCHAR(64) NOT NULL,
      topic TEXT NOT NULL,
      start_time DATETIME NOT NULL,
      duration INT NOT NULL,
      join_url TEXT NOT NULL,
      password VARCHAR(32) NULL,
      participant_email VARCHAR(100) NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY meeting_id (meeting_id)
    ) {$charset};";
    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// AJAX hooks
add_action('wp_ajax_schedule_zoom_meeting_ajax',        'cob_handle_zoom_meeting_ajax');
add_action('wp_ajax_nopriv_schedule_zoom_meeting_ajax', 'cob_handle_zoom_meeting_ajax');
add_action('wp_ajax_create_zoom_live_meeting_ajax',        'cob_handle_zoom_live_meeting_ajax');
add_action('wp_ajax_nopriv_create_zoom_live_meeting_ajax', 'cob_handle_zoom_live_meeting_ajax');

// Scheduled meeting handler
function cob_handle_zoom_meeting_ajax(){
    $name  = sanitize_text_field($_POST['zm_name']??'');
    $date  = sanitize_text_field($_POST['zm_date']??'');
    $time  = sanitize_text_field($_POST['zm_time']??'');
    $email = sanitize_email($_POST['zm_participant_email']??'');
    if(!$name||!$date||!$time||!is_email($email)){
        wp_send_json_error(__('Invalid input','cob_theme'));
    }
    $start = gmdate("Y-m-d\TH:i:00\Z",strtotime("$date $time"));
    $mt    = cob_zoom_request([
        'topic'=>"Meeting with {$name}",
        'type'=>2,
        'start_time'=>$start,
        'duration'=>60,
        'settings'=>['join_before_host'=>true,'mute_upon_entry'=>true]
    ]);
    if(empty($mt['id'])){
        wp_send_json_error(__('Zoom API error','cob_theme'));
    }
    // حفظ بالقاعدة
    global $wpdb;
    $table = $wpdb->prefix.'zoom_meetings';
    $wpdb->insert($table,[
        'meeting_id'=>$mt['id'],
        'topic'=>$mt['topic'],
        'start_time'=>date('Y-m-d H:i:s',strtotime($mt['start_time'])),
        'duration'=>$mt['duration'],
        'join_url'=>$mt['join_url'],
        'password'=>$mt['password'],
        'participant_email'=>$email
    ]);
    cob_send_emails($mt,$email);
    wp_send_json_success([
        'join_url'=>$mt['join_url'],
        'password'=>$mt['password'],
        'start_time'=>$mt['start_time'],
        'topic'=>$mt['topic'],
        'duration'=>$mt['duration'],
    ]);
}

// Instant meeting handler
function cob_handle_zoom_live_meeting_ajax(){
    $name  = sanitize_text_field($_POST['zm_name']??'');
    $phone = sanitize_text_field($_POST['zm_phone']??'');
    if(!$name||!$phone){
        wp_send_json_error(__('Invalid input','cob_theme'));
    }
    $mt = cob_zoom_request([
        'topic'=>"Live Call: {$name}",
        'type'=>1,
        'settings'=>['join_before_host'=>true,'mute_upon_entry'=>true]
    ]);
    if(empty($mt['id'])){
        wp_send_json_error(__('Zoom API error','cob_theme'));
    }
    // حفظ بالقاعدة
    global $wpdb;
    $table = $wpdb->prefix.'zoom_meetings';
    $wpdb->insert($table,[
        'meeting_id'=>$mt['id'],
        'topic'=>$mt['topic'],
        'start_time'=>date('Y-m-d H:i:s',strtotime($mt['start_time'])),
        'duration'=>$mt['duration'] ?? 0,
        'join_url'=>$mt['join_url'],
        'password'=>$mt['password'],
        'participant_email'=>wp_get_current_user()->user_email
    ]);
    cob_send_emails($mt, wp_get_current_user()->user_email);
    wp_send_json_success([
        'join_url'=>$mt['join_url'],
        'password'=>$mt['password'],
        'start_time'=>$mt['start_time'],
        'topic'=>$mt['topic'],
        'duration'=>$mt['duration'],
    ]);
}

// Send emails
function cob_send_emails($data,$participant){
    $join    = esc_url($data['join_url']);
    $pwd     = esc_html($data['password']);
    $topic   = esc_html($data['topic']);
    $sub     = __('Your Zoom Meeting Details','cob_theme');
    $hdrs    = ['Content-Type'=>'text/html; charset=UTF-8'];
    $host    = wp_get_current_user()->user_email;
    $msg = "
      <div class='left-landing-flat'>
        <h2 class='email-title'>{$topic}</h2>
        <p class='email-text'>" . __('Join URL:','cob_theme') . " <a href='{$join}' class='email-link'>{$join}</a></p>
        <p class='email-text'>" . __('Password:','cob_theme') . " <strong>{$pwd}</strong></p>
      </div>";
    foreach([$host,$participant] as $to){
        wp_mail($to,$sub,$msg,$hdrs);
    }
}

// Zoom API request
function cob_zoom_request($body){
    $token = cob_get_zoom_oauth_token();
    $resp  = wp_remote_post("https://api.zoom.us/v2/users/me/meetings",[
        'headers'=>[
            'Authorization'=>"Bearer {$token}",
            'Content-Type'=>'application/json'
        ],
        'body'=>wp_json_encode($body)
    ]);
    if(is_wp_error($resp)) return [];
    return json_decode(wp_remote_retrieve_body($resp),true);
}

// OAuth token
function cob_get_zoom_oauth_token(){
    $key = 'cob_zoom_oauth_token';
    if($t=get_transient($key)) return $t;
    $id     = 'CxESA4jKSxiXkp_m7Yp_zg';
    $secret = 'krg6rhGRSDTV2FeljUZHHByBp26Gib6a';
    $acct   = '8JY4L6HSRciV1ezn9Vtxow';
    $url    = "https://zoom.us/oauth/token?grant_type=account_credentials&account_id={$acct}";
    $hdr    = [
        'Authorization'=>'Basic '.base64_encode("{$id}:{$secret}"),
        'Content-Type'=>'application/json'
    ];
    $r = wp_remote_post($url,['headers'=>$hdr]);
    if(is_wp_error($r)) wp_die($r->get_error_message());
    $b = json_decode(wp_remote_retrieve_body($r),true);
    if(empty($b['access_token'])) wp_die(__('Failed to obtain token','cob_theme'));
    set_transient($key,$b['access_token'],50*MINUTE_IN_SECONDS);
    return $b['access_token'];
}

// Admin menu to list meetings
add_action('admin_menu','cob_zoom_admin_menu');
function cob_zoom_admin_menu(){
    add_menu_page(
        __('Zoom Meetings','cob_theme'),
        __('Zoom Meetings','cob_theme'),
        'manage_options',
        'cob_zoom_meetings',
        'cob_zoom_meetings_page',
        'dashicons-video-alt3',
        6
    );
}

function cob_zoom_meetings_page(){
    global $wpdb;
    $table = $wpdb->prefix . 'zoom_meetings';
    $rows  = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC");

    // تحقق من عملية الإلغاء إن وُجِدت في الـ URL
    if ( isset($_GET['action'], $_GET['meeting_id'], $_GET['_wpnonce'])
        && $_GET['action']==='cancel'
        && wp_verify_nonce($_GET['_wpnonce'],'cob_cancel_'.$_GET['meeting_id'])
    ) {
        cob_cancel_zoom_meeting( sanitize_text_field($_GET['meeting_id']) );
        echo '<div class="notice notice-success"><p>'.__('Meeting canceled.','cob_theme').'</p></div>';
        // إعادة تحميل الصفحة بدون باراميترات
        $redirect = remove_query_arg(['action','meeting_id','_wpnonce']);
        echo '<script>location.href="'.esc_url($redirect).'";</script>';
        return;
    }

    echo '<div class="wrap"><h1>'.__('Zoom Meetings','cob_theme').'</h1>';
    echo '<table class="widefat fixed striped"><thead><tr>
      <th>'.__('ID','cob_theme').'</th>
      <th>'.__('Topic','cob_theme').'</th>
      <th>'.__('Start Time','cob_theme').'</th>
      <th>'.__('Participant','cob_theme').'</th>
      <th>'.__('Actions','cob_theme').'</th>
    </tr></thead><tbody>';

    foreach($rows as $r){
        $nonce = wp_create_nonce('cob_cancel_'.$r->meeting_id);
        // رابط الإلغاء
        $cancel_url = add_query_arg([
            'page'       => 'cob_zoom_meetings',
            'action'     => 'cancel',
            'meeting_id' => $r->meeting_id,
            '_wpnonce'   => $nonce,
        ], admin_url('admin.php'));
        printf("<tr>
          <td>%d</td>
          <td>%s</td>
          <td>%s</td>
          <td>%s</td>
          <td>
            <a href='%s' class='button button-primary' target='_blank'>%s</a>
            <a href='%s' class='button button-secondary' onclick='return confirm(\"%s\");'>%s</a>
          </td>
        </tr>",
            $r->id,
            esc_html($r->topic),
            esc_html($r->start_time),
            esc_html($r->participant_email),
            esc_url($r->join_url),
            __('Join','cob_theme'),
            esc_url($cancel_url),
            esc_js(__('Are you sure you want to cancel this meeting?','cob_theme')),
            __('Cancel','cob_theme')
        );
    }

    echo '</tbody></table></div>';
}
/**
 * Cancel Zoom meeting by ID and remove from DB
 */
function cob_cancel_zoom_meeting( $meeting_id ) {
    $token = cob_get_zoom_oauth_token();
    $url   = "https://api.zoom.us/v2/meetings/{$meeting_id}";
    wp_remote_request( $url, [
        'method'  => 'DELETE',
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
        ],
    ] );

    global $wpdb;
    $table = $wpdb->prefix . 'zoom_meetings';
    $wpdb->delete( $table, [ 'meeting_id' => $meeting_id ], [ '%s' ] );
}
