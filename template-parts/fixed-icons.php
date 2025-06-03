<?php
$theme_dir = get_template_directory_uri();
?>
<div class="fixed-icons">
    <a class="fixed-svg" href="tel:<?php echo esc_attr( get_option( 'company_phone', '0123456789' ) ); ?>">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="15.4286" fill="#EC3C43" />
            <path
                    d="M19.9411 15.0182L19.3658 13.7239C18.9897 12.8777 18.8017 12.4545 18.5204 12.1307C18.168 11.7249 17.7085 11.4263 17.1945 11.2691C16.7844 11.1436 16.3214 11.1436 15.3953 11.1436C14.0406 11.1436 13.3632 11.1436 12.7946 11.404C12.1248 11.7107 11.5199 12.3768 11.2789 13.073C11.0743 13.664 11.1329 14.2713 11.2501 15.486C12.4977 28.4153 19.5862 35.5037 32.5154 36.7513C33.7301 36.8686 34.3375 36.9271 34.9284 36.7226C35.6246 36.4816 36.2906 35.8766 36.5975 35.2068C36.8578 34.6381 36.8578 33.9608 36.8578 32.6061C36.8578 31.68 36.8578 31.217 36.7324 30.8068C36.5751 30.2928 36.2765 29.8334 35.8706 29.481C35.5469 29.1997 35.1238 29.0117 34.2775 28.6356L32.9832 28.0603C32.0668 27.653 31.6085 27.4494 31.1429 27.4051C30.6972 27.3627 30.2479 27.4253 29.8308 27.5877C29.3949 27.7574 29.0098 28.0784 28.2392 28.7204C27.4724 29.3596 27.0889 29.6791 26.6203 29.8503C26.2049 30.002 25.6558 30.0583 25.2184 29.9937C24.7248 29.921 24.3469 29.7191 23.5911 29.3151C21.2396 28.0586 19.9429 26.7618 18.6862 24.4103C18.2823 23.6546 18.0804 23.2766 18.0077 22.7831C17.9432 22.3456 17.9994 21.7964 18.1511 21.3811C18.3223 20.9125 18.6419 20.5291 19.281 19.7621C19.923 18.9917 20.244 18.6064 20.4138 18.1706C20.5762 17.7535 20.6387 17.3041 20.5963 16.8585C20.552 16.3929 20.3484 15.9346 19.9411 15.0182Z"
                    stroke="white" stroke-width="2.14286" stroke-linecap="round" />
        </svg>
    </a>
    <a class="fixed-svg" href="https://api.whatsapp.com/send?phone=2<?php echo esc_attr( get_option( 'company_whatsapp', '0123456789' ) ); ?>">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="15.4286" fill="#00DE3E" />
            <path
                    d="M24.0006 38.2863C31.8903 38.2863 38.2863 31.8903 38.2863 24.0006C38.2863 16.1108 31.8903 9.71484 24.0006 9.71484C16.1108 9.71484 9.71484 16.1108 9.71484 24.0006C9.71484 25.9704 10.1135 27.8471 10.8346 29.5544C11.2331 30.4978 11.4324 30.9697 11.457 31.3263C11.4817 31.6828 11.3768 32.075 11.1669 32.8594L9.71484 38.2863L15.1417 36.8343C15.9261 36.6244 16.3183 36.5194 16.6749 36.5441C17.0314 36.5687 17.5032 36.768 18.4468 37.1666C20.1541 37.8876 22.0307 38.2863 24.0006 38.2863Z"
                    stroke="white" stroke-width="2.14286" stroke-linejoin="round" />
            <path
                    d="M19.1262 24.5403L20.3704 22.995C20.8948 22.3437 21.543 21.7374 21.5939 20.8702C21.6066 20.6512 21.4526 19.6678 21.1443 17.7011C21.0232 16.9281 20.3015 16.8584 19.6765 16.8584C18.8619 16.8584 18.4546 16.8584 18.0502 17.0431C17.5391 17.2766 17.0143 17.9331 16.8991 18.4832C16.808 18.9183 16.8757 19.2182 17.0111 19.818C17.5863 22.3653 18.9357 24.881 21.0281 26.9734C23.1206 29.0658 25.6363 30.4153 28.1836 30.9904C28.7833 31.1258 29.0831 31.1935 29.5184 31.1024C30.0684 30.9873 30.7249 30.4625 30.9584 29.9513C31.1431 29.5468 31.1431 29.1397 31.1431 28.3251C31.1431 27.7 31.0734 26.9784 30.3004 26.8573C28.3337 26.549 27.3504 26.395 27.1313 26.4077C26.2641 26.4585 25.6579 27.1067 25.0066 27.6311L23.4613 28.8753"
                    stroke="white" stroke-width="2.14286" />
        </svg>
    </a>

    <button class="button zoom-home fixed-svg" id="togglepopupContactOpety">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="15.4286" fill="black" />
            <path
                    d="M24.7148 11.1489C23.4361 11.1298 22.1501 11.162 20.8997 11.2451C14.924 11.6423 10.164 16.4698 9.77233 22.5303C9.69568 23.7163 9.69568 24.9446 9.77233 26.1306C9.91498 28.3378 10.8912 30.3816 12.0404 32.1073C12.7077 33.3154 12.2673 34.8233 11.5723 36.1404C11.0711 37.0901 10.8206 37.565 11.0218 37.908C11.223 38.251 11.6724 38.262 12.5712 38.2838C14.3487 38.3271 15.5472 37.8231 16.4987 37.1216C17.0383 36.7237 17.3081 36.5247 17.494 36.5018C17.68 36.479 18.0459 36.6297 18.7777 36.9311C19.4354 37.202 20.1991 37.3691 20.8997 37.4157C22.9343 37.551 25.0627 37.5513 27.1014 37.4157C33.0771 37.0186 37.8371 32.191 38.2288 26.1306C38.2893 25.195 38.302 24.2331 38.2671 23.2864"
                    stroke="white" stroke-width="2.14286" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M19 28.2864H29M19 21.1436H24" stroke="white" stroke-width="2.14286" stroke-linecap="round"
                  stroke-linejoin="round" />
            <path d="M28.2861 14.7148H38.2861M33.2861 9.71484V19.7148" stroke="white" stroke-width="2.14286"
                  stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </button>

    <div class="overlay" id="overlayContact"></div>
    <div class="popup black" id="popupContactOpety">
        <button id="closepopupContactOpety" style="background: white">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="#000000" xmlns="http://www.w3.org/2000/svg">
                <path d="M31.375 8.625L8.625 31.375M8.625 8.625L31.375 31.375" stroke="#000000" stroke-width="2.4375"
                      stroke-linecap="round" stroke-linejoin="round" />
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

    <button class="button zoom zoom-home fixed-svg" id="togglePopupZoomOpety">
        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect width="48" height="48" rx="15.4286" fill="#357CDC" />
            <path d="M22.5713 18.2861H25.4284" stroke="white" stroke-width="2.14286" stroke-linecap="round" />
            <path
                    d="M9.71484 22.5713C9.71484 17.8572 9.71484 15.5002 11.1793 14.0358C12.6438 12.5713 15.0008 12.5713 19.7148 12.5713H21.1434C25.8574 12.5713 28.2144 12.5713 29.679 14.0358C31.1434 15.5002 31.1434 17.8572 31.1434 22.5713V25.4284C31.1434 30.1424 31.1434 32.4994 29.679 33.964C28.2144 35.4284 25.8574 35.4284 21.1434 35.4284H19.7148C15.0008 35.4284 12.6438 35.4284 11.1793 33.964C9.71484 32.4994 9.71484 30.1424 9.71484 25.4284V22.5713Z"
                    stroke="white" stroke-width="2.14286" />
            <path
                    d="M31.1436 19.5805L31.3234 19.4321C34.346 16.9382 35.8573 15.6912 37.0718 16.2933C38.2864 16.8954 38.2864 18.8915 38.2864 22.8837V25.1177C38.2864 29.11 38.2864 31.106 37.0718 31.7081C35.8573 32.3101 34.346 31.0633 31.3234 28.5693L31.1436 28.4208"
                    stroke="white" stroke-width="2.14286" stroke-linecap="round" />
        </svg>
    </button>
    <div class="overlay" id="overlayZoomOpety"></div>
    <div class="popup" id="PopupZoomOpety">
        <button id="closePopupZoomOpety">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M31.375 8.625L8.625 31.375M8.625 8.625L31.375 31.375"
                      stroke="white" stroke-width="2.4375" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </button>
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/imgs/logo.png' ); ?>"
             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
        <div class="popup-tabs-sec">
            <button class="popup-flatTab active" data-tab="popup-meeting">
                <?php esc_html_e( 'Schedule Appointment', 'cob_theme' ); ?>
            </button>
            <button class="popup-flatTab" data-tab="popup-service">
                <?php esc_html_e( 'Live Meeting', 'cob_theme' ); ?>
            </button>
        </div>

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
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Popup Zoom Toggle
        const togglePopupZoomOpety = document.getElementById("togglePopupZoomOpety");
        const PopupZoomOpety = document.getElementById("PopupZoomOpety");
        const closePopupZoomOpety = document.getElementById("closePopupZoomOpety");
        const overlayZoomOpety = document.getElementById("overlayZoomOpety");

        if (togglePopupZoomOpety && PopupZoomOpety && closePopupZoomOpety && overlayZoomOpety) {
            togglePopupZoomOpety.addEventListener("click", () => {
                PopupZoomOpety.style.display = "block";
                overlayZoomOpety.style.display = "block";
            });
            closePopupZoomOpety.addEventListener("click", () => {
                PopupZoomOpety.style.display = "none";
                overlayZoomOpety.style.display = "none";
            });
            overlayZoomOpety.addEventListener("click", () => {
                PopupZoomOpety.style.display = "none";
                overlayZoomOpety.style.display = "none";
            });
        }

        // Tabs for popup .popup-flatTab elements
        const popupTabs = document.querySelectorAll(".popup-flatTab");
        const popupContents = document.querySelectorAll(".popup-flatTab-content");
        popupTabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                popupTabs.forEach((t) => t.classList.remove("active"));
                popupContents.forEach((c) => c.classList.remove("active"));
                tab.classList.add("active");
                const target = document.getElementById(tab.dataset.tab);
                if (target) {
                    target.classList.add("active");
                }
            });
        });

        // Date Swiper for popup
        let dateSwiperZoom = new Swiper(".date-swiper-zoom", {
            slidesPerView: 4,
            spaceBetween: 10,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: { slidesPerView: 4, spaceBetween: 10 },
                768: { slidesPerView: 4, spaceBetween: 10 },
                1024: { slidesPerView: 4, spaceBetween: 10 },
            },
        });
    });

    // Popup Contact Toggle
    const togglepopupContactOpety = document.getElementById("togglepopupContactOpety");
    const popupContactOpety = document.getElementById("popupContactOpety");
    const closepopupContactOpety = document.getElementById("closepopupContactOpety");
    const overlayContact = document.getElementById("overlayContact");

    if (togglepopupContactOpety && popupContactOpety && closepopupContactOpety && overlayContact) {
        togglepopupContactOpety.addEventListener("click", () => {
            popupContactOpety.style.display = "block";
            overlayContact.style.display = "block";
        });
        closepopupContactOpety.addEventListener("click", () => {
            popupContactOpety.style.display = "none";
            overlayContact.style.display = "none";
        });
        overlayContact.addEventListener("click", () => {
            popupContactOpety.style.display = "none";
            overlayContact.style.display = "none";
        });
    }
</script>
