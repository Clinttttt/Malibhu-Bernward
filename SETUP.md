# SQLite Setup - No XAMPP Needed!

## Requirements:
- PHP 7.0+ with SQLite support (built-in on most systems)

## How to Run:

1. Open terminal/command prompt in the project folder:
   ```
   cd C:\Users\HomePC\Downloads\malibhu_reservation\malibhu_reservation
   ```

2. Start PHP built-in server:
   ```
   php -S localhost:8000
   ```

3. Open browser and go to:
   ```
   http://localhost:8000
   ```

## Database:
- SQLite database file `database.db` will be created automatically
- No manual database setup needed!

## Test Account:
- Register a new account through the website
- Or manually create one by accessing the database

## Notes:
- The system now uses SQLite instead of MySQL
- All SQL injection vulnerabilities have been fixed with prepared statements
- Database file will be created in the root directory on first run
