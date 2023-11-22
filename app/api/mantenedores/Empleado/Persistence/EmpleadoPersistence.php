<?php

namespace Mnt\mantenedores\Empleado\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class EmpleadoPersistence
{
    public static function Listar($start, $length, $search, $order)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $query = "SELECT * FROM empleado";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Crear($body)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($body) {

            $query = "INSERT INTO empleado 
            (dni_ruc,tipo_documento,nombres,apellido_paterno,apellido_materno,nombre_completo,fecha_creacion,habilitado,es_planilla,telefono,direccion,idgrupo_empleado,descripcion)
            VALUES(:dni_ruc,:tipo_documento,:nombres,:apellido_paterno,:apellido_materno,:nombre_completo,:fecha_creacion,:habilitado,:es_planilla,:telefono,:direccion,:idgrupo_empleado,:descripcion)";

            $stmt = $con->prepare($query);

            $stmt->bindParam(":dni_ruc", $body["dni_ruc"], PDO::PARAM_STR);
            $stmt->bindParam(":tipo_documento", $body["tipo_documento"], PDO::PARAM_INT);
            $stmt->bindParam(":nombres", $body["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellido_paterno", $body["apellido_paterno"], PDO::PARAM_STR);
            $stmt->bindParam(":apellido_materno", $body["apellido_materno"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_completo", $body["nombre_completo"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion", $body["fecha_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":habilitado", $body["habilitado"], PDO::PARAM_INT); // Assuming it's a numeric field (boolean or flag)
            $stmt->bindParam(":es_planilla", $body["es_planilla"], PDO::PARAM_INT);
            $stmt->bindParam(":telefono", $body["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $body["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":idgrupo_empleado", $body["idgrupo_empleado"], PDO::PARAM_INT);
            $stmt->bindParam(":descripcion", $body["descripcion"], PDO::PARAM_STR);

            $stmt->execute();
            return $con->lastInsertId();
        });
    }

    public static function BuscarPorId($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT * FROM empleado WHERE id=$id";
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

            $query = "UPDATE empleado SET
            nombres = :nombres,
            apellido_paterno = :apellido_paterno,
            apellido_materno = :apellido_materno,
            nombre_completo = :nombre_completo,
            fecha_creacion = :fecha_creacion,
            habilitado = :habilitado,
            es_planilla=:es_planilla,
            telefono = :telefono,
            direccion = :direccion,
            idgrupo_empleado = :idgrupo_empleado,
            descripcion = :descripcion
            WHERE id = :id"; // Reemplaza "id" con el nombre del campo que identifica al empleado

            $stmt = $con->prepare($query);

            $stmt->bindParam(":nombres", $body["nombres"], PDO::PARAM_STR);
            $stmt->bindParam(":apellido_paterno", $body["apellido_paterno"], PDO::PARAM_STR);
            $stmt->bindParam(":apellido_materno", $body["apellido_materno"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_completo", $body["nombre_completo"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha_creacion", $body["fecha_creacion"], PDO::PARAM_STR);
            $stmt->bindParam(":habilitado", $body["habilitado"], PDO::PARAM_INT); // Suponiendo que es un campo numérico (booleano o bandera)
            $stmt->bindParam(":es_planilla", $body["es_planilla"], PDO::PARAM_INT);
            $stmt->bindParam(":telefono", $body["telefono"], PDO::PARAM_STR);
            $stmt->bindParam(":direccion", $body["direccion"], PDO::PARAM_STR);
            $stmt->bindParam(":idgrupo_empleado", $body["idgrupo_empleado"], PDO::PARAM_INT);
            $stmt->bindParam(":descripcion", $body["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT); // Agrega el valor del campo "id" que identifica al empleado

            $stmt->execute();
            return $stmt->rowCount(); // Otra opción: return true si quieres indicar éxito en la actualización
        });
    }

    public static function Eliminar($id)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM empleado WHERE id=$id";
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

            $query = "UPDATE empleado SET habilitado =$status WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}
