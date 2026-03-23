# TechExpo Newsletter Platform

<div align="center">
  <img src="path/to/logo.png" alt="TechExpo Logo" width="200"/>
  
  **A dynamic newsletter management platform for technology content creators**
  
  [![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php)](https://php.net)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql)](https://www.mysql.com)
  [![Bootstrap](https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=flat&logo=bootstrap)](https://getbootstrap.com)
</div>

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technologies](#technologies)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [User Roles](#user-roles)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About

TechExpo is a comprehensive web-based newsletter management system designed specifically for the technology sector. It empowers content creators, tech professionals, and communities to create, schedule, and distribute curated technology news and updates to their subscribers.

The platform provides an intuitive interface for managing articles, newsletters, and subscriber relationships while offering readers the ability to browse, subscribe, and engage with content organized by topic and niche.

**Academic Context:** This project was developed as a final year Computer Science degree project at Université Abdelhamid Ibn Badis - Mostaganem (2024-2025).

---

## ✨ Features

### For Readers/Subscribers
- 📰 Browse and search technology articles
- 🔖 Save articles for later reading
- 💬 Comment and like articles
- 👤 Follow favorite authors
- 📧 Receive personalized newsletter subscriptions
- 🔐 Secure authentication with email verification
- 🌐 OAuth integration (Google Login)

### For Authors/Content Creators
- ✍️ Rich-text article editor powered by TinyMCE
- 📝 Create, edit, and preview articles
- 📊 Comprehensive analytics dashboard
  - View counts
  - Engagement metrics
  - Follower statistics
- 📅 Schedule article publishing
- 💾 Save drafts for later completion
- 📧 Create and send newsletters to followers
- 🎯 Target specific subscriber groups
- 📈 Track newsletter performance

### For Administrators
- 👥 User management and moderation
- 📨 System-wide email broadcasts
- 💬 Review and respond to contact messages
- 🛡️ Monitor system activity
- ⚙️ Configure platform settings

---

## 🛠️ Technologies

### Backend
- **PHP 8.1+** - Server-side scripting language
- **Laravel 12.x** - PHP framework with MVC architecture
- **MySQL 8.0+** - Relational database management
- **PDO** - PHP Data Objects for secure database interactions

### Frontend
- **HTML5** - Markup structure
- **CSS3** - Styling
- **Bootstrap 5** - Responsive UI framework
- **JavaScript** - Client-side interactivity
- **TinyMCE** - WYSIWYG rich-text editor

### Development Tools
- **XAMPP** - Local development environment (Apache/MySQL)
- **VS Code** - Code editor
- **Composer** - PHP dependency management
- **NPM** - JavaScript package management

---

## 📦 Prerequisites

Before installing TechExpo, ensure you have the following:

- **PHP** >= 8.1
- **Composer** >= 2.0
- **MySQL** >= 8.0
- **Node.js** >= 16.x and NPM
- **Apache/Nginx** web server
- Modern web browser (Chrome, Firefox, Edge)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/techexpo.git
cd techexpo
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

Create a MySQL database:

```sql
CREATE DATABASE techexpo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techexpo
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Seed Database (Optional)

```bash
php artisan db:seed
```

### 8. Build Frontend Assets

```bash
npm run build
# For development with hot reload:
# npm run dev
```

### 9. Start Development Server

```bash
php artisan serve
```

Access the application at `http://localhost:8000`

---

## ⚙️ Configuration

### Mail Configuration

Configure your email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@techexpo.com
MAIL_FROM_NAME="${APP_NAME}"
```

### TinyMCE API Key

Obtain an API key from [TinyMCE](https://www.tiny.cloud/) and add it to your `.env`:

```env
TINYMCE_API_KEY=your_tinymce_api_key
```

### Google OAuth (Optional)

For Google login integration:

```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## 📖 Usage

### Creating an Account

1. Navigate to the registration page
2. Provide email, username, and password
3. Verify your email address via the confirmation link
4. Log in to access the platform

### Writing an Article (Authors)

1. Navigate to the Dashboard
2. Click "Create Article"
3. Use the rich-text editor to compose your content
4. Add categories and tags
5. Choose to:
   - **Save as Draft** - Continue editing later
   - **Schedule** - Set a future publish date
   - **Publish** - Make it live immediately

### Creating a Newsletter

1. Go to Dashboard → Newsletters
2. Click "Create Newsletter"
3. Compose your newsletter content
4. Select recipients:
   - All followers
   - Specific subscriber groups
5. Schedule or send immediately

### Reading and Engaging (Readers)

1. Browse articles on the home page
2. Use search to find specific topics
3. Save articles for later reading
4. Follow authors you enjoy
5. Leave comments and likes

---

## 📁 Project Structure

```
techexpo/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Application controllers
│   │   └── Middleware/      # HTTP middleware
│   ├── Models/              # Eloquent models
│   └── Mail/                # Mailable classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── public/                  # Public assets (CSS, JS, images)
├── resources/
│   ├── views/               # Blade templates
│   └── js/                  # JavaScript files
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
├── storage/                 # Application storage
├── tests/                   # Automated tests
├── .env.example             # Example environment file
├── composer.json            # PHP dependencies
├── package.json             # JavaScript dependencies
└── README.md                # This file
```

---

## 👥 User Roles

### Reader (Subscriber)
- **Access Level:** Public content access
- **Capabilities:** Browse, read, comment, like, subscribe

### Author (Content Creator)
- **Access Level:** Content management access
- **Capabilities:** All Reader capabilities + create/edit articles, send newsletters, view analytics

### Administrator
- **Access Level:** Full system access
- **Capabilities:** User management, system configuration, moderation, system-wide communications

---

## 🔒 Security

TechExpo implements multiple security measures:

- **CSRF Protection** - Laravel's built-in CSRF tokens
- **XSS Prevention** - Input sanitization and output escaping
- **SQL Injection Protection** - PDO prepared statements
- **Password Hashing** - Bcrypt encryption
- **Email Verification** - Confirmed email addresses
- **Role-Based Access Control** - Permission-based authorization
- **Session Management** - Secure session handling
- **Rate Limiting** - Protection against brute force attacks

### Password Requirements

- Minimum 8 characters, maximum 30
- At least 1 uppercase letter
- At least 1 lowercase letter
- At least 1 number
- At least 1 special character
- No whitespace allowed

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

Please ensure your code follows Laravel coding standards and includes appropriate tests.

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Nairf Moatamen**

- University: Université Abdelhamid Ibn Badis - Mostaganem
- Department: Mathematics and Computer Science
- Academic Year: 2024-2025
- Supervisor: Henni Karim Abdelkader

---

## 📧 Contact & Support

For questions, issues, or suggestions:

- Open an issue on GitHub
- Email: contact@techexpo.com
- Documentation: [docs.techexpo.com](https://docs.techexpo.com)

---

## 🙏 Acknowledgments

- Laravel Framework Documentation
- TinyMCE Editor
- Bootstrap Framework
- PHP Community
- Université Abdelhamid Ibn Badis - Mostaganem

---

## 📚 References

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [TinyMCE Documentation](https://www.tiny.cloud/docs/)
