# Online Examination System

## Overview

The Online Examination System is a comprehensive platform designed to facilitate the creation, management, and execution of exams for students. It supports various types of questions, including multiple-choice, single-choice, and coding questions. The system also includes features for teachers to manage student records, view results, and send announcements.

## Features

### General Features
- **User Authentication**: Secure login for students, teachers, and administrators.
- **Role-Based Access**: Different dashboards and functionalities for students, teachers, and administrators.
- **Session Management**: Secure session handling to ensure user data is protected.
- **Error Handling**: Comprehensive error handling and logging for debugging and maintenance.

### Teacher Features
- **Exam Management**: Create, edit, and delete exams.
- **Question Management**: Add, edit, and delete questions for exams.
- **Student Records**: View and manage student records.
- **Results**: View and manage exam results.
- **Announcements**: Send announcements to students.

### Student Features
- **Exam Portal**: Take exams with a timer and full-screen mode to prevent cheating.
- **Results**: View exam results and performance.
- **Announcements**: Receive announcements from teachers.

### Admin Features
- **User Management**: Manage users, including students, teachers, and other administrators.
- **Assessment Configuration**: Configure and schedule assessments.
- **System Monitoring**: Monitor system performance and user activity.
- **Reports**: Generate and export reports.
- **Settings**: Manage system settings, user roles, and API configurations.

## Installation

### Prerequisites
- PHP 7.0 or higher
- MySQL 5.6 or higher
- Web server (Apache, Nginx, etc.)

### Steps
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/yourusername/assessment-system-main.git
   cd online-examination-system
   ```

2. **Database Setup**:
   - Create a new MySQL database.
   - Import the database schema from `db_eval.sql`.

3. **Configuration**:
   - Update the database connection settings in `config.php`.

4. **Web Server Configuration**:
   - Configure your web server to point to the project directory.
   - Ensure PHP and MySQL are properly configured.

5. **Access the Application**:
   - Open your web browser and navigate to the application URL.

## Usage

### Logging In
- **Students**: Navigate to `login_student.php` and log in with your credentials.
- **Teachers**: Navigate to `login_teacher.php` and log in with your credentials.
- **Administrators**: Navigate to `login_Admin.php` and log in with your credentials.

### Dashboard
- **Students**: Access your dashboard to view exams, results, and announcements.
- **Teachers**: Access your dashboard to manage exams, students, and view results.
- **Administrators**: Access your dashboard to manage users, configure assessments, and monitor system performance.

## Contributing

We welcome contributions to improve the Online Examination System. Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Make your changes and commit them (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a new Pull Request.

## License

This project is licensed under the MIT License. See the `LICENSE` file for more details.

## Contact

For any questions or support, please contact the project maintainers at [email@example.com](mailto:email@example.com).

---

Thank you for using the Online Examination System! We hope it serves your needs effectively.