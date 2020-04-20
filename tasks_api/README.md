# Prerequisitos

- PHP 7.3+ (https://www.php.net/manual/es/install.php)
- Docker o MySQL (https://docs.docker.com/get-docker/)
- Symfony CLI (https://symfony.com/download)
- Composer (https://getcomposer.org/download/)

Nota: Si se prefiere usar una instalación local de MySQL en lugar de usar Docker, se debe cambiar la configuración `DATABASE_URL` de conexión en el archivo `.env` e ingnorar el paso de Docker en la siguien sección.

# Instalación y ejecución

- Instalar las dependencias
```shell script
$> composer install
```

- Levantar la instancia de MySQL dentro de Docker
```shell script
$> docker-compose up -d
```

- Crear el esquema en la base de datos
```shell script
$> php bin/console doctrine:schema:create
```

- Ejecutar el aplicativo con Symfony CLI:
```shell script
$> symfony serve
```

La API estará disponible en http://127.0.0.1:8000