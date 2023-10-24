<?php
require __DIR__ . '../../vendor/autoload.php';

//|Requiere la librería de Symfony
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/*
|--------------------------------------------------------------------------
|Define el comando
|--------------------------------------------------------------------------
*/

$command = new Command('generate');
$command
    ->addArgument('c', InputArgument::REQUIRED, 'Abreviatura  del component')
    ->addArgument('component', InputArgument::REQUIRED, 'Nombre del componente')
    ->setDescription('Genera un componente')
    ->addOption('ns', null, InputOption::VALUE_OPTIONAL, 'Estructura de directorios y archivos', 'App')
    ->addOption('path', null, InputOption::VALUE_OPTIONAL, 'Ruta del componente', null)
    ->setCode(function (InputInterface $input, OutputInterface $output) {

        //|--------------------------------------------------------------------------
        //|Comprueba si se ha especificado la opción de versión
        //|--------------------------------------------------------------------------
        if ($input->getOption('version')) {
            $output->writeln('JE v1.0.0');
            return 0;
        }
        //|--------------------------------------------------------------------------
        //|Obtiene el nombre del componente
        //|--------------------------------------------------------------------------
        $component = $input->getArgument('component');
        if (!$component) {
            $io = new SymfonyStyle($input, $output);
            $io->error('Debe especificar el nombre del componente.');
            return 1;
        }

        //|--------------------------------------------------------------------------
        //|Ejecuta el comando PHP para crear el componente
        //|--------------------------------------------------------------------------
        $componentName = ucfirst($component);
        $ns = ucfirst($input->getOption('ns'));
        $componentPath =  ($input->getOption('path') === null ? null : $input->getOption('path'));

        $componentPath = ($componentPath === null || $componentPath === 'null' || $componentPath === '') ? "" : $componentPath;


        $loader = new FilesystemLoader(__DIR__ . '/templates');
        $twig = new Environment($loader);

        $template = $twig->load('Mnt/mnt.twig');
        $generated = $template->render([
            'name' => $componentName,
            'class' => $componentName . 'Mnt',
            'ns' => $ns,
            'namespace' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName,
        ]);

        $base = 'app/api/' . $componentPath . '/' . ucfirst($componentName);
        if ($componentPath === "") {
            $base = 'app/api/' . ucfirst($componentName);
        }

        if (file_exists($base)) {
            $io = new SymfonyStyle($input, $output);
            $io->error(sprintf("Ocurrió un error al crear el %s, El archivo %s existe!\n", $componentName, $componentName));
            return 1;
        }
        mkdir($base, 0755, true);
        echo "$base/{$componentName}Mnt.php";
        file_put_contents(
            "$base/$componentName" . "Mnt.php",
            $generated
        );
        file_put_contents("$base/$componentName" . "Mnt.php", $generated);

        //|--------------------------------------------------------------------------
        //|Agregamos la estructura MVC
        //|--------------------------------------------------------------------------
        $directories = ['Models', 'Mapping', 'Persistence', 'Http'];
        foreach ($directories as $directory) {
            if ($directory == 'Models') {
                mkdir("$base/$directory", 0755, true);
                echo "\n$base/$directory/{$componentName}" . ucfirst($directory) . ".php";
                $model = $twig->load('Mnt/Models/models.twig');
                $mdl = $model->render([
                    'name' => ucfirst($componentName),
                    'class' => ucfirst($componentName) . ucfirst($directory),
                    'ns' => $ns,
                    'namespace' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName . '\\' . ucfirst($directory),
                ]);
                file_put_contents("$base/$directory/{$componentName}" . ucfirst($directory) . ".php", $mdl);
            }

            if ($directory == 'Persistence') {
                mkdir("$base/$directory", 0755, true);
                echo "\n$base/$directory/{$componentName}" . ucfirst($directory) . ".php";
                $Persistence = $twig->load('Mnt/Persistence/persistence.twig');
                $prt = $Persistence->render([
                    'name' => ucfirst($componentName),
                    'class' => ucfirst($componentName) . ucfirst($directory),
                    'ns' => $ns,
                    'namespace' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName . '\\' . ucfirst($directory),
                ]);
                file_put_contents("$base/$directory/{$componentName}" . ucfirst($directory) . ".php", $prt);
            }
            if (
                $directory === 'Http'
            ) {
                mkdir("$base/Http/Routes", 0755, true);
                echo "\n$base/Http/Routes/{$componentName}Routes.php";

                $router = $twig->load('Mnt/Http/Routes/routes.twig');
                $rtr = $router->render([
                    'name' => ucfirst($componentName),
                    'class' => ucfirst($componentName) . ucfirst('Routes'),
                    'ns' => $ns,
                    'namespace' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName . '\\' . ucfirst($directory) . '\\' . ucfirst('Routes'),
                    'controller' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName . '\\' . ucfirst($directory) . '\\' . ucfirst('Controller'),
                ]);
                file_put_contents("$base/http/Routes/{$componentName}Routes.php", $rtr);

                mkdir("$base/Http/Controller", 0755, true);
                echo "\n$base/Http/Controller/{$componentName}Controller.php";
                $controller = $twig->load('Mnt/Http/Controller/controller.twig');
                $gr = $controller->render([
                    'name' => ucfirst($componentName),
                    'class' => ucfirst($componentName) . ucfirst('Controller'),
                    'ns' => $ns,
                    'namespace' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName . '\\' . ucfirst($directory) . '\\' . ucfirst('Controller'),
                    'persistence' => ($componentPath === '' ? '' : $componentPath . '\\') . $componentName,
                ]);
                file_put_contents("$base/Http/Controller/{$componentName}Controller.php", $gr);
            }
        }

        //|--------------------------------------------------------------------------
        //|NOTE: |AGREGA LAS CLASES Y USA LOS NAMESPACES EN EL ARCHIVO APP.PHP
        //|--------------------------------------------------------------------------

        $archivo = __DIR__ . '../../app/cmd/Services/Enpoints.php';


        $nuevaLinea = '        ' . $componentName . 'Mnt::Create($router);';
        $referencia_ns = "$ns\\" . ($componentPath === '' ? $componentName : $componentPath);

        if (!file_exists($archivo)) {
            $io = new SymfonyStyle($input, $output);
            $io->warning(sprintf("Ocurrió un error al agregar 'Use %sMnt;', El archivo app.php no existe!", $referencia_ns . "\\$componentName"));
            $io->warning(sprintf("Ocurrió un error al agregar '%s', El archivo Enpoints.php no existe!", $nuevaLinea));
            $io->success(sprintf('%s %s creado! ', $ns, $component));
            return 1;
        }

        $contenido = file_get_contents($archivo);
        $lineas = explode("\n", $contenido);


        $ultimaLlamada = '';
        $posicionUltimoMnt = false;
        foreach ($lineas as $linea) {
            if (strpos($linea, '::Create($router)') !== false) {
                $ultimaLlamada = $linea;
            } elseif (strpos($linea, $referencia_ns) !== false) {
                $posicionUltimoMnt = array_search($linea, $lineas);
            } elseif (strpos($linea, 'public static function initEndpoints($router)') !== false) {
                $posicionClass = array_search($linea, $lineas);
            } elseif (strpos($linea, 'class Enpoints') !== false) {
                $posicionUse = array_search($linea, $lineas);
            }
        }

        if (empty($ultimaLlamada)) {
            // Si no se encuentra ninguna referencia a ::Create($router), se agrega despues depublic static function initEndpoints($router){

            array_splice($lineas, ($posicionClass + 2), 0,  [$nuevaLinea]);
        } else {
            // Si se encuentra alguna referencia a ::Create($router), se agrega después de la última
            $posicionUltimaLlamada = array_search($ultimaLlamada, $lineas);
            $posicionNuevaLinea = $posicionUltimaLlamada + 1;
            array_splice($lineas, $posicionNuevaLinea,  0, [$nuevaLinea]);
        }

        $use = "use " . $ns . "\\" . ($componentPath === '' ? '' : $componentPath . '\\') . $componentName . "\\" . $componentName . "Mnt;";

        if ($posicionUltimoMnt === false) {
            // Si no se encuentra ninguna referencia a Mnt\mantenedores, se agrega antes de $app = AppFactory::create();
            array_splice($lineas, ($posicionUse - 1), 0, [$use]);
        } else {
            // Si se encuentra alguna referencia a Mnt\mantenedores, se agrega después de la última
            $posicionNuevaLinea = $posicionUltimoMnt + 1;
            array_splice($lineas, $posicionNuevaLinea, 0, [$use]);
        }

        $contenidoActualizado = implode("\n", $lineas);
        file_put_contents($archivo, $contenidoActualizado);

        //|--------------------------------------------------------------------------
        //|Imprime el mensaje de éxito
        //|--------------------------------------------------------------------------
        $io = new SymfonyStyle($input, $output);
        $io->success(sprintf('%s %s creado! ', $ns, $component));
    });

//|--------------------------------------------------------------------------
//|Crea la aplicación
//|--------------------------------------------------------------------------
$application = new Application();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

//|--------------------------------------------------------------------------
//|Ejecuta la aplicación
//|--------------------------------------------------------------------------
$application->run();
