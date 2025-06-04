document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".job-listing");
  const paginationButtons = document.querySelectorAll(".page");
  const itemsPerPage = 5; // Number of items per page
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

document.addEventListener("DOMContentLoaded", () => {
  const swiper = new Swiper(".hiring-swiper", {
    slidesPerView: 2, // Number of thumbs visible at a time
    spaceBetween: 10, // Space between thumbnails
    loop: true, // Enable loop
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});
function toggleDetails(element) {
  const jobDetails = element.nextElementSibling;
  const jobListing = element.parentElement;

  jobDetails.style.display =
    jobDetails.style.display === "block" ? "none" : "block";
  jobListing.classList.toggle("active");
}

const toggleJobPopup = document.getElementById("toggleJobPopup");
const jobPopup = document.getElementById("jobPopup");
const closeJobPopup = document.getElementById("closeJobPopup");
const jobOverlay = document.getElementById("jobOverlay");

toggleJobPopup.addEventListener("click", function () {
  jobPopup.style.display = "block";
  jobOverlay.style.display = "block";
});

closeJobPopup.addEventListener("click", function () {
  jobPopup.style.display = "none"; // Hide the popup
  jobOverlay.style.display = "none"; // Hide the overlay
});
// swiper7 //experts
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".owners-swiper", {
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
overlay.addEventListener("click", function () {
  jobPopup.style.display = "none";
  jobOverlay.style.display = "none";
});

