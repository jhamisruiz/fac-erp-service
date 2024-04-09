<?php

namespace Mnt\mantenedores\Producto\Domain\Repository;

use Mnt\mantenedores\Producto\Domain\Response\ProductoResponse;
use Mnt\mantenedores\Producto\Persistence\ProductoPersistence;
use App\Utils\Utils;
use App\Utils\Sunat\Unspsc;

class ProductoRepository
{
    private $request;
    private $response;
    private $service;

    public function __construct($request = null, $response = null, $service = null)
    {
        $this->request = $request;
        $this->response = $response;
        $this->service = $service;
    }

    public function Buscar($start, $length, $search, $order, $user)
    {
        $data = ProductoPersistence::Buscar($start, $length, $search, $order, $user);

        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Listar($start, $length, $search, $order, $user)
    {
        $data = ProductoPersistence::Listar($start, $length, $search, $order, $user);

        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }

    public function Crear($body)
    {
        // validators
        $res = ProductoPersistence::Crear($body);

        //$rs = new ProductoResponse($this->service);

        return $res;
    }

    public function BuscarPorId($id)
    {
        $res = ProductoPersistence::BuscarPorId($id);

        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($res);

        return  $data[0];
    }

    public function Actualizar($id, $body)
    {
        // validators
        $data = ProductoPersistence::Actualizar($id, $body);

        //$rs = new ProductoResponse($this->service);
        $res = Utils::responseParamsUpdate($data, $id);
        return  $res;
    }

    public function Eliminar($id)
    {
        $res = ProductoPersistence::Eliminar($id);

        return  $res;
    }

    public function HabilitarDeshabilitar($id, $status)
    {
        return ProductoPersistence::HabilitarDeshabilitar($id, $status);
    }

    public function Codigo($codigo)
    {
        $res = ProductoPersistence::Codigo($codigo);

        if ($res === 1) {
            return true;
        }

        return false;
    }

    public function Segmentos()
    {
        $data = Unspsc::getUnspsc();
        if ($data) {
            // Filtrar los elementos que cumplen con las condiciones
            $segmentos = array_filter($data, function ($item) {
                return (substr($item->codigo, -6) === '000000');
            });
            // Convertir el resultado nuevamente a JSON si es necesario
            return array_values($segmentos);
        }
        return null;
    }

    public function Familias($codigo)
    {
        $data = Unspsc::getUnspsc();
        if ($data) {
            $f = substr($codigo, 0, 2);
            // Filtrar los elementos que cumplen con las condiciones
            $familias = array_filter($data, function ($item) use ($f, $codigo) {
                return (substr($item->codigo, 0, 2) === $f) &&
                    (substr($item->codigo, -4) === '0000') &&
                    (intval($item->codigo) > intval($codigo));
            });
            // Convertir el resultado nuevamente a JSON si es necesario
            return array_values($familias);
        }
        return null;
    }

    public function Clases($codigo)
    {
        $data = Unspsc::getUnspsc();
        if ($data) {
            $f = substr($codigo, 0, 4);
            // Filtrar los elementos que cumplen con las condiciones
            $clases = array_filter($data, function ($item) use ($f, $codigo) {
                return (substr($item->codigo, 0, 4) === $f) &&
                    (substr($item->codigo, -2) === '00') &&
                    (intval($item->codigo) > intval($codigo));
            });
            // Convertir el resultado nuevamente a JSON si es necesario
            return array_values($clases);
        }
        return null;
    }

    public function Productos($codigo, $desc)
    {
        $data = Unspsc::getUnspsc();
        if ($data) {

            if ($codigo) {
                $ff = substr($codigo, -2);
                if ($ff === '00') {
                    $f = substr($codigo, 0, 6);
                    $productos = array_filter($data, function ($item) use ($f, $codigo) {
                        return (substr($item->codigo, 0, 6) === $f) &&
                            (intval($item->codigo) > intval($codigo));
                    });
                } else {
                    $productos = array_filter($data, function ($item) use ($codigo) {
                        return ($item->codigo === $codigo);
                    });
                }
                // Convertir el resultado nuevamente a JSON si es necesario
                return array_values($productos);
            }
            if ($desc) {
                $productos = array_filter($data, function ($item) use ($desc) {
                    // return ($item->descripcion === $desc);
                    return stripos($item->descripcion, $desc) !== false &&
                        (substr($item->codigo, -2) !== '00');
                });
                // Convertir el resultado nuevamente a JSON si es necesario
                return array_values(array_slice($productos, 0, 40));
            }
        }
        return null;
    }

    public function BuscarUMedida($start, $length, $search, $order)
    {
        $data = ProductoPersistence::BuscarUMedida($start, $length, $search, $order);

        $rs = new ProductoResponse($this->service);
        $data = $rs->ListaResponse($data);

        return  $data;
    }
}
