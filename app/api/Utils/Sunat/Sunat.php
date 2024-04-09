<?php

namespace App\Utils\Sunat;

use App\Utils\Service\NewError;

class Sunat
{

    public static function Init($obj, $document)
    {
        //return $obj->zip_name;
        $contenido_del_zip = base64_encode(file_get_contents($obj->zip_dir));
        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
                xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                <soapenv:Header>
                    <wsse:Security>
                        <wsse:UsernameToken>
                            <wsse:Username>' . $obj->emisor['numero_documento'] . $obj->emisor['usuario_emisor'] . '</wsse:Username>
                            <wsse:Password>' . $obj->emisor['clave_emisor'] . '</wsse:Password>
                        </wsse:UsernameToken>
                        </wsse:Security>
                </soapenv:Header>
                <soapenv:Body>
                <ser:sendBill>
                    <fileName>' . $obj->zip_name . '</fileName>
                    <contentFile>' . $contenido_del_zip . '</contentFile>
                </ser:sendBill>
                </soapenv:Body>
                </soapenv:Envelope>';

        //ENVÍO DEL CPE A WS DE SUNAT
        $header = array(
            "Content-type: text/xml; charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: ",
            "Content-lenght: " . strlen($xml_envio)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, API_SUNAT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_envio);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        if (!(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . "/../../../../public/certs/cacert.pem");
        }
        $response = curl_exec($ch);

        // OBTENEMOS RESPUESTA (CDR)
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $estadofe = "0";
        if ($httpcode === 200) {
            $file_cdr = FILE_CDR . $document;
            $doc = new \DOMDocument();
            $doc->loadXML($response);
            $doc->save(FILE_CDR . "respuesta.XML");
            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                $cdr = base64_decode($cdr);
                if (!file_exists($file_cdr)) {
                    mkdir($file_cdr, 0777, true);
                }
                file_put_contents($file_cdr . "R-" . $obj->zip_name, $cdr);
                $zip = new \ZipArchive;
                if ($zip->open($file_cdr . "R-" . $obj->zip_name) === true) {
                    $zip->extractTo($file_cdr . 'R-' . $obj->xml_name);
                    $zip->close();
                }
                //LEEMOS EL CDR
                $docCDR = new \DOMDocument();
                $cdrContent = file_get_contents($file_cdr . 'R-' . $obj->xml_name . '/' . 'R-' . $obj->xml_name . '.XML');
                $docCDR->loadXML($cdrContent);

                $responseCode = $docCDR->getElementsByTagName("ResponseCode")->item(0)->nodeValue;

                if ($responseCode != 0) {
                    curl_close($ch);
                    //echo "FACTURA RECHAZADA CON CODIGO DE ERROR:" . $responseCode;//3270
                    return NewError::__Log("FACTURA RECHAZADA-", null, $responseCode);
                }
                curl_close($ch);
                return $docCDR->getElementsByTagName("Description")->item(0)->nodeValue;
            } else {
                $codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
                if (preg_match('/(\d+)/', $codigo, $matches)) {
                    $faultcode = $matches[1];
                } else {
                    $faultcode = null; // Manejar el caso en que no se encuentra un número//3270
                }
                $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
                //echo "error " . $codigo . ": " . $mensaje;

                curl_close($ch);
                return NewError::__Log($mensaje, null, $faultcode);
            }
        }
        curl_close($ch);
        return self::getRespuesta($response);
    }

    static function getRespuesta($response)
    {

        // Cargar el archivo XML
        $xml = new \SimpleXMLElement($response);

        // Extraer la información de faultcode y faultstring con expresión regular
        // Registrar los espacios de nombres
        $xml->registerXPathNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');

        // Utilizar XPath para extraer la información de faultcode y faultstring
        $faultcode = $xml->xpath('//soap-env:Fault/faultcode');
        $faultstring = $xml->xpath('//soap-env:Fault/faultstring');

        // Obtener el valor del primer elemento encontrado en cada caso
        $faultcode_value = (string)reset($faultcode);
        $faultstring_value = '';
        $faultstring_value = (string)reset($faultstring);
        $faultcode_number = 500;
        // Utilizar expresión regular para extraer el número de faultcode
        if (preg_match('/(\d+)/', $faultcode_value, $matches)) {
            $faultcode_number = $matches[1];
        }
        return NewError::__Log("Problema de conexión SUNAT...$faultstring_value", null, $faultcode_number);
    }
}
