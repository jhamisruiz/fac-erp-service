<?php

namespace App\Utils\Sunat;

use DOMDocument;

class XmlBoleta
{
    public static function Boleta($emisor, $cliente, $cabecera, $cuotas, $items)
    {
        $nombrexml = $emisor['numero_documento'] . "-" . $cabecera['tipo_comprobante'] . "-" . $cabecera['serie'] . "-" . $cabecera['correlativo'];

        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        $xml = '<?xml version="1.0" encoding="utf-8"?>
            <Invoice xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2">
            <ext:UBLExtensions>
                <ext:UBLExtension>
                    <ext:ExtensionContent/>
                </ext:UBLExtension>
            </ext:UBLExtensions>
            <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
            <cbc:CustomizationID schemeAgencyName="PE:SUNAT">2.0</cbc:CustomizationID>
            <cbc:ProfileID schemeName="Tipo de Operacion" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo51">' . $cabecera['tipo_operacion'] . '</cbc:ProfileID>
            <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
            <cbc:IssueDate>' . $cabecera['fecha_emision'] . '</cbc:IssueDate>
            <cbc:IssueTime>' . $cabecera['hora_emision'] . '</cbc:IssueTime>
            <cbc:DueDate>' . $cabecera['fecha_vencimiento'] . '</cbc:DueDate>
            <cbc:InvoiceTypeCode listAgencyName="PE:SUNAT" listName="Tipo de Documento" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo01" listID="0101" name="Tipo de Operacion">' . $cabecera['tipo_comprobante'] . '</cbc:InvoiceTypeCode>
            <cbc:DocumentCurrencyCode listID="ISO 4217 Alpha" listName="Currency" listAgencyName="United Nations Economic Commission for Europe">' . $cabecera['codigo_moneda'] . '</cbc:DocumentCurrencyCode>
                        <cbc:LineCountNumeric>' . count($items) . '</cbc:LineCountNumeric>
                <cac:Signature>
                <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
                <cac:SignatoryParty>
                    <cac:PartyIdentification>
                        <cbc:ID>' . $emisor['numero_documento'] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA[' . $emisor['razon_social'] . ']]></cbc:Name>
                    </cac:PartyName>
                </cac:SignatoryParty>
                <cac:DigitalSignatureAttachment>
                    <cac:ExternalReference>
                        <cbc:URI>#SignatureSP</cbc:URI>
                    </cac:ExternalReference>
                </cac:DigitalSignatureAttachment>
            </cac:Signature>
            <cac:AccountingSupplierParty>
                <cac:Party>
                    <cac:PartyIdentification>
                        <cbc:ID schemeID="' . $emisor['tipo_documento'] . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $emisor['numero_documento'] . '</cbc:ID>
                    </cac:PartyIdentification>
                    <cac:PartyName>
                        <cbc:Name><![CDATA[' . $emisor['razon_social'] . ']]></cbc:Name>
                    </cac:PartyName>
                    <cac:PartyTaxScheme>
                        <cbc:RegistrationName><![CDATA[' . $emisor['razon_social'] . ']]></cbc:RegistrationName>
                        <cbc:CompanyID schemeID="' . $emisor['tipo_documento'] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $emisor['numero_documento'] . '</cbc:CompanyID>
                        <cac:TaxScheme>
                        <cbc:ID schemeID="' . $emisor['tipo_documento'] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $emisor['numero_documento'] . '</cbc:ID>
                        </cac:TaxScheme>
                    </cac:PartyTaxScheme>
                    <cac:PartyLegalEntity>
                        <cbc:RegistrationName><![CDATA[' . $emisor['razon_social'] . ']]></cbc:RegistrationName>
                        <cac:RegistrationAddress>
                        <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI">' . $emisor['ubigeo'] . '</cbc:ID>
                        <cbc:AddressTypeCode listAgencyName="PE:SUNAT" listName="Establecimientos anexos">' . $cabecera['anexo_sucursal'] . '</cbc:AddressTypeCode>
                        <cbc:CityName><![CDATA[' . $emisor['provincia'] . ']]></cbc:CityName>
                        <cbc:CountrySubentity><![CDATA[' . $emisor['departamento'] . ']]></cbc:CountrySubentity>
                        <cbc:District><![CDATA[' . $emisor['distrito'] . ']]></cbc:District>
                        <cac:AddressLine>
                            <cbc:Line><![CDATA[' . $emisor['direccion'] . ']]></cbc:Line>
                        </cac:AddressLine>
                        <cac:Country>
                            <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country">PE</cbc:IdentificationCode>
                        </cac:Country>
                        </cac:RegistrationAddress>
                    </cac:PartyLegalEntity>
                    <cac:Contact>
                        <cbc:Name><![CDATA[]]></cbc:Name>
                    </cac:Contact>
                </cac:Party>
            </cac:AccountingSupplierParty>
            <cac:AccountingCustomerParty>
            <cac:Party>
                <cac:PartyIdentification>
                    <cbc:ID schemeID="' . $cliente['tipo_documento'] . '" schemeName="Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cliente['numero_documento'] . '</cbc:ID>
                </cac:PartyIdentification>
                <cac:PartyName>
                    <cbc:Name><![CDATA[' . $cliente['razon_social'] . ']]></cbc:Name>
                </cac:PartyName>
                <cac:PartyTaxScheme>
                    <cbc:RegistrationName><![CDATA[' . $cliente['razon_social'] . ']]></cbc:RegistrationName>
                    <cbc:CompanyID schemeID="' . $cliente['tipo_documento'] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cliente['numero_documento'] . '</cbc:CompanyID>
                    <cac:TaxScheme>
                    <cbc:ID schemeID="' . $cliente['tipo_documento'] . '" schemeName="SUNAT:Identificador de Documento de Identidad" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cliente['numero_documento'] . '</cbc:ID>
                    </cac:TaxScheme>
                </cac:PartyTaxScheme>
                <cac:PartyLegalEntity>
                    <cbc:RegistrationName><![CDATA[' . $cliente['razon_social'] . ']]></cbc:RegistrationName>
                    <cac:RegistrationAddress>
                    <cbc:ID schemeName="Ubigeos" schemeAgencyName="PE:INEI"/>
                    <cbc:CityName><![CDATA[]]></cbc:CityName>
                    <cbc:CountrySubentity><![CDATA[]]></cbc:CountrySubentity>
                    <cbc:District><![CDATA[]]></cbc:District>
                    <cac:AddressLine>
                        <cbc:Line><![CDATA[' . $cliente['direccion'] . ']]></cbc:Line>
                    </cac:AddressLine>                                        
                    <cac:Country>
                        <cbc:IdentificationCode listID="ISO 3166-1" listAgencyName="United Nations Economic Commission for Europe" listName="Country"/>
                    </cac:Country>
                    </cac:RegistrationAddress>
                </cac:PartyLegalEntity>
            </cac:Party>
        </cac:AccountingCustomerParty>';

        $xml .= '<cac:PaymentTerms>
                <cbc:ID>FormaPago</cbc:ID>
                <cbc:PaymentMeansID>' . $cabecera['forma_pago'] . '</cbc:PaymentMeansID>
                <cbc:Amount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['monto_credito'] . '</cbc:Amount>
            </cac:PaymentTerms>';


        foreach ($cuotas as $k => $v) {
            $xml .= '<cac:PaymentTerms>
                        <cbc:ID>FormaPago</cbc:ID>
                        <cbc:PaymentMeansID>Cuota' . $v['numero'] . '</cbc:PaymentMeansID>
                        <cbc:Amount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['importe'] . '</cbc:Amount>
                        <cbc:PaymentDueDate>' . $v['vencimiento'] . '</cbc:PaymentDueDate>
                    </cac:PaymentTerms>
                    ';
        }

        $xml .= '<cac:TaxTotal>
            <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_impuestos'] . '</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_op_gravadas'] . '</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['igv'] . '</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">S</cbc:ID>
                    <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">1000</cbc:ID>
                    <cbc:Name>IGV</cbc:Name>
                    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';

        if ($cabecera['total_op_exoneradas'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_op_exoneradas'] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
                        <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9997</cbc:ID>
                        <cbc:Name>EXO</cbc:Name>
                        <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';
        }

        if ($cabecera['total_op_inafectas'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_op_inafectas'] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">0.00</cbc:TaxAmount>
                    <cac:TaxCategory>
                        <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">O</cbc:ID>
                        <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">9998</cbc:ID>
                        <cbc:Name>INA</cbc:Name>
                        <cbc:TaxTypeCode>FRE</cbc:TaxTypeCode>
                        </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';
        }


        if ($cabecera['icbper'] > 0) {
            $xml .= '<cac:TaxSubtotal>
                <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['icbper'] . '</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cac:TaxScheme>
                    <cbc:ID schemeID="UN/ECE 5153" schemeAgencyID="6">7152</cbc:ID>
                    <cbc:Name>ICBPER</cbc:Name>
                    <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>';
        }

        $xml .= '</cac:TaxTotal>
        <cac:LegalMonetaryTotal>
            <cbc:LineExtensionAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_antes_impuestos'] . '</cbc:LineExtensionAmount>
            <cbc:TaxInclusiveAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_despues_impuestos'] . '</cbc:TaxInclusiveAmount>
            <cbc:PayableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_a_pagar'] . '</cbc:PayableAmount>
        </cac:LegalMonetaryTotal>';

        foreach ($items as $k => $v) {

            $xml .= '<cac:InvoiceLine>
            <cbc:ID>' . $v['item'] . '</cbc:ID>
            <cbc:InvoicedQuantity unitCode="' . $v['unidad'] . '" unitCodeListID="UN/ECE rec 20" unitCodeListAgencyName="United Nations Economic Commission for Europe">' . $v['cantidad'] . '</cbc:InvoicedQuantity>
            <cbc:LineExtensionAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['total_antes_impuestos'] . '</cbc:LineExtensionAmount>
            <cac:PricingReference>
                <cac:AlternativeConditionPrice>
                    <cbc:PriceAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['precio_lista'] . '</cbc:PriceAmount>
                    <cbc:PriceTypeCode listName="Tipo de Precio" listAgencyName="PE:SUNAT" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo16">01</cbc:PriceTypeCode>
                </cac:AlternativeConditionPrice>
            </cac:PricingReference>
            <cac:TaxTotal>
                <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['total_impuestos'] . '</cbc:TaxAmount>
                <cac:TaxSubtotal>
                    <cbc:TaxableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['valor_total'] . '</cbc:TaxableAmount>
                    <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['igv'] . '</cbc:TaxAmount>
                    <cac:TaxCategory>
                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">' . $v['codigos'][0] . '</cbc:ID>
                    <cbc:Percent>18</cbc:Percent>
                    <cbc:TaxExemptionReasonCode listAgencyName="PE:SUNAT" listName="Afectacion del IGV" listURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo07">' . $v['codigos'][1] . '</cbc:TaxExemptionReasonCode>
                    <cac:TaxScheme>
                        <cbc:ID schemeID="UN/ECE 5153" schemeName="Codigo de tributos" schemeAgencyName="PE:SUNAT">' . $v['codigos'][2] . '</cbc:ID>
                        <cbc:Name>' . $v['codigos'][3] . '</cbc:Name>
                        <cbc:TaxTypeCode>' . $v['codigos'][4] . '</cbc:TaxTypeCode>
                    </cac:TaxScheme>
                    </cac:TaxCategory>
                </cac:TaxSubtotal>';

            if ($v['icbper'] > 0) {
                $xml .= '<cac:TaxSubtotal>
                        <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['icbper'] . '</cbc:TaxAmount>
                        <cbc:BaseUnitMeasure unitCode="' . $v['unidad'] . '">' . $v['cantidad'] . '</cbc:BaseUnitMeasure>            
                        <cac:TaxCategory>
                                <cbc:PerUnitAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['factor_icbper'] . '</cbc:PerUnitAmount>
                                <cac:TaxScheme>
                                        <cbc:ID>7152</cbc:ID>
                                        <cbc:Name>ICBPER</cbc:Name>
                                        <cbc:TaxTypeCode>OTH</cbc:TaxTypeCode>
                                </cac:TaxScheme>
                        </cac:TaxCategory>
                    </cac:TaxSubtotal>';
            }

            $xml .= '</cac:TaxTotal>
            <cac:Item>
                <cbc:Description><![CDATA[' . $v['nombre'] . ']]></cbc:Description>
                <cac:SellersItemIdentification>
                    <cbc:ID><![CDATA[' . $v['codigo'] . ']]></cbc:ID>
                </cac:SellersItemIdentification>
                <cac:CommodityClassification>
                    <cbc:ItemClassificationCode listID="UNSPSC" listAgencyName="GS1 US" listName="Item Classification">' . $v['codigo_unspsc'] . '</cbc:ItemClassificationCode>
                </cac:CommodityClassification>
            </cac:Item>
            <cac:Price>
                <cbc:PriceAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['valor_unitario'] . '</cbc:PriceAmount>
            </cac:Price>
        </cac:InvoiceLine>';
        }

        $xml .= '</Invoice>';

        $file = FILE_XML . 'botela/';
        $doc->loadXML($xml);
        $doc->save($file . $nombrexml . '.XML');

        error_log("[Boleta]------->: {$nombrexml} generado");
        $obj = new \stdClass();
        $obj->file = $file;
        $obj->xml_name = $nombrexml;
        return $obj;
    }
}
