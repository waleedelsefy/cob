// custom-swiper-init.js

// Initializing swiper instance for .swiper1 (compounds)
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

// Pagination for properties cards
document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".properties-card");
  const paginationButtons = document.querySelectorAll(".page");
  const itemsPerPage = 10; // Number of items per page
  let currentPage = 1;

  // Function to update displayed cards
  function updatePagination() {
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    // Show only cards for the current page
    cards.forEach((card, index) => {
      card.style.display = index >= start && index < end ? "block" : "none";
    });

    // Update active button
    paginationButtons.forEach((button) => {
      button.classList.remove("active");
      if (parseInt(button.dataset.page, 10) === currentPage) {
        button.classList.add("active");
      }
    });
  }

  // Handle pagination button clicks
  paginationButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const page = button.dataset.page;

      if (page === "prev" && currentPage > 1) {
        currentPage--;
      } else if (
          page === "next" &&
          currentPage < Math.ceil(cards.length / itemsPerPage)
      ) {
        currentPage++;
      } else if (!isNaN(page)) {
        currentPage = parseInt(page, 10);
      }

      updatePagination();
    });
  });

  // Initialize pagination
  updatePagination();
});

// Initializing swiper instance for .swiper6 (articles)
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

// Initializing swiper instance for .swiper13-in (another instance)
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper13-in", {
    spaceBetween: 50,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});

// Initializing swiper instance for .swiper9-in (nested swiper example)
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper9-in", {
    spaceBetween: 50,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});

// Initializing swiper instance for .swiper10-in (nested swiper example)
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper10-in", {
    spaceBetween: 50,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});

// Initializing swiper instance for .swiper11-in (nested swiper example)
document.addEventListen
