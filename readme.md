# Takomatic-api
> tako = 蛸 = pulpo

Esta api maneja y simula la interacción entre clientes y conductores. La simulación se hace a través de jobs y los eventos son transmitidos utilizando Pusher

## Dependencias

  - php >= 5.6
  - MySQL
  - [Composer](https://getcomposer.org/doc/00-intro.md)

## Puesta en marcha

Crear una base en MySQL con el nombre 'takomatic'

```sh
mysql> create database takomatic;
```

Instalar dependencias
```sh
$ cd takomatic-api
$ composer install
```
Dar de alta las tablas necesarias y correr el servidor web, por default correrá en el puerto 8000
```sh
$ php artisan migrate --seed
$ php artisan:serve
```
Correr el sistema de colas, esto es necesario para la simulación de movimiento
```sh
$ php artisan queue:work --daemon
```

## Configuración
En el archivo .env se encuentra la configuración de la base de datos así como la llave de Mapbox.

Los límites del mapa están puestos entre las coordenadas de: El Castillo de Chapultepec y La Alberca Olímpica. Estas configuraciones se pueden cambiar en la tabla settings en las entradas con los siguientes key: 
```sh
SOUTH_WEST_BOUND_LAT
SOUTH_WEST_BOUND_LNG
NORTH_EAST_BOUND_LAT
NORTH_EAST_BOUND_LNG
```
## Notas

Ya que la simulación solo mueve un conductor/cliente entre un vértice y el siguiente, ésta se encunetra alterada para que el movimiento se vea más fluido (no le tome el tiempo que en realidad debería). Así que se toma el tiempo que le debería tomar según Mapbox y se divide entre 100.

Econtré una diferencia en el comportamiento de los jobs programados entre Windows 10 y Mac, desconozco el comportamiento en linux.

En el archivo app/jobs/GenerateRoute.php, línea 94:

```sh
$duration = ($_step->duration / sizeof($_step->intersections)) / 100;
```

Cambiar para Mac el divisor a 10:
```sh
$duration = ($_step->duration / sizeof($_step->intersections)) / 10;
```
