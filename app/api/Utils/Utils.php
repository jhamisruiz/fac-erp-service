<?php

namespace App\Utils;

use ZipArchive;
use App\Utils\Signature\Signature;
//use stdClass;

class Utils
{
    public static function XMLSignature($emisor, $file, $nombrexml)
    {
        //FIRMAR EL XML
        $objSignature = new Signature();

        $flg_firma = "0";
        $ruta = $file . $nombrexml . '.XML';

        $cert_name = isset($emisor['certificado']) ? $emisor['certificado'] :   "/certificado_prueba.pfx";
        $ruta_firma = "pulic/certs/" . $emisor['numero_documento'] . $cert_name;
        $pass_firma = isset($emisor['clave_certificado']) ? $emisor['clave_certificado'] : "prueba123";

        $resp = $objSignature->signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma);

        error_log("[{$nombrexml}]------->: firmado");

        //zip
        $zip = new ZipArchive();
        $nombrezip = $nombrexml . ".ZIP";
        $rutazip = $file . $nombrexml . ".ZIP";

        if ($zip->open($rutazip, ZIPARCHIVE::CREATE) === true) {
            $zip->addFile($file . $nombrexml . '.XML', $nombrexml . '.XML');
            $zip->close();
        }

        $obj = new \stdClass();
        $obj->zip_name = $nombrezip;
        $obj->zip_dir = $rutazip;
        $obj->emisor = (array)$emisor;
        $obj->xml_name = $nombrexml;

        return $obj;
    }

    /**
     * Y-m-d H:i:s.
     * */
    public static function Now()
    {
        ini_set('date.timezone', 'America/Lima');
        return date('Y-m-d H:i:s', time());
    }

    /**
     * @param string $zone default 'America/Lima'
     * @param string $format default 'Y-m-d H:i:s'
     *  */
    public static function DateTime($zone = 'America/Lima', $format = 'Y-m-d H:i:s')
    {
        ini_set('date.timezone', $zone);
        return date($format, time());
    }

    public static function responseParamsUpdate($res, $id = null)
    {
        if ($res === 1 || $res === '1') {
            return (object)[
                'id' => (int)$id ?? null,
                'rowCount' => $res,
                'message' => 'Datos guardados.',
                'data' => null,
            ];
        }

        if ($res === 0 || $res === '0') {
            return (object)[
                'id' => (int)$id ?? null,
                'rowCount' => $res,
                'message' => 'Datos guardados sin cambios.',
                'data' => null,
            ];
        }
        return $res;
    }

    public static function  eliminarCarpetaYArchivos($carpeta)
    {
        if (is_dir($carpeta)) {
            // Escanea todos los archivos y subdirectorios dentro de la carpeta
            $archivos = scandir($carpeta);

            // Elimina los archivos dentro de la carpeta
            foreach ($archivos as $archivo) {
                if ($archivo != "." && $archivo != "..") {
                    $archivoRuta = $carpeta . DIRECTORY_SEPARATOR . $archivo;

                    if (is_dir($archivoRuta)) {
                        // Si es un directorio, llamamos a la función recursivamente
                        self::eliminarCarpetaYArchivos($archivoRuta);
                    } else {
                        // Si es un archivo, lo eliminamos
                        unlink($archivoRuta);
                    }
                }
            }

            // Elimina la carpeta vacía
            rmdir($carpeta);
        }
    }
}
