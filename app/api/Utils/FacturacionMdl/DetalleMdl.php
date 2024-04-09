<?php

namespace App\Utils\FacturacionMdl;

class DetalleMdl
{

    private $id;
    private $id_producto;
    private $nombre_producto;
    private $codigo;
    private $codigo_unspsc;
    private $idunidad_medida;
    private $unidad_medida;
    private $codigos;
    private $tipo_afectacion;
    private $afecto_icbper;
    private $cantidad_actual;
    private $cantidad;
    private $valor_unitario;
    private $sub_total;
    private $descuento;
    private $subtotal_con_dscto;
    private $igv_porcentaje;
    private $igv;
    private $factor_icbper;
    private $icbper;
    private $op_exoneradas;
    private $op_inafectas;
    private $total;
    private $item;
    private $tipo_precio;
    private $precio_lista;
    private $desc_porcentaje;
    private $porcentaje_igv;
    private $total_antes_impuestos;
    private $valor_total;
    private $total_impuestos;
    private $importe_total;
    private $id_tipoafectacion;

    private function setItem($data)
    {
        $igv_porct = 0.0;
        $factor_igv = 1;
        $data['item'] = ($data['index'] + 1);
        $afectacion = $data['codigos'];
        $data['tipo_afectacion'] = $afectacion['codigo'];
        $data['codigo'] = ($data['codigo'] ? $data['codigo'] : 195);
        $data['tipo_precio'] = ('01');

        $_sub_total = 0.00;
        $_descuento = 0.00;
        $_op_gravadas = 0.00;
        $_op_exoneradas = 0.00;
        $_op_inafectas = 0.00;
        $_icbper = 0.00;
        $_igv = 0.0;
        $_total = 0.0;
        if ($data['tipo_afectacion'] === 10) {
            $_op_gravadas = ($data['valor_unitario'] ?? 0) * ($data['cantidad'] ?? 0);
            $_sub_total = $_op_gravadas;
            $_descuento = $_op_gravadas - ($data['descuento'] ?? 0);
            $igv_porct = $data['igv_porcentaje'];
            $factor_igv = 1 + $igv_porct; #NOTE: 2
        }

        if ($data['tipo_afectacion'] === 20) {
            $_op_exoneradas = ($data['valor_unitario'] ?? 0) * ($data['cantidad'] ?? 0);
            $_sub_total = $_op_exoneradas;
            $_descuento = $_op_exoneradas - ($data['descuento'] ?? 0);
            $_op_exoneradas = $_descuento;
        }

        if ($data['tipo_afectacion'] === 30) {
            $_op_inafectas = ($data['valor_unitario'] ?? 0) * ($data['cantidad'] ?? 0);
            $_sub_total = $_op_inafectas;
            $_descuento = $_op_inafectas - ($data['descuento'] ?? 0);
            $_op_inafectas = $_descuento;
        }

        $factor_icbper = 0;
        if ($data['afecto_icbper'] === 1) {
            $_icbper = ($data['factor_icbper'] ?? 0) * ($data['cantidad'] ?? 0);
            $factor_icbper = $data['factor_icbper'];
        }

        $_op_exoneradas = number_format($_op_exoneradas, 2, '.', '');
        $_op_inafectas = number_format($_op_inafectas, 2, '.', '');

        $total_descuento = number_format($_descuento, 2, '.', '');
        $total_descuento = (float)$total_descuento;

        $porcent_igv = $data['tipo_afectacion'] === 10 ? $data['igv_porcentaje'] : 0.00;

        $_igv = ($_sub_total * $porcent_igv);

        $_igv = number_format($_igv, 2, '.', '');
        $_total = (float)$total_descuento + $_igv + $_icbper;
        $total_impuestos_item = $_igv + $_icbper;

        $desc = isset($data['descuento']) ? $data['descuento'] : 0;
        $desc_porc = $desc > 0 ? ((($desc / $_sub_total) * 100) / 100) : 0;
        //$desc_porc = $desc > 0 ? (($desc / $_sub_total) * 100) : 0;

        $data['valor_unitario'] = $data['valor_unitario'];
        $data['precio_lista'] = (($data['valor_unitario'] * $factor_igv)) - $desc; #NOTE: 1
        $data['sub_total'] = (self::fomatMoney($_sub_total));
        $data['descuento'] = (self::fomatMoney($desc));
        $data['desc_porcentaje'] = (self::fomatMoney($desc_porc));
        $data['subtotal_con_dscto'] = (self::fomatMoney($total_descuento));
        $data['igv_porcentaje'] = ($porcent_igv);
        $data['porcentaje_igv'] = ($porcent_igv * 100);
        $data['igv'] = (self::fomatMoney($_igv));
        $data['factor_icbper'] = (self::fomatMoney($factor_icbper));
        $data['icbper'] = (self::fomatMoney($_icbper));
        $data['op_exoneradas'] = (self::fomatMoney($_op_exoneradas));
        $data['op_inafectas'] = (self::fomatMoney($_op_inafectas));
        $data['total'] = (self::fomatMoney($_total));
        $data['total_antes_impuestos'] = (self::fomatMoney($_sub_total));
        $data['valor_total'] = (self::fomatMoney($_sub_total));
        $data['total_impuestos'] = (self::fomatMoney($total_impuestos_item));
        $data['importe_total'] = (self::fomatMoney($_total));

        $data['id_tipoafectacion'] = $afectacion['id'];
        $data['codigos'] = array(
            $afectacion['letra'],
            $afectacion['codigo'],
            $afectacion['codigo_trbto'],
            $afectacion['nombre'],
            $afectacion['tipo']
        );

        return $data;
    }
    function fomatMoney($n)
    {
        return number_format($n, 2);
    }

    /**
     * Get the value of item
     */
    public function getItem($data)
    {
        $item = self::setItem($data);
        $data =  [];
        foreach ($item as $clave => $valor) {
            // Verificar si la propiedad existe antes de asignarla
            if (property_exists($this, $clave)) {
                $data[$clave] = $valor;
            }
        }

        return $data;
    }
}
