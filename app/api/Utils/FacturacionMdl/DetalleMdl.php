<?php

namespace App\Utils\FacturacionMdl;

class DetalleMdl
{
    private $id;
    private $id_producto;
    private $item;
    private $codigo;
    private $codigo_unspsc;
    private $idunidad_medida;
    private $unidad_medida;
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

    public function __construct($data)
    {
        $this->setItem(($data['index'] + 1));
        $this->setId($data['id']);
        $this->setId_producto($data['id_producto']);
        $this->setCodigo($data['codigo']);
        $this->setCodigo_unspsc($data['codigo_unspsc']);
        $this->setIdunidad_medida($data['idunidad_medida']);
        $this->setUnidad_medida($data['unidad_medida']);
        $this->setTipo_afectacion($data['tipo_afectacion']);
        $this->setAfecto_icbper($data['afecto_icbper']);
        $this->setCantidad_actual($data['cantidad_actual']);
        $this->setCantidad($data['cantidad']);
        $this->setValor_unitario($data['valor_unitario']);

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

        if ($data['afecto_icbper'] === 1) {
            $_icbper = ($data['factor_icbper'] ?? 0) * ($data['cantidad'] ?? 0);
        }

        $_op_exoneradas = number_format($_op_exoneradas, 2, '.', '');
        $_op_inafectas = number_format($_op_inafectas, 2, '.', '');

        $total_descuento = number_format($_descuento, 2, '.', '');
        $total_descuento = (float)$total_descuento;

        $_igv = $data['tipo_afectacion'] === 10 ? ($total_descuento * $data['igv_porcentaje']) : 0.00;

        $_igv = number_format($_igv, 2, '.', '');
        $_total = (float)$total_descuento + $_igv + $_icbper;

        $this->setSub_total(self::fomatMoney($_sub_total));
        $this->setDescuento(self::fomatMoney($data['descuento']));
        $this->setSubtotal_con_dscto(self::fomatMoney($total_descuento));
        $this->setIgv_porcentaje($data['igv_porcentaje']);
        $this->setIgv(self::fomatMoney($_igv));
        $this->setFactor_icbper(self::fomatMoney($data['factor_icbper']));
        $this->setIcbper(self::fomatMoney($_icbper));
        $this->setOp_exoneradas(self::fomatMoney($_op_exoneradas));
        $this->setOp_inafectas(self::fomatMoney($_op_inafectas));
        $this->setTotal(self::fomatMoney($_total));
    }
    function fomatMoney($n)
    {
        return (float)number_format($n, 2);
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id_producto
     */
    public function getId_producto()
    {
        return $this->id_producto;
    }

    /**
     * Set the value of id_producto
     *
     * @return  self
     */
    public function setId_producto($id_producto)
    {
        $this->id_producto = $id_producto;

        return $this;
    }

    /**
     * Get the value of item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set the value of item
     *
     * @return  self
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get the value of codigo
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set the value of codigo
     *
     * @return  self
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get the value of codigo_unspsc
     */
    public function getCodigo_unspsc()
    {
        return $this->codigo_unspsc;
    }

    /**
     * Set the value of codigo_unspsc
     *
     * @return  self
     */
    public function setCodigo_unspsc($codigo_unspsc)
    {
        $this->codigo_unspsc = $codigo_unspsc;

        return $this;
    }

    /**
     * Get the value of idunidad_medida
     */
    public function getIdunidad_medida()
    {
        return $this->idunidad_medida;
    }

    /**
     * Set the value of idunidad_medida
     *
     * @return  self
     */
    public function setIdunidad_medida($idunidad_medida)
    {
        $this->idunidad_medida = $idunidad_medida;

        return $this;
    }

    /**
     * Get the value of unidad_medida
     */
    public function getUnidad_medida()
    {
        return $this->unidad_medida;
    }

    /**
     * Set the value of unidad_medida
     *
     * @return  self
     */
    public function setUnidad_medida($unidad_medida)
    {
        $this->unidad_medida = $unidad_medida;

        return $this;
    }

    /**
     * Get the value of tipo_afectacion
     */
    public function getTipo_afectacion()
    {
        return $this->tipo_afectacion;
    }

    /**
     * Set the value of tipo_afectacion
     *
     * @return  self
     */
    public function setTipo_afectacion($tipo_afectacion)
    {
        $this->tipo_afectacion = $tipo_afectacion;

        return $this;
    }

    /**
     * Get the value of afecto_icbper
     */
    public function getAfecto_icbper()
    {
        return $this->afecto_icbper;
    }

    /**
     * Set the value of afecto_icbper
     *
     * @return  self
     */
    public function setAfecto_icbper($afecto_icbper)
    {
        $this->afecto_icbper = $afecto_icbper;

        return $this;
    }

    /**
     * Get the value of cantidad_actual
     */
    public function getCantidad_actual()
    {
        return $this->cantidad_actual;
    }

    /**
     * Set the value of cantidad_actual
     *
     * @return  self
     */
    public function setCantidad_actual($cantidad_actual)
    {
        $this->cantidad_actual = $cantidad_actual;

        return $this;
    }

    /**
     * Get the value of cantidad
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set the value of cantidad
     *
     * @return  self
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get the value of valor_unitario
     */
    public function getValor_unitario()
    {
        return $this->valor_unitario;
    }

    /**
     * Set the value of valor_unitario
     *
     * @return  self
     */
    public function setValor_unitario($valor_unitario)
    {
        $this->valor_unitario = $valor_unitario;

        return $this;
    }

    /**
     * Get the value of sub_total
     */
    public function getSub_total()
    {
        return $this->sub_total;
    }

    /**
     * Set the value of sub_total
     *
     * @return  self
     */
    public function setSub_total($sub_total)
    {
        $this->sub_total = $sub_total;

        return $this;
    }

    /**
     * Get the value of descuento
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set the value of descuento
     *
     * @return  self
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get the value of subtotal_con_dscto
     */
    public function getSubtotal_con_dscto()
    {
        return $this->subtotal_con_dscto;
    }

    /**
     * Set the value of subtotal_con_dscto
     *
     * @return  self
     */
    public function setSubtotal_con_dscto($subtotal_con_dscto)
    {
        $this->subtotal_con_dscto = $subtotal_con_dscto;

        return $this;
    }

    /**
     * Get the value of igv
     */
    public function getIgv()
    {
        return $this->igv;
    }

    /**
     * Set the value of igv
     *
     * @return  self
     */
    public function setIgv($igv)
    {
        $this->igv = $igv;

        return $this;
    }

    /**
     * Get the value of factor_icbper
     */
    public function getFactor_icbper()
    {
        return $this->factor_icbper;
    }

    /**
     * Set the value of factor_icbper
     *
     * @return  self
     */
    public function setFactor_icbper($factor_icbper)
    {
        $this->factor_icbper = $factor_icbper;

        return $this;
    }

    /**
     * Get the value of icbper
     */
    public function getIcbper()
    {
        return $this->icbper;
    }

    /**
     * Set the value of icbper
     *
     * @return  self
     */
    public function setIcbper($icbper)
    {
        $this->icbper = $icbper;

        return $this;
    }

    /**
     * Get the value of op_exoneradas
     */
    public function getOp_exoneradas()
    {
        return $this->op_exoneradas;
    }

    /**
     * Set the value of op_exoneradas
     *
     * @return  self
     */
    public function setOp_exoneradas($op_exoneradas)
    {
        $this->op_exoneradas = $op_exoneradas;

        return $this;
    }

    /**
     * Get the value of op_inafectas
     */
    public function getOp_inafectas()
    {
        return $this->op_inafectas;
    }

    /**
     * Set the value of op_inafectas
     *
     * @return  self
     */
    public function setOp_inafectas($op_inafectas)
    {
        $this->op_inafectas = $op_inafectas;

        return $this;
    }

    /**
     * Get the value of total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set the value of total
     *
     * @return  self
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get the value of igv_porcentaje
     */
    public function getIgv_porcentaje()
    {
        return $this->igv_porcentaje;
    }

    /**
     * Set the value of igv_porcentaje
     *
     * @return  self
     */
    public function setIgv_porcentaje($igv_porcentaje)
    {
        $this->igv_porcentaje = $igv_porcentaje;

        return $this;
    }
}
