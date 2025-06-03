document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("toggleButton");
  const hiddenContent = document.getElementById("hiddenContent");
  const sliderIcon = document.getElementById("sliderIcon");
  const closeIcon = document.getElementById("closeIcon");
  const searchButton = document.getElementById("searchAutocomplete");
  const searchTitle = document.getElementById("searchTitle");
  const SearchTitle2 = document.getElementById("SearchTitle2");
  const searchIcon = document.getElementById("searchIcon");
  const navbar2 = document.querySelector(".navbar");
  const areas = document.querySelector(".areas");
  const compounds = document.querySelector(".compounds");

  // Function to toggle the hidden content
  toggleButton.addEventListener("click", () => {
    if (
      hiddenContent.style.display === "none" ||
      hiddenContent.style.display === ""
    ) {
      hiddenContent.style.display = "block"; // Show the full-screen content
      sliderIcon.style.display = "none"; // Hide the slider icon
      searchTitle.style.display = "block";
      SearchTitle2.style.display = "none";
      searchButton.style.display = "none";
      searchIcon.style.display = "none";
      closeIcon.style.display = "block"; // Show the close icon
      navbar2.style.zIndex = "0";
      areas.style.position = "relative";
      areas.style.zIndex = "-1";
      compounds.style.zIndex = "-1";
    } else {
      hiddenContent.style.display = "none"; // Hide the content
      sliderIcon.style.display = "block"; // Show the slider icon
      closeIcon.style.display = "none"; // Hide the close icon
      searchTitle.style.display = "none";
      SearchTitle2.style.display = "block";
      searchButton.style.display = "block";
      searchIcon.style.display = "block";
      navbar2.style.zIndex = "1";
      areas.style.zIndex = "1";
      compounds.style.zIndex = "1";
    }
  });

  // Function to hide content on desktop view
  const handleResize = () => {
    if (window.innerWidth > 768) {
      hiddenContent.style.display = "none";
      sliderIcon.style.display = "block";
      closeIcon.style.display = "none";
      navbar2.style.zIndex = "1";
    }
  };
  window.addEventListener("resize", handleResize);

  handleResize();
});
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".landing-swiper", {
    pagination: {
      el: ".swiper-pagination",
      type: "progressbar",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    preventClicks: true,
    preventClicksPropagation: true,
    hashNavigation: false,
  });
});
// mySwiper //first
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".mySwiper", {
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
// swiper1 //compounds
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper1", {
    spaceBetween: 50,
    slidesPerView: 4,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination", // Target the pagination element
      clickable: true, // Allows users to click bullets to navigate
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
// swiper2 //projects
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper2", {
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
// swiper3 //properties
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper3", {
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

// swiper4 //motaoron
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper4", {
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
// swiper1-in //swiper1-in-swiper3
var swiper = new Swiper(".swiper1-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
// swiper2-in //swiper2-in-swiper3
var swiper = new Swiper(".swiper2-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
// swiper3-in //swiper3-in-swiper3
var swiper = new Swiper(".swiper3-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
// swiper4-in //swiper4-in-swiper3
var swiper = new Swiper(".swiper4-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});

// swiper5-in //swiper5-in-swiper3
var swiper = new Swiper(".swiper5-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
// swiper6-in //swiper6-in-swiper3
var swiper = new Swiper(".swiper6-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
// swiper7-in //swiper7-in-swiper5
var swiper = new Swiper(".swiper7-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
// swiper8-in //swiper8-in-swiper5
var swiper = new Swiper(".swiper8-in", {
  spaceBetween: 50,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
});
document.querySelectorAll(".swiper-in").forEach((swiperElement) => {
  const swiperId = swiperElement.getAttribute("data-swiper-id");
  if (!swiperId) return;

  new Swiper("." + swiperId, {
    spaceBetween: 50,
    pagination: {
      el: "." + swiperId + "-pagination", // Ensure pagination is linked properly
      clickable: true,
    },
  });
});
