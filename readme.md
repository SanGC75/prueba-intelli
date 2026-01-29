Este proyecto implementa una API RESTful con Laravel 5.3, utilizando **PostgreSQL** como motor de base de datos. Incluye autenticación JWT, gestión de Autores y Libros, un sistema de Jobs para consistencia de datos y exportación de reportes.

## Requisitos

* Docker y Docker Compose
* PHP 7.x (en contenedor)
* PostgreSQL 9.x/10.x+ (en contenedor)
* Composer

## Configuración y Ejecución

1.  **Clonar el repositorio:**
    ```bash
    git clone <https://github.com/SanGC75/prueba-intelli.git>
    cd <prueba-intelli>
    ```

2.  **Configurar variables de entorno (`.env`):**
    Asegúrate de configurar la conexión a PostgreSQL:
    ```env
		DB_CONNECTION=pgsql
		DB_HOST=prueba_intelli_db
		DB_PORT=5432
		DB_DATABASE=intelli_db
		DB_USERNAME=admin
		DB_PASSWORD=123456
    ```

3.  **Levantar entorno con Docker:**
    ```bash
    sudo docker-compose up -d --build
    ```

4.  **Instalar dependencias y preparar DB:**
    ```bash
    sudo docker exec -it prueba_intelli composer install
    sudo docker exec -it prueba_intelli php artisan migrate:refresh --seed
    sudo docker exec -it prueba_intelli php artisan jwt:secret
    ```

5.  **Permisos de carpetas:**
    ```bash
    sudo docker exec -it prueba_intelli chmod -R 775 storage bootstrap/cache
    sudo docker exec -it prueba_intelli chown -R www-data:www-data storage
    ```

## Endpoints de la API

Las rutas están protegidas por el middleware `auth.jwt`. Se debe incluir el token en el header: `Authorization: Bearer {token}`.

### 1. Autenticación y Usuarios
| Método | URL | Descripción |
| :--- | :--- | :--- |
| `POST` | `/api/login` | Login de usuario y obtención de token. |
| `POST` | `/api/logout` | Cierre de sesión e invalidación de token. |
| `GET` | `/api/me` | Obtener datos del usuario autenticado. |

### 2. Módulo de Autores
| Método | URL | Descripción |
| :--- | :--- | :--- |
| `GET` | `/api/authors` | Lista todos los autores. |
| `POST` | `/api/authors` | Crear un nuevo autor. |
| `GET` | `/api/authors/{id}` | Ver detalle de un autor. |
| `DELETE` | `/api/authors/{id}` | Eliminar un autor. |

### 3. Módulo de Libros
| Método | URL | Descripción |
| :--- | :--- | :--- |
| `GET` | `/api/books` | Lista libros (activos). |
| `POST` | `/api/books` | Crear libro (dispara Job para `books_count`). |
| `GET` | `/api/books/{id}` | Ver detalle de un libro. |
| `DELETE` | `/api/books/{id}` | Borrado lógico (dispara Job para actualizar contador). |

### 4. Exportación
| Método | URL | Descripción |
| :--- | :--- | :--- |
| `GET` | `/api/export-library` | Exporta autores y libros a formato XLS. |

## Notas Técnicas
* **Base de Datos:** Se utiliza PostgreSQL. Asegúrate de tener instalada la extensión `php-pdo_pgsql` en el contenedor.
* **Consistencia:** El campo `books_count` en la tabla autores se actualiza mediante un **Job** disparado por un **Observer** del modelo `Book`.
* **Borrado Lógico:** Los libros utilizan una columna `deleted` (boolean) para el control de registros eliminados.