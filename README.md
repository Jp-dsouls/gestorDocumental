# Sistema de Gestión de Documentos

Sistema escalable para la gestión y procesamiento de documentos desarrollado con Laravel 12.

## Requisitos

- PHP >= 8.2
- Composer
- Node.js y npm
- Base de datos (MySQL, PostgreSQL)
- Redis (para colas y caché)

## Instalación

1. Clonar el repositorio:
```
git clone <url-del-repositorio>
cd gestor-documentos
```

2. Instalar dependencias de PHP:
```
composer install
```

3. Instalar dependencias de JavaScript:
```
npm install
```

4. Copiar el archivo de configuración:
```
cp .env.example .env
```

5. Generar clave de aplicación:
```
php artisan key:generate
```

6. Configurar la base de datos en el archivo .env

7. Ejecutar migraciones:
```
php artisan migrate
```

8. Compilar assets:
```
npm run dev
```

## Levantar el sistema

### Servidor de desarrollo
```
php artisan serve
```

### Compilación de assets en tiempo real
```
npm run dev
```

### Procesamiento de colas
```
php artisan queue:work
```

### Horizon (monitoreo de colas)
```
php artisan horizon
```

## Comandos útiles

### Crear componentes

#### Controlador
```
php artisan make:controller NombreController --resource
```

#### Modelo con migración, factory y controlador
```
php artisan make:model Nombre -mfc
```

#### Migración
```
php artisan make:migration crear_tabla_nombre
```

#### Vista (Blade)
```
php artisan make:view nombre
```

#### Componente
```
php artisan make:component Nombre
```

#### Policy
```
php artisan make:policy NombrePolicy --model=Nombre
```

#### Middleware
```
php artisan make:middleware NombreMiddleware
```

### Base de datos

#### Ejecutar migraciones
```
php artisan migrate
```

#### Revertir migraciones
```
php artisan migrate:rollback
```

#### Actualizar base de datos y seeders
```
php artisan migrate:fresh --seed
```

#### Crear seeder
```
php artisan make:seeder NombreSeeder
```

### Otros

#### Limpiar caché
```
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Optimizar la aplicación
```
php artisan optimize
```

#### Generar links simbólicos
```
php artisan storage:link
```

## Librerías integradas

- **Sentry**: Monitoreo de errores en tiempo real
- **Laravel Horizon**: Gestión de colas y trabajos en segundo plano
- **Intervention Image**: Procesamiento de imágenes
- **Laravel Log Viewer**: Visualización y gestión de logs

## Licencia

Este proyecto está licenciado bajo [MIT license](https://opensource.org/licenses/MIT).
