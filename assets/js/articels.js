document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab");
  const tabPanels = document.querySelectorAll(".tab-panel");
  const paginationButtons = document.querySelectorAll(".page");
  const itemsPerPage = 8;
  let currentPage = 1;

  // Handle Tab Clicks
  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      const targetId = tab.getAttribute("data-target");

      // Activate the clicked tab
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");

      // Show the corresponding tab panel
      tabPanels.forEach((panel) => {
        if (panel.id === targetId) {
          panel.classList.add("active");
          panel.style.display = "block"; // Make it visible
        } else {
          panel.classList.remove("active");
          panel.style.display = "none"; // Hide others
        }
      });

      // Reset Pagination to First Page
      currentPage = 1;
      updatePagination();

      // Update Total Count in the new tab
      updateTotalCount();
    });
  });

  // Handle Pagination Clicks
  paginationButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const text = button.textContent.trim();

      if (text === "السابق" && currentPage > 1) {
        currentPage--;
      } else if (
        text === "التالي" &&
        currentPage < paginationButtons.length - 2
      ) {
        currentPage++;
      } else if (!isNaN(text)) {
        currentPage = parseInt(text, 10);
      }

      updatePagination();
      updateTotalCount();
    });
  });

  // Update Pagination Functionality
  function updatePagination() {
    const activePanel = document.querySelector(".tab-panel.active");
    const cards = activePanel.querySelectorAll(".articles-card");
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    // Show only the cards for the current page
    cards.forEach((card, index) => {
      card.style.display = index >= start && index < end ? "block" : "none";
    });

    // Update Active Page Button
    paginationButtons.forEach((btn) => btn.classList.remove("active"));
    paginationButtons[currentPage].classList.add("active");
  }

  // Update Total Count Functionality
  function updateTotalCount() {
    const totalTab = document.querySelector('.tab[data-target="total"]');
    const activePanel = document.querySelector(".tab-panel.active");
    const totalItems = activePanel.querySelectorAll(".articles-card").length;

    if (totalTab) {
      const totalCountDisplay = document.querySelector(".total-count");
      if (totalCountDisplay) {
        totalCountDisplay.textContent = `Total Items: ${totalItems}`;
      }
    }
  }

  // Initialize: Show First Tab and First Page
  tabs[0].click();
});
