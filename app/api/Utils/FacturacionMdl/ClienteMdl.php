<?php

namespace App\Utils\FacturacionMdl;

class ClienteMdl
{
    private $tipo_documento;
    private $numero_documento;
    private $razon_social;
    private $direccion;
    public function __construct($data)
    {
        $this->setTipo_documento($data['tipo_documento']);
        $this->setNumero_documento($data['numero_documento']);
        $this->setRazon_social($data['razon_social']);
        $this->setDireccion($data['direccion']);
    }

    /**
     * Get the value of tipo_documento
     */
    public function getTipo_documento()
    {
        return $this->tipo_documento;
    }

    /**
     * Set the value of tipo_documento
     *
     * @return  self
     */
    public function setTipo_documento($tipo_documento)
    {
        $this->tipo_documento = $tipo_documento;

        return $this;
    }

    /**
     * Get the value of numero_documento
     */
    public function getNumero_documento()
    {
        return $this->numero_documento;
    }

    /**
     * Set the value of numero_documento
     *
     * @return  self
     */
    public function setNumero_documento($numero_documento)
    {
        $this->numero_documento = $numero_documento;

        return $this;
    }

    /**
     * Get the value of razon_social
     */
    public function getRazon_social()
    {
        return $this->razon_social;
    }

    /**
     * Set the value of razon_social
     *
     * @return  self
     */
    public function setRazon_social($razon_social)
    {
        $this->razon_social = $razon_social;

        return $this;
    }

    /**
     * Get the value of direccion
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set the value of direccion
     *
     * @return  self
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }
}
