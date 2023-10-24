<?php

namespace Cmd\Middleware;

use Cmd\Server\Server;
use Klein\Exceptions\ValidationException;
use Klein\Exceptions\RegularExpressionCompilationException;

class RouterMiddleware
{
    /**
     * The regular expression used to compile and match URL's
     *
     * @type string
     */
    const ROUTE_COMPILE_REGEX = '`(\\\?(?:/|\.|))(?:\[([^:\]]*)(?::([^:\]]*))?\])(\?|)`';

    /**
     * The regular expression used to escape the non-named param section of a route URL
     *
     * @type string
     */
    const ROUTE_ESCAPE_REGEX = '`(?<=^|\])[^\]\[\?]+?(?=\[|$)`';

    protected $match_types = array(
        'i'  => '[0-9]++',
        'a'  => '[0-9A-Za-z]++',
        'h'  => '[0-9A-Fa-f]++',
        's'  => '[0-9A-Za-z-_]++',
        '*'  => '.+?',
        '**' => '.++',
        ''   => '[^/]+?'
    );

    protected function compileRoute($route)
    {
        // First escape all of the non-named param (non [block]s) for regex-chars
        $route = preg_replace_callback(
            static::ROUTE_ESCAPE_REGEX,
            function ($match) {
                return preg_quote($match[0]);
            },
            $route
        );

        // Get a local reference of the match types to pass into our closure
        $mt = new RouterMiddleware();
        $match_types = $mt->match_types;

        // Now let's actually compile the path
        $route = preg_replace_callback(
            static::ROUTE_COMPILE_REGEX,
            function ($match) use ($match_types) {
                list(, $pre, $type, $param, $optional) = $match;

                if (isset($match_types[$type])) {
                    $type = $match_types[$type];
                }

                // Older versions of PCRE require the 'P' in (?P<named>)
                $pattern = '(?:'
                    . ($pre !== '' ? $pre : null)
                    . '('
                    . ($param !== '' ? "?P<$param>" : null)
                    . $type
                    . '))'
                    . ($optional !== '' ? '?' : null);

                return $pattern;
            },
            $route
        );

        $regex = "`^$route$`";

        // Check if our regular expression is valid
        $vr = new RouterMiddleware();
        $vr->validateRegularExpression($regex);

        return $regex;
    }

    private function validateRegularExpression($regex)
    {
        $error_string = null;

        // Set an error handler temporarily
        set_error_handler(
            function ($errno, $errstr) use (&$error_string) {
                $error_string = $errstr;
            },
            E_NOTICE | E_WARNING
        );

        if (false === preg_match($regex, null ?? '') || !empty($error_string)) {
            // Remove our temporary error handler
            restore_error_handler();

            throw new RegularExpressionCompilationException(
                $error_string ?? '',
                preg_last_error()
            );
        }

        // Remove our temporary error handler
        restore_error_handler();

        return true;
    }
    public function getPathFor($route_name, $routers, array $params = null, $flatten_regex = true)
    {
        // First, grab the route
        //$route = $this->routes->get($route_name);
        $route = $route_name;
        // Make sure we are getting a valid route
        if (null === $route) {
            echo "No such route with name:  $route_name \n";
        }

        //$path = $route->getPath();
        $path = $routers;
        // Use our compilation regex to reverse the path's compilation from its definition
        $reversed_path = preg_replace_callback(
            static::ROUTE_COMPILE_REGEX,
            function ($match) use ($params) {
                list($block, $pre,, $param, $optional) = $match;

                if (isset($params[$param])) {
                    return $pre . $params[$param];
                } elseif ($optional) {
                    return '';
                }

                return $block;
            },
            $path
        );

        // If the path and reversed_path are the same, the regex must have not matched/replaced
        if ($path === $reversed_path && $flatten_regex && strpos($path, '@') === 0) {
            // If the path is a custom regular expression and we're "flattening", just return a slash
            $path = '/';
        } else {
            $path = $reversed_path;
        }

        return $path;
    }

    public static function getMatch($routes)
    {

        $REQUEST_URI = explode("?", $_SERVER['REQUEST_URI']);
        //--- /v1/usuarios/10
        $PATH_INFO = explode("/", $REQUEST_URI[0]);
        $endpoint_name = null;
        $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $dominio = $_SERVER['HTTP_HOST'];
        $full_endpoint = null;
        $apv = '';
        if ($PATH_INFO[1] == 'v1') {
            $apv = $PATH_INFO[1];
            //http://localhost:9080
            array_splice($PATH_INFO, 0, 2);
            $endpoint_name = $PATH_INFO[0]; //implode('/', $PATH_INFO);
            $request_uri = '/' . API_V . '/' . implode('/', $PATH_INFO);
            $full_endpoint = $protocolo . '://' . $dominio . $_SERVER['REQUEST_URI'];
        } else if ($PATH_INFO[2] == 'v1') {
            $apv = $PATH_INFO[2];
            //http://localhost/core-back
            array_splice($PATH_INFO, 0, 3);
            $endpoint_name = $PATH_INFO[0]; //implode('/', $PATH_INFO);
            $request_uri = '/' . API_V . '/' . implode('/', $PATH_INFO);
            $full_endpoint = $protocolo . '://' . $dominio . $_SERVER['REQUEST_URI'];
        }

        if (!isset($request_uri)) {
            return 0;
        }
        /* 
        || compara si la ruta existe
         */
        $_routes = [];
        foreach ($routes as $route) {
            $_routes[] .= $route->getPath();
        }
        $result = 0;
        $self = new self();
        //echo json_encode($_routes);
        foreach ($_routes as $key => $val) {

            if ($key > 1) {
                $compileRoute = $self->compileRoute($val);

                if (preg_match($compileRoute, $request_uri)) {
                    $result = 1;
                    break;
                };
            }
        }

        return $result;
    }

    public static function getMatchPath($request)
    {
        $result = 0;
        $self = new self();

        $keysParams = [];
        foreach ($request->paramsGet() as $key => $value) {
            //echo $value;
            $keysParams[] = $key;
        }

        foreach (Server::$routes as $key => $route) {
            $compileRoute = $self->compileRoute($route['endpoint']);


            if (preg_match($compileRoute, $request->pathname())) {
                if ($route['method'] === $request->method()) {

                    $issetQueryParams = null;
                    if (empty(array_diff_key($keysParams, $route['querystring_params'] ?? []))) {
                        $issetQueryParams = 1;
                        if (count($route['querystring_params'] ?? [])) {
                            $issetQueryParams = 1;
                            foreach ($route['querystring_params'] as $value) {
                                if ($request->param($value) === null) {
                                    $issetQueryParams = null;
                                    break;
                                }
                            }
                        }
                    }

                    if ($issetQueryParams === null) {
                        $result = 'error';
                        break;
                    }

                    $result = $route['headers_to_pass'] ? $route['headers_to_pass'][0] : $route['headers_to_pass'];
                    break;
                }
            };
        }
        return $result;
    }
}
