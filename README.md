# PUP Taguig Registration System

A comprehensive web-based registration and management system for Polytechnic University of the Philippines - Taguig Campus. This system handles student and faculty registration, course management, enrollment, and administrative functions.

## ✨ Key Features

### 🎓 Student Management
- **Smart Student Registration**: Dropdown-based registration with existing student lookup
- **Account Status Indicators**: Visual indicators for existing accounts (🔐) vs new registrations (📝)
- **Student Dashboard**: Personalized dashboard for course enrollment and academic information
- **Course Enrollment**: Students can enroll in available courses and sections
- **Academic Records**: View enrolled courses, grades, and academic progress

### 👨‍🏫 Faculty Management
- **Faculty Registration System**: Department-organized faculty lookup and registration
- **Faculty Dashboard**: Course management and student roster access
- **Course Assignment**: Faculty can view and manage assigned courses
- **Student Roster Management**: Access to enrolled student lists and academic records

### 🏛️ Administrative Features
- **Department Management**: Create and manage academic departments
- **Program Management**: Define and organize academic programs
- **Course Management**: Create courses with prerequisites and requirements
- **Section Management**: Organize courses into sections with schedules
- **Room Management**: Manage classroom and facility assignments
- **Term Management**: Handle academic terms and scheduling

### 🔐 Authentication & Security
- **Role-based Access Control**: Separate dashboards for students, faculty, and administrators
- **Secure Registration**: Password hashing and email verification
- **Session Management**: Secure login/logout functionality
- **Account Recovery**: Password reset and account management tools

## 🧩 Tech Stack

### Frontend
- **HTML5**: Semantic markup and modern web standards
- **CSS3**: Responsive design with custom styling and animations
- **JavaScript (ES6+)**: Dynamic interactions and API communication
- **Font Awesome**: Icon library for enhanced UI/UX

### Backend
- **PHP 8.x**: Server-side logic and API endpoints
- **MySQL**: Relational database for data persistence
- **RESTful APIs**: JSON-based API architecture for frontend-backend communication

### Development Tools
- **Git**: Version control and collaboration
- **PHP Built-in Server**: Development server for local testing
- **JSON**: Data exchange format for API communication

## 🚀 Quick Start

### Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Git for version control

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/pup-taguig-registration.git
   cd pup-taguig-registration
   ```

2. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE pup_taguig_db;
   
   # Import database schema (if available)
   mysql -u root -p pup_taguig_db < database/schema.sql
   ```

3. **Configure Database Connection**
   ```php
   // Edit api/db.php with your database credentials
   $host = 'localhost';
   $dbname = 'pup_taguig_db';
   $username = 'your_username';
   $password = 'your_password';
   ```

4. **Start Development Server**
   ```bash
   # Using PHP built-in server
   php -S localhost:8000
   
   # Or configure your web server to point to the project directory
   ```

5. **Access the Application**
   - Open your browser and navigate to `http://localhost:8000`
   - Start with the landing page: `http://localhost:8000/landing.html`

### Initial Setup

1. **Create Admin Account**: Use the admin setup scripts to create initial administrative accounts
2. **Add Departments**: Create academic departments through the department management interface
3. **Add Programs**: Define academic programs and associate them with departments
4. **Add Faculty**: Register faculty members and assign them to departments
5. **Add Students**: Import or register students into the system

## 📁 Project Structure

```
pup-taguig-registration/
├── api/                          # Backend API endpoints
│   ├── auth.php                  # Authentication API
│   ├── db.php                    # Database connection
│   ├── register.php              # Registration API
│   ├── student.php               # Student management API
│   ├── instructor.php            # Faculty management API
│   ├── course.php                # Course management API
│   ├── enrollment.php            # Enrollment API
│   └── ...                       # Other API endpoints
├── assets/                       # Static assets (if any)
├── css/                          # Stylesheets (embedded in HTML)
├── js/                           # JavaScript files
│   └── auth.js                   # Authentication utilities
├── pages/                        # HTML pages
│   ├── landing.html              # Landing page
│   ├── login.html                # Login page
│   ├── student_register.html     # Student registration
│   ├── faculty_register.html     # Faculty registration
│   ├── student-dashboard.html    # Student dashboard
│   ├── faculty-dashboard.html    # Faculty dashboard
│   ├── admin-dashboard.html      # Admin dashboard
│   └── ...                       # Other pages
├── database/                     # Database files
│   ├── schema.sql                # Database schema
│   └── migrations/               # Database migrations
├── tests/                        # Test files
└── README.md                     # This file
```

## 🔧 API Endpoints

### Authentication
- `POST /api/auth.php` - User login
- `POST /api/register.php` - User registration

### Student Management
- `GET /api/student.php` - Get students list
- `GET /api/student_list.php` - Get students with program info
- `POST /api/student.php` - Create new student

### Faculty Management
- `GET /api/instructor.php` - Get faculty list with departments
- `POST /api/instructor.php` - Create new faculty member

### Course Management
- `GET /api/course.php` - Get courses list
- `GET /api/enrollment.php` - Get enrollment data
- `POST /api/enrollment.php` - Enroll student in course

### Administrative
- `GET /api/department.php` - Get departments
- `GET /api/program.php` - Get academic programs
- `GET /api/term.php` - Get academic terms

## 🗂️ Git Workflow Guidelines

### Branch Naming Convention
```
feature/feature-name          # New features
bugfix/bug-description        # Bug fixes
hotfix/critical-fix           # Critical production fixes
release/version-number        # Release preparation
```

### Commit Message Format
```
type(scope): description

Types:
- feat: New feature
- fix: Bug fix
- docs: Documentation changes
- style: Code style changes
- refactor: Code refactoring
- test: Adding tests
- chore: Maintenance tasks

Examples:
feat(auth): add faculty registration system
fix(api): resolve student lookup database query
docs(readme): update installation instructions
```

### Development Workflow

1. **Create Feature Branch**
   ```bash
   git checkout -b feature/new-feature-name
   ```

2. **Make Changes and Commit**
   ```bash
   git add .
   git commit -m "feat(scope): description of changes"
   ```

3. **Push and Create Pull Request**
   ```bash
   git push origin feature/new-feature-name
   # Create PR on GitHub
   ```

4. **Code Review and Merge**
   - Request code review from team members
   - Address feedback and make necessary changes
   - Merge to main branch after approval

### Code Standards

- **PHP**: Follow PSR-12 coding standards
- **JavaScript**: Use ES6+ features and consistent formatting
- **HTML**: Use semantic HTML5 elements
- **CSS**: Use consistent naming conventions (BEM methodology recommended)
- **Database**: Use descriptive table and column names with consistent prefixes

### Testing Guidelines

- Test all API endpoints before committing
- Verify frontend functionality across different browsers
- Test user registration and authentication flows
- Validate database operations and data integrity
- Use the provided test files in the `/test_*` files for API testing

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Contact the development team
- Check the documentation in the `/docs` folder (if available)

## 🔄 Version History

- **v1.0.0** - Initial release with basic registration and management features
- **v1.1.0** - Added faculty registration system and improved UI/UX
- **v1.2.0** - Enhanced course management and enrollment features

---

**Polytechnic University of the Philippines - Taguig Campus**  
*Empowering students through technology and innovation*