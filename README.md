# 🎫 HELPDESK LITE — Sistema de Tickets de Soporte

> Aplicación web profesional, minimalista y moderna construida con **Laravel 11 + PostgreSQL + TailwindCSS + Docker**.

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=flat-square&logo=postgresql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)
![Tailwind](https://img.shields.io/badge/TailwindCSS-3.4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Setup Modo 1 — Docker Full](#-setup-modo-1--docker-full)
- [Setup Modo 2 — PostgreSQL Local](#-setup-modo-2--postgresql-local-windows)
- [Credenciales por defecto](#-credenciales-por-defecto)
- [Tests](#-tests)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Git Workflow](#-git-workflow)

---

## ✨ Características

- **CRUD completo de tickets** con paginación, búsqueda y filtros por estado/prioridad
- **Adjuntos de imágenes** (jpg, png, webp — max 5MB) con galería visual
- **Comentarios** por ticket con historial
- **Roles**: admin ve todo, user solo sus tickets
- **Policies/Gates** para autorización granular
- **UI premium** tipo SaaS con TailwindCSS (glassmorphism, gradients, badges)
- **Docker** con doble modo (full / postgres local)
- **Tests** completos (Unit + Feature) con SQLite in-memory
- **Seeders** con datos de ejemplo listos

---

## 📦 Requisitos

- **Docker Desktop** (para cualquier modo)
- **PHP 8.2+** (si quieres correr local sin Docker)
- **Composer 2+**
- **Node.js 18+** + npm
- **PostgreSQL 15/16** (solo para Modo 2)
- **Git**

---

## 🐳 Setup Modo 1 — Docker Full

> App + PostgreSQL + pgAdmin, todo en contenedores.

### 1. Clonar y configurar

```bash
cd helpdesk-lite
cp .env.docker.example .env
```

### 2. Levantar contenedores

```bash
docker compose up --build -d
```

### 3. Configurar la app (dentro del contenedor)

```bash
docker exec -it helpdesk-app bash

# Dentro del contenedor:
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
exit
```

### 4. Acceder

| Servicio  | URL                     | Credenciales                          |
|-----------|-------------------------|---------------------------------------|
| **App**   | http://localhost:8000   | admin@test.com / password             |
| **pgAdmin** | http://localhost:5050 | admin@helpdesk.local / admin123      |

### Configurar pgAdmin para conectar a la DB

1. Abrir http://localhost:5050
2. **Add New Server**:
   - Name: `helpdesk`
   - Host: `db`
   - Port: `5432`
   - Database: `helpdesk`
   - Username: `helpdesk_user`
   - Password: `helpdesk_pass`

---

## 🖥️ Setup Modo 2 — PostgreSQL Local (Windows)

> La app corre en Docker (o local), PostgreSQL lo tienes instalado en tu PC con pgAdmin4.

### 1. Crear DB en tu PostgreSQL local

Abrir **pgAdmin4** → Query Tool → ejecutar el contenido de `scripts/create_db.sql`:

```sql
CREATE USER helpdesk_user WITH PASSWORD 'helpdesk_pass';
CREATE DATABASE helpdesk OWNER helpdesk_user;
GRANT ALL PRIVILEGES ON DATABASE helpdesk TO helpdesk_user;
\c helpdesk
GRANT ALL ON SCHEMA public TO helpdesk_user;
```

> **Alternativamente**, abrír `psql` y ejecutar: `\i scripts/create_db.sql`

### 2. Configurar .env

```bash
cp .env.localpg.example .env
```

El `DB_HOST=host.docker.internal` permite al contenedor Docker conectarse a tu PostgreSQL local.

### 3. Levantar la app

```bash
docker compose -f docker-compose.local.yml up --build -d
```

### 4. Migrar y seedear

```bash
docker exec -it helpdesk-app bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
exit
```

### 5. Acceder

- App: http://localhost:8000

### ⚠️ Tips Windows + PostgreSQL Local

1. **Permitir conexiones remotas en PostgreSQL**:
   - Editar `postgresql.conf` → `listen_addresses = '*'`
   - Editar `pg_hba.conf` → Agregar línea:
     ```
     host    all    all    0.0.0.0/0    md5
     ```
   - Reiniciar servicio PostgreSQL

2. **Firewall**: Asegúrate de que el puerto 5432 no esté bloqueado.

3. **host.docker.internal**: Docker Desktop en Windows resuelve este hostname automáticamente al host.

---

## 🔐 Credenciales por defecto

| Rol   | Email           | Password   |
|-------|-----------------|------------|
| Admin | admin@test.com  | password   |
| User  | user@test.com   | password   |

---

## 🧪 Tests

Los tests usan **SQLite in-memory** (configurado en `phpunit.xml`), así que no requieren PostgreSQL.

### Ejecutar todos los tests

```bash
# En Docker:
docker exec -it helpdesk-app php artisan test

# Local:
php artisan test
```

### Tests incluidos

| Archivo              | Tests |
|----------------------|-------|
| `AuthTest`           | Login, registro, guest redirect |
| `TicketTest`         | CRUD, permisos, imágenes, status/priority por rol |
| `CommentTest`        | Comentar propio, ajeno, admin, validación |
| `TicketModelTest`    | Constantes, isAdmin helper |

---

## 📁 Estructura del proyecto

```
helpdesk-lite/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/ (Login, Register)
│   │   │   ├── DashboardController.php
│   │   │   ├── TicketController.php
│   │   │   ├── TicketCommentController.php
│   │   │   └── TicketAttachmentController.php
│   │   ├── Middleware/AdminMiddleware.php
│   │   └── Requests/ (StoreTicket, UpdateTicket)
│   ├── Models/ (User, Ticket, TicketAttachment, TicketComment)
│   ├── Policies/TicketPolicy.php
│   └── Providers/AppServiceProvider.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/DatabaseSeeder.php
├── resources/views/
│   ├── layouts/ (app, guest)
│   ├── auth/ (login, register)
│   ├── tickets/ (index, create, show, edit)
│   └── dashboard.blade.php
├── tests/
│   ├── Feature/ (AuthTest, TicketTest, CommentTest)
│   └── Unit/TicketModelTest.php
├── docker/
│   ├── nginx.conf
│   └── supervisord.conf
├── scripts/create_db.sql
├── docker-compose.yml         (Modo 1: Docker full)
├── docker-compose.local.yml   (Modo 2: PG local)
├── Dockerfile
├── .env.example
├── .env.docker.example
└── .env.localpg.example
```

---

## 🌿 Git Workflow

### Ramas

| Rama                       | Propósito                    |
|----------------------------|------------------------------|
| `main`                     | Producción / estable         |
| `develop`                  | Integración                  |
| `feature/helpdesk-core`    | Desarrollo de features       |

### Comandos para reproducir el flujo Git

```bash
# ── 1. Inicializar repositorio ──
cd helpdesk-lite
git init
git add .
git commit -m "chore: initial project scaffold"

# ── 2. Crear ramas ──
git branch develop
git branch feature/helpdesk-core

# ── 3. Ir a la feature branch y desarrollar ──
git checkout feature/helpdesk-core

# (El proyecto ya está completo, pero si hicieras cambios incrementales:)
git add .
git commit -m "feat: add ticket CRUD with images and comments"

git add .
git commit -m "feat: add policies and role-based authorization"

git add .
git commit -m "feat: add premium UI with TailwindCSS"

git add .
git commit -m "test: add feature and unit tests"

git add .
git commit -m "ci: add Docker + PostgreSQL dual mode setup"

# ── 4. Merge feature → develop ──
git checkout develop
git merge --no-ff feature/helpdesk-core -m "merge: feature/helpdesk-core into develop"

# ── 5. Merge develop → main ──
git checkout main
git merge --no-ff develop -m "release: merge develop into main — v1.0.0"

# ── 6. Tag de versión ──
git tag -a v1.0.0 -m "v1.0.0 — HELPDESK LITE first release"

# ── 7. Verificar ──
git log --oneline --graph --all
```

### Resultado esperado del log

```
*   release: merge develop into main — v1.0.0 (HEAD -> main, tag: v1.0.0)
|\
| *   merge: feature/helpdesk-core into develop (develop)
| |\
| | * ci: add Docker + PostgreSQL dual mode setup (feature/helpdesk-core)
| | * test: add feature and unit tests
| | * feat: add premium UI with TailwindCSS
| | * feat: add policies and role-based authorization
| | * feat: add ticket CRUD with images and comments
| |/
|/
* chore: initial project scaffold
```

---

## 📄 Licencia

MIT License — [helpdesk-lite](.)

---

Hecho con ❤️ usando Laravel 11, PostgreSQL 16 & TailwindCSS.
