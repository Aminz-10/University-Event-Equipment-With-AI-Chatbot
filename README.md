# UniEquip - University Equipment Rental Management System with AI Assistant

A modern web-based equipment rental management system for universities with an integrated AI chatbot assistant. Built with PHP, MySQL, and OpenRouter AI API.

## 🌟 Features

### Core Features
- **Equipment Management** - Browse, search, and view detailed equipment information
- **Booking System** - Reserve equipment for your academic needs with date and quantity selection
- **Booking Approval Workflow** - Admin approval system for all equipment requests
- **Real-time Equipment Status** - Check availability and current booking status
- **Multi-User Support** - Student and Admin roles with different permissions
- **Club Management** - Support for organization-based equipment bookings
- **Late Return Tracking** - Monitor overdue equipment returns
- **Analytics & Reports** - Generate booking trends and equipment usage reports

### AI Assistant Features
- **Intelligent Equipment Search** - Ask natural language questions about equipment availability
- **Smart Recommendations** - Get equipment suggestions based on your needs
- **Booking Guidance** - Step-by-step booking process instructions
- **Real-time Availability** - Equipment stock levels and status updates
- **Category Browsing** - Explore equipment by categories
- **Booking Status Tracking** - Check your current reservations
- **24/7 Support** - Always-available AI support for equipment queries
- **Chat History** - Persistent chat conversations across sessions
- **Message Management** - Delete and manage chat messages

## 📸 Screenshots

