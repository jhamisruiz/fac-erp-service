<?php

namespace Mnt\mantenedores\Rol\Persistence;

use App\Utils\Service\NewSql;
use PDO;

class RolPersistence
{
    public static function Buscar($start, $length, $search, $order)
    {

        $sql = new NewSql();
        $params = ['id', 'codigo', 'nombre']; #columnas por las que se realizara la busqueda
        $search = $sql->like_sql_to_string($params, $search);
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $table = 'rol';
            $columns = 'id,codigo,nombre,habilitado';

            $stmt = $con->prepare("CALL SP_SELECT_ALL(:start,:length,\"$search\",'$table','$columns','$order')");
            $stmt->bindParam("start", $start, PDO::PARAM_INT);
            $stmt->bindParam("length", $length, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Listar($start, $length, $search, $order)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($start, $length, $search, $order) {
            $query = "SELECT * FROM rol";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Crear($data)
    {

        $sql = new NewSql();
        return $sql->Exec(
            function ($con) use ($data) {
                $json = json_encode($data);

                $sql = "CALL SP_ROL_I(:json)";
                $stmt = $con->prepare($sql);
                $stmt->bindParam(':json', $json, PDO::PARAM_STR);

                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            false
        );
    }

    public static function BuscarPorId($id)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT
                    r.id,
                    r.codigo,
                    r.nombre,
                    r.habilitado
            FROM rol r
            WHERE r.id =$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function BuscarDetallePorId($id)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "SELECT m.*,
                            IFNULL(
                                (
                                    SELECT 
                                    CONCAT('[',GROUP_CONCAT(
                                        JSON_OBJECT(
                                            'id', d.id,
                                            'nombre', d.nombre,
                                            'url', d.url,
                                            'icon', d.icon,
                                            'id_menu', d.id_menu,
                                            'orden', d.orden,
                                            'style', d.style,
                                            'id_rol', CASE WHEN $id = 0 THEN null ELSE (select rp.id_rol from rol_permiso rp where rp.id_submenu=d.id AND rp.id_rol=$id) END,
                                            'idrol_permiso', CASE WHEN $id = 0 THEN null ELSE (select rp.id from rol_permiso rp where rp.id_submenu=d.id AND rp.id_rol=$id) END,
                                            'user_create',CASE WHEN $id = 0 THEN false ELSE COALESCE((select CASE WHEN rp.user_create = 1 THEN TRUE ELSE FALSE END from rol_permiso rp where rp.id_submenu=d.id AND rp.id_rol=$id),false) END,
                                            'user_read', CASE WHEN $id = 0 THEN false ELSE COALESCE((select CASE WHEN rp.user_read = 1 THEN TRUE ELSE FALSE END from rol_permiso rp where rp.id_submenu=d.id AND rp.id_rol=$id),false) END,
                                            'user_update', CASE WHEN $id = 0 THEN false ELSE COALESCE((select CASE WHEN rp.user_update = 1 THEN TRUE ELSE FALSE END from rol_permiso rp where rp.id_submenu=d.id AND rp.id_rol=$id),false) END,
                                            'user_delete', CASE WHEN $id = 0 THEN false ELSE COALESCE((select CASE WHEN rp.user_delete = 1 THEN TRUE ELSE FALSE END from rol_permiso rp where rp.id_submenu=d.id AND rp.id_rol=$id),false) END
                                        )order by d.orden asc SEPARATOR ','
                                    ), ']')
                                FROM menu_submenu d
                                WHERE d.id_menu=m.id
                                ), NULL
                            ) AS componentes
                        FROM menu m order by m.orden asc";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        });
    }

    public static function Actualizar($id, $data)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $data) {

            $json = json_encode($data);
            $sql = "CALL SP_ROL_U(:json,:id)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':json', $json, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return 1;
            }
            return $stmt->execute();
        });
    }

    public static function Eliminar($id)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id) {

            $query = "DELETE FROM rol WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function HabilitarDeshabilitar($id, $status)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($id, $status) {

            $query = "UPDATE rol SET habilitado =$status WHERE id=$id";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }

    public static function Codigo($codigo)
    {

        $sql = new NewSql();
        return $sql->Exec(function ($con) use ($codigo) {

            $query = "SELECT * FROM rol WHERE codigo='$codigo'";
            $stmt = $con->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        });
    }
}


/*

$query = "SELECT m.*,
                            IFNULL(
                                (
                                    SELECT 
                                    CONCAT('[',GROUP_CONCAT(
                                        JSON_OBJECT(
                                            'id', d.id,
                                            'nombre', d.nombre,
                                            'url', d.url,
                                            'icon', d.icon,
                                            'id_menu', d.id_menu,
                                            'orden', d.orden,
                                            'style', d.style,
                                            'id_rol', rp.id_rol,
                                            'idrol_permiso', rp.id,
                                            'user_create', CASE WHEN rp.user_create = 1 THEN TRUE ELSE FALSE END,
                                            'user_read', CASE WHEN rp.user_read = 1 THEN TRUE ELSE FALSE END,
                                            'user_update', CASE WHEN rp.user_update = 1 THEN TRUE ELSE FALSE END,
                                            'user_delete', CASE WHEN rp.user_delete = 1 THEN TRUE ELSE FALSE END
                                        )order by d.orden asc SEPARATOR ','
                                    ), ']')
                                FROM menu_submenu d
                                left JOIN rol_permiso rp ON rp.id_submenu = d.id
                                WHERE d.id_menu=m.id AND rp.id_rol = $id
                                ), NULL
                            ) AS componentes
                        FROM menu m order by m.orden asc";
                        */