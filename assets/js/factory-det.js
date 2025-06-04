document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".factory-swiper", {
    spaceBetween: 10,
    slidesPerView: 3,
    watchSlidesProgress: true,
  });
  var swiper2 = new Swiper(".factory-swiper2", {
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: swiper,
    },
  });
});
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".factory-pop-swiper", {
    spaceBetween: 10,
    slidesPerView: 4,
    watchSlidesProgress: true,
    breakpoints: {
      320: {
        slidesPerView: 3,
        spaceBetween: 40,
      },
      640: {
        slidesPerView: 3,
        spaceBetween: 40,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 40,
      },
      1024: {
        slidesPerView: 4,
        spaceBetween: 10,
      },
    },
  });
  var swiper2 = new Swiper(".factory-pop-swiper2", {
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },

    thumbs: {
      swiper: swiper,
    },
  });
});

// swiper5 //factorys
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper5", {
    spaceBetween: 20,
    slidesPerView: 3,
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
        slidesPerView: 3,
        spaceBetween: 20,
      },
    },
    preventClicks: true,
    preventClicksPropagation: true,
    hashNavigation: false,
  });
});
