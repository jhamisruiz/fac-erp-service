<?php

namespace App\Utils\Sunat;

use App\Utils\Service\NewError;

class Sunat
{
    public static function Init($obj, $doc)
    {
        //return $obj->{'zip_name'};
        $contenido_del_zip = base64_encode(file_get_contents($obj->{'zip_dir'}));
        $xml_envio = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" 
                xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                    <wsse:Security>
                        <wsse:UsernameToken>
                            <wsse:Username>' . $obj->{'emisor'}['numero_documento'] . $obj->{'emisor'}['usuario_emisor'] . '</wsse:Username>
        <wsse:Password>' . $obj->{'emisor'}['clave_emisor'] . '</wsse:Password>
                        </wsse:UsernameToken>
                </wsse:Security>
        </soapenv:Header>
        <soapenv:Body>
        <ser:sendBill>
            <fileName>' . $obj->{'zip_name'} . '</fileName>
            <contentFile>' . $contenido_del_zip . '</contentFile>
        </ser:sendBill>
        </soapenv:Body>
        </soapenv:Envelope>';

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

        ///__DIR__ . '/../../'
        // OBTENEMOS RESPUESTA (CDR)
        $statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($statuscode === 200) {
            $file_cdr = FILE_CDR . $doc;

            $doc = new \DOMDocument();
            $doc->loadXML($response);
            $doc->save(FILE_CDR . "respuesta.XML");
            if (isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)) {
                $cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
                $cdr = base64_decode($cdr);
                file_put_contents($file_cdr . "R-" . $obj->{'zip_name'}, $cdr);
                $zip = new \ZipArchive;
                if ($zip->open($file_cdr . "R-" . $obj->{'zip_name'}) === true) {
                    $zip->extractTo($file_cdr . 'R-' . $obj->{'xml_name'});
                    $zip->close();
                }
                //LEEMOS EL CDR
                $docCDR = new \DOMDocument();
                $cdrContent = file_get_contents($file_cdr . 'R-' . $obj->{'xml_name'} . '/' . 'R-' . $obj->{'xml_name'} . '.XML');
                $docCDR->loadXML($cdrContent);

                $responseCode = $docCDR->getElementsByTagName("ResponseCode")->item(0)->nodeValue;

                if ($responseCode != 0) {
                    curl_close($ch);
                    //echo "FACTURA RECHAZADA CON CODIGO DE ERROR:" . $responseCode;
                    return NewError::__Log("FACTURA RECHAZADA-", $responseCode);
                }
                curl_close($ch);
                return $docCDR->getElementsByTagName("Description")->item(0)->nodeValue;
            } else {
                $codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
                $mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
                //echo "error " . $codigo . ": " . $mensaje;
                curl_close($ch);
                return NewError::__Log("error-" . $mensaje, $codigo);
            }
        }
        curl_close($ch);

        //echo curl_error($ch);

        return NewError::__Log("Problema de conexión", $statuscode);
    }
}
