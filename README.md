# Daily Habit Tracker CMS

A professional, feature-rich habit tracking application built with modern web technologies. Track your daily habits, monitor progress, and build better routines with an intuitive and responsive interface.

## 🚀 Project Overview

The Daily Habit Tracker CMS is a comprehensive web application designed to help users build and maintain positive habits through systematic tracking, progress monitoring, and data-driven insights. The application provides a clean, modern interface that makes habit formation engaging and sustainable.

## 🛠️ Technologies Used

### Frontend
- **HTML5** - Semantic markup and modern web standards
- **CSS3** - Advanced styling with gradients, animations, and responsive design
- **JavaScript (ES6+)** - Modern JavaScript for interactive features
- **Bootstrap 5** - Responsive UI framework for mobile-first design
- **Chart.js** - Interactive charts and data visualization
- **FontAwesome 6** - Professional icon library for enhanced UI elements

### Backend
- **PHP 7.4+** - Server-side scripting and application logic
- **MySQL/MariaDB** - Relational database management
- **PDO** - Secure database connections with prepared statements
- **Sessions** - User authentication and state management

### Design & UX
- **FontAwesome Icons** - Professional iconography and visual elements
- **CSS Gradients** - Modern, attractive color schemes
- **Responsive Design** - Mobile-first approach for all devices
- **Smooth Animations** - Enhanced user experience with CSS transitions

## ✨ Key Features

### 🔐 User Authentication
- Secure user registration and login system
- Password hashing with bcrypt for maximum security
- Session-based authentication with automatic timeout
- Demo account included for immediate testing

### 📝 Habit Management
- Create, edit, and delete habits with rich metadata
- Categorize habits (Health, Education, Wellness, etc.)
- Set target frequencies (daily/weekly)
- Custom color coding for visual organization
- Rich descriptions and notes support

### 📊 Progress Tracking
- Daily habit completion tracking with timestamps
- Add detailed notes and observations for each session
- Real-time progress updates and visual indicators
- Completion status with color-coded feedback

### 📈 Analytics & Reports
- Comprehensive completion rate statistics
- Interactive daily trend charts using Chart.js
- Habit performance analysis and insights
- Date range filtering (7, 30, 90 days)
- Detailed progress reports with visual data

### 🎨 User Interface
- Modern, responsive Bootstrap 5 design
- Mobile-friendly interface for on-the-go tracking
- Intuitive navigation with active state indicators
- Clean, professional appearance with custom branding
- Smooth animations and hover effects

## 👥 User Roles

### Regular Users
- **Authentication**: Register, login, and manage account
- **Habit Management**: Create, edit, and delete personal habits
- **Daily Tracking**: Mark habits as completed and add notes
- **Progress Monitoring**: View personal statistics and trends
- **Data Export**: Access personal habit data and reports

### System Administrator
- **User Management**: Monitor user accounts and activity
- **Database Maintenance**: Manage data integrity and backups
- **System Configuration**: Update application settings
- **Performance Monitoring**: Track system usage and optimization

## 📁 Project Structure

```
daily-habit-tracker/
├── sql/                      # Database files
│   ├── setup.sql             # Database schema creation
│   └── sample_data.sql       # Sample data and demo accounts
├── index.php                 # Main entry point (redirects to login)
├── login.php                 # Authentication system
├── dashboard.php             # Main dashboard
├── habits.php                # Habit management
├── track.php                 # Daily habit tracking
├── progress.php              # Analytics and reports
├── logout.php                # Session termination
├── .htaccess                 # Apache configuration
└── README.md                 # Project documentation
```

## 🚀 Setup Instructions

### Prerequisites
- **PHP 7.4 or higher** - Modern PHP with full feature support
- **MySQL 5.7+ or MariaDB 10.2+** - Relational database system
- **Web Server** - Apache/Nginx with PHP support
- **Local Environment** - XAMPP, WAMP, or similar development stack

### Installation Steps

1. **Clone or Download the Project**
   ```bash
   git clone https://github.com/soikot-shahriaar/daily-habit-tracker
   cd daily-habit-tracker
   ```

2. **Database Setup**
   - Create a new MySQL database named `habit_tracker_cms`
   - Import the database schema: `sql/setup.sql`
   - Import sample data: `sql/sample_data.sql`

3. **Configuration**
   - Update database connection details in PHP files if needed
   - Default settings:
     - Host: `localhost`
     - Database: `habit_tracker_cms`
     - Username: `root`
     - Password: `` (empty)

4. **Web Server Configuration**
   - Place files in your web server's document root
   - Ensure PHP has write permissions for sessions
   - Access via: `http://localhost/daily-habit-tracker/`

5. **Verify Installation**
   - Access the application in your browser
   - Login with demo account: `demo_user` / `Demo123!`
   - Test all features and functionality