### Dashboard
![Screenshot_23-11-2025_115219_localhost](https://github.com/user-attachments/assets/84866d46-6ea5-40a3-9dd9-6e647993ea21)


### AI Assistant Chat Interface
<img width="1909" height="1030" alt="screenshot-1763869808053" src="https://github.com/user-attachments/assets/72db2bd4-0d1e-4aec-8ee7-a6ad27e400a7" />
<img width="1909" height="1030" alt="screenshot-1763869915864" src="https://github.com/user-attachments/assets/304f73c8-2d7d-423d-bd54-9ebcfecdf7fd" />
<img width="1909" height="1030" alt="screenshot-1763869882067" src="https://github.com/user-attachments/assets/e31727a5-ae7a-4fad-be1c-500333663292" />


### Booking Confirmation
![Screenshot_23-11-2025_114858_localhost](https://github.com/user-attachments/assets/b193c04b-e743-43c0-bf9e-afae0b72d40a)


## 🛠️ Tech Stack

### Backend
- **Language**: PHP 7.2+
- **Database**: MySQL
- **API**: OpenRouter AI API
- **Authentication**: Session-based login system

### Frontend
- **HTML5** & **CSS3**
- **Bootstrap 5** - Responsive design framework
- **JavaScript (Vanilla)** - Interactive UI without dependencies
- **Font Awesome 6** - Icon library
- **Google Fonts (Inter)** - Typography

### Database Schema
- `equipment` - Equipment inventory
- `booking` - Booking records
- `booking_equipment` - Equipment items in bookings
- `user` - Student accounts
- `admin` - Administrator accounts
- `club` - Organization management
- `late_return` - Late return tracking

## 🚀 Getting Started

### Prerequisites
- PHP 7.2 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- OpenRouter API Key

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/UniEquip.git
cd UniEquip
```

2. **Database Setup**
```bash
# Import the database schema
mysql -u root -p < if0_39265998_system.sql
```

3. **Configure Database Connection**
Edit `config.php` with your database credentials:
```php
$host = "localhost";
$user = "root";
$password = "your_password";
$database = "your_database_name";
```

4. **Set OpenRouter API Key**
In `ai_openrouter_api.php`, update:
```php
$api_key = "your_openrouter_api_key";
```

5. **Deploy Files**
- Upload `htdocs/` directory to your web server
- Ensure proper file permissions (755 for directories, 644 for files)

6. **Access the Application**
- Student Portal: `http://localhost/htdocs/index.html`
- Admin Portal: `http://localhost/htdocs/admin_dashboard.php`

## 📖 Usage Guide

### For Students

#### 1. **Dashboard**
- View upcoming bookings
- Quick access to bookings, profile, and AI assistant
- Real-time notification of booking status

#### 2. **Browse Equipment**
- Use the AI assistant to ask about equipment availability
- Examples:
  - "How many cameras are available?"
  - "Show me all audio equipment"
  - "What are the categories?"

#### 3. **Make a Booking**
1. Click "New Booking" or navigate to Equipment
2. Select equipment and quantities
3. Choose start and end dates
4. Review booking details
5. Submit for admin approval
6. Receive approval within 24 hours

#### 4. **AI Assistant**
- Access from any page via floating chat widget
- Or visit dedicated AI Assistant page
- Features:
  - Real-time equipment availability queries
  - Booking history review
  - Step-by-step booking guidance
  - Category exploration
  - Message deletion and chat history

### For Administrators

#### 1. **Dashboard**
- Overview of pending bookings
- Equipment management
- Club administration
- Analytics and reports

#### 2. **Booking Approval**
- Review pending equipment requests
- Approve or deny bookings
- Provide feedback to students

#### 3. **Equipment Management**
- Add, edit, or delete equipment
- Update stock levels
- Manage equipment conditions
- Track late returns

#### 4. **Reports**
- Monthly booking trends
- Most-booked equipment
- Club booking statistics
- Late return reports

## 🤖 AI Assistant Details

### Supported Query Types

#### Equipment Availability
```
"How many projectors are available?"
"Are there cameras in stock?"
"Projector availability"
```

#### Equipment Categories
```
"Show me all audio equipment"
"What categories are available?"
"Furniture equipment"
```

#### Booking Information
```
"Check my bookings"
"What is my booking status?"
"Show my current reservations"
```

#### Booking Guidance
```
"How to book equipment?"
"Guide me through booking"
"What is the booking process?"
```

### Response Format
- Plain text with emoji indicators
- Visual progress bars for availability
- Equipment status with condition indicators
- Booking summaries with key information
- Step-by-step process guides

## 🔐 Security Features

- **Session Authentication** - Secure login system
- **Prepared Statements** - SQL injection prevention
- **Role-Based Access Control** - Student vs Admin permissions
- **Input Validation** - Sanitize all user inputs
- **CSRF Protection** - Secure form submissions
- **Password Hashing** - Secure password storage

## 📁 Project Structure

```
htdocs/
├── index.html                      # Landing page
├── login.php                       # Authentication
├── config.php                      # Database config
├── db.php                          # Database connection
├── ai_openrouter_api.php          # AI Assistant backend
├── chatbot_widget.php             # Reusable chat widget
├── chatbot.php                    # Full chatbot page
│
├── User Portal/
│   ├── user_dashboard.php         # Student dashboard
│   ├── user_profile.php           # Profile management
│   ├── user_edit_profile.php      # Edit profile
│   ├── available_equipment.php    # Browse equipment
│   ├── addBooking.php             # New booking
│   ├── user_view_booking.php      # View bookings
│   ├── return_equipment.php       # Return booked items
│   └── user_style.css             # User styling
│
├── Admin Portal/
│   ├── admin_dashboard.php        # Admin overview
│   ├── admin_view_equipment.php   # Equipment list
│   ├── add_equipment.php          # Add equipment
│   ├── update_equipment.php       # Edit equipment
│   ├── delete_equipment.php       # Remove equipment
│   ├── admin_approve_booking.php  # Approve requests
│   ├── admin_view_all_record.php  # All bookings
│   ├── admin_edit_profile.php     # Admin profile
│   ├── view_club.php              # Club management
│   ├── report.php                 # Generate reports
│   └── adminGraphLateReturn.php   # Late return graph
│
├── Reports/
│   ├── report_club_bookingcount.php
│   ├── report_month_bookingcount.php
│   ├── report_mostequip_month.php
│   └── get_monthly_trends.php
│
├── API/
│   ├── get_equipment_late_returns.php
│   ├── get_stats.php
│   └── get_monthly_trends.php
│
├── Assets/
│   ├── style.css                  # Main styling
│   ├── user_style.css             # User-specific styling
│   └── uploads/                   # File uploads
│
└── Database/
    └── if0_39265998_system.sql    # Database schema
```

## 🔧 API Endpoints

### AI Chatbot API
**Endpoint**: `/ai_openrouter_api.php`

**Method**: POST

**Request**:
```json
{
  "question": "How many cameras are available?",
  "user_id": "student_id"
}
```

**Response**:
```json
{
  "answer": "📷 Camera Availability: 8 available units out of 10 total",
  "type": "availability",
  "data": {
    "equipment": "Camera",
    "available": 8,
    "total": 10
  }
}
```

## 🎨 Design Features

### Modern UI/UX
- **Gradient Design** - Purple to blue gradient color scheme
- **Responsive Layout** - Works on desktop, tablet, and mobile
- **Smooth Animations** - Subtle transitions and slide effects
- **Clean Typography** - Inter font for excellent readability
- **Accessibility** - WCAG compliant colors and contrast

### Chat Interface
- **Sidebar Navigation** - Quick questions and help shortcuts
- **Smooth Scrolling** - Invisible scrollbars for clean look
- **Message Positioning** - User messages right, bot messages left
- **Timestamps** - All messages timestamped for reference
- **Delete Functionality** - Remove messages with confirmation modal
- **Persistent History** - Chat history saved in localStorage

## 🐛 Troubleshooting

### Common Issues

**Q: "Unable to fetch availability data" error**
- A: Check OpenRouter API key in `ai_openrouter_api.php`
- Verify API key has valid credits
- Check database connection in `config.php`

**Q: Chat widget not appearing on pages**
- A: Verify `chatbot_widget.php` is included before `</body>` tag
- Check browser console for JavaScript errors
- Clear browser cache and cookies

**Q: Booking approval not working**
- A: Ensure logged in as admin
- Check user role in database (role = 'admin')
- Verify booking exists in database

**Q: Equipment not showing in availability search**
- A: Check equipment status in database (should be active)
- Verify equipment has name and quantity fields
- Ensure quantity is greater than 0

## 📊 Database Queries

### Check Equipment Availability
```sql
SELECT name, qty, status FROM equipment WHERE name LIKE '%camera%';
```

### View User Bookings
```sql
SELECT * FROM booking WHERE stud_num = 'STUDENT_ID' ORDER BY start_date DESC;
```

### Get Booking Status
```sql
SELECT id_booking, status, start_date, end_date FROM booking WHERE status = 'pending';
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Team

- **Developer**: Amin Abudin
- **Institution**: Universiti Teknologi MARA (UiTM)
- **System**: University Equipment Rental Management

## 📞 Support

For support, contact:
- **Email**: info@uniequip.edu.my
- **Phone**: +603-5544 2000
- **Website**: https://www.uniequip.edu.my

## 🙏 Acknowledgments

- OpenRouter AI for the API integration
- Bootstrap for the responsive framework
- Font Awesome for the icon library
- UiTM for the project opportunity

## ⚠️ Important Notes

- Keep OpenRouter API key secure - never commit to repository
- Update database credentials in production
- Use HTTPS in production environment
- Regularly backup database
- Monitor API usage for cost management
- Test all features before deploying to production

## 🔄 Version History

### v1.0.0 (Current)
- Initial release
- Equipment management system
- AI chatbot integration
- Booking system with approval workflow
- Admin dashboard and analytics
- Responsive design for all devices
- Chat history and persistent conversations

---

**Made with ❤️ for UiTM students**

Last Updated: November 23, 2025

