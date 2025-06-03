
# COB Theme – Capital of Business WordPress Theme

Welcome to **COB Theme**, a modern, secure, and modular WordPress theme designed specifically for real estate and corporate service websites. This theme is built with flexibility, security, and extensibility in mind.

---

## 🧱 Theme Structure

```
cob-theme/
├── assets/                # CSS/JS libraries like Bootstrap
├── inc/                  # Core theme functions
│   ├── post-types/       # Custom Post Types (e.g., properties, jobs, services)
│   ├── metaboxes/        # Custom Metaboxes for CPTs
│   ├── redux/            # Redux Framework Settings
│   ├── theme-setup/      # Theme setup scripts
│   ├── contact-forms/    # Contact form handling
│   ├── city/, developer/ # Term field extensions
│   ├── search/           # Custom search logic
│   └── transients/       # Cache cleanup
├── template-parts/       # Reusable UI sections (loaded in pages)
├── page-templates/       # Static pages (about, contact, etc.)
├── functions.php         # Main function loader
├── style.css             # Theme stylesheet (includes theme meta)
└── README.md             # You are here!
```

---

## 🚀 Features

- **Custom Post Types**: `properties`, `jobs`, `services`, `sliders`
- **Redux Framework**: Advanced theme options panel
- **Dynamic Metaboxes**: Custom fields per post type (e.g., job qualifications, project facilities)
- **Admin UI Enhancements**: Quick edit & bulk edit for relationships
- **Select2 + Swiper.js Integration**
- **Polylang compatible** (multi-language)
- **Clean Template Parts**: Modular and organized layout
- **Secure**: Nonce verification, input sanitization, role checks

---

## ⚙️ Installation

1. Upload `cob-theme` to your WordPress `/wp-content/themes/` directory.
2. Activate the theme via **Appearance > Themes**.
3. (Optional) Configure theme options via the **Redux Settings Panel**.

---

## 📑 Page Templates

- `front-page.php` – Homepage
- `about-us-page.php`
- `areas-page.php`
- `developers-page.php`
- `factories-page.php`
- `projects-page.php`
- `services-page.php`
- `hiring-page.php`
- `contact-us.php` – Includes secure contact form handling

---

## 🧪 Development Notes

- Uses `glob()` autoloading to cleanly load all PHP logic files
- Clean separation between logic and UI
- Reusable template parts
- Uses **Bootstrap Grid** for layout

---

## 📌 Missing Parts

Some files like `template-parts/home/developers.php` are placeholders.
Replace them with your actual content or components as needed.

---

## 📬 Support

For bugs, suggestions, or improvements:
Contact: **Waleed Elsefy**  
Email: [info@dido.pro](mailto:info@dido.pro)

---

## 📄 License

This theme is licensed under the MIT License.
You are free to use, modify, and distribute it for personal or commercial projects.

---

Enjoy building with **COB Theme**! 🚀