## 📱 Usage Guide

### Getting Started
1. **Access Application**: Navigate to the project URL
2. **Create Account**: Register with email and password
3. **Login**: Access your personalized dashboard
4. **First Habit**: Create your initial habit routine

### Creating Habits
1. Navigate to "Habits" page
2. Fill in habit details (title, description, category)
3. Choose frequency and visual color
4. Save and start tracking

### Daily Tracking
1. Go to "Track" page each day
2. Mark habits as completed
3. Add notes about your progress
4. Save daily progress

### Viewing Progress
1. Visit "Progress" page
2. Select date range for analysis
3. Review completion rates and trends
4. Analyze habit performance

### Dashboard Overview
- Quick access to all features
- Daily statistics and insights
- Recent habit activity
- Quick action buttons

## 🎯 Intended Use

### Personal Development
- **Individual Habit Building**: Track personal goals and routines
- **Progress Monitoring**: Visualize improvement over time
- **Motivation**: Celebrate achievements and maintain momentum
- **Accountability**: Daily tracking creates commitment

### Educational Purposes
- **Learning PHP Development**: Study modern PHP practices
- **Database Design**: Understand relational database concepts
- **Frontend Development**: Learn responsive design principles
- **Full-Stack Development**: Complete web application example

### Professional Applications
- **Small Business**: Employee habit tracking and wellness programs
- **Healthcare**: Patient compliance and treatment adherence
- **Education**: Student study habits and academic progress
- **Fitness**: Workout routines and health goals

### Demo and Portfolio
- **Developer Portfolio**: Showcase full-stack development skills
- **Client Demonstrations**: Present habit tracking solutions
- **Learning Resource**: Educational material for developers
- **Template Base**: Foundation for custom habit tracking apps

## 🔧 Customization

### Adding New Habit Categories
Edit the category options in `habits.php`:
```php
<option value="New Category">New Category</option>
```

### Modifying Date Ranges
Update the progress page date options in `progress.php`:
```php
<a href="?days=14" class="btn btn-outline-primary">Last 14 Days</a>
```

### Styling Changes
- Modify CSS variables in `:root` for color schemes
- Update Bootstrap classes for layout changes
- Customize Chart.js options in `progress.php`
- Add new animations and transitions
- Replace FontAwesome icons with alternative icon sets if needed

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
- Verify MySQL service is running
- Check database credentials and permissions
- Ensure database exists and is accessible

**Login Not Working**
- Verify database tables are created
- Check if sample data is imported
- Clear browser cookies and sessions
- Verify PHP session configuration

**Page Not Loading**
- Check PHP error logs for specific issues
- Verify file permissions and ownership
- Ensure web server is configured correctly
- Check for syntax errors in PHP files

### Debug Mode
Enable error reporting in PHP files for development:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📈 Future Enhancements

### Planned Features
- **Email Notifications**: Daily reminders and progress updates
- **Mobile App**: Native iOS and Android applications
- **Social Features**: Share progress and compete with friends
- **Advanced Analytics**: Machine learning insights and predictions
- **Habit Streaks**: Track consecutive completion records
- **Goal Setting**: Milestone tracking and achievement badges

### Technical Improvements
- **API Development**: RESTful API for third-party integrations
- **Real-time Updates**: WebSocket implementation for live data
- **Offline Support**: Progressive Web App capabilities
- **Performance Optimization**: Caching and database optimization

## 🤝 Contributing

### Development Guidelines
1. Fork the repository
2. Create a feature branch
3. Follow coding standards and best practices
4. Test thoroughly before submitting
5. Submit a detailed pull request

### Code Standards
- **PHP**: PSR-12 coding standards
- **CSS**: BEM methodology for class naming
- **JavaScript**: ES6+ with consistent formatting
- **Database**: Proper indexing and query optimization
- **Icons**: FontAwesome 6 icon library for consistent iconography

## 📄 License

**License for RiverTheme**

RiverTheme makes this project available for demo, instructional, and personal use. You can ask for or buy a license from [RiverTheme.com](https://RiverTheme.com) if you want a pro website, sophisticated features, or expert setup and assistance. A Pro license is needed for production deployments, customizations, and commercial use.

**Disclaimer**

The free version is offered "as is" with no warranty and might not function on all devices or browsers. It might also have some coding or security flaws. For additional information or to get a Pro license, please get in touch with [RiverTheme.com](https://RiverTheme.com).

## 🆘 Support

### Getting Help
- **Documentation**: Review this README thoroughly
- **Code Comments**: Check inline documentation in source files
- **Professional Support**: Contact RiverTheme for Pro assistance


**Developed by RiverTheme for better habit building and personal development**

*Transform your daily routines into lasting positive habits with the Daily Habit Tracker CMS.*
