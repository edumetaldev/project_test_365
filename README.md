# Technical Test for 365

Sistema de gestión de reservas desarrollado con Laravel 12, siguiendo arquitectura DDD (Domain Driven Design).

## Requisitos

- PHP 8.2+
- Composer
- Node.js y npm
- Docker (opcional, para base de datos MariaDB)

## Instalación y Configuración

### 1. Clonar el repositorio
```bash
git clone <url-del-repositorio>
cd project_test_365
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias Node.js
```bash
npm install
```

### 4. Configurar variables de entorno
```bash
cp .env.example .env
```

Editar el archivo `.env` con la configuración necesaria:

**Para SQLite (por defecto):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

**Para MariaDB con Docker:**
```env
DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=password
```

### 5. Generar clave de aplicación
```bash
php artisan key:generate
```

### 6. Ejecutar migraciones
```bash
# Para SQLite
php artisan migrate

# Para MariaDB con Docker
dockrun php artisan migrate
```

### 7. Ejecutar seeders (opcional)
```bash
# Para SQLite
php artisan db:seed

# Para MariaDB con Docker
dockrun php artisan db:seed
```

## Ejecutar la Aplicación

### Desarrollo
```bash
# Iniciar servidor Laravel
php artisan serve

# En otra terminal, compilar assets
npm run dev
```

### Con Docker (para MariaDB)
```bash
# Iniciar contenedores
docker-compose up -d

# Ejecutar comandos Laravel con dockrun
dockrun php artisan serve
dockrun npm run dev
```

### Comando de desarrollo completo
```bash
composer run dev
```
Este comando inicia simultáneamente:
- Servidor Laravel
- Cola de trabajos
- Logs en tiempo real
- Compilación de assets con Vite

## Testing

```bash
# Ejecutar todos los tests
php artisan test

# O usando composer
composer run test
```

## Estructura del Proyecto

El proyecto sigue arquitectura DDD con las siguientes capas:

- `app/Domain/`: Entidades y lógica de negocio
- `app/Application/`: Casos de uso y servicios de aplicación
- `app/Infrastructure/`: Implementaciones concretas y persistencia
- `app/Http/`: Controladores, requests y resources de la API

## API Endpoints

- `GET /api/passengers` - Listar pasajeros
- `POST /api/reservations` - Crear reserva
- `GET /api/reservations` - Listar reservas
- `PUT /api/reservations/{id}/status` - Cambiar estado de reserva

## Tecnologías Utilizadas

- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vite, TailwindCSS
- **Base de datos**: SQLite (por defecto) / MariaDB
- **Testing**: PHPUnit
- **Arquitectura**: DDD (Domain Driven Design)

