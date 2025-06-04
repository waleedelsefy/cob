document.addEventListener("DOMContentLoaded", () => {
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");
  const navbar = document.querySelector(".navbar");
  const logo = document.querySelector(".logo");
  const languageSelector = document.querySelector(".language-selector");
  const mobileBreakpoint = 768;

  const resetForDesktop = () => {
    if (window.innerWidth >= mobileBreakpoint) {
      hamburger.classList.remove("active");
      navMenu.classList.remove("active");
      hamburger.setAttribute("aria-expanded", "false");
      navbar.style.flexDirection = "row";
      navbar.style.gap = "";
      languageSelector.style.display = "block";
    }
  };

  // const toggleMenu = () => {
  //   if (window.innerWidth < mobileBreakpoint) {
  //     hamburger.classList.toggle("active");
  //     navMenu.classList.toggle("active");

  //     const menuOpen = navMenu.classList.contains("active");
  //     hamburger.setAttribute("aria-expanded", menuOpen);

  //     if (menuOpen) {
  //       navbar.style.flexDirection = "row-reverse";
  //       navbar.style.flexWrap = "wrap";
  //       logo.style.marginInlineStart = "0px";
  //       navbar.style.gap = "50%";
  //       languageSelector.style.display = "none";
  //     } else {
  //       navbar.style.flexDirection = "row";
  //       navbar.style.gap = "";
  //       languageSelector.style.display = "block";
  //     }
  //   }
  // };

  const closeMenuOnLinkClick = () => {
    if (window.innerWidth < mobileBreakpoint) {
      hamburger.classList.remove("active");
      navMenu.classList.remove("active");
      hamburger.setAttribute("aria-expanded", "false");
      navbar.style.flexDirection = "row";
      navbar.style.gap = "";
      languageSelector.style.display = "block";
    }
  };

  // Event listeners
  hamburger.addEventListener("click", toggleMenu);
  document.querySelectorAll(".nav-links a").forEach((link) => {
    link.addEventListener("click", closeMenuOnLinkClick);
  });
  window.addEventListener("resize", resetForDesktop);

  // Initial check on page load
  resetForDesktop();
});
// footer
// Select all collapsible footer sections
document.querySelectorAll(".footer-section").forEach((section) => {
  const header = section.querySelector("h4");

  // Add click event listener for mobile behavior
  header.addEventListener("click", () => {
    if (window.innerWidth < 992) {
      // Only allow toggling on mobile
      const isOpen = section.classList.contains("open");

      // Close all sections
      document
        .querySelectorAll(".footer-section")
        .forEach((s) => s.classList.remove("open"));

      // Toggle current section
      if (!isOpen) section.classList.add("open");
    }
  });
});

// Add responsive handling to reset styles for desktop
window.addEventListener("resize", () => {
  if (window.innerWidth >= 992) {
    // On desktop, ensure all sections are open
    document.querySelectorAll(".footer-section").forEach((section) => {
      section.classList.add("open");
    });
  } else {
    // On mobile, close all sections by default
    document.querySelectorAll(".footer-section").forEach((section) => {
      section.classList.remove("open");
    });
  }
});

// Initial check for the current viewport size
if (window.innerWidth >= 992) {
  // Ensure all sections are open on desktop
  document.querySelectorAll(".footer-section").forEach((section) => {
    section.classList.add("open");
  });
} else {
  // Close all sections by default on mobile
  document.querySelectorAll(".footer-section").forEach((section) => {
    section.classList.remove("open");
  });
}
//LazyLoad
window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.loadMode = 1;

// document.addEventListener("DOMContentLoaded", function () {
//   const dropdownContainer = document.querySelector(".dropdown-container");
//   const mazedLink = document.querySelector(".mazed");

//   // Ensure dropdown is hidden at the start
//   dropdownContainer.classList.remove("active2");

//   mazedLink.addEventListener("click", function (event) {
//     event.preventDefault();
//     dropdownContainer.classList.toggle("active2");
//   });

//   // Close dropdown when clicking outside
//   document.addEventListener("click", function (event) {
//     if (
//       !dropdownContainer.contains(event.target) &&
//       !mazedLink.contains(event.target)
//     ) {
//       dropdownContainer.classList.remove("active2");
//     }
//   });
// });
// function toggleDetails(element) {
//   const mazed = element.nextElementSibling;
//   const dropDown = element.parentElement;

//   mazed.style.display = mazed.style.display === "block" ? "none" : "block";
//   dropDown.classList.toggle("active");
// }
document.addEventListener("DOMContentLoaded", function () {
  const dropdownContainers = document.querySelectorAll(".dropdown-container");

  dropdownContainers.forEach((container) => {
    const link = container.querySelector("a");
    const dropdown = container.querySelector(".dropdown");

    if (!dropdown) return;

    link.addEventListener("click", function (e) {
      // On non-touch devices, let hover handle it
      if (!("ontouchstart" in window) && window.innerWidth > 768) {
        return;
      }

      e.preventDefault();

      // Close other dropdowns
      dropdownContainers.forEach((otherContainer) => {
        if (otherContainer !== container) {
          otherContainer.classList.remove("active");
        }
      });

      // Toggle this dropdown
      container.classList.toggle("active");
    });
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".dropdown-container")) {
      dropdownContainers.forEach((container) => {
        container.classList.remove("active");
      });
    }
  });
});
