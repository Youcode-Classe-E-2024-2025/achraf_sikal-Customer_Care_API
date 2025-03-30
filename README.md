# Customer_Car_API

## 🚀 Features
- User authentication & authorization (Laravel Sanctum)
- CRUD operations for managing resources
- Request validation
- Database migrations
- Logging & error handling

---

## 📦 Installation

### **1. Clone the Repository**
```sh
git clone https://github.com/Youcode-Classe-E-2024-2025/achraf_sikal-Customer_Car_API.git

cd achraf_sikal-Customer_Car_API
```

### **2. Install Dependencies**
Make sure you have Composer installed, then run:
```sh
composer install
```
### **3. Set Up Environment**
Copy the .env.example file and update it with your database credentials:
```sh
cp .env.example .env
```
Then generate the application key:
```sh
cp .env.example .env
```
### **4. Set Up Database**
Make sure your database is running and update the .env file:

```ini
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```
Then, run migrations:
```sh
php artisan migrate
```
### Start the Laravel Development Server
```sh
php artisan serve
```
### install react Dependencies
```sh
cd frontend
npm install
```

### Start the react Development Server
```sh
npm run dev
```
