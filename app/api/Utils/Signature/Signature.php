<?php

namespace App\Utils\Signature;

use DOMDocument;
use App\Utils\Service\NewError;
use App\Utils\Signature\XMLSecEnc;
use App\Utils\Signature\XMLSecurityKey;
use App\Utils\Signature\XMLSecurityDSig;

class Signature
{
    public function signature_xml($flg_firma, $ruta, $ruta_firma, $pass_firma, $nombrexml = null)
    {
        $doc = new DOMDocument();

        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->load($ruta);

        $objDSig = new XMLSecurityDSig(FALSE);
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $options['force_uri'] = TRUE;
        $options['id_name'] = 'ID';
        $options['overwrite'] = FALSE;

        $objDSig->addReference($doc, XMLSecurityDSig::SHA1, array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'), $options);
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));

        $pfx = file_get_contents("$ruta_firma");
        $key = array();

        if (openssl_pkcs12_read($pfx, $key, "$pass_firma")) {
            //print_r($key);//exit;
            $objKey->loadKey($key["pkey"]);
            $objDSig->add509Cert($key["cert"], TRUE, FALSE);
            $objDSig->sign($objKey, $doc->documentElement->getElementsByTagName("ExtensionContent")->item($flg_firma));

            $atributo = $doc->getElementsByTagName('Signature')->item(0);
            $atributo->setAttribute('Id', 'SignatureSP');

            //===================rescatamos Codigo(HASH_CPE)==================
            $hash_cpe = $doc->getElementsByTagName('DigestValue')->item(0)->nodeValue;
            $firma_cpe = $doc->getElementsByTagName('SignatureValue')->item(0)->nodeValue;

            $doc->save($ruta);
            $resp['respuesta'] = 'ok';
            $resp['hash_cpe'] = $hash_cpe;
            $resp['firma_cpe'] = $firma_cpe;
            error_log("openssl_pkcs12_read[{$nombrexml}]------->: firmado");
            return $resp;
        }
        error_log("ERROR:========[XML]------->: NO FIRMADO.");
        //FIXME: cambiar el codigo de error
        return NewError::__Log("XML no firmado, error de certificado digital", 500);
    }
}
