# ContentScheduler - Social Media Content Scheduling Platform

A modern, feature-rich content scheduling application built with Laravel and Alpine.js that allows users to create, schedule, and manage posts across multiple social media platforms.


## 🚀 Features

### Core Functionality
- **Multi-Platform Publishing**: Schedule posts across Twitter, Instagram, LinkedIn, and Facebook
- **Smart Scheduling**: Advanced scheduling with timezone support and optimal posting times
- **Content Management**: Rich text editor with image upload and preview capabilities
- **Character Counter**: Real-time character counting with platform-specific limits
- **Rate Limiting**: Built-in protection with max 10 posts per day per user

### User Experience
- **Dashboard Views**: Switch between list and calendar views for scheduled content
- **Analytics & Insights**: Comprehensive analytics with charts and performance tracking
- **Activity Logging**: Detailed audit trail of all user actions
- **Responsive Design**: Mobile-first design that works on all devices
- **Real-time Updates**: Live notifications and status updates

### Creative Challenges Implemented
- **📊 Post Analytics**: Detailed statistics showing posts per platform, success rates, and publishing trends
- **🚦 Rate Limiting**: Intelligent rate limiting to prevent spam (10 posts/day max)
- **📝 Activity Logging**: Comprehensive logging system tracking all user actions
- **🎨 Modern UI/UX**: Beautiful, intuitive interface with smooth animations
- **📱 Progressive Enhancement**: Works without JavaScript, enhanced with Alpine.js

## 🛠 Technology Stack

### Backend
- **Laravel 10+** - PHP framework with elegant syntax
- **Laravel Sanctum** - API authentication system
- **MySQL/PostgreSQL** - Database for data persistence
- **Laravel Commands** - Background job processing for scheduled posts

### Frontend
- **Alpine.js** - Lightweight reactive framework
- **Tailwind CSS v4** - Utility-first CSS framework
- **Chart.js** - Beautiful charts for analytics
- **Flatpickr** - Elegant date/time picker
- **Vite** - Fast build tool and development server

### Development Tools
- **PHP 8.1+** - Modern PHP features
- **Composer** - Dependency management
- **NPM** - Frontend package management
- **Laravel Artisan** - Command-line tools

## 📦 Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- Node.js 16+ and NPM
- MySQL/PostgreSQL database
- Redis Server (for caching)

### Quick Setup

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/content-scheduler.git
cd content-scheduler
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Set up the database**
```bash
# Create a fresh database with seeders
php artisan migrate:fresh --seed

# Create a test user
php artisan db:seed --class=UserSeeder

# Running Schedule Task
php artisan schedule:work
```

4. **Install JavaScript dependencies**
```bash
npm install
```

5. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

6. **Configure your database and Redis in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=content_scheduler
DB_USERNAME=your_username
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

7. **Start the development servers**
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server
npm run dev
```

8. **Visit your application**
   - Open http://localhost:8000 in your browser
   - Login with the seeded user credentials:
     - Email: `demo@contentscheduler.com`
     - Password: `password123`

### Database Schema (ERD)
![Content Scheduler ERD]
```
ERD Design/Post_Schedule_ERD.jpg
```
<img src="ERD Design/Post_Schedule_ERD.jpg" width="600"/>

### API Documentation
The API endpoints are documented in the Postman collection. You can import the collection from:
https://blue-meteor-377620.postman.co/workspace/601034f5-f451-4829-84a2-3ba5859ab33e/documentation/43071457-34da1890-802c-44f2-bc5f-1266af9a9776
```
Postman/ContentScheduler.postman_collection.json
```

## 🏗 Architecture

### Database Schema

```
Users
├── id, name, email, password
├── created_at, updated_at

Posts
├── id, title, content, image_url
├── scheduled_time, status, user_id
├── created_at, updated_at, deleted_at

Platforms
├── id, name, type
├── created_at, updated_at

PostPlatforms (Pivot)
├── post_id, platform_id
├── platform_status
├── created_at, updated_at

UserPlatforms (Pivot)
├── user_id, platform_id
├── created_at, updated_at

