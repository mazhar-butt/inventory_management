# Multi-Branch Inventory & Order System

A clean and structured Inventory & Sales Management System built with Laravel, Vue and Inertia.

---

## 👥 User Roles & Permissions

### 🔹 Admin

Admin has full system access:

* Create and manage **Branches**
* Create and manage **Products**
* Manage **Inventory**
* View full **Inventory Movement History**
* Assign inventory to Managers
* Control system-level settings

---

### 🔹 Manager

Manager access is limited based on Admin assignment:

* View assigned **Branch Inventory**
* View **Inventory Movements** (only for assigned branch)
* View and manage **Orders** for their branch

---

### 🔹 Sales

Sales role has limited operational access:

* Create Orders
* View their own Orders
* View their own Inventory Movements only

---

## 📊 Features

* Multi-branch inventory system
* Role-based access control
* Inventory assignment system
* Inventory movement tracking (Stock In / Stock Out)
* Clean and modern dashboard UI
* Secure authentication system

---

# ⚙️ Installation Guide

Follow these steps to install and run the project locally.

---

## 1️⃣ Clone the Repository

```bash
git clone https://github.com/mazhar-butt/inventory_management
```

---

## 2️⃣ Install Dependencies

```bash
composer install
```

If using frontend assets:

```bash
npm install
npm run dev
```

---

## 3️⃣ Setup Environment File

Copy the example environment file:

```bash
cp .env.example .env
```

Update your database credentials inside `.env` file.

---

## 4️⃣ Generate Application Key

```bash
php artisan key:generate
```

---

## 5️⃣ Run Fresh Migration with Seeder

⚠️ This will drop all tables and recreate them.

```bash
php artisan migrate:fresh --seed
```

This will:

* Create all database tables
* Insert default roles and sample data

---

## 6️⃣ Run the Application

```bash
php artisan serve
```

Open in browser:

```
http://assignments.test/dashboard
```

---

# 🚀 Pushing Code to GitHub

If pushing for the first time:

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/mazhar-butt/inventory_management
git push -u origin main
```

---

# 🗄 Default Setup Requirement

After cloning the project, you MUST run:

```bash
php artisan migrate:fresh --seed
```

Otherwise, roles and sample data will not be available.

---

# 🧹 Clean Dashboard

The system includes a clean and organized dashboard with role-based access visibility. Each user only sees features allowed for their role.

---

# 📌 Notes

* Make sure your database is created before running migrations.
* Ensure correct permissions for storage and bootstrap/cache folders.
* Use proper environment configuration for production.

---

# 📄 License

This project is open-source and available for educational or business use.
