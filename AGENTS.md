# AGENTS.md

## Purpose
This file provides instructions and conventions for AI coding agents working in the Web-Katalog-Film project, with a focus on the "gemini" area or pattern as requested.

## Project Overview
- This is a web-based film catalog project (Web-Katalog-Film).
- Main entry point: `Web Katalog Film/index.php`.
- Key folders:
  - `assets/`: Contains CSS (`css/style.css`) and JavaScript (`js/script.js`).
  - `config/`: Configuration files, e.g., `data.php`.
  - `includes/`: Common includes like `header.php` and `footer.php`.
  - `pages/`: Contains main pages such as `home.php` and `search.php`.
  - `New folder/`: Appears to be for experimental or new features (HTML, JS, CSS).
  - `reference/`: Reserved for documentation or references (currently empty).

## Conventions
- Use PHP for backend logic and HTML/CSS/JS for frontend.
- Place shared UI components in `includes/`.
- Store configuration and data logic in `config/`.
- Add new pages to `pages/` and link them from the main navigation.
- For new features or experiments, use `New folder/` until stable.

## Build/Test/Run
- No explicit build/test scripts found; project is likely run via a local web server (e.g., XAMPP/Apache) and accessed through `index.php`.
- No npm or Node.js integration detected; ignore `npm start` errors unless Node.js is added.

## Documentation
- See [README.md](README.md) for a brief project description.
- Add further documentation to `reference/` as needed.

## Gemini Pattern
- No explicit "gemini" pattern or file found. If you intend to add Gemini-specific logic, create a new file or section and document its conventions here.

---

*Update this file as the project evolves or when new conventions are established.*
