<?php
/**
 * Template Name: Zoom Booking AJAX
 */
?>

<div class="left-landing-flat">

    <div class="tabs-sec">
        <button class="flatTab active" data-tab="meeting"><?php esc_html_e( 'Schedule Appointment', 'cob_theme' ); ?></button>
        <button class="flatTab" data-tab="service"><?php esc_html_e( 'Live Meeting', 'cob_theme' ); ?></button>
    </div>

    <!-- Scheduled Meeting Form -->
    <form id="meeting" class="flatTab-content active ajax-zoom-form">
        <label for="zm_name_meeting"><?php esc_html_e( 'Full Name', 'cob_theme' ); ?></label>
        <input type="text" name="zm_name" id="zm_name_meeting" class="flat-input" required />

        <div class="date-picker">
            <label for="zm_date"><?php esc_html_e( 'Select Date', 'cob_theme' ); ?></label>
            <div class="swiper date-swiper"><div class="swiper-wrapper">
                    <?php for ( $i = 0; $i < 7; $i++ ):
                        $ts  = strtotime("+{$i} day");
                        $iso = date( 'Y-m-d', $ts );
                        ?>
                        <div class="swiper-slide">
                            <button type="button" class="date-btn" data-date="<?php echo esc_attr( $iso ); ?>">
                                <p><?php echo esc_html( date_i18n( 'l', $ts ) ); ?></p>
                                <p><?php echo esc_html( date_i18n( 'd', $ts ) ); ?></p>
                                <p><?php echo esc_html( date_i18n( 'F', $ts ) ); ?></p>
                            </button>
                        </div>
                    <?php endfor; ?>
                </div></div>
            <input type="hidden" name="zm_date" id="zm_date" required>
        </div>

        <div class="time-picker">
            <label for="zm_time_meeting"><?php esc_html_e( 'Select Time', 'cob_theme' ); ?></label>
            <select name="zm_time" id="zm_time_meeting" class="flat-select" required>
                <option value="10:00">10:00 AM</option>
                <option value="12:00">12:00 PM</option>
                <option value="14:00">02:00 PM</option>
            </select>
        </div>

        <label for="zm_participant_email_meeting"><?php esc_html_e( 'Participant Email', 'cob_theme' ); ?></label>
        <input type="email" name="zm_participant_email" id="zm_participant_email_meeting" class="flat-input" required />

        <button type="submit" class="confirm"><?php esc_html_e( 'Confirm Booking', 'cob_theme' ); ?></button>
        <span class="zoom-response"></span>
    </form>

    <!-- Instant Meeting Form -->
    <form id="service" class="flatTab-content ajax-zoom-form">
        <label for="zm_name_service"><?php esc_html_e( 'Full Name', 'cob_theme' ); ?></label>
        <input type="text" name="zm_name" id="zm_name_service" class="flat-input" required />

        <label for="zm_phone_service"><?php esc_html_e( 'Enter Phone Number', 'cob_theme' ); ?></label>
        <input type="text" name="zm_phone" id="zm_phone_service" class="flat-input" required />

        <button type="submit" class="confirm"><?php esc_html_e( 'Create Meeting', 'cob_theme' ); ?></button>
        <span class="zoom-response"></span>
    </form>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Date picker
        document.querySelectorAll('.date-btn').forEach(btn => {
            btn.addEventListener('click', function(e){
                e.preventDefault();
                const zmDateInput = document.getElementById('zm_date');
                if (zmDateInput) {
                    zmDateInput.value = this.dataset.date;
                }
                document.querySelectorAll('.date-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Tabs switch
        document.querySelectorAll('.flatTab').forEach(tab => {
            tab.addEventListener('click', function(e){
                e.preventDefault();
                document.querySelectorAll('.flatTab').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.flatTab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                const content = document.getElementById(this.dataset.tab);
                if (content) content.classList.add('active');
            });
        });

        // Initialize Swiper if available
        if (typeof Swiper !== 'undefined') {
            new Swiper('.date-swiper', {
                slidesPerView: 4,
                spaceBetween: 10,
                freeMode: true
            });
        }

        // AJAX form submission
        document.querySelectorAll('.ajax-zoom-form').forEach(form => {
            form.addEventListener('submit', function(e){
                e.preventDefault();

                const data = new FormData(form);
                const isScheduled = form.id === 'meeting';
                const action = isScheduled
                    ? 'schedule_zoom_meeting_ajax'
                    : 'create_zoom_live_meeting_ajax';
                data.append('action', action);

                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.textContent;
                btn.disabled = true;
                btn.textContent = '<?php esc_html_e("Please wait...", "cob_theme"); ?>';

                const responseSpan = form.querySelector('.zoom-response');
                if (responseSpan) responseSpan.textContent = '';

                fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: data
                })
                    .then(r => {
                        if (!r.ok) throw new Error(`HTTP ${r.status}`);
                        return r.json();
                    })
                    .then(json => {
                        btn.disabled = false;
                        btn.textContent = originalText;

                        if (json.success) {
                            // Hide the form
                            form.style.display = 'none';

                            // Shorten URL via is.gd
                            const longUrl = json.data.join_url;
                            fetch(`https://is.gd/create.php?format=simple&url=${encodeURIComponent(longUrl)}`)
                                .then(r => {
                                    if (!r.ok) throw new Error(`Shortener HTTP ${r.status}`);
                                    return r.text();
                                })
                                .then(shortUrl => {
                                    // Create result box
                                    const box = document.createElement('div');
                                    box.className = 'zoom-result-box';
                                    box.innerHTML = `
                <label for="zoom-link-${form.id}"><?php esc_html_e("Your Meeting Link", "cob_theme"); ?></label>
                <div class="copy-box">
                  <input type="text" id="zoom-link-${form.id}" class="flat-input" readonly value="${shortUrl}" />
                  <button type="button" class="btn-copy"><?php esc_html_e("Copy", "cob_theme"); ?></button>
                </div>
                <a href="#" target="_blank" class="add-calendar btn"><?php esc_html_e("Add to Google Calendar", "cob_theme"); ?></a>
              `;
                                    form.parentNode.appendChild(box);

                                    // Calendar link
                                    const start = new Date(json.data.start_time);
                                    const end = new Date(start.getTime() + (json.data.duration||60)*60000);
                                    const fmt = d => d.toISOString().replace(/-|:|\.\d+/g,'');
                                    const dates = `${fmt(start)}/${fmt(end)}`;
                                    const topic = encodeURIComponent(json.data.topic || 'Zoom Meeting');
                                    const gcalUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE`
                                        + `&text=${topic}`
                                        + `&dates=${dates}`
                                        + `&details=${encodeURIComponent(shortUrl)}`;
                                    box.querySelector('.add-calendar').href = gcalUrl;

                                    // Copy button handler
                                    const copyBtn = box.querySelector('.btn-copy');
                                    const linkInput = box.querySelector('input');
                                    copyBtn.addEventListener('click', () => {
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText(linkInput.value).then(() => {
                                                copyBtn.textContent = '<?php esc_html_e("Copied!", "cob_theme"); ?>';
                                                setTimeout(() => copyBtn.textContent = '<?php esc_html_e("Copy", "cob_theme"); ?>', 2000);
                                            }).catch(() => {
                                                linkInput.select();
                                                document.execCommand('copy');
                                                copyBtn.textContent = '<?php esc_html_e("Copied!", "cob_theme"); ?>';
                                                setTimeout(() => copyBtn.textContent = '<?php esc_html_e("Copy", "cob_theme"); ?>', 2000);
                                            });
                                        } else {
                                            linkInput.select();
                                            document.execCommand('copy');
                                            copyBtn.textContent = '<?php esc_html_e("Copied!", "cob_theme"); ?>';
                                            setTimeout(() => copyBtn.textContent = '<?php esc_html_e("Copy", "cob_theme"); ?>', 2000);
                                        }
                                    });
                                })
                                .catch(err => {
                                    console.error('URL shortening failed:', err);
                                    if (responseSpan) responseSpan.textContent = '<?php esc_html_e("Could not shorten URL. Showing original.", "cob_theme"); ?>';
                                    // Show original link as fallback
                                    const fallbackBox = document.createElement('div');
                                    fallbackBox.className = 'zoom-result-box';
                                    fallbackBox.innerHTML = `
                <label for="zoom-link-fb-${form.id}"><?php esc_html_e("Your Meeting Link", "cob_theme"); ?></label>
                <div class="copy-box">
                  <input type="text" id="zoom-link-fb-${form.id}" class="flat-input" readonly value="${longUrl}" />
                  <button type="button" class="btn-copy"><?php esc_html_e("Copy", "cob_theme"); ?></button>
                </div>
              `;
                                    form.parentNode.appendChild(fallbackBox);
                                });

                        } else {
                            if (responseSpan) responseSpan.textContent = json.data || '<?php esc_html_e("An error occurred.", "cob_theme"); ?>';
                        }
                    })
                    .catch(err => {
                        console.error('AJAX error:', err);
                        btn.disabled = false;
                        btn.textContent = originalText;
                        if (responseSpan) responseSpan.textContent = '<?php esc_html_e("Please try again.", "cob_theme"); ?>';
                    });
            });
        });
    });
</script>

