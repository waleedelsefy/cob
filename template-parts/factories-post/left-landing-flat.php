<div class="left-landing-flat">
    <div class="tabs-sec">
        <button class="flatTab active" data-tab="meeting"><?php esc_html_e( 'Schedule Appointment', 'cob_theme' ); ?></button>
        <button class="flatTab" data-tab="service"><?php esc_html_e( 'Live Meeting', 'cob_theme' ); ?></button>
    </div>
    <div class="flatTab-content active" id="meeting">
        <label><?php esc_html_e( 'Full Name', 'cob_theme' ); ?></label>
        <input type="text" placeholder="<?php esc_attr_e( 'Full Name', 'cob_theme' ); ?>" />
        <div class="date-picker">
            <label><?php esc_html_e( 'Select Date', 'cob_theme' ); ?></label>
            <!-- Swiper -->
            <div class="swiper date-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Sunday', 'cob_theme' ); ?></p>
                            <p>07</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Monday', 'cob_theme' ); ?></p>
                            <p>08</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Tuesday', 'cob_theme' ); ?></p>
                            <p>09</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Wednesday', 'cob_theme' ); ?></p>
                            <p>10</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Thursday', 'cob_theme' ); ?></p>
                            <p>11</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Friday', 'cob_theme' ); ?></p>
                            <p>12</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                    <div class="swiper-slide">
                        <button class="date-btn">
                            <p><?php esc_html_e( 'Saturday', 'cob_theme' ); ?></p>
                            <p>13</p>
                            <p><?php esc_html_e( 'October', 'cob_theme' ); ?></p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="time-picker">
            <label><?php esc_html_e( 'Select Time', 'cob_theme' ); ?></label>
            <select>
                <option>10:00 AM</option>
                <option>12:00 PM</option>
                <option>02:00 PM</option>
            </select>
        </div>
        <button class="confirm"><?php esc_html_e( 'Confirm Booking', 'cob_theme' ); ?></button>
        <span><?php esc_html_e( 'Book your meeting now', 'cob_theme' ); ?></span>
    </div>
    <div class="flatTab-content" id="service">
        <label><?php esc_html_e( 'Full Name', 'cob_theme' ); ?></label>
        <input type="text" placeholder="<?php esc_attr_e( 'Full Name', 'cob_theme' ); ?>" />
        <label><?php esc_html_e( 'Enter Phone Number', 'cob_theme' ); ?></label>
        <input type="text" placeholder="<?php esc_attr_e( 'Phone Number', 'cob_theme' ); ?>" />
        <button class="confirm"><?php esc_html_e( 'Create Meeting', 'cob_theme' ); ?></button>
        <span><?php esc_html_e( 'Book your meeting now', 'cob_theme' ); ?></span>
    </div>
</div>
