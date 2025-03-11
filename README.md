# ZenFlow - Daily Management System

ZenFlow is a modern personal management system designed to help you organize tasks, journal your thoughts, and track your productivity over time. With a clean, intuitive interface and thoughtful features, ZenFlow aims to bring mindfulness and structure to your daily routine.

## Features

### Dashboard
* **Weekly Productivity Visualization**: Track your task completion rates with an interactive chart showing completed vs. total tasks
* **Daily Inspiration**: Enjoy a different Rumi quote on each visit to keep you motivated
* **Recent Activity**: Quick access to your latest tasks and journal entries

### Task Management
* **Priority Levels**: Categorize tasks by urgency (high, medium, low)
* **Completion Tracking**: Mark tasks as complete and see your progress
* **Activity History**: Keep a record of all your completed tasks

### Journaling
* **Daily Reflections**: Record your thoughts, ideas, and experiences
* **Simple Editor**: Distraction-free writing environment
* **Journal Archive**: Browse through past entries by date

### Random Picker ("Do Things?")
* **Decision Assistant**: When you're overwhelmed with choices, let the random picker decide for you
* **Task Integration**: Load options directly from your existing tasks or add custom options

## Technical Details

### Built With
* PHP/Laravel (backend framework)
* JavaScript (frontend interactivity)
* Chart.js (productivity visualization)
* CSS (custom styling)

### System Requirements
* PHP 8.0+
* MySQL 5.7+
* Web server (Apache/Nginx)
* Composer (for dependency management)

## Installation

1. Clone the repository
```
git clone https://github.com/yourusername/zenflow.git
cd zenflow
```

2. Install dependencies
```
composer install
npm install
```

3. Set up environment file
```
cp .env.example .env
php artisan key:generate
```

4. Configure your database in the `.env` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zenflow
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations
```
php artisan migrate
```

6. Create your user account by editing the DatabaseSeeder.php file
```php
// In database/seeders/DatabaseSeeder.php
// Change the user details to your own
User::create([
    'name' => 'Your Name',
    'username' => 'your_username',
    'password' => Hash::make('your_password')
]);
```

7. Run the database seeder
```
php artisan db:seed
```

8. Start the development server
```
php artisan serve
```

9. Visit `http://localhost:8000` in your browser

## Usage

### First-time Setup
1. Edit the database seeder to create your account as shown in installation step 6
2. Set up your initial categories and priorities
3. Add your first task and journal entry

### Daily Workflow
1. Check your dashboard for an overview of your day
2. Review and update your task list
3. Take time to journal your thoughts and experiences
4. Use the random picker when you need help making decisions

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgements

* Rumi for the inspirational quotes
* Chart.js for the beautiful data visualizations
* Laravel for the powerful backend framework
* Lucide Icons for the UI icons

Created with ❤️ for better personal productivity and mindfulness.
