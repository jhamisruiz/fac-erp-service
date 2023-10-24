<?php

namespace App\Utils\Sunat;

use DOMDocument;

class XmlNotaDebito
{
    public static function XMLNotaDebito($emisor, $cliente, $cabecera,  $items)
    {
        $nombrexml = $emisor['numero_documento'] . "-" . $cabecera['tipo_comprobante'] . "-" . $cabecera['serie'] . "-" . $cabecera['correlativo'];

        $doc = new DOMDocument();
        $doc->formatOutput = FALSE;
        $doc->preserveWhiteSpace = TRUE;
        $doc->encoding = 'utf-8';

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ccts="urn:un:unece:uncefact:documentation:2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                    <ext:UBLExtensions>
                        <ext:UBLExtension>
                            <ext:ExtensionContent/>
                        </ext:UBLExtension>
                    </ext:UBLExtensions>
                    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
                    <cbc:CustomizationID>2.0</cbc:CustomizationID>
                    <cbc:ID>' . $cabecera['serie'] . '-' . $cabecera['correlativo'] . '</cbc:ID>
                    <cbc:IssueDate>' . $cabecera['fecha_emision'] . '</cbc:IssueDate>
                    <cbc:IssueTime>00:00:00</cbc:IssueTime>
                    <cbc:DocumentCurrencyCode>' . $cabecera['codigo_moneda'] . '</cbc:DocumentCurrencyCode>
                    <cac:DiscrepancyResponse>
                        <cbc:ReferenceID>' . $cabecera['serie_comp_ref'] . '-' . $cabecera['correlativo_comp_ref'] . '</cbc:ReferenceID>
                        <cbc:ResponseCode>' . $cabecera['codigo_motivo'] . '</cbc:ResponseCode>
                        <cbc:Description><![CDATA[' . $cabecera['descripcion_motivo'] . ']]></cbc:Description>
                    </cac:DiscrepancyResponse>
                    <cac:BillingReference>
                        <cac:InvoiceDocumentReference>
                            <cbc:ID>' . $cabecera['serie_comp_ref'] . '-' . $cabecera['correlativo_comp_ref'] . '</cbc:ID>
                            <cbc:DocumentTypeCode>' . $cabecera['tipo_comp_ref'] . '</cbc:DocumentTypeCode>
                        </cac:InvoiceDocumentReference>
                    </cac:BillingReference>
                    <cac:Signature>
                        <cbc:ID>IDSignST</cbc:ID>
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
                                <cbc:ID schemeID="6" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $emisor['numero_documento'] . '</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyName>
                                <cbc:Name><![CDATA[' . $emisor['razon_social'] . ']]></cbc:Name>
                            </cac:PartyName>
                            <cac:PartyLegalEntity>
                                <cbc:RegistrationName><![CDATA[' . $emisor['razon_social'] . ']]></cbc:RegistrationName>
                                <cac:RegistrationAddress>
                                    <cbc:AddressTypeCode>0001</cbc:AddressTypeCode>
                                </cac:RegistrationAddress>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:AccountingSupplierParty>
                    <cac:AccountingCustomerParty>
                        <cac:Party>
                            <cac:PartyIdentification>
                                <cbc:ID schemeID="6" schemeAgencyName="PE:SUNAT" schemeURI="urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06">' . $cliente['numero_documento'] . '</cbc:ID>
                            </cac:PartyIdentification>
                            <cac:PartyLegalEntity>
                <cbc:RegistrationName><![CDATA[JUAN PEREZ]]></cbc:RegistrationName>
                            </cac:PartyLegalEntity>
                        </cac:Party>
                    </cac:AccountingCustomerParty>
                    <cac:TaxTotal>
                    <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['igv'] . '</cbc:TaxAmount>
                    <cac:TaxSubtotal>
                        <cbc:TaxableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_op_gravadas'] . '</cbc:TaxableAmount>
                        <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['igv'] . '</cbc:TaxAmount>
                        <cac:TaxCategory>
                            <cac:TaxScheme>
                            <cbc:ID>1000</cbc:ID>
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
                    <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">E</cbc:ID>
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
                <cac:RequestedMonetaryTotal>
                    <cbc:PayableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $cabecera['total_a_pagar'] . '</cbc:PayableAmount>
                </cac:RequestedMonetaryTotal>';

        foreach ($items as $k => $v) {

            $xml .= '<cac:DebitNoteLine>
                    <cbc:ID>' . $v['item'] . '</cbc:ID>
                    <cbc:DebitedQuantity unitCode="' . $v['unidad'] . '">' . $v['cantidad'] . '</cbc:DebitedQuantity>
                    <cbc:LineExtensionAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['total_antes_impuestos'] . '</cbc:LineExtensionAmount>
                    <cac:PricingReference>
                        <cac:AlternativeConditionPrice>
                        <cbc:PriceAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['precio_lista'] . '</cbc:PriceAmount>
                        <cbc:PriceTypeCode>01</cbc:PriceTypeCode>
                        </cac:AlternativeConditionPrice>
                    </cac:PricingReference>
                    <cac:TaxTotal>
                        <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['igv'] . '</cbc:TaxAmount>
                        <cac:TaxSubtotal>
                        <cbc:TaxableAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['valor_total'] . '</cbc:TaxableAmount>
                        <cbc:TaxAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['igv'] . '</cbc:TaxAmount>
                        <cac:TaxCategory>
                            <cbc:ID schemeID="UN/ECE 5305" schemeName="Tax Category Identifier" schemeAgencyName="United Nations Economic Commission for Europe">' . $v['codigos'][0] . '</cbc:ID>
                                <cbc:Percent>' . $v['porcentaje_igv'] . '</cbc:Percent>
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
                            <cbc:ID>' . $v['item'] . '</cbc:ID>
                            </cac:SellersItemIdentification>
                        </cac:Item>
                        <cac:Price>
                            <cbc:PriceAmount currencyID="' . $cabecera['codigo_moneda'] . '">' . $v['valor_unitario'] . '</cbc:PriceAmount>
                        </cac:Price>
                    </cac:DebitNoteLine>';
        }

        $xml .= '</DebitNote>';

        $file = FILE_XML . 'nota_debito/';
        $doc->loadXML($xml);
        $doc->save($file . $nombrexml . '.XML');

        error_log("[Nota Debito]------->: {$nombrexml} generado");
        // FIRMAR EL XML
        $obj = new \stdClass();
        $obj->file = $file;
        $obj->xml_name = $nombrexml;
        return $obj;
    }
}
