# Archivo LIS - Portal Symfony Ligero

Este proyecto implementa una interfaz web ligera inspirada en Symfony que permite explorar carpetas de eventos y sus archivos `.Lis`. Está pensado para integrarse posteriormente con plantillas personalizadas de encabezado y pie de página, y para conectarse a una base de datos MariaDB con la información general de cada evento.

## Características principales

- **Listado de eventos**: se inspecciona la carpeta configurada (`var/events` por defecto) y se muestran los subdirectorios detectados como "eventos".
- **Sincronización con base de datos**: si existe conexión con la base de datos `Informes` y la tabla `archivoLis`, se muestran la magnitud y fecha registradas para cada evento.
- **Detalle del evento**: al entrar a un evento se procesa el encabezado de los archivos `.Lis` para extraer Epicentro, Fecha, Magnitud, Código de estación y valores PGA.
- **Ordenamiento**: la tabla de archivos permite ordenar por código de estación o por el PGA máximo.
- **Descargas**: se pueden seleccionar uno o varios archivos para descargar; en caso múltiple se genera automáticamente un ZIP temporal.

## Estructura

```
symfony/
├── config/             # Configuración básica de la aplicación
├── public/             # Punto de entrada (front controller)
├── src/                # Controladores, servicios y utilidades
├── templates/          # Vistas PHP con layout base/header/footer
├── var/events/         # Carpeta observada con eventos de ejemplo
└── vendor/autoload.php # Autocargador PSR-4 simple
```

## Configuración

1. **Carpeta de eventos**: por defecto se lee `var/events`. Puedes cambiarla exportando la variable de entorno `EVENT_BASE_PATH` o modificando `config/app.php`.
2. **Base de datos**: ajusta las variables `DATABASE_DSN`, `DATABASE_USER` y `DATABASE_PASSWORD` para apuntar al servidor MariaDB. La tabla esperada es `archivoLis` con columnas `event_code`, `event_name`, `event_magnitude` y `event_date`.

## Ejecución

Puedes probar la aplicación iniciando un servidor embebido de PHP desde la carpeta `symfony`:

```bash
php -S localhost:8000 -t public
```

Luego navega a <http://localhost:8000> para ver el listado de eventos.

## Notas

- El layout base (`templates/base.html.php`) contiene marcadores de posición para insertar un encabezado y pie personalizados en el futuro.
- El analizador de archivos `.Lis` detiene la lectura del encabezado al encontrar la primera línea vacía, lo que evita procesar el cuerpo de datos numéricos.
- Si la base de datos no está disponible, la aplicación sigue funcionando únicamente con la información extraída de los archivos.
