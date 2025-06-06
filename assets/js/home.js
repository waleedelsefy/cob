/**
 * Main JavaScript file for the Cob Theme.
 *
 * Contains:
 * - UI logic for the advanced search filter toggle.
 * - Algolia AJAX search functionality for the landing page.
 * - Swiper slider initializations.
 */
document.addEventListener('DOMContentLoaded', () => {

  // --- Advanced Search Filter Toggle Functionality ---
  const toggleButton = document.getElementById('toggleButton');
  const hiddenContent = document.getElementById('hiddenContent'); // This is the container for ALL filters now
  const sliderIcon = document.getElementById('sliderIcon');
  const closeIcon = document.getElementById('closeIcon');
  const searchTitle = document.getElementById('searchTitle');
  const SearchTitle2 = document.getElementById('SearchTitle2');
  const searchIcon = document.getElementById('searchIcon'); // Icon inside the text input

  // Elements to be affected by z-index
  const navbar2 = document.querySelector(".navbar");
  const areasSection = document.querySelector(".areas");
  const compoundsSection = document.querySelector(".compounds");

  // Check for the toggle button before adding a listener to prevent errors
  if (toggleButton && hiddenContent) {
    // This is the logic from your original home.js file, adapted slightly
    toggleButton.addEventListener('click', () => {
      const isHidden = hiddenContent.style.display === 'none' || hiddenContent.style.display === '';
      const searchButton = document.getElementById('searchAutocomplete'); // Container for the main input

      if (isHidden) {
        hiddenContent.style.display = 'block';
        if(sliderIcon) sliderIcon.style.display = 'none';
        if(closeIcon) closeIcon.style.display = 'inline-block';
        if(searchTitle) searchTitle.style.display = 'block';
        if(SearchTitle2) SearchTitle2.style.display = 'none';
        if(searchButton) searchButton.style.display = 'none';
        if(searchIcon) searchIcon.style.display = 'none';

        if (navbar2) navbar2.style.zIndex = "0";
        if (areasSection) {
          areasSection.style.position = "relative";
          areasSection.style.zIndex = "-1";
        }
        if (compoundsSection) compoundsSection.style.zIndex = "-1";

      } else {
        hiddenContent.style.display = 'none';
        if(sliderIcon) sliderIcon.style.display = 'inline-block';
        if(closeIcon) closeIcon.style.display = 'none';
        if(searchTitle) searchTitle.style.display = 'none';
        if(SearchTitle2) SearchTitle2.style.display = 'block';
        if(searchButton) searchButton.style.display = 'block';
        if(searchIcon) searchIcon.style.display = 'block';

        if (navbar2) navbar2.style.zIndex = "1";
        if (areasSection) areasSection.style.zIndex = "1";
        if (compoundsSection) compoundsSection.style.zIndex = "1";
      }
    });
  }

  // --- Algolia AJAX Search Functionality ---
  const searchForm = document.getElementById('cob-algolia-ajax-search-form');

  if (searchForm && typeof cobAlgoliaAjax !== 'undefined') {
    const searchInput = document.getElementById('basicSearchInput');
    const resultsContainer = document.getElementById('algolia-ajax-hits-container');
    const paginationContainer = document.getElementById('algolia-ajax-pagination-container');
    let searchTimeout;

    const performAlgoliaAjaxSearch = (page = 1) => {
      if (!resultsContainer) {
        console.error('Algolia results container not found.');
        return;
      }

      resultsContainer.innerHTML = '<p class="loading-results">Loading results...</p>';
      if (paginationContainer) paginationContainer.innerHTML = '';

      const formData = new FormData(searchForm);
      formData.append('action', 'cob_algolia_ajax_search');
      formData.append('nonce', cobAlgoliaAjax.nonce);
      formData.append('paged', page);

      fetch(cobAlgoliaAjax.ajax_url, {
        method: 'POST',
        body: formData
      })
          .then(response => response.json())
          .then(response => {
            if (response.success && response.data) {
              resultsContainer.innerHTML = response.data.hits_html || '<p>No results found.</p>';
              if (paginationContainer) {
                paginationContainer.innerHTML = response.data.pagination_html || '';
              }
            } else {
              resultsContainer.innerHTML = `<p class="error-message">Error: ${response.data ? response.data.message : 'Unknown error.'}</p>`;
            }
          })
          .catch(error => {
            console.error('Algolia AJAX Search Error:', error);
            resultsContainer.innerHTML = '<p class="error-message">An error occurred. Please try again.</p>';
          });
    };

    // Event listener for form submission (clicking the "Search" button)
    searchForm.addEventListener('submit', (e) => {
      e.preventDefault();
      clearTimeout(searchTimeout);
      performAlgoliaAjaxSearch(1);
    });

    // Event listeners for any filter change
    const allFilters = searchForm.querySelectorAll('select, input[type="checkbox"]');
    allFilters.forEach(filter => {
      filter.addEventListener('change', () => {
        searchForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
      });
    });

    // Event listener for live search with debouncing
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          performAlgoliaAjaxSearch(1);
        }, 350);
      });
    }

    // Event listener for pagination clicks (using event delegation)
    if (paginationContainer) {
      paginationContainer.addEventListener('click', (e) => {
        const targetLink = e.target.closest('a.page-numbers');
        if (targetLink && !targetLink.classList.contains('current')) {
          e.preventDefault();
          const url = new URL(targetLink.href, window.location.origin);
          const newPage = parseInt(url.searchParams.get('paged') || '1', 10);
          performAlgoliaAjaxSearch(newPage);
          if (resultsContainer.scrollIntoView) {
            resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        }
      });
    }
  }

  // --- Swiper Slider Initializations ---
  // (Your existing Swiper code remains here. I've included a cleaned-up version for completeness)
  const initResponsiveSwiper = (selector, settings = {}) => {
    const swiperContainer = document.querySelector(selector);
    if (swiperContainer) {
      const parentContainer = swiperContainer.parentElement;
      const defaultConfig = {
        spaceBetween: 50,
        slidesPerView: 4,
        navigation: {
          nextEl: parentContainer.querySelector(".swiper-button-next"),
          prevEl: parentContainer.querySelector(".swiper-button-prev"),
        },
        pagination: {
          el: parentContainer.querySelector(".swiper-pagination"),
          clickable: true,
        },
        breakpoints: {
          320: { slidesPerView: 1, spaceBetween: 20 },
          640: { slidesPerView: 2, spaceBetween: 20 },
          768: { slidesPerView: 3, spaceBetween: 40 },
          1024: { slidesPerView: 4, spaceBetween: 50 },
        },
        preventClicks: true,
        preventClicksPropagation: true,
        hashNavigation: false,
      };
      new Swiper(selector, { ...defaultConfig, ...settings });
    }
  };

  if (document.querySelector('.landing-swiper')) { new Swiper(".landing-swiper", { pagination: { el: ".swiper-pagination", type: "progressbar", clickable: true }, navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" } }); }
  initResponsiveSwiper(".mySwiper");
  initResponsiveSwiper(".swiper1");
  initResponsiveSwiper(".swiper2");
  initResponsiveSwiper(".swiper4");
  initResponsiveSwiper(".swiper6");
  initResponsiveSwiper(".swiper3", { spaceBetween: 20, slidesPerView: 3, breakpoints: { 1024: { slidesPerView: 3, spaceBetween: 20 } } });
  initResponsiveSwiper(".swiper5", { spaceBetween: 20, slidesPerView: 3, breakpoints: { 1024: { slidesPerView: 3, spaceBetween: 20 } } });

});
