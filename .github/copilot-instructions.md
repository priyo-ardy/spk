# Copilot Instructions for CodeIgniter 4 SPK Project

## Project Overview
This is a PHP web application built on CodeIgniter 4. It uses a modular structure for controllers, models, views, and services. The entry point for web requests is `public/index.php`—web servers must be configured to serve from the `public/` directory for security.

## Key Architecture
- **Controllers**: Located in `app/Controllers/`, organized by feature (e.g., `Auth/`, `Dashboard/`, `MasterData/`, `Transaction/`).
- **Models**: In `app/Models/`, grouped similarly to controllers.
- **Views**: In `app/Views/`, matching controller structure.
- **Config**: All configuration files are in `app/Config/`.
- **Helpers & Libraries**: Custom helpers in `app/Helpers/`, libraries in `app/Libraries/`.
- **Services**: Shared logic in `app/Services/`.
- **Database**: Migrations and seeds in `app/Database/`.

## Developer Workflows
- **Install dependencies**: `composer install`
- **Update framework**: `composer update`
- **Environment setup**: Copy `env` to `.env` and configure `baseURL` and database settings.
- **Run locally**: Use PHP's built-in server: `php spark serve` (serves from `public/`)
- **Testing**: PHPUnit config is in `phpunit.xml.dist`. Run tests with `vendor\bin\phpunit`.
- **Database**: SQL files for schema in project root and `public/`. Migrations/seeds in `app/Database/`.

## Project Conventions
- **Feature folders**: Controllers, models, and views are grouped by feature for maintainability.
- **No direct access**: Never serve the project root; always use the `public/` folder as the web root.
- **Config changes**: Update `app/Config/` for environment, routing, and service settings.
- **Custom helpers**: Use `app/Helpers/` for reusable functions (see `app_helper.php`, `excel_helper.php`).
- **Logging/debug**: Logs are in `writable/logs/`. Debugbar output is in `writable/debugbar/`.

## Integration Points
- **Composer**: Manages PHP dependencies.
- **External libraries**: See `vendor/` for installed packages (e.g., PHPExcel, Faker).
- **Front-end assets**: Static files in `public/` (CSS, JS, images, plugins).
- **Docker/Container**: See `Containerfile` and `public/Dockerfile` for containerization setup.
- **Supervisor**: `config/supervisord.conf` for process management.
- **Nginx**: Example config in `config/nginx.conf`.

## Examples
- To add a new feature, create a folder in `app/Controllers/`, `app/Models/`, and `app/Views/`.
- To add a helper, place a file in `app/Helpers/` and autoload it in `app/Config/Autoload.php`.
- To run migrations: `php spark migrate`

## References
- [CodeIgniter 4 User Guide](https://codeigniter.com/user_guide/)
- See `README.md` for more details and official links.

---
*Update this file if project structure or workflows change. Focus on actionable, project-specific guidance for AI agents.*
