# app

# VALIDAR GIONES MEDIOS EN NOMBRE CARPETAS Y ARCHIVOS POR CONVENCION PSR-4


# requiere YARGS
# -npm install yargs

# --------------NODEJS----------------------
# node generate c prods
# node generate c prods --path=mantenedores
# node generate c prods -p mantenedores
# node generate c prods --ns=Mnt
# node generate c prods --ns=Mnt --path=mantenedores
# =====dependencias=====

# --------------COMPOSER----------------------
## importar archivos
## -php composer.phar install
## - composer install
## - composer dump-autoload


## _ php artisan c NombreComponente --ns=Mnt --path=Carpeta/Nueva/Componente
## _ php artisan c productos 
## _ php artisan c productos --ns Mnt
## _ php artisan c productos --ns=Mnt
## _ php artisan c productos --path=mantenedores
## _ php artisan c productos --path mantenedores
## _ php artisan c productos --ns Mnt --path mantenedores
## _ php artisan c productos --ns=Mnt --path=mantenedores

####//NOTE: agregar el valor de `--ns` a  "autoload":{"psr-4"}

# =====dependencias=====
# -composer require aura/cli

# composer require symfony/console
# composer require symfony/contracts
# composer require symfony/process
# composer require vlucas/phpdotenv
# composer require psr/log


# ===rutas==
## composer require slim/slim:"4.*"
## composer require slim/psr7
## composer require slim/http
## composer require slim/middleware-methodoverride

## PSR-4 es una convención de carga automática de clases en PHP que establece una estructura de directorios y nombres de archivo para las clases. 

## Según la convención PSR-4, cada espacio de nombres (namespace) se corresponde con un prefijo de ruta de archivo (file path prefix). Es decir, si una clase está en el espacio de nombres `MyApp\Controllers`, su archivo debe estar en `src/Controllers` y tener un nombre como `MyController.php`.

## Además, cada espacio de nombres debe estar en su propio archivo y el nombre del archivo debe coincidir con el nombre de la clase (incluyendo el espacio de nombres). Por ejemplo, la clase `MyApp\Controllers\MyController` debería estar en el archivo `src/Controllers/MyController.php`.

## Con la convención PSR-4, los nombres de los espacios de nombres y las clases deben estar en CamelCase (también conocido como PascalCase), lo que significa que la primera letra de cada palabra debe estar en mayúscula, excepto la primera palabra.

## La convención PSR-4 también establece que las clases deben seguir una convención de nomenclatura CamelCase, donde la primera letra de cada palabra se escribe en mayúscula (por ejemplo, `MyClass`, `MyOtherClass`). 

## Estas convenciones facilitan la carga automática de clases en PHP y ayudan a mantener un código claro y organizado.

# ==============V ==============
## composer require guzzlehttp/guzzle
# composer require tectalic/openai
# "openai-php/client": "dev-main",
# tectalic/openai
# composer require nikic/fast-route:^1.3



# //NOTE: requerimientos de php habilitado en php.ini -> extension=gd y extension=zip
#