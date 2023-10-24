<?php

namespace App\config\Ubigeo\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class UbigeoPersistence
{
    public static function Departamento()
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) {

            $query = "SELECT `id_ubigeo`,
            `Departamento` 
            FROM ubigeo WHERE SUBSTRING(`id_ubigeo`,3,4)='0000' 
            ORDER BY `Departamento` ASC";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Provincia($prov)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($prov) {

            $query = "SELECT 
             id_ubigeo,
             Provincia
             FROM ubigeo 
             WHERE SUBSTRING(`id_ubigeo`,5,2)='00' AND
             SUBSTRING(`id_ubigeo`,3,4) <>'0000' AND
             SUBSTRING(`id_ubigeo`,1,2) =SUBSTRING('$prov',1,2)
             ORDER BY `Provincia` ASC";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }
    public static function Distrito($dist)
    {
        // Ejemplo de uso
        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($dist) {

            $query = "SELECT 
             id_ubigeo,
             Distrito
             FROM ubigeo 
             WHERE SUBSTRING(`id_ubigeo`,5,2) <>'00' AND
             SUBSTRING(`id_ubigeo`,1,4) = SUBSTRING('$dist',1,4)
             ORDER BY `Distrito` ASC";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }
}
