-- =============================================================
-- scripts/create_db.sql
-- Ejecutar en psql o pgAdmin4 para Modo 2 (Postgres local)
-- =============================================================

-- 1. Crear usuario
CREATE USER helpdesk_user WITH PASSWORD 'helpdesk_pass';

-- 2. Crear base de datos
CREATE DATABASE helpdesk OWNER helpdesk_user;

-- 3. Permisos
GRANT ALL PRIVILEGES ON DATABASE helpdesk TO helpdesk_user;

-- Conectar a la DB helpdesk y dar permisos al schema
\c helpdesk
GRANT ALL ON SCHEMA public TO helpdesk_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO helpdesk_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO helpdesk_user;
