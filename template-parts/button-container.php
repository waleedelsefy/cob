<?php
$theme_dir = get_template_directory_uri();
?>
<div class="button-container">

    <!-- Call button (already translatable) -->
    <a href="tel:<?php echo esc_attr( get_option( 'company_phone', '0123456789' ) ); ?>" class="button call">
        <svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M7.65923 3.71223L7.25657 2.80625C6.99329 2.21388 6.86165 1.91768 6.66478 1.69101C6.41805 1.40694 6.09644 1.19794 5.73665 1.08785C5.44956 1 5.12543 1 4.47718 1C3.52888 1 3.05473 1 2.6567 1.18229C2.18784 1.39702 1.76441 1.86328 1.5957 2.3506C1.45248 2.76429 1.49351 3.18943 1.57555 4.0397C2.44888 13.0902 7.41079 18.0521 16.4612 18.9254C17.3115 19.0075 17.7367 19.0485 18.1503 18.9053C18.6377 18.7366 19.1039 18.3131 19.3187 17.8443C19.5009 17.4462 19.5009 16.9721 19.5009 16.0238C19.5009 15.3755 19.5009 15.0514 19.4131 14.7643C19.303 14.4045 19.094 14.0829 18.8099 13.8362C18.5833 13.6393 18.2871 13.5077 17.6947 13.2444L16.7887 12.8417C16.1472 12.5566 15.8264 12.4141 15.5005 12.3831C15.1885 12.3534 14.874 12.3972 14.582 12.5109C14.2769 12.6297 14.0073 12.8544 13.4679 13.3038C12.9311 13.7512 12.6627 13.9749 12.3347 14.0947C12.0439 14.2009 11.6595 14.2403 11.3533 14.1951C11.0078 14.1442 10.7433 14.0029 10.2142 13.7201C8.56823 12.8405 7.6605 11.9328 6.78084 10.2867C6.49811 9.7577 6.35675 9.4931 6.30584 9.1477C6.26071 8.8414 6.30005 8.457 6.40627 8.1663C6.52609 7.83828 6.74978 7.56986 7.19716 7.033C7.64659 6.49368 7.87131 6.22402 7.99012 5.91891C8.10382 5.62694 8.14759 5.3124 8.11792 5.00048C8.08691 4.67452 7.94435 4.35376 7.65923 3.71223Z"
                stroke="white" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <span style="color: white">
            <?php esc_html_e( 'Call Us', 'cob_theme' ); ?>
        </span>
    </a>

    <!-- WhatsApp button (already translatable) -->
    <a href="https://api.whatsapp.com/send?phone=2<?php echo esc_attr( get_option( 'company_whatsapp', '0123456789' ) ); ?>" class="button whatsapp">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M12.001 22C17.5238 22 22.001 17.5228 22.001 12C22.001 6.47715 17.5238 2 12.001 2C6.47813 2 2.00098 6.47715 2.00098 12C2.00098 13.3789 2.28005 14.6926 2.7848 15.8877C3.06376 16.5481 3.20324 16.8784 3.22051 17.128C3.23778 17.3776 3.16432 17.6521 3.0174 18.2012L2.00098 22L5.79975 20.9836C6.34886 20.8367 6.62342 20.7632 6.873 20.7805C7.12259 20.7977 7.45283 20.9372 8.11333 21.2162C9.30843 21.7209 10.6221 22 12.001 22Z"
                stroke="white" stroke-width="1.5" stroke-linejoin="round" />
            <path
                d="M8.58815 12.3773L9.45909 11.2956C9.82616 10.8397 10.2799 10.4153 10.3155 9.80826C10.3244 9.65494 10.2166 8.96657 10.0008 7.58986C9.91601 7.04881 9.41086 7 8.97332 7C8.40314 7 8.11805 7 7.83495 7.12931C7.47714 7.29275 7.10979 7.75231 7.02917 8.13733C6.96539 8.44196 7.01279 8.65187 7.10759 9.07169C7.51023 10.8548 8.45481 12.6158 9.91948 14.0805C11.3842 15.5452 13.1452 16.4898 14.9283 16.8924C15.3481 16.9872 15.558 17.0346 15.8627 16.9708C16.2477 16.8902 16.7072 16.5229 16.8707 16.165C17 15.8819 17 15.5969 17 15.0267C17 14.5891 16.9512 14.084 16.4101 13.9992C15.0334 13.7834 14.3451 13.6756 14.1917 13.6845C13.5847 13.7201 13.1603 14.1738 12.7044 14.5409L11.6227 15.4118"
                stroke="white" stroke-width="1.5" />
        </svg>
        <span style="color: white">
            <?php esc_html_e( 'WhatsApp', 'cob_theme' ); ?>
        </span>
    </a>

    <!-- Zoom button -->
    <button class="button zoom" id="togglePopupZoom">
        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.5 8H13.5" stroke="white" stroke-width="1.5" stroke-linecap="round" />
            <path
                d="M2.50098 11C2.50098 7.70017 2.50098 6.05025 3.52611 5.02513C4.55123 4 6.20115 4 9.50098 4H10.501C13.8008 4 15.4507 4 16.4759 5.02513C17.501 6.05025 17.501 7.70017 17.501 11V13C17.501 16.2998 17.501 17.9497 16.4759 18.9749C15.4507 20 13.8008 20 10.501 20H9.50098C6.20115 20 4.55123 20 3.52611 18.9749C2.50098 17.9497 2.50098 16.2998 2.50098 13V11Z"
                stroke="white" stroke-width="1.5" />
            <path
                d="M17.501 8.90585L17.6269 8.80196C19.7427 7.05623 20.8006 6.18336 21.6508 6.60482C22.501 7.02628 22.501 8.42355 22.501 11.2181V12.7819C22.501 15.5765 22.501 16.9737 21.6508 17.3952C20.8006 17.8166 19.7427 16.9438 17.6269 15.198L17.501 15.0941"
                stroke="white" stroke-width="1.5" stroke-linecap="round" />
        </svg>
        <span style="color: white">
            <?php esc_html_e( 'Zoom', 'cob_theme' ); ?>
        </span>
    </button>

    <div class="overlay" id="overlayZoom"></div>
    <div class="popup" id="popupZoom">
        <button id="closePopupZoom">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M31.375 8.625L8.625 31.375M8.625 8.625L31.375 31.375" stroke="white" stroke-width="2.4375"
                      stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <!-- Logo inside popup -->
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/logo.png' ); ?>"
             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />

        <!-- Popup tabs (translatable) -->
        <div class="popup-tabs-sec">
            <button class="popup-flatTab active" data-tab="popup-meeting">
                <?php esc_html_e( 'Schedule Appointment', 'cob_theme' ); ?>
            </button>
            <button class="popup-flatTab" data-tab="popup-service">
                <?php esc_html_e( 'Live Meeting', 'cob_theme' ); ?>
            </button>
        </div>

        <!-- “Schedule Appointment” Tab Content -->
        <div class="popup-flatTab-content active" id="popup-meeting">
            <label>
                <?php esc_html_e( 'Full Name', 'cob_theme' ); ?>
            </label>
            <input type="text" placeholder="<?php esc_attr_e( 'Full Name', 'cob_theme' ); ?>" />

            <div class="date-picker">
                <label>
                    <?php esc_html_e( 'Select Date', 'cob_theme' ); ?>
                </label>
                <div class="swiper date-swiper-zoom">
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
                <label>
                    <?php esc_html_e( 'Select Time', 'cob_theme' ); ?>
                </label>
                <select>
                    <option><?php esc_html_e( '10:00 AM', 'cob_theme' ); ?></option>
                    <option><?php esc_html_e( '12:00 PM', 'cob_theme' ); ?></option>
                    <option><?php esc_html_e( '02:00 PM', 'cob_theme' ); ?></option>
                </select>
            </div>

            <button class="confirm">
                <?php esc_html_e( 'Confirm Booking', 'cob_theme' ); ?>
            </button>
            <span>
                <?php esc_html_e( 'Book your meeting now', 'cob_theme' ); ?>
            </span>
        </div>

        <!-- “Live Meeting” Tab Content -->
        <div class="popup-flatTab-content" id="popup-service">
            <label>
                <?php esc_html_e( 'Full Name', 'cob_theme' ); ?>
            </label>
            <input type="text" placeholder="<?php esc_attr_e( 'Full Name', 'cob_theme' ); ?>" />

            <label>
                <?php esc_html_e( 'Enter Phone Number', 'cob_theme' ); ?>
            </label>
            <input type="text" placeholder="<?php esc_attr_e( 'Phone Number', 'cob_theme' ); ?>" />

            <button class="confirm">
                <?php esc_html_e( 'Create Meeting', 'cob_theme' ); ?>
            </button>
            <span>
                <?php esc_html_e( 'Book your meeting now', 'cob_theme' ); ?>
            </span>
        </div>
    </div>

    <!-- “Your Assistant” button -->
    <button class="button help" id="togglePopupContact">
        <svg width="25" height="24" viewBox="0 0 25 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M13 3.00372C12.1049 2.99039 11.2047 3.01289 10.3294 3.07107C6.14639 3.34913 2.81441 6.72838 2.54024 10.9707C2.48659 11.8009 2.48659 12.6607 2.54024 13.4909C2.6401 15.036 3.32343 16.4666 4.12791 17.6746C4.59501 18.5203 4.28674 19.5758 3.80021 20.4978C3.44941 21.1626 3.27401 21.495 3.41484 21.7351C3.55568 21.9752 3.87026 21.9829 4.49943 21.9982C5.74367 22.0285 6.58268 21.6757 7.24868 21.1846C7.6264 20.9061 7.81527 20.7668 7.94544 20.7508C8.0756 20.7348 8.33177 20.8403 8.84401 21.0513C9.3044 21.2409 9.83896 21.3579 10.3294 21.3905C11.7536 21.4852 13.2435 21.4854 14.6706 21.3905C18.8536 21.1125 22.1856 17.7332 22.4598 13.4909C22.5021 12.836 22.511 12.1627 22.4866 11.5"
                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9 15H16M9 10H12.5" stroke="white" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
            <path d="M15.5 5.5H22.5M19 2V9" stroke="white" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
        </svg>
        <span style="color: white">
            <?php esc_html_e( 'Your Assistant', 'cob_theme' ); ?>
        </span>
    </button>

    <div class="overlay" id="overlayContact"></div>
    <div class="popup black" id="popupContact">
        <button id="closePopupContact" style="background: white">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="#000000" xmlns="http://www.w3.org/2000/svg">
                <path d="M31.375 8.625L8.625 31.375M8.625 8.625L31.375 31.375" stroke="#000000"
                      stroke-width="2.4375" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>

        <div class="contact-content">
            <div class="form-container-pop">
                <h2 class="head">
                    <?php esc_html_e( 'Need Help?', 'cob_theme' ); ?>
                </h2>
                <p>
                    <?php esc_html_e( 'Fill in your details and a real estate expert will contact you as soon as possible.', 'cob_theme' ); ?>
                </p>
                <form>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="<?php esc_attr_e( 'Name', 'cob_theme' ); ?>"
                        required
                    />
                    <input
                        type="tel"
                        id="mobile"
                        name="mobile"
                        pattern="[0-9]*"
                        placeholder="<?php esc_attr_e( 'Phone Number', 'cob_theme' ); ?>"
                        required
                    />
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="<?php esc_attr_e( 'Email', 'cob_theme' ); ?>"
                        required
                    />
                    <textarea
                        id="message"
                        name="message"
                        rows="4"
                        placeholder="<?php esc_attr_e( 'Your Message', 'cob_theme' ); ?>"
                        required
                    ></textarea>
                    <button type="submit">
                        <?php esc_html_e( 'Send', 'cob_theme' ); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // (Your existing JS for toggling those popups can remain here unchanged.)
</script>
