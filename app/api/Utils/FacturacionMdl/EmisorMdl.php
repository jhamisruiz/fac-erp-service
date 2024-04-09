<?php

namespace App\Utils\FacturacionMdl;

class EmisorMdl
{
    private $emisor;
    public function __construct($data)
    {

        $this->setEmisor((object)$data);
    }

    public function setdata($field, $value)
    {
        $this->emisor = $value;

        return $this;
    }
    /**
     * Get the value of emisor
     */
    public function getEmisor()
    {
        return $this->emisor;
    }

    /**
     * Set the value of emisor
     *
     * @return  self
     */
    public function setEmisor($emisor)
    {
        $this->emisor = $emisor;

        return $this;
    }
}
