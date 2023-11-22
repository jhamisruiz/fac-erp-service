<?php

namespace App\config\Menu\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class MenuPersistence
{
    public static function Listar($id)
    {
        // Ejemplo de uso

        $sql = new NewSql();

        return $sql->Exec(
            function ($con) use ($id) {

                $user_id = $id;
                $stmt = $con->prepare("CALL SP_MENU(:user_id);");
                $stmt->bindParam(':user_id', $user_id);

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        );
    }
}