ActivityLogs
├── id, user_id, type, description
├── properties, ip_address, user_agent
├── created_at, updated_at
```

### API Endpoints

#### Authentication
```
POST   /api/register          - User registration
POST   /api/login             - User login
GET    /api/user              - Get current user
```

#### Posts Management
```
GET    /api/posts             - List user posts (with filters)
POST   /api/posts             - Create new post
PUT    /api/posts/{id}        - Update post
DELETE /api/posts/{id}        - Delete post
PUT    /api/posts/{id}/schedule - Schedule post
PUT    /api/posts/{id}/publish  - Publish immediately
```

#### Platform Management
```
GET    /api/platforms         - List all platforms
GET    /api/user/platforms    - Get user's connected platforms
PUT    /api/user/platforms    - Update user platform connections
```

#### Analytics & Logs
```
GET    /api/analytics         - Get user analytics
GET    /api/activity-logs     - Get user activity logs
```

### Frontend Architecture

#### Alpine.js Components
- **auth** - Global authentication state management
- **dashboard** - Post listing and calendar functionality
- **postEditor** - Post creation and editing logic
- **settings** - Platform management and preferences
- **analytics** - Chart rendering and data visualization

#### Key Features
- **Persistent Authentication**: Uses Alpine.js persistence for auth state
- **Real-time Validation**: Client-side validation with server-side verification
- **Progressive Enhancement**: Works without JavaScript, enhanced with Alpine.js
- **Responsive Design**: Mobile-first approach with Tailwind CSS

## 📱 Usage Guide

### Getting Started

1. **Create Account**: Register with your email and password
2. **Connect Platforms**: Go to Settings to enable your social media platforms
3. **Create First Post**: Use the "Create Post" button to start scheduling content

### Creating Posts

1. **Post Details**: Add title and content for your post
2. **Platform Selection**: Choose which platforms to publish to
3. **Character Limits**: Real-time counter shows limits for each platform
4. **Image Upload**: Optional image attachment with preview
5. **Scheduling**: Choose to save as draft, schedule for later, or publish immediately

### Dashboard Features

- **List View**: See all posts in a detailed list format
- **Calendar View**: Visual calendar showing scheduled posts
- **Filters**: Filter by status, date, or platform
- **Quick Actions**: Edit, delete, or change post status

### Analytics Insights

- **Performance Metrics**: Track total posts, published count, and success rates
- **Platform Distribution**: See which platforms you use most
- **Timeline Analysis**: Understand your posting patterns over time
- **Activity Tracking**: Monitor all account activities

## ⚙️ Configuration

### Environment Variables

```env
# Application
APP_NAME="ContentScheduler"
APP_ENV=production
APP_KEY=base64:your-app-key
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=content_scheduler
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Rate Limiting
POSTS_PER_DAY_LIMIT=10

# Social Media API Keys (for future integration)
TWITTER_API_KEY=your-twitter-key
INSTAGRAM_API_KEY=your-instagram-key
LINKEDIN_API_KEY=your-linkedin-key
FACEBOOK_API_KEY=your-facebook-key
```

### Customization

#### Adding New Platforms
1. Add platform to database via seeder
2. Update character limits in `resources/js/app.js`
3. Add platform icon in post editor template
4. Implement publishing logic in backend

#### Modifying Rate Limits
Update the rate limiting logic in:
- Backend: Post creation controllers
- Frontend: Usage display in settings

## 🔐 Security Features

- **Authentication**: Laravel Sanctum for secure API access
- **Authorization**: Route-based middleware protection
- **CSRF Protection**: Built-in CSRF token validation
- **Rate Limiting**: API rate limiting and daily post limits
- **Data Validation**: Server-side validation for all inputs
- **SQL Injection Prevention**: Eloquent ORM with prepared statements

## 🚀 Deployment

### Production Deployment

1. **Server Requirements**
   - PHP 8.1+ with required extensions
   - Web server (Apache/Nginx)
   - MySQL/PostgreSQL database
   - SSL certificate for HTTPS

2. **Build for production**
```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Set up cron job for scheduled posts**
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

4. **Configure web server**
   - Point document root to `/public` directory
   - Enable URL rewriting for Laravel routing

### Docker Deployment

```dockerfile
FROM php:8.1-apache
# Copy application files
# Install dependencies
# Configure Apache
# Set up scheduling
```

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run tests with coverage
php artisan test --coverage
```

### Frontend Testing
```bash
# Run JavaScript tests
npm test

# Run E2E tests
npm run test:e2e
```

## 📚 API Documentation

### Rate Limiting
- **Posts**: Maximum 10 posts per day per user
- **API Calls**: 60 requests per minute per user
- **Login Attempts**: 5 attempts per minute per IP

### Error Handling
All API responses follow a consistent format:
```json
{
  "success": true,
  "data": {},
  "message": "Success message"
}
```

Error responses:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error"]
  }
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for API changes
- Use conventional commit messages

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Support

- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs via GitHub Issues
- **Discussions**: Use GitHub Discussions for questions
- **Email**: support@contentscheduler.com

## 🎯 Roadmap

### Upcoming Features
- [ ] Real social media API integrations
- [ ] Bulk post upload via CSV
- [ ] Team collaboration features
- [ ] Advanced analytics and reporting
- [ ] Mobile app (React Native)
- [ ] AI-powered content suggestions
- [ ] Social media listening features

### Performance Improvements
- [ ] Redis caching implementation
- [ ] Queue system for background processing
- [ ] CDN integration for image handling
- [ ] Database optimization and indexing

---

**Built with ❤️ using Laravel & Alpine.js**

*ContentScheduler - Simplifying social media management, one post at a time.*
