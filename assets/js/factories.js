document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll(".factorys-card");
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

// swiper14-in
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper14-in", {
    spaceBetween: 50,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});
// swiper15-in
document.addEventListener("DOMContentLoaded", () => {
  var swiper = new Swiper(".swiper15-in", {
    spaceBetween: 50,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
});
