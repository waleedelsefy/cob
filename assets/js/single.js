document.addEventListener("DOMContentLoaded", () => {
  // Initialize Thumbnails Swiper
  const thumbnailsSwiper = new Swiper(".thumbnails-swiper", {
    direction: "vertical",
    slidesPerView: 3,
    spaceBetween: 10,
    watchSlidesProgress: true, // Sync with main swiper
  });

  // Initialize Main Swiper
  const mainSwiper = new Swiper(".main-swiper", {
    slidesPerView: 1,
    spaceBetween: 10,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    thumbs: {
      swiper: thumbnailsSwiper, // Connect thumbnails swiper
    },
  });
});
// swiper6 //articles
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper6", {
    spaceBetween: 50,
    slidesPerView: 4,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      320: {
        slidesPerView: 1,
        spaceBetween: 20,
      },
      640: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 40,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 50,
      },
    },
    preventClicks: true,
    preventClicksPropagation: true,
    hashNavigation: false,
  });
});
