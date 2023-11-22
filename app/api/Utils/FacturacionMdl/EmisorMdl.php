<?php

namespace App\Utils\FacturacionMdl;

class EmisorMdl
{
    private $id_empresa;
    private $id_sucursal;
    private $tipo_documento;
    private $numero_documento;
    private $razon_social;
    private $nombre_comercial;
    private $departamento;
    private $provincia;
    private $distrito;
    private $direccion;
    private $ubigeo;
    private $usuario_emisor;
    private $clave_emisor;
    private $clave_certificado;

    public function __construct($data)
    {

        $this->setId_empresa($data['id_empresa']);
        $this->setId_sucursal($data['id_sucursal']);
        $this->setTipo_documento($data['tipo_documento']);
        $this->setNumero_documento($data['numero_documento']);
        $this->setRazon_social($data['razon_social']);
        $this->setNombre_comercial($data['nombre_comercial']);
        $this->setDepartamento($data['departamento']);
        $this->setProvincia($data['provincia']);
        $this->setDistrito($data['distrito']);
        $this->setDireccion($data['direccion']);
        $this->setUbigeo($data['ubigeo']);
        $this->setUsuario_emisor($data['usuario_emisor']);
        $this->setClave_emisor($data['clave_emisor']);
        $this->setClave_certificado($data['clave_certificado']);
    }

    /**
     * Get the value of id_empresa
     */
    public function getId_empresa()
    {
        return $this->id_empresa;
    }

    /**
     * Set the value of id_empresa
     *
     * @return  self
     */
    public function setId_empresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;

        return $this;
    }

    /**
     * Get the value of id_sucursal
     */
    public function getId_sucursal()
    {
        return $this->id_sucursal;
    }

    /**
     * Set the value of id_sucursal
     *
     * @return  self
     */
    public function setId_sucursal($id_sucursal)
    {
        $this->id_sucursal = $id_sucursal;

        return $this;
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
     * Get the value of nombre_comercial
     */
    public function getNombre_comercial()
    {
        return $this->nombre_comercial;
    }

    /**
     * Set the value of nombre_comercial
     *
     * @return  self
     */
    public function setNombre_comercial($nombre_comercial)
    {
        $this->nombre_comercial = $nombre_comercial;

        return $this;
    }

    /**
     * Get the value of departamento
     */
    public function getDepartamento()
    {
        return $this->departamento;
    }

    /**
     * Set the value of departamento
     *
     * @return  self
     */
    public function setDepartamento($departamento)
    {
        $this->departamento = $departamento;

        return $this;
    }

    /**
     * Get the value of provincia
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * Set the value of provincia
     *
     * @return  self
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * Get the value of distrito
     */
    public function getDistrito()
    {
        return $this->distrito;
    }

    /**
     * Set the value of distrito
     *
     * @return  self
     */
    public function setDistrito($distrito)
    {
        $this->distrito = $distrito;

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

    /**
     * Get the value of ubigeo
     */
    public function getUbigeo()
    {
        return $this->ubigeo;
    }

    /**
     * Set the value of ubigeo
     *
     * @return  self
     */
    public function setUbigeo($ubigeo)
    {
        $this->ubigeo = $ubigeo;

        return $this;
    }

    /**
     * Get the value of usuario_emisor
     */
    public function getUsuario_emisor()
    {
        return $this->usuario_emisor;
    }

    /**
     * Set the value of usuario_emisor
     *
     * @return  self
     */
    public function setUsuario_emisor($usuario_emisor)
    {
        $this->usuario_emisor = $usuario_emisor;

        return $this;
    }

    /**
     * Get the value of clave_emisor
     */
    public function getClave_emisor()
    {
        return $this->clave_emisor;
    }

    /**
     * Set the value of clave_emisor
     *
     * @return  self
     */
    public function setClave_emisor($clave_emisor)
    {
        $this->clave_emisor = $clave_emisor;

        return $this;
    }

    /**
     * Get the value of clave_certificado
     */
    public function getClave_certificado()
    {
        return $this->clave_certificado;
    }

    /**
     * Set the value of clave_certificado
     *
     * @return  self
     */
    public function setClave_certificado($clave_certificado)
    {
        $this->clave_certificado = $clave_certificado;

        return $this;
    }
}
