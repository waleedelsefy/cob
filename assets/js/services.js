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

// swiper7 //experts
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper7", {
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
  // ====================================================
  // Pop Swiper & Thumbs with Breakpoints
  // ====================================================
  let popSwiper = new Swiper(".pop-swiper", {
    spaceBetween: 10,
    slidesPerView: 4,
    watchSlidesProgress: true,
    breakpoints: {
      320: { slidesPerView: 3, spaceBetween: 40 },
      640: { slidesPerView: 3, spaceBetween: 40 },
      768: { slidesPerView: 3, spaceBetween: 40 },
      1024: { slidesPerView: 4, spaceBetween: 10 },
    },
  });
  let popSwiper2 = new Swiper(".pop-swiper2", {
    spaceBetween: 10,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    thumbs: {
      swiper: popSwiper,
    },
  });

  // Date Swiper for main page
  let dateSwiper = new Swiper(".date-swiper", {
    slidesPerView: 3,
    spaceBetween: 10,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      640: { slidesPerView: 3, spaceBetween: 10 },
      768: { slidesPerView: 3, spaceBetween: 10 },
      1024: { slidesPerView: 3, spaceBetween: 10 },
    },
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
  // ====================================================
  // Popup Toggle
  // ====================================================
  const togglePopup = document.getElementById("togglePopup");
  const popup = document.getElementById("popup");
  const closePopup = document.getElementById("closePopup");
  const overlay = document.getElementById("overlay");

  if (togglePopup && popup && closePopup && overlay) {
    togglePopup.addEventListener("click", () => {
      popup.style.display = "block";
      overlay.style.display = "block";
    });
    closePopup.addEventListener("click", () => {
      popup.style.display = "none";
      overlay.style.display = "none";
    });
    overlay.addEventListener("click", () => {
      popup.style.display = "none";
      overlay.style.display = "none";
    });
  }

  // Popup Zoom Toggle
  const togglePopupZoom = document.getElementById("togglePopupZoom");
  const popupZoom = document.getElementById("popupZoom");
  const closePopupZoom = document.getElementById("closePopupZoom");
  const overlayZoom = document.getElementById("overlayZoom");

  if (togglePopupZoom && popupZoom && closePopupZoom && overlayZoom) {
    togglePopupZoom.addEventListener("click", () => {
      popupZoom.style.display = "block";
      overlayZoom.style.display = "block";
    });
    closePopupZoom.addEventListener("click", () => {
      popupZoom.style.display = "none";
      overlayZoom.style.display = "none";
    });
    overlayZoom.addEventListener("click", () => {
      popupZoom.style.display = "none";
      overlayZoom.style.display = "none";
    });
  }

  // Tabs for main page .flatTab elements
  const mainTabs = document.querySelectorAll(".flatTab");
  const mainContents = document.querySelectorAll(".flatTab-content");
  mainTabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      mainTabs.forEach((t) => t.classList.remove("active"));
      mainContents.forEach((c) => c.classList.remove("active"));
      tab.classList.add("active");
      const target = document.getElementById(tab.dataset.tab);
      if (target) {
        target.classList.add("active");
      }
    });
  });

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
  // Popup Contact Toggle
  const togglePopupContact = document.getElementById("togglePopupContact");
  const popupContact = document.getElementById("popupContact");
  const closePopupContact = document.getElementById("closePopupContact");
  const overlayContact = document.getElementById("overlayContact");

  if (
    togglePopupContact &&
    popupContact &&
    closePopupContact &&
    overlayContact
  ) {
    togglePopupContact.addEventListener("click", () => {
      popupContact.style.display = "block";
      overlayContact.style.display = "block";
    });
    closePopupContact.addEventListener("click", () => {
      popupContact.style.display = "none";
      overlayContact.style.display = "none";
    });
    overlayContact.addEventListener("click", () => {
      popupContact.style.display = "none";
      overlayContact.style.display = "none";
    });
  }
});
