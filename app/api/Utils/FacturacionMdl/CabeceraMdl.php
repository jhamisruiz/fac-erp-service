<?php

namespace App\Utils\FacturacionMdl;

use App\Utils\Utils;

class CabeceraMdl
{
    private $cabecera;
    public function __construct($data)
    {
        // Usar los setters para insertar datos
        $monto_credito = 0;
        $cuotas = array();
        if ($data['forma_pago'] == 'Credito') {

            foreach ($data['data'] as $key => $v) {
                $numero = str_pad(($key + 1), 3, "0", STR_PAD_LEFT);

                $cuotas[] = (object)[
                    "numero" => $numero,
                    "importe" => $v['importe'],
                    "vencimiento" => $v['fecha_vencimiento']
                ];
                $monto_credito = $monto_credito + $v['importe'];
            }
        }

        $motivo = array('descripcion' => '');
        if ($data['tipo_comprobante'] != '07' && $data['tipo_comprobante'] != '08') {
            $data['tcomprobante_ref'] = '';
            $data['serie_ref'] = '';
            $data['correlativo_ref'] = 0;
            $data['motivo'] = '';
        }
        if ($data['tipo_comprobante'] == '07') {
            //$motivo = $objCompartido->getRegistroTablaParametrica('C', $data['motivo']);//FIXME: nota credito
            $motivo = $motivo;
        }

        if ($data['tipo_comprobante'] == '08') {
            //$motivo = $objCompartido->getRegistroTablaParametrica('D', $data['motivo']);//FIXME: nota debito
            $motivo = $motivo;
        }

        $head['tipo_operacion'] = ($data['tipo_operacion']);
        $head['tipo_comprobante'] = ($data['tipo_comprobante']); //01,03,07,08,...
        $head['serie_id'] = ($data['id_serie']);
        $head['serie'] = ($data['serie']);
        $head['correlativo'] = ($data['correlativo']);
        $head['fecha_emision'] = ($data['fecha_emision']);
        $head['hora_emision'] = (Utils::Time());
        $head['forma_pago'] = ($data['forma_pago']);
        $head['monto_credito'] = ($monto_credito);
        $head['fecha_vencimiento'] = ($data['fecha_vencimiento']);
        $head['moneda'] = ($data['codigo_moneda']);
        $head['tipo_comprobante_ref_id'] = ($data['tcomprobante_ref']);
        $head['serie_ref'] = ($data['serie_ref']);
        $head['correlativo_ref'] = ($data['correlativo_ref']);
        $head['codMotivo'] = ($data['motivo']);
        $head['descripcion'] = ($motivo['descripcion']);
        $head['cuotas'] = ($cuotas);

        $this->setCabecera($head);
    }

    /**
     * Get the value of cabecera
     */
    public function getCabecera()
    {
        return $this->cabecera;
    }

    /**
     * Set the value of cabecera
     *
     * @return  self
     */
    public function setCabecera($cabecera)
    {
        $this->cabecera = $cabecera;

        return $this;
    }
}
