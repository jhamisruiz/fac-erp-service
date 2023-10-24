<?php

namespace App\Utils\Service;

use DB\Conexion\Conexion;
use App\Utils\Service\NewError;
use Exception;

class NewSql
{
    private $type;

    /**
     * @param string $type
     * @return any|any[]|error
     */
    function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * @param function exec(func,store_proceduer)
     * @param bool $sp
     * @return $stmt
     */
    function Exec($function, $sp = false)
    {

        // Abrimos la conexión
        $stmt = Conexion::conectar();

        // Iniciamos la transacción

        $stmt->beginTransaction();
        try {
            // Ejecutamos la función que contiene la consulta preparada
            $result = $function($stmt);

            // Confirmamos la transacción
            if (!$sp) {
                $stmt->commit();
            };

            // Cerramos la conexión
            Conexion::close($stmt);
            // Retornamos el resultado
            return $result;
        } catch (Exception $ex) {
            // Si hay algún error, deshacemos la transacción
            $stmt->rollBack();

            // Cerramos la conexión
            Conexion::close($stmt);

            // Lanzamos la excepción
            //NewError::__Log($ex->getMessage());
            error_log("[MYSQL]------->: {$ex->getMessage()}");
            if ($ex->getCode() == '23000') {
                return NewError::__Log('El valor que intenta ingresar ya existe.', 202);
            }

            return NewError::__Log($ex->getMessage(), 202);
            //return ErrorCode::getCode($ex->getMessage(), 500);
        }

        //validar con base de datos
        if (1) {
        } else {
            echo json_encode("Tipo de DB no soportado");
        }
    }

    /**
     *@param array $params = ["name1","name2"]; 

     * @return string
     **/
    public function like_sql_to_string($params, $search)
    {
        $res = '';
        foreach ($params as $key => $value) {
            $key = $key + 1;
            $or = ($key < count($params)) ? ' OR ' : '';
            $res .=  $value . " LIKE '%$search%'" . $or;
        }
        return $res;
    }
}
