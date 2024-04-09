<?php

namespace App\Utils\FacturacionMdl;

use App\Utils\Utils;
use App\Utils\FacturacionMdl\EmisorMdl;
use App\Utils\FacturacionMdl\ClienteMdl;
use App\Utils\FacturacionMdl\DetalleMdl;

class FacturacionMdl
{
    private $emisor;
    private $cliente;
    private $detalle;
    private $cabecera;
    private $data;

    public function __construct($data)
    {
        $g_sub_total = 0.0;
        $g_descuento = 0.0;
        $g_subtotal_con_dscto = 0.0;
        $g_igv = 0.0;
        $g_op_gravadas = 0.0;
        $g_op_exoneradas = 0.0;
        $g_op_inafectas = 0.0;
        $g_icbper = 0.0;
        $g_descuento_global = 0.0;
        $g_total_sin_desc = 0.0;
        $g_total_descuento = 0.0;
        $g_total = 0.0;

        $data['id_sucursal'] = $data['emisor']['id_sucursal'];
        $data['id_documentoelectronico'] = 1;

        $data['emisor']['anexo_sucursal'] = $data['anexo_sucursal'];
        $data['cliente']['cliente_id'] = ($data['cliente']['id']);
        $data['id_cliente'] = ($data['cliente']['id']);
        $_detalle = [];

        foreach ($data['detalle'] as $item) {
            $product = new DetalleMdl();
            $prod = $product->getItem($item);
            $_sub_total = 0.00;
            $_descuento = 0.00;
            $_op_gravadas = 0.00;
            $_op_exoneradas = 0.00;
            $_op_inafectas = 0.00;
            $_icbper = 0.00;
            $_igv = 0.0;
            $_total = 0.0;
            $_detalle[] = $prod;
            if ($prod['tipo_afectacion'] === 10) {
                $_op_gravadas = ($prod['valor_unitario'] ?? 0) * ($prod['cantidad'] ?? 0);
                $_sub_total = $_op_gravadas;
                $_descuento = $_op_gravadas - ($prod['descuento'] ?? 0);
            }

            if ($prod['tipo_afectacion'] === 20) {
                $_op_exoneradas = ($prod['valor_unitario'] ?? 0) * ($prod['cantidad'] ?? 0);
                $_sub_total = $_op_exoneradas;
                $_descuento = $_op_exoneradas - ($prod['descuento'] ?? 0);
                $_op_exoneradas = $_descuento;
            }

            if ($prod['tipo_afectacion'] === 30) {
                $_op_inafectas = ($prod['valor_unitario'] ?? 0) * ($prod['cantidad'] ?? 0);
                $_sub_total = $_op_inafectas;
                $_descuento = $_op_inafectas - ($prod['descuento'] ?? 0);
                $_op_inafectas = $_descuento;
            }

            if ($prod['afecto_icbper'] === 1) {
                $_icbper = ($prod['factor_icbper'] ?? 0) * ($prod['cantidad'] ?? 0);
            }

            $total_descuento = number_format($_descuento, 2, '.', '');

            $_igv = $prod['tipo_afectacion'] === 10 ? ($_sub_total * $prod['igv_porcentaje']) : 0.00;

            $_total = (float)$total_descuento + $_igv + $_icbper;

            $g_sub_total += self::fomatMoney($_sub_total);
            $g_descuento += self::fomatMoney($prod['descuento']);
            $g_subtotal_con_dscto += (float)$total_descuento;
            $g_igv += (float)$_igv;
            $g_op_gravadas += (float)$_op_gravadas;
            $g_op_exoneradas += (float)$_op_exoneradas;
            $g_op_inafectas += (float)$_op_inafectas;
            $g_icbper += self::fomatMoney($_icbper);
            $g_total += self::fomatMoney($_total);
        }

        $data['detalle'] = $_detalle;
        $data['id'] = ($data['id']);
        $data['tipo_comprobante'] = ($data['tipo_comprobante']);
        $data['tipo_operacion'] = ($data['tipo_operacion']);
        $data['serie'] = ($data['serie']);
        $data['correlativo'] = ($data['correlativo']);
        $data['codigo_moneda'] = ($data['codigo_moneda']);
        $data['fecha_emision'] = ($data['fecha_emision']);
        $data['fecha_vencimiento'] = ($data['fecha_vencimiento']);
        $data['fecha_creacion'] = (Utils::Now());

        $data['sub_total'] = (self::fomatMoney($g_sub_total));
        $data['descuento'] = (self::fomatMoney($g_descuento));
        $data['subtotal_con_dscto'] = (self::fomatMoney($g_subtotal_con_dscto));
        $data['igv'] = (self::fomatMoney($g_igv));
        $data['op_gravadas'] = (self::fomatMoney($g_op_gravadas));
        $data['op_exoneradas'] = (self::fomatMoney($g_op_exoneradas));
        $data['op_inafectas'] = (self::fomatMoney($g_op_inafectas));
        $data['icbper'] = (self::fomatMoney($g_icbper));

        $g_total_sin_desc = (self::fomatMoney($g_total));

        $data['total_sin_desc'] = ($g_total_sin_desc);
        $data['total_descuento'] = self::fomatMoney($g_descuento + $data['descuento_global']);

        $data['total'] = (self::fomatMoney($g_total_sin_desc - $data['descuento_global'] ?? 0));


        //$desc_porc = (($data['descuento_global'] / $g_sub_total) * 100) / 100;
        $desc_porc = (($data['descuento_global'] / $g_total_sin_desc) * 100) / 100;

        //cabecera
        $h = new CabeceraMdl($data);
        $header = $h->getCabecera();
        $header['descuento'] = (self::fomatMoney($g_descuento));
        $header['sub_total'] = (self::fomatMoney($g_sub_total));
        $header['desc_porcentaje'] = (self::fomatMoney($desc_porc));
        $header['total_op_gravadas'] = (self::fomatMoney($g_sub_total - ($g_op_exoneradas + $g_op_inafectas)));
        $header['igv'] = (self::fomatMoney($g_igv));
        $header['icbper'] = (self::fomatMoney($g_icbper));
        $header['total_op_exoneradas'] = (self::fomatMoney($g_op_exoneradas));
        $header['total_op_inafectas'] = ($g_op_inafectas);
        $header['total_antes_impuestos'] = (self::fomatMoney($g_sub_total));
        $header['total_impuestos'] = (self::fomatMoney($g_igv));
        $header['total_despues_impuestos'] = (self::fomatMoney($data['total'] + $data['total_descuento']));
        $header['descuento_global'] = self::fomatMoney($data['descuento_global'] ?? 0);
        $header['total_descuento'] = self::fomatMoney($data['total_descuento']);
        $header['total_sin_desc'] = self::fomatMoney($data['total_sin_desc']);
        $header['total_a_pagar'] = (self::fomatMoney($data['total']));
        $header['total_texto'] = (Utils::CantidadEnLetra($data['total'], $data['codigo_moneda']));

        $header['cliente_id'] = ($data['cliente']['id']);
        $header['anexo_sucursal'] = ($data['anexo_sucursal']);

        $data['cabecera'] = ($header);
        $nombre = $data['emisor']['numero_documento'] . "-" . $header['tipo_comprobante'] . "-" . $data['serie'] . "-" . $data['correlativo'];
        $data['nombre_documento'] = (strtoupper($nombre));
        $data['nombre_xml'] = (strtoupper($nombre)) . '.XML';

        $this->setData($data);
    }

    function fomatMoney($n)
    {
        //number_format($n, 2);
        return number_format($n, 2);
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
