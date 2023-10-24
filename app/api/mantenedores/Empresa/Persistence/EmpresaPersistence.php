<?php

namespace Mnt\mantenedores\Empresa\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class EmpresaPersistence
{
    public static function Buscar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        $params = ['numero_documento', 'razon_social', 'nombre_comercial']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(
            function ($con) use ($start, $length, $search, $order) {
                $table = 'empresa';
                $columns = 'id,tipo_documento,numero_documento,razon_social,nombre_comercial,departamento,provincia,distrito,direccion,
                        ubigeo,usuario_emisor,clave_emisor,fecha_creacion,habilitado,certificado,clave_certificado';

                $stmt = $con->prepare("CALL SP_SELECT_ALL(:start,:length,\"$search\",'$table','$columns','$order')");
                $stmt->bindParam("start", $start, PDO::PARAM_INT);
                $stmt->bindParam("length", $length, PDO::PARAM_INT);

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            true
        );
    }

    public static function Listar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(
            function ($con) use ($start, $length, $search, $order) {
                $query = "SELECT * FROM empresa";
                $stmt = $con->prepare($query);

                $stmt->execute();


                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            true
        );
    }

    public static function Crear($body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($body) {

            $cert = null;
            if (isset($body["certificado"])) {
                $data = $body["certificado"];
                $cert = isset($data['nombre']) ? $data['nombre'] : null;
            }

            $query = "INSERT INTO empresa 
            (tipo_documento,
            numero_documento,
            razon_social,
            nombre_comercial,
            departamento,
            provincia,
            distrito,
            direccion,
            ubigeo,
            usuario_emisor,
            clave_emisor,
            fecha_creacion,
            habilitado,
            certificado,
            clave_certificado
            )
            VALUES (:tipo_documento,:numero_documento,:razon_social,:nombre_comercial,:departamento,:provincia,:distrito,
            :direccion,:ubigeo,:usuario_emisor,:clave_emisor,:fecha_creacion,:habilitado,:certificado,:clave_certificado)";
            $stmt = $con->prepare($query);

            $stmt->bindParam(":tipo_documento", $body["tipo_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_documento", $body["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":razon_social", $body["razon_social"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre_comercial", $body["nombre_comercial"], PDO::PARAM_STR);
            $stmt->bindParam(":departamento", $body["departamento"], PDO::PARAM_STR);
            $stmt->bindParam(":provincia", $body["provincia"], PDO::PARAM_STR);
            $stmt->bindParam(":distrito", $body["distrito"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $body["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ubigeo", $body["ubigeo"], PDO::PARAM_STR);
            $stmt->bindParam(":usuario_emisor", $body["usuario_emisor"], PDO::PARAM_STR);
            $stmt->bindParam(":clave_emisor", $body["clave_emisor"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion", $body["fecha_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":habilitado", $body["habilitado"], PDO::PARAM_STR);
            $stmt->bindParam(":certificado", $cert, PDO::PARAM_STR);
            $stmt->bindParam(":clave_certificado", $body["clave_certificado"], PDO::PARAM_STR);

            $stmt->execute();
            return $con->lastInsertId();
        });
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT * FROM empresa WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Actualizar($id, $body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $body) {
            $cert = null;
            if (isset($body["certificado"])) {
                $data = $body["certificado"];
                $cert = isset($data['nombre']) ? $data['nombre'] : null;
            }

            $query = "UPDATE empresa SET 
            tipo_documento=:tipo_documento,
            numero_documento=:numero_documento,
            razon_social=:razon_social,
            nombre_comercial=:nombre_comercial,
            departamento=:departamento,
            provincia=:provincia,
            distrito=:distrito,
            direccion=:direccion,
            ubigeo=:ubigeo,
            usuario_emisor=:usuario_emisor,
            clave_emisor=:clave_emisor,
            fecha_creacion=:fecha_creacion,
            habilitado=:habilitado,
            certificado=:certificado,
            clave_certificado=:clave_certificado
            WHERE id=:id";
            $stmt = $con->prepare($query);

            $stmt->bindParam(":tipo_documento", $body["tipo_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":numero_documento", $body["numero_documento"], PDO::PARAM_STR);
            $stmt->bindParam(":razon_social", $body["razon_social"], PDO::PARAM_INT);
            $stmt->bindParam(":nombre_comercial", $body["nombre_comercial"], PDO::PARAM_STR);
            $stmt->bindParam(":departamento", $body["departamento"], PDO::PARAM_STR);
            $stmt->bindParam(":provincia", $body["provincia"], PDO::PARAM_STR);
            $stmt->bindParam(":distrito", $body["distrito"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $body["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":ubigeo", $body["ubigeo"], PDO::PARAM_STR);
            $stmt->bindParam(":usuario_emisor", $body["usuario_emisor"], PDO::PARAM_STR);
            $stmt->bindParam(":clave_emisor", $body["clave_emisor"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion", $body["fecha_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":habilitado", $body["habilitado"], PDO::PARAM_STR);
            $stmt->bindParam(":certificado", $cert, PDO::PARAM_STR);
            $stmt->bindParam(":clave_certificado", $body["clave_certificado"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Eliminar($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM empresa WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function HabilitarDeshabilitar($id, $status)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $status) {

            $query = "UPDATE empresa SET habilitado =$status WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Codigo($codigo)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($codigo) {

            $query = "SELECT * FROM empresa WHERE numero_documento=$codigo";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
