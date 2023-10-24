<?php

namespace App\config\Menu\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class MenuPersistence
{
    public static function Listar()
    {
        // Ejemplo de uso

        $sql = new NewSql();

        return $sql->Exec(
            function ($con) {

                $user_id = '1';
                $stmt = $con->prepare("CALL SP_MENU(:user_id);");
                $stmt->bindParam(':user_id', $user_id);

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            true
        );
    }
}
