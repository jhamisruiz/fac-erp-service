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

    private $id;
    private $tipo_comprobante;
    private $serie;
    private $correlativo;
    private $codigo_moneda;
    private $fecha_emision;
    private $fecha_vencimiento;
    private $fecha_creacion;

    private $sub_total;
    private $descuento;
    private $subtotal_con_dscto;
    private $igv;
    private $op_exoneradas;
    private $op_inafectas;
    private $icbper;
    private $total;

    public function __construct($data)
    {
        $g_sub_total = 0.0;
        $g_descuento = 0.0;
        $g_subtotal_con_dscto = 0.0;
        $g_igv = 0.0;
        $g_op_exoneradas = 0.0;
        $g_op_inafectas = 0.0;
        $g_icbper = 0.0;
        $g_total = 0.0;

        $this->setEmisor(new EmisorMdl($data['emisor']));
        $this->setCliente(new ClienteMdl($data['cliente']));

        $detalles = [];
        foreach ($data['detalle'] as $detalleData) {
            $prod = new DetalleMdl($detalleData);
            $_sub_total = 0.00;
            $_descuento = 0.00;
            $_op_gravadas = 0.00;
            $_op_exoneradas = 0.00;
            $_op_inafectas = 0.00;
            $_icbper = 0.00;
            $_igv = 0.0;
            $_total = 0.0;
            $detalles[] = $prod;
            if ($prod->getTipo_afectacion() === 10) {
                $_op_gravadas = ($prod->getValor_unitario() ?? 0) * ($prod->getCantidad() ?? 0);
                $_sub_total = $_op_gravadas;
                $_descuento = $_op_gravadas - ($prod->getDescuento() ?? 0);
            }

            if ($prod->getTipo_afectacion() === 20) {
                $_op_exoneradas = ($prod->getvalor_unitario() ?? 0) * ($prod->getcantidad() ?? 0);
                $_sub_total = $_op_exoneradas;
                $_descuento = $_op_exoneradas - ($prod->getDescuento() ?? 0);
                $_op_exoneradas = $_descuento;
            }

            if ($prod->getTipo_afectacion() === 30) {
                $_op_inafectas = ($prod->getvalor_unitario() ?? 0) * ($prod->getcantidad() ?? 0);
                $_sub_total = $_op_inafectas;
                $_descuento = $_op_inafectas - ($prod->getDescuento() ?? 0);
                $_op_inafectas = $_descuento;
            }

            if ($prod->getAfecto_icbper() === 1) {
                $_icbper = ($prod->getFactor_icbper() ?? 0) * ($prod->getcantidad() ?? 0);
            }

            $total_descuento = number_format($_descuento, 2, '.', '');

            $_igv = $prod->getTipo_afectacion() === 10 ? ($total_descuento * $prod->getIgv_porcentaje()) : 0.00;

            $_total = (float)$total_descuento + $_igv + $_icbper;

            $g_sub_total += $_sub_total;
            $g_descuento += $prod->getDescuento();
            $g_subtotal_con_dscto += (float)$total_descuento;
            $g_igv += (float)$_igv;
            $g_op_exoneradas += (float)$_op_exoneradas;
            $g_op_inafectas += (float)$_op_inafectas;
            $g_icbper += $_icbper;
            $g_total += $_total;
        }

        $this->setDetalle($detalles);

        $this->setId($data['id']);
        $this->getTipo_comprobante($data['tipo_comprobante']);
        $this->setSerie($data['correlativo']);
        $this->setCorrelativo($data['serie']);
        $this->setCodigo_moneda($data['codigo_moneda']);
        $this->setFecha_emision($data['fecha_emision']);
        $this->setFecha_vencimiento($data['fecha_vencimiento']);
        $this->setFecha_creacion(Utils::Now());

        $this->setSub_total(self::fomatMoney($g_sub_total));
        $this->setDescuento(self::fomatMoney($g_descuento));
        $this->setSubtotal_con_dscto(self::fomatMoney($g_subtotal_con_dscto));
        $this->setIgv(self::fomatMoney($g_igv));
        $this->setOp_exoneradas(self::fomatMoney($g_op_exoneradas));
        $this->setOp_inafectas(self::fomatMoney($g_op_inafectas));
        $this->setIcbper(self::fomatMoney($g_icbper));
        $this->setTotal(self::fomatMoney($g_total));
    }

    function fomatMoney($n)
    {
        //number_format($n, 2);
        return (float)number_format($n, 2);
    }

    public function getEmisor()
    {
        return $this->emisor;
    }
    public function setEmisor($emisor)
    {
        $this->emisor = $emisor;

        return $this;
    }

    public function getCliente()
    {
        return $this->cliente;
    }

    public function setCliente($cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getDetalle()
    {
        return $this->detalle;
    }

    public function setDetalle($detalle)
    {
        $this->detalle = $detalle;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getTipo_comprobante()
    {
        return $this->tipo_comprobante;
    }

    public function setTipo_comprobante($tipo_comprobante)
    {
        $this->tipo_comprobante = $tipo_comprobante;

        return $this;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;

        return $this;
    }

    public function getCorrelativo()
    {
        return $this->correlativo;
    }

    public function setCorrelativo($correlativo)
    {
        $this->correlativo = $correlativo;

        return $this;
    }

    public function getCodigo_moneda()
    {
        return $this->codigo_moneda;
    }

    public function setCodigo_moneda($codigo_moneda)
    {
        $this->codigo_moneda = $codigo_moneda;

        return $this;
    }

    public function getFecha_emision()
    {
        return $this->fecha_emision;
    }

    public function setFecha_emision($fecha_emision)
    {
        $this->fecha_emision = $fecha_emision;

        return $this;
    }

    public function getFecha_vencimiento()
    {
        return $this->fecha_vencimiento;
    }

    public function setFecha_vencimiento($fecha_vencimiento)
    {
        $this->fecha_vencimiento = $fecha_vencimiento;

        return $this;
    }

    public function getFecha_creacion()
    {
        return $this->fecha_creacion;
    }

    public function setFecha_creacion($fecha_creacion)
    {
        $this->fecha_creacion = $fecha_creacion;

        return $this;
    }

    public function getSub_total()
    {
        return $this->sub_total;
    }

    public function setSub_total($sub_total)
    {
        $this->sub_total = $sub_total;

        return $this;
    }

    public function getDescuento()
    {
        return $this->descuento;
    }

    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    public function getSubtotal_con_dscto()
    {
        return $this->subtotal_con_dscto;
    }

    public function setSubtotal_con_dscto($subtotal_con_dscto)
    {
        $this->subtotal_con_dscto = $subtotal_con_dscto;

        return $this;
    }

    public function getIgv()
    {
        return $this->igv;
    }

    public function setIgv($igv)
    {
        $this->igv = $igv;

        return $this;
    }

    public function getOp_exoneradas()
    {
        return $this->op_exoneradas;
    }

    public function setOp_exoneradas($op_exoneradas)
    {
        $this->op_exoneradas = $op_exoneradas;

        return $this;
    }

    public function getOp_inafectas()
    {
        return $this->op_inafectas;
    }

    public function setOp_inafectas($op_inafectas)
    {
        $this->op_inafectas = $op_inafectas;

        return $this;
    }

    public function getIcbper()
    {
        return $this->icbper;
    }

    public function setIcbper($icbper)
    {
        $this->icbper = $icbper;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }
}
