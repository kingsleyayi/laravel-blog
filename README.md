# Blog API

A Laravel-based RESTful API for managing blog posts with authors and comments.

## Features

- **Paginated API endpoint for blog posts**
- **Filter posts by author or title**
- **Eager loading of relationships to prevent N+1 queries**
- **Request validation**
- **Comprehensive test suite**
- **API documentation with Swagger/OpenAPI**

---

## Requirements

- **PHP**
- **MySQL**
- **Composer**

---

## Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd blog-api
   ```

2. **Install dependencies**

   ```bash
   composer install
   ```

3. **Set up environment file**

   ```bash
   cp .env.example .env
   ```

4. **Configure your database in `.env`**

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=blog_api
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Generate application key**

   ```bash
   php artisan key:generate
   ```

6. **Run migrations and seed the database**

   ```bash
   php artisan migrate:fresh --seed
   ```

---

## Running the Application

1. **Start the development server:**

   ```bash
   php artisan serve
   ```

2. The API will be available at:

   ```
   http://localhost:8000
   ```

---

## API Documentation

Swagger documentation is available at:

```bash
http://localhost:8000/api/documentation
```

---

## API Endpoints

### **GET /api/posts**

Fetch paginated list of posts with their authors and comments.

#### Query Parameters:

- **`author_id`** *(optional)*: Filter posts by author ID
- **`title`** *(optional)*: Search posts by title
- **`page`** *(optional)*: Page number *(default: 1)*
- **`per_page`** *(optional)*: Items per page *(default: 15, max: 100)*

---

## Running Tests

Run the test suite:

```bash
php artisan test
```

