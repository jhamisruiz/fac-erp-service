# app

# VALIDAR GIONES MEDIOS EN NOMBRE CARPETAS Y ARCHIVOS POR CONVENCION PSR-4


# requiere YARGS
# -npm install yargs

# --------------NODEJS----------------------
# node artisan c prods
# node artisan c prods --path=mantenedores
# node artisan c prods -p mantenedores
# node artisan c prods --ns=Mnt
# node artisan c prods --ns=Mnt --path=mantenedores
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
## _ php artisan c productos --p mantenedores
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

## `PSR-4` es una `convención` de carga automática de clases en PHP que establece una estructura de directorios y nombres de archivo para las clases. 

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
##      DEPLOYMENBT DE EJEMPLO KUBS

********************
arquitectura
********************
```
pedido
 ├── pedido.mv.go
 │
 ├── application/
 │   ├── usecase
 │   ├── service
 │
 ├── domain/
 │   ├── dto
 │   ├── entity
 │   └── mapping
 │   └── model
 │   └── repository
 │   └── usecase
 │   └── valueobject
 │
 ├── infrastructure/
 │   ├── delivery
 │       └── http
 │           └── controller
 │           └── routes
 │   ├── persistence
 │       └── mssql_repository

```

```php
<?php
/*
 * This is needed for cookie based authentication to encrypt password in
 * cookie
 */
$cfg['blowfish_secret'] = 'xampp'; /* YOU SHOULD CHANGE THIS FOR A MORE SECURE COOKIE AUTH! */

/*
 * Servers configuration
 */
$i = 0;

/*
 * First server
 */
$i++;

/* Authentication type and info */
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';
$cfg['Servers'][$i]['extension'] = 'mysqli';
$cfg['Servers'][$i]['AllowNoPassword'] = true;
$cfg['Lang'] = '';

/* Bind to the localhost ipv4 address and tcp */
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['connect_type'] = 'tcp';

/* User for advanced features */
$cfg['Servers'][$i]['controluser'] = 'pma';
$cfg['Servers'][$i]['controlpass'] = '';

/* Advanced phpMyAdmin features */
$cfg['Servers'][$i]['pmadb'] = 'phpmyadmin';
$cfg['Servers'][$i]['bookmarktable'] = 'pma__bookmark';
$cfg['Servers'][$i]['relation'] = 'pma__relation';
$cfg['Servers'][$i]['table_info'] = 'pma__table_info';
$cfg['Servers'][$i]['table_coords'] = 'pma__table_coords';
$cfg['Servers'][$i]['pdf_pages'] = 'pma__pdf_pages';
$cfg['Servers'][$i]['column_info'] = 'pma__column_info';
$cfg['Servers'][$i]['history'] = 'pma__history';
$cfg['Servers'][$i]['designer_coords'] = 'pma__designer_coords';
$cfg['Servers'][$i]['tracking'] = 'pma__tracking';
$cfg['Servers'][$i]['userconfig'] = 'pma__userconfig';
$cfg['Servers'][$i]['recent'] = 'pma__recent';
$cfg['Servers'][$i]['table_uiprefs'] = 'pma__table_uiprefs';
$cfg['Servers'][$i]['users'] = 'pma__users';
$cfg['Servers'][$i]['usergroups'] = 'pma__usergroups';
$cfg['Servers'][$i]['navigationhiding'] = 'pma__navigationhiding';
$cfg['Servers'][$i]['savedsearches'] = 'pma__savedsearches';
$cfg['Servers'][$i]['central_columns'] = 'pma__central_columns';
$cfg['Servers'][$i]['designer_settings'] = 'pma__designer_settings';
$cfg['Servers'][$i]['export_templates'] = 'pma__export_templates';
$cfg['Servers'][$i]['favorite'] = 'pma__favorite';

/*
 * End of servers configuration
 */

?>

```

Bryan Diaz
/Company/CvDownloader/Company/CvDetail/Download?ic=7A9071A5E6529C5361373E686DCF3405&oi=AC13F0D26B2C529961373E686DCF3405&ims=20C57F56F0201C04ACADAE07A88B195B&mfid=1C2525A3281C678F61373E686DCF3405
/Company/MatchCvDetail/MatchDetail?oi=AC13F0D26B2C529961373E686DCF3405&ims=20C57F56F0201C04ACADAE07A88B195B



///
/Company/CvDownloader/Company/CvDetail/Download?ic=D224811F4750251961373E686DCF3405&oi=AC13F0D26B2C529961373E686DCF3405&ims=CCBA3C295DDAA58391A06E28CCF39A9E&mfid=B6CBFE99C9AFFE4F61373E686DCF3405



/Company/CvDownloader/Company/CvDetail/Download?ic=BA48530345C7472161373E686DCF3405&oi=AC13F0D26B2C529961373E686DCF3405&ims=9A511164B46F4AE77D1AF5614416819D&mfid=39D49CFB781C6F6861373E686DCF3405
/Company/MatchCvDetail/MatchDetail?oi=AC13F0D26B2C529961373E686DCF3405&ims=9A511164B46F4AE77D1AF5614416819D


/Company/CvDownloader/Company/CvDetail/Download?ic=4FFBA369473E2EA661373E686DCF3405&oi=AC13F0D26B2C529961373E686DCF3405&ims=1F5BBB7D4C0FC8F4BE1C80A2C8D02CF5&mfid=728CE46252D4486D61373E686DCF3405
/Company/MatchCvDetail/MatchDetail?oi=AC13F0D26B2C529961373E686DCF3405&ims=1F5BBB7D4C0FC8F4BE1C80A2C8D02CF5
