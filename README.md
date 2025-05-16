<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



## Database teamplete:

<!-- Users -->
-- Admin user
INSERT INTO `users` (`id`, `password`, `role`, `email`, `full_name`, `birthday`, `last_login`, `created_at`, `updated_at`)
VALUES 
('admin-001', 'admin', 'admin', 'admin@example.com', 'System Admin', '1980-01-01', NOW(), NOW(), NOW());

-- 5 student users
INSERT INTO `users` (`id`, `password`, `role`, `email`, `full_name`, `birthday`, `last_login`, `created_at`, `updated_at`)
VALUES
('student-001', '1234567890', 'student', 'student1@example.com', 'Student One', '2005-05-01', NOW(), NOW(), NOW()),
('student-002', '12345', 'student', 'student2@example.com', 'Student Two', '2005-06-02', NOW(), NOW(), NOW()),
('student-003', 'hashed_password', 'student', 'student3@example.com', 'Student Three', '2005-07-03', NOW(), NOW(), NOW()),
('student-004', 'hashed_password', 'student', 'student4@example.com', 'Student Four', '2005-08-04', NOW(), NOW(), NOW()),
('student-005', 'hashed_password', 'student', 'student5@example.com', 'Student Five', '2005-09-05', NOW(), NOW(), NOW());

-- 5 teacher users
INSERT INTO `users` (`id`, `password`, `role`, `email`, `full_name`, `birthday`, `last_login`, `created_at`, `updated_at`)
VALUES
('teacher-001', 'hashed_password', 'teacher', 'teacher1@example.com', 'Teacher One', '1985-05-01', NOW(), NOW(), NOW()),
('teacher-002', 'hashed_password', 'teacher', 'teacher2@example.com', 'Teacher Two', '1985-06-02', NOW(), NOW(), NOW()),
('teacher-003', 'hashed_password', 'teacher', 'teacher3@example.com', 'Teacher Three', '1985-07-03', NOW(), NOW(), NOW()),
('teacher-004', 'hashed_password', 'teacher', 'teacher4@example.com', 'Teacher Four', '1985-08-04', NOW(), NOW(), NOW()),
('teacher-005', 'hashed_password', 'teacher', 'teacher5@example.com', 'Teacher Five', '1985-09-05', NOW(), NOW(), NOW());

<!-- Student -->
INSERT INTO `students`(`user_id`, `student_code`, `admission_date`, `current_semester`)
VALUES
(2, 'STU001', '2022-09-01', 3),
(3, 'STU002', '2022-09-01', 3),
(4, 'STU003', '2022-09-01', 3),
(5, 'STU004', '2022-09-01', 3),
(6, 'STU005', '2022-09-01', 3);

<!-- Teacher -->
INSERT INTO `teachers`(`user_id`, `specialization`, `join_date`, `bio`)
VALUES
(7, 'Mathematics', '2020-01-10', 'Experienced math teacher.'),
(8, 'Physics', '2020-01-10', 'Physics enthusiast with a PhD.'),
(9, 'Chemistry', '2020-01-10', 'Specialist in organic chemistry.'),
(10, 'Biology', '2020-01-10', 'Passionate about life sciences.'),
(11, 'Computer Science', '2020-01-10', 'Software engineer turned teacher.');
