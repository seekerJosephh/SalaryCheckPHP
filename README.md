# SalaryCheckPHP

<<<<<<< HEAD
=======

>>>>>>> b4a604745467195b4a97af6163b2279bcbd784ba
Scope

The project's scope covers a minimal viable product (MVP) for salary slip generation and viewing, focused on core functionality without advanced features like user authentication or multi-user dashboards. It includes:

Core Features:

Data Preparation: A generate.php script to create test/mock salary data (30 variables, including Khmer strings and numbers) as JSON, compress it (gzcompress), encrypt it (AES-256-CBC), and generate short URLs.
URL Handling: Support for two modes:
Direct encrypted URLs (e.g., ?enc=... with URL-safe Base64).
Database-driven short URLs (e.g., /s/abc123 via MySQL table for storage, with 6-character keys).
Web Display: An index.php page that decrypts/decompresses data (or fetches from DB), maps short keys (e.g., e for Emp_ID) to long names, and renders a responsive HTML view with Bootstrap tables showing employee info, OT details, allowances, absences, and totals.
Export Options:
PDF generation via mPDF (A4 portrait, Khmer fonts like Noto Sans Khmer, custom CSS for tables/headers).
Image export via html2canvas (PNG screenshot of the salary card).
Localization: Full Khmer Unicode support in HTML, PDF, and data (e.g., labels like "ប្រាក់ខែគោល" for basic salary).
Technical Components:
Backend: PHP 8+ with Composer dependencies (mpdf/mpdf for PDFs).
Security: Encryption (encryption.php with secret key from config.php), URL-safe encoding.
Database (Optional): MySQL for URL shortener (table: url_shortener with short_key, encrypted_data).
Fonts and Temp Files: Custom fonts (fonts/ dir for Noto Sans Khmer TTF), writable temp dir (tmp/ for mPDF processing).
Server Configuration: Web server rewrites (Apache/Nginx) for short paths (e.g., /s alias to /SalaryCheck/public/index.php).
Assumptions and Limitations:
In Scope: Local development/testing on XAMPP/macOS; mock data generation; single-user access via URLs; basic error handling (e.g., decryption failures).
Out of Scope: User authentication/login; multi-employee database; email integration; advanced analytics; mobile app; production scaling (e.g., load balancing); full Khmer PDF testing on all devices.
Dependencies: Requires PHP extensions (mbstring, gd, zlib); no external APIs; assumes a single secret key for encryption.
Deployment Scope: Designed for local (XAMPP) or simple web hosting (e.g., Apache/Nginx with PHP/MySQL). Production would need HTTPS, domain aliasing (e.g., ex.com/s), and temp file cleanup.



Purposes / Goals

The primary objectives of the Salary Check project are to provide a secure, efficient, and user-accessible system for generating, sharing, and viewing personalized salary information. Key goals include:

Secure Data Sharing: Enable the creation of shareable URLs containing encrypted salary details for employees or stakeholders, ensuring sensitive information (e.g., employee ID, name, salary components, overtime, allowances, deductions) is protected during transmission. This prevents unauthorized access by using AES-256-CBC encryption and optional database-backed shortening.
User-Friendly Access and Viewing: Allow end-users (e.g., employees) to access salary slips via short, memorable URLs (e.g., https://ex.com/s/abc123) on web browsers, displaying formatted Khmer-language information in a readable, table-based layout with Bootstrap styling.
Multi-Format Export: Facilitate easy downloading of salary slips as PDFs (using mPDF for Khmer-compatible rendering) or images (using html2canvas for PNG screenshots), supporting offline viewing and printing.
Localization and Accessibility: Support Khmer (Cambodian) language for employee names, salary dates, and labels, ensuring cultural and linguistic relevance for a target audience in Cambodia or Khmer-speaking regions.
Efficiency and Scalability: Optimize URL length for easy sharing (via compression, short variable names, and database shorteners) while maintaining data integrity for up to 30 salary variables (e.g., basic salary, OT hours, allowances, absences, totals).
Development Simplicity: Leverage PHP tools like Composer (for mPDF), encryption libraries, and optional MySQL for backend storage, making it easy to deploy on local servers (e.g., XAMPP) or production environments.
<<<<<<< HEAD
Overall, the project aims to streamline HR/salary communication in a secure, mobile-friendly way, reducing the need for email attachments or physical documents.
=======
Overall, the project aims to streamline HR/salary communication in a secure, mobile-friendly way, reducing the need for email attachments or physical documents.
>>>>>>> b4a604745467195b4a97af6163b2279bcbd784ba
