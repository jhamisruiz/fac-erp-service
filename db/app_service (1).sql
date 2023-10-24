-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2023 a las 14:24:03
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `app_service`
--
CREATE DATABASE IF NOT EXISTS `app_service` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `app_service`;

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `EliminarRegistro` (IN `json` JSON, IN `ids` INT)   BEGIN
    -- Verificar si el registro existe antes de eliminarlo
    DELETE FROM sucursal_documento WHERE id = 100;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_MENU` (IN `user_id` INT)   BEGIN
    -- Crea una tabla temporal para almacenar los resultados intermedios
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_menu_json (
        id INT,
        nombre VARCHAR(255),
        url VARCHAR(255),
        habilitado BIT,
        componente TEXT,
        PRIMARY KEY (id)
    );

    -- Obtiene los datos del menú y los inserta en la tabla temporal
    INSERT INTO temp_menu_json
    SELECT m.id, m.nombre, m.url, m.habilitado, NULL
    FROM menu m;

    -- Actualiza la columna 'componente' con los datos de los componentes y submenús
    UPDATE temp_menu_json AS t
    SET t.componente = (
        SELECT CONCAT(
            '[',
            GROUP_CONCAT(
                CONCAT(
                    '{',
                    '"id": ', c.id, ',',
                    '"nombre": "', c.nombre, '",',
                    '"url": "', c.url, '",',
                    '"habilitado": ', IF(c.habilitado = 1, 'true', 'false'), ',',
                    '"submenu": [',
                    IFNULL(
                        (
                            SELECT GROUP_CONCAT(
                                CONCAT(
                                    '{',
                                    '"id": ', s.id, ',',
                                    '"nombre": "', s.nombre, '",',
                                    '"url": "', s.url, '",',
                                    '"habilitado": ', IF(s.habilitado = 1, 'true', 'false'),
                                    '}'
                                )
                                SEPARATOR ','
                            )
                            FROM menu_submenu s
                            WHERE s.id_componente = c.id
                        ),
                        ''
                    ),
                    ']',
                    '}'
                )
            ),
            ']'
        )
        FROM menu_componente c
        WHERE c.id_menu = t.id
    );

    -- Construye el JSON final con el formato deseado
    SELECT *
    FROM temp_menu_json AS t;

    -- Limpia la tabla temporal
    DROP TABLE IF EXISTS temp_menu_json;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_SELECT_ALL` (IN `start` INT(11), IN `length` INT(11), IN `search` VARCHAR(2000), IN `table_name` VARCHAR(100), IN `params` VARCHAR(2000), IN `orderby` VARCHAR(15))   BEGIN
	SET @table_name = table_name;
 	SET @start = start;
    IF (@start = '' || @start=null) THEN
        SET @start = 0;
    END IF;
    IF (@start >0) THEN
        SET @start = @start-1;
    END IF;
    
    SET @length = length;
    IF (@length = '' || @length=null) THEN
    	set @count= CONCAT('select COUNT(*) into @outvar from ',@table_name);
    	PREPARE countquery from @count;
        execute countquery;
        SET @length = @outvar;
    END IF;
    SET @search = search;
    SET @order = orderby;
    IF (@order = '' || @order=null) THEN
        SET @order = 'ASC';
    END IF;
    SET @params = params;
    SET @sql_query = CONCAT('create temporary table temp as SELECT ',@params,' FROM ',
                            @table_name,
                            
                            '  WHERE habilitado=1 LIMIT ', @start,',', @length,
                            ';');
    PREPARE stmt1 FROM @sql_query;
    EXECUTE stmt1;
    SET @select = CONCAT('select * FROM temp ', ' WHERE ', @search,' ORDER BY id ', @order,';');
    PREPARE stmt2 FROM @select;
    EXECUTE stmt2;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_SUCURSAL_I` (IN `json` JSON)   BEGIN
SET @json = json;

INSERT INTO sucursal (id_empresa,nombre, codigo, ubigeo, direccion, telefono, email, fecha_creacion, habilitado)

SELECT JSON_UNQUOTE(JSON_EXTRACT(@json, '$.id_empresa')) AS id_empresa, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.nombre')) AS nombre, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.codigo')) AS codigo, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.ubigeo')) AS ubigeo, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.direccion')) AS direccion, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.telefono')) AS telefono, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.email')) AS email, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.fecha_creacion')) AS fecha_creacion, 
JSON_UNQUOTE(JSON_EXTRACT(@json, '$.habilitado')) AS habilitado;
set @id=LAST_INSERT_ID();

SET @detalle_json = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.detalle'));

SET @i = 0;
WHILE @i < JSON_LENGTH(@detalle_json) DO
	INSERT INTO sucursal_documento (id_sucursal, iddocumento_electronico,serie, correlativo, fecha_creacion, habilitado)
	select  
	@id AS id_sucursal,
 	JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,  CONCAT('$[',@i,'].iddocumento_electronico'))) AS iddocumento_electronico,
  	JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,  CONCAT('$[',@i,'].serie'))) AS serie,
    JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,  CONCAT('$[',@i,'].correlativo'))) AS correlativo,
    JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,  CONCAT('$[',@i,'].fecha_creacion'))) AS fecha_creacion,
  	JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,  CONCAT('$[',@i,'].habilitado'))) AS habilitado;
	SET @i = @i + 1;
END WHILE;
select @id as id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_SUCURSAL_U` (IN `json` JSON, IN `idSucursal` INT)   BEGIN
set @json=json;
set @id=idSucursal;
    UPDATE sucursal S
    SET 
        S.id_empresa = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.id_empresa')),
        S.nombre = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.nombre')),
       	-- S.codigo = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.codigo')),
        S.ubigeo = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.ubigeo')),
        S.direccion = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.direccion')),
        S.telefono = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.telefono')),
        S.email = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.email')),
        S.fecha_creacion = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.fecha_creacion')),
        S.habilitado = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.habilitado'))
    WHERE S.id = @id;
    
    SET @detalle_json = JSON_UNQUOTE(JSON_EXTRACT(@json, '$.detalle'));
    create temporary table temp (id INT NULL,id_sucursal int null,iddocumento_electronico int,serie VARCHAR(20),correlativo VARCHAR(20),fecha_creacion VARCHAR(40),habilitado bit(1));
    
    SET @i = 0;
    WHILE @i < JSON_LENGTH(@detalle_json) DO
      -- 2. Actualizar detalles existentes si es necesario
      UPDATE sucursal_documento UD
      SET
        UD.iddocumento_electronico = JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].iddocumento_electronico'))),
        UD.serie = JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].serie'))),
        UD.correlativo = JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].correlativo')))
      WHERE UD.id = JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].id')));
      -- isert en tabla temp
      INSERT INTO temp (id_sucursal, iddocumento_electronico,serie, correlativo, fecha_creacion, habilitado,id)
      SELECT 
       @id AS id_sucursal,
       JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].iddocumento_electronico'))) AS iddocumento_electronico,
       JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].serie'))) AS serie,
       JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].correlativo'))) AS correlativo,
       JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].fecha_creacion'))) AS fecha_creacion,
       JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].habilitado'))) AS habilitado,
       JSON_UNQUOTE(JSON_EXTRACT(@detalle_json,CONCAT('$[',@i,'].id'))) as id;
      SET @i = @i + 1;
    END WHILE;
    -- Eliminar detalles existentes que no están en el JSON
     DELETE FROM sucursal_documento
    -- WHERE id NOT IN (select t.id from temp t WHERE t.id > 0 OR  t.id is not null) and id_sucursal=@id;
     WHERE id IN (SELECT ss.id FROM sucursal_documento ss WHERE  ss.id not in (select t.id from temp t WHERE t.id > 0 OR  t.id is not null) and ss.id_sucursal=@id);
     -- WHERE id =100;
     -- . Insertar nuevos detalles del JSON en "sucursal_documento"--
     INSERT INTO sucursal_documento (id_sucursal, iddocumento_electronico,serie, correlativo, fecha_creacion, habilitado)

    select @id as id_sucursal, ti.iddocumento_electronico,ti.serie, ti.correlativo, ti.fecha_creacion, ti.habilitado 
    from temp ti where ti.id=0 or ti.id is null;
    -- SELECT ss.id FROM sucursal_documento ss WHERE  ss.id not in (select t.id from temp t WHERE t.id > 0 OR  t.id is not null) and ss.id_sucursal=@id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE `almacen` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `idUbigeo` varchar(11) DEFAULT NULL,
  `habilitado` bit(1) DEFAULT b'1',
  `descripcion` varchar(255) DEFAULT NULL,
  `idSucursal` int(11) DEFAULT NULL,
  `tipo` enum('PRINCIPAL','TEMPORAL') DEFAULT 'PRINCIPAL',
  `fecha_cierre` varchar(50) DEFAULT NULL,
  `referencia` varchar(250) DEFAULT NULL,
  `ingreso` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacen`
--

INSERT INTO `almacen` (`id`, `nombre`, `direccion`, `idUbigeo`, `habilitado`, `descripcion`, `idSucursal`, `tipo`, `fecha_cierre`, `referencia`, `ingreso`) VALUES
(1, 'almacen principal', 'lima centro', '020401', b'0', 'lima centro', 1, 'PRINCIPAL', '', 'lima centro', '1'),
(2, 'Almacen temporal obra 1', 'canta', '150401', b'0', 'canta', 2, 'TEMPORAL', '2021-09-20', 'canta', '1'),
(4, 'almacen prueba 3', 'sireccion pruba mz 3', '030201', b'0', '', 1, 'PRINCIPAL', '', 'opcional ref', '1'),
(5, 'almacen temporal 3', 'sireccion requiere', '030302', b'1', '', 1, 'TEMPORAL', '2022-07-27', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asistencia`
--

CREATE TABLE `asistencia` (
  `id` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `valor` bit(1) NOT NULL DEFAULT b'1',
  `fecha_creacion` datetime NOT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `nombre` varchar(20) NOT NULL,
  `dia` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asistencia`
--

INSERT INTO `asistencia` (`id`, `id_empleado`, `valor`, `fecha_creacion`, `fecha_update`, `nombre`, `dia`, `fecha`, `hora_inicio`, `hora_fin`) VALUES
(1, 14, b'1', '2023-08-08 23:29:58', NULL, 'uno', 1, '2023-08-01', NULL, NULL),
(2, 14, b'0', '2023-08-09 00:38:36', NULL, 'dos', 2, '2023-08-02', NULL, NULL),
(3, 14, b'1', '2023-08-09 00:39:30', NULL, 'tres', 3, '2023-08-03', NULL, NULL),
(4, 12, b'0', '2023-08-09 03:04:56', '2023-08-09 11:43:59', 'ocho', 8, '2023-08-09', NULL, NULL),
(7, 12, b'1', '2023-08-09 00:00:00', NULL, 'tres', 3, '2023-08-03', NULL, NULL),
(10, 15, b'1', '0000-00-00 00:00:00', NULL, 'uno', 1, '2023-08-01', NULL, NULL),
(11, 12, b'1', '0000-00-00 00:00:00', NULL, 'uno', 1, '2023-07-01', NULL, NULL),
(12, 14, b'1', '0000-00-00 00:00:00', NULL, 'uno', 1, '2023-07-01', NULL, NULL),
(13, 15, b'1', '0000-00-00 00:00:00', NULL, 'uno', 1, '2023-07-01', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `habilitado` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `habilitado`) VALUES
(2, 'herramientas', 'herramientas de trabajoasd', b'1'),
(3, 'aglutinantes', 'productos derivados de pulverizantes', b'0'),
(12, 'metalicos', 'productos metalicos editado', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `id_documento_type` int(11) NOT NULL,
  `names` varchar(250) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `document_number` varchar(25) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `date_create` datetime NOT NULL,
  `id_ubigeo` varchar(15) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `referencia` varchar(250) DEFAULT NULL,
  `estado` int(11) NOT NULL DEFAULT 1,
  `eliminar` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `id_documento_type`, `names`, `last_name`, `document_number`, `email`, `phone`, `date_create`, `id_ubigeo`, `direccion`, `referencia`, `estado`, `eliminar`) VALUES
(1, 3, 'pruebas', 'pruebas', '33333333', 'jhamsel.rec@gmail.com', '987456321', '2022-04-08 21:56:02', '120204', 'asd asd', NULL, 1, 0),
(2, 1, 'jhamis', 'ruiz', '456311', 'jhamsel.rec@gmail.com', '928432385', '2022-04-08 22:35:46', '020505', 'mz c lt 25 asdasd', NULL, 1, 0),
(3, 1, 'prueba pedido', 'ruiz', '222222222', 'juanmonca1075@gmail.com', '44444444', '2022-04-09 04:11:30', '070101', 'mz c lt 25', '', 1, 0),
(4, 1, 'jhamis', 'ruiz', '74586321', 'admin@email.com', '987456321', '2022-09-20 02:45:27', '130103', 'asd asd', NULL, 1, 0),
(5, 1, 'prueba de nuevo', 'ruiz', '66666666', 'admin@email.com', '987456321', '2022-09-22 22:42:04', '130103', 'asd asd', NULL, 1, 0),
(6, 1, 'email', 'emilio', '56235632', 'email@email.com', '987456321', '2022-09-26 14:38:26', '010202', 'sin direccion', NULL, 1, 0),
(7, 1, 'pago', 'mercado', '45632141', 'me@email.com', '987456321', '2022-09-26 21:40:47', '150104', 'asd asd', NULL, 1, 0),
(8, 1, 'Jhono', 'Does', '25252525', 'does@email.com', '987456321', '2022-09-27 00:43:33', '150104', 'asd asd', NULL, 1, 0),
(9, 1, 'wolpic', 'wolpic', '74123658', 'wolpic@email.com', '478564563', '2022-10-10 21:37:17', '010202', 'asd', NULL, 1, 0),
(16, 1, 'asd', 'asd', '6544523', 'asd@email.com', '789789789', '2022-10-13 19:02:12', '160404', 'asd', NULL, 1, 0),
(17, 1, 'asd', 'asd', '74123685', 'jhamsel.raec@gmail.com', '98765454332', '2022-10-23 11:03:05', '160804', 'asd', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_electronico`
--

CREATE TABLE `documento_electronico` (
  `id` int(11) NOT NULL,
  `codigo` varchar(2) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documento_electronico`
--

INSERT INTO `documento_electronico` (`id`, `codigo`, `nombre`, `descripcion`, `habilitado`) VALUES
(1, '01', 'Factura Electrónica', 'Factura Electrónica', b'1'),
(2, '03', 'Boleta de venta', 'Boleta de venta', b'1'),
(3, '07', 'Nota de Crédito', 'Nota de Crédito', b'1'),
(4, '08', 'Nota de Débito', 'Nota de Débito', b'1'),
(5, '40', 'Comprobante de Percepción', 'Comprobante de Percepción', b'1'),
(6, '20', 'Comprobante de Retención', 'Comprobante de Retención', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int(11) NOT NULL,
  `dni_ruc` varchar(30) NOT NULL,
  `tipo_documento` int(10) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) NOT NULL,
  `nombre_completo` varchar(250) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1',
  `telefono` varchar(250) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `idgrupo_empleado` int(11) NOT NULL,
  `es_planilla` bit(1) NOT NULL DEFAULT b'1',
  `descripcion` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `tipo_documento` varchar(2) NOT NULL,
  `numero_documento` varchar(14) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `nombre_comercial` varchar(150) NOT NULL,
  `departamento` varchar(70) NOT NULL,
  `provincia` varchar(70) DEFAULT NULL,
  `distrito` varchar(70) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `usuario_emisor` varchar(250) DEFAULT NULL,
  `clave_emisor` varchar(250) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `habilitado` bit(1) DEFAULT b'1',
  `certificado` varchar(250) DEFAULT NULL,
  `clave_certificado` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `tipo_documento`, `numero_documento`, `razon_social`, `nombre_comercial`, `departamento`, `provincia`, `distrito`, `direccion`, `ubigeo`, `usuario_emisor`, `clave_emisor`, `fecha_creacion`, `habilitado`, `certificado`, `clave_certificado`) VALUES
(3, '6', '20607599727', 'INSTITUTO INTERNACIONAL DE SOFTWARE S.A.C.', 'ACADEMIA DE SOFTWARE', 'Lambayeque', 'Chiclayo ', 'Cayalti', 'CAL. 8 DE OCTUBRE NRO 274 INT. 1 ', '140116', 'MODDATOS', 'MODDATOS', '2023-10-18 00:00:00', b'1', 'certificado_prueba.pfx', 'prueba123'),
(7, '6', '10477325721', 'RUIZ CASTILLO JHAMIS JHOEL', '-', 'La Libertad', 'Gran ChimU ', 'Sayapullo', '-', '131104', 'a', 'a', '2023-10-21 00:00:00', b'1', NULL, '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `url` varchar(250) NOT NULL,
  `icon` varchar(250) NOT NULL,
  `submenu` int(11) DEFAULT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1',
  `orden` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `nombre`, `url`, `icon`, `submenu`, `habilitado`, `orden`) VALUES
(1, 'Dashboard', 'dashboard', 'fas fa-chart-bar', NULL, b'1', 1),
(2, 'Administracion', 'administracion', 'fas fa-landmark', NULL, b'1', 2),
(3, 'Orden de Compra', 'orden-de-compra', 'fas fa-user', NULL, b'1', 3),
(4, 'Almacen', 'almacen', 'fas fa-box', NULL, b'1', 4),
(5, 'Producción', 'produccion', 'fas fa-chart-line', NULL, b'1', 5),
(6, 'Ventas', 'ventas', 'fas fa-shopping-cart', NULL, b'1', 6),
(7, 'Prelavado', 'prelavado', 'fas fa-water', NULL, b'1', 7),
(8, 'Soplado', 'soplado', 'fas fa-cogs', NULL, b'1', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_componente`
--

CREATE TABLE `menu_componente` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `url` varchar(150) DEFAULT NULL,
  `icon` int(40) DEFAULT NULL,
  `id_menu` int(11) NOT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu_componente`
--

INSERT INTO `menu_componente` (`id`, `nombre`, `url`, `icon`, `id_menu`, `habilitado`) VALUES
(1, 'mantenedores', 'mantenedores', NULL, 4, b'1'),
(2, 'movimientos', 'movimientos', NULL, 4, b'1'),
(3, 'mantenedores', 'mantenedores', NULL, 2, b'1'),
(4, 'movimientos', 'movimientos', NULL, 2, b'1'),
(5, 'mantenedores', 'mantenedores', NULL, 3, b'1'),
(6, 'movimientos', 'movimientos', NULL, 3, b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_submenu`
--

CREATE TABLE `menu_submenu` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `url` varchar(150) NOT NULL,
  `icon` varchar(150) NOT NULL,
  `id_componente` int(11) NOT NULL DEFAULT 1,
  `habilitado` bit(1) NOT NULL DEFAULT b'1',
  `orden` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `menu_submenu`
--

INSERT INTO `menu_submenu` (`id`, `nombre`, `url`, `icon`, `id_componente`, `habilitado`, `orden`) VALUES
(1, 'Proveedores', 'proveedores', 'fas fa-chalkboard-teacher', 3, b'1', 1),
(2, 'Usuarios', 'usuarios', 'fas fa-users-cog', 3, b'1', 2),
(7, 'Configuracion', 'configuracion', 'fas fa-cogs', 4, b'1', 3),
(9, 'Crear Orden', 'nueva-orden', 'fas fa-folder-plus	', 5, b'1', 1),
(10, 'Verificar Orden', 'verificar-orden', 'far fa-calendar-check', 6, b'1', 3),
(11, 'Listar Orden', 'lista-orden', 'far fa-list-alt', 6, b'1', 2),
(12, 'Ingreso de Insumos', 'ingreso-insumos', 'fas fa-arrow-alt-circle-left', 1, b'1', 1),
(13, 'Salida de Insumos', 'salida-insumos', 'far fa-arrow-alt-circle-right', 2, b'1', 2),
(14, 'Stock Actual', 'stock', 'fas fa-box-open', 2, b'1', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(22) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `idCategoria` int(11) DEFAULT NULL,
  `idUmedida` int(11) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_update` datetime DEFAULT NULL,
  `fecha_end` date DEFAULT NULL,
  `cantidad` int(10) DEFAULT NULL,
  `condicion` varchar(250) DEFAULT NULL,
  `idAlmacen` int(11) DEFAULT NULL,
  `idInfraestructura` int(11) DEFAULT NULL,
  `id_marca` int(11) DEFAULT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `descripcion`, `idCategoria`, `idUmedida`, `fecha_ingreso`, `fecha_update`, `fecha_end`, `cantidad`, `condicion`, `idAlmacen`, `idInfraestructura`, `id_marca`, `habilitado`) VALUES
(1, 'cemento', 'cemento 40 kilos', 3, 1, '2021-09-20', NULL, '2021-09-20', 3900, 'NUEVO', 1, 1, 2, b'1'),
(2, 'palana', 'palanas', 2, 2, '2021-09-20', NULL, NULL, 66, 'NUEVO', 1, 2, 3, b'1'),
(5, 'palana', 'palanas', 2, 2, '2021-09-20', NULL, '0000-00-00', 10, 'NUEVO', 2, 10, 3, b'1'),
(6, 'cemento', 'cemento 40 kilos', 3, 1, '2021-09-20', NULL, '2021-09-20', 500, 'NUEVO', NULL, 5, 2, b'1'),
(7, 'palana', 'palanas', 2, 2, '2021-09-20', NULL, NULL, 10, 'NUEVO', NULL, 5, 3, b'1'),
(8, 'cemento', 'cemento 40 kilos', 3, 1, '2021-09-20', NULL, '2021-09-20', 300, 'NUEVO', 2, 6, 2, b'1'),
(9, 'palana', 'palanas', 2, 2, '2021-09-20', NULL, NULL, 4, 'NUEVO', 2, 6, 3, b'1'),
(10, 'cemento', 'cemento 40 kilos', 3, 1, '2021-09-20', NULL, '2021-09-20', 100, 'NUEVO', NULL, 7, 2, b'1'),
(11, 'palana', 'palanas', 2, 2, '2021-09-20', NULL, NULL, 10, 'NUEVO', 2, 8, 3, b'1'),
(14, 'nuevo producto 3', '', 12, 5, '2021-10-10', NULL, '2022-10-29', 900, 'NUEVO', 4, 11, 7, b'1'),
(15, 'prueba prod temporal', 'asd asd', 2, 6, '2021-10-10', NULL, '2022-07-12', 500, 'NUEVO', 5, 13, 7, b'1'),
(16, 'nuevo producto 3', NULL, 12, 5, '2021-10-10', NULL, '2022-10-29', 70, 'NUEVO', 4, 14, 7, b'1'),
(17, 'nuevo producto 3', NULL, 12, 5, '2021-10-10', NULL, '2022-10-29', 30, 'NUEVO', NULL, 15, 7, b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id` int(11) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `id_documento` int(11) NOT NULL,
  `numero_documento` varchar(20) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(200) NOT NULL,
  `direccion` varchar(250) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `estado` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id`, `nombre`, `id_documento`, `numero_documento`, `telefono`, `email`, `direccion`, `fecha_registro`, `estado`) VALUES
(33, 'ARTESCO', 1, '2015935789', '987654321', 'artesco@gmail.com', 'av. javier prado #4234', '2022-05-17 09:54:46', 0),
(42, 'THERMOPOL', 3, '2015935798', '9856325687', 'thermopol@gmail.com', 'AV. AMERICA 256', '2022-05-17 13:01:49', 1),
(43, 'SUAVE S.A.C', 3, '201593548', '987652356', 'suave@gmail.com', 'av, fatima 802', '2022-05-17 13:05:12', 0),
(44, 'nuevo', 1, '85858585', '987654321', 'jhamsel.raec@gmail.com', 'qe qwe', '2022-11-22 21:10:38', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `codigo` varchar(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `ubigeo` varchar(6) NOT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `telefono` varchar(60) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `habilitado` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`id`, `id_empresa`, `codigo`, `nombre`, `ubigeo`, `direccion`, `telefono`, `email`, `fecha_creacion`, `habilitado`) VALUES
(1, 3, 'xd', 'a vers', '130202', 'asa', 'aSas', 'aa@xda', '2023-10-21 00:00:00', b'1'),
(3, 3, 'a', 'dd', '030303', 'xxaaa', 'dd', 'ax', '2023-10-22 00:00:00', b'1'),
(4, 3, 'p', 'p', '010202', 'p', 'p', 'p', '2023-10-22 00:00:00', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_documento`
--

CREATE TABLE `sucursal_documento` (
  `id` int(11) NOT NULL,
  `id_sucursal` int(11) NOT NULL,
  `iddocumento_electronico` int(11) NOT NULL,
  `serie` varchar(6) NOT NULL,
  `correlativo` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `habilitado` bit(1) DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sucursal_documento`
--

INSERT INTO `sucursal_documento` (`id`, `id_sucursal`, `iddocumento_electronico`, `serie`, `correlativo`, `fecha_creacion`, `habilitado`) VALUES
(107, 1, 1, 'F1', 1, '0000-00-00 00:00:00', b'1'),
(108, 1, 2, 'B1', 1, '2023-10-22 00:00:00', b'1'),
(109, 1, 3, 'BC', 111, '2023-10-22 00:00:00', b'1'),
(111, 3, 2, 'S', 1, NULL, NULL),
(112, 4, 0, '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `time_exp` varchar(50) NOT NULL,
  `sid` varchar(250) DEFAULT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1',
  `id_usuario` int(11) NOT NULL,
  `last_sesion` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `token`
--

INSERT INTO `token` (`id`, `token`, `time_exp`, `sid`, `habilitado`, `id_usuario`, `last_sesion`, `last_logout`) VALUES
(1, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2OTc5OTY3MjgsImV4cCI6MTY5ODA4MzEyOCwiZGF0YSI6eyJpZCI6MTUsImVtYWlsIjoianJ1aXpAZW1haWwuY29tIn19.9SDP0QUAJmWORbSVTCcFqPMeoIsUdBxWNc8B5TFP6G3t6eyb3MqcjSo8AA2dfM1cLmgq7HjCWiLvBHWAXeSO7g', '1698083128', '120DF37295A64838B2E8DF280E590AA6', b'1', 15, '2023-10-22 12:45:28', '2023-09-15 14:26:09'),
(2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2NjUzNzI1MDUsImV4cCI6MTY2NTQ1ODkwNSwiZGF0YSI6eyJpZCI6IjIwIiwiZW1haWwiOiJhZG1pbnRhaWxzQGVtYWlsLmNvbSJ9fQ.Oy2DHKapsmMt_2XOdqwpGqURwXmn0jR61rddkYHZfFPSKkpWjsRRw3nvUvaw97v4kcPGvyxZkFz2c_oTXexyzQ', '1665458905', NULL, b'1', 20, NULL, NULL),
(3, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2NjEzNTYwNTEsImV4cCI6MTY2MTQ0MjQ1MSwiZGF0YSI6eyJpZCI6MywiZW1haWwiOiJlbGlhc2NvdHJpbmFAbHVja2ltb3RvcnMuY29tIn19.HCdE25DG49dIt61yUNIkl5Ryi4UJ183cRnROdzaa5uQ4jF4gmGpL9gQfreXS74E-ODjESdSmlAHZajPMVJFM_Q', '1661442451', NULL, b'1', 3, NULL, NULL),
(6, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2OTgxMjE0MjAsImV4cCI6MTY5ODIwNzgyMCwiZGF0YSI6eyJpZCI6MTQsImVtYWlsIjoia2FybGEuYWx2YXJhZG9AbHVja2ltb3RvcnMuY29tIn19.X2eVqacN5QJIVrDsmwrmrtMZhOXjEIXbtQ4mMArQiV_qcuWMQzwKoT8pVYpF8bCsgJE6Kmbsr7AKUj65V5CHUw', '1698207820', '120DF37295A64838B2E8DF280E590AA6', b'1', 14, '2023-10-23 23:23:40', NULL),
(9, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE2OTYzNzAyOTAsImV4cCI6MTY5NjQ1NjY5MCwiZGF0YSI6eyJpZCI6MSwiZW1haWwiOiJhbmdAZW1haWwuY29tIn19.0ogKZ7XCOte-hrBEtxzSlCmD4KbHi9XUDuZV3bHutG2d7451SeoRNhZIzoPOi3jb3lrOrF8Fi8swUMRDnYIk2Q', '1696456690', '120DF37295A64838B2E8DF280E590AA6', b'1', 1, '2023-10-03 16:58:10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubigeo`
--

CREATE TABLE `ubigeo` (
  `Departamento` varchar(200) DEFAULT NULL,
  `Provincia` varchar(200) DEFAULT NULL,
  `Distrito` varchar(200) DEFAULT NULL,
  `id_ubigeo` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubigeo`
--

INSERT INTO `ubigeo` (`Departamento`, `Provincia`, `Distrito`, `id_ubigeo`) VALUES
('Amazonas', '', '', '010000'),
('Amazonas', 'Chachapoyas', '', '010100'),
('Amazonas', 'Chachapoyas', 'Chachapoyas', '010101'),
('Amazonas', 'Chachapoyas', 'AsunciOn', '010102'),
('Amazonas', 'Chachapoyas', 'Balsas', '010103'),
('Amazonas', 'Chachapoyas', 'Cheto', '010104'),
('Amazonas', 'Chachapoyas', 'Chiliquin', '010105'),
('Amazonas', 'Chachapoyas', 'Chuquibamba', '010106'),
('Amazonas', 'Chachapoyas', 'Granada', '010107'),
('Amazonas', 'Chachapoyas', 'Huancas', '010108'),
('Amazonas', 'Chachapoyas', 'La Jalca', '010109'),
('Amazonas', 'Chachapoyas', 'Leimebamba', '010110'),
('Amazonas', 'Chachapoyas', 'Levanto', '010111'),
('Amazonas', 'Chachapoyas', 'Magdalena', '010112'),
('Amazonas', 'Chachapoyas', 'Mariscal Castilla', '010113'),
('Amazonas', 'Chachapoyas', 'Molinopampa', '010114'),
('Amazonas', 'Chachapoyas', 'Montevideo', '010115'),
('Amazonas', 'Chachapoyas', 'Olleros', '010116'),
('Amazonas', 'Chachapoyas', 'Quinjalca', '010117'),
('Amazonas', 'Chachapoyas', 'San Francisco de Daguas', '010118'),
('Amazonas', 'Chachapoyas', 'San Isidro de Maino', '010119'),
('Amazonas', 'Chachapoyas', 'Soloco', '010120'),
('Amazonas', 'Chachapoyas', 'Sonche', '010121'),
('Amazonas', 'Bagua', '', '010200'),
('Amazonas', 'Bagua', 'Bagua', '010201'),
('Amazonas', 'Bagua', 'Aramango', '010202'),
('Amazonas', 'Bagua', 'Copallin', '010203'),
('Amazonas', 'Bagua', 'El Parco', '010204'),
('Amazonas', 'Bagua', 'Imaza', '010205'),
('Amazonas', 'Bagua', 'La Peca', '010206'),
('Amazonas', 'BongarA', '', '010300'),
('Amazonas', 'BongarA', 'Jumbilla', '010301'),
('Amazonas', 'BongarA', 'Chisquilla', '010302'),
('Amazonas', 'BongarA', 'Churuja', '010303'),
('Amazonas', 'BongarA', 'Corosha', '010304'),
('Amazonas', 'BongarA', 'Cuispes', '010305'),
('Amazonas', 'BongarA', 'Florida', '010306'),
('Amazonas', 'BongarA', 'Jazan', '010307'),
('Amazonas', 'BongarA', 'Recta', '010308'),
('Amazonas', 'BongarA', 'San Carlos', '010309'),
('Amazonas', 'BongarA', 'Shipasbamba', '010310'),
('Amazonas', 'BongarA', 'Valera', '010311'),
('Amazonas', 'BongarA', 'Yambrasbamba', '010312'),
('Amazonas', 'Condorcanqui', '', '010400'),
('Amazonas', 'Condorcanqui', 'Nieva', '010401'),
('Amazonas', 'Condorcanqui', 'El Cenepa', '010402'),
('Amazonas', 'Condorcanqui', 'RIo Santiago', '010403'),
('Amazonas', 'Luya', '', '010500'),
('Amazonas', 'Luya', 'Lamud', '010501'),
('Amazonas', 'Luya', 'Camporredondo', '010502'),
('Amazonas', 'Luya', 'Cocabamba', '010503'),
('Amazonas', 'Luya', 'Colcamar', '010504'),
('Amazonas', 'Luya', 'Conila', '010505'),
('Amazonas', 'Luya', 'Inguilpata', '010506'),
('Amazonas', 'Luya', 'Longuita', '010507'),
('Amazonas', 'Luya', 'Lonya Chico', '010508'),
('Amazonas', 'Luya', 'Luya', '010509'),
('Amazonas', 'Luya', 'Luya Viejo', '010510'),
('Amazonas', 'Luya', 'MarIa', '010511'),
('Amazonas', 'Luya', 'Ocalli', '010512'),
('Amazonas', 'Luya', 'Ocumal', '010513'),
('Amazonas', 'Luya', 'Pisuquia', '010514'),
('Amazonas', 'Luya', 'Providencia', '010515'),
('Amazonas', 'Luya', 'San CristObal', '010516'),
('Amazonas', 'Luya', 'San Francisco de Yeso', '010517'),
('Amazonas', 'Luya', 'San JerOnimo', '010518'),
('Amazonas', 'Luya', 'San Juan de Lopecancha', '010519'),
('Amazonas', 'Luya', 'Santa Catalina', '010520'),
('Amazonas', 'Luya', 'Santo Tomas', '010521'),
('Amazonas', 'Luya', 'Tingo', '010522'),
('Amazonas', 'Luya', 'Trita', '010523'),
('Amazonas', 'RodrIguez de Mendoza', '', '010600'),
('Amazonas', 'RodrIguez de Mendoza', 'San NicolAs', '010601'),
('Amazonas', 'RodrIguez de Mendoza', 'Chirimoto', '010602'),
('Amazonas', 'RodrIguez de Mendoza', 'Cochamal', '010603'),
('Amazonas', 'RodrIguez de Mendoza', 'Huambo', '010604'),
('Amazonas', 'RodrIguez de Mendoza', 'Limabamba', '010605'),
('Amazonas', 'RodrIguez de Mendoza', 'Longar', '010606'),
('Amazonas', 'RodrIguez de Mendoza', 'Mariscal Benavides', '010607'),
('Amazonas', 'RodrIguez de Mendoza', 'Milpuc', '010608'),
('Amazonas', 'RodrIguez de Mendoza', 'Omia', '010609'),
('Amazonas', 'RodrIguez de Mendoza', 'Santa Rosa', '010610'),
('Amazonas', 'RodrIguez de Mendoza', 'Totora', '010611'),
('Amazonas', 'RodrIguez de Mendoza', 'Vista Alegre', '010612'),
('Amazonas', 'Utcubamba', '', '010700'),
('Amazonas', 'Utcubamba', 'Bagua Grande', '010701'),
('Amazonas', 'Utcubamba', 'Cajaruro', '010702'),
('Amazonas', 'Utcubamba', 'Cumba', '010703'),
('Amazonas', 'Utcubamba', 'El Milagro', '010704'),
('Amazonas', 'Utcubamba', 'Jamalca', '010705'),
('Amazonas', 'Utcubamba', 'Lonya Grande', '010706'),
('Amazonas', 'Utcubamba', 'Yamon', '010707'),
('Ancash', '', '', '020000'),
('Ancash', 'Huaraz', '', '020100'),
('Ancash', 'Huaraz', 'Huaraz', '020101'),
('Ancash', 'Huaraz', 'Cochabamba', '020102'),
('Ancash', 'Huaraz', 'Colcabamba', '020103'),
('Ancash', 'Huaraz', 'Huanchay', '020104'),
('Ancash', 'Huaraz', 'Independencia', '020105'),
('Ancash', 'Huaraz', 'Jangas', '020106'),
('Ancash', 'Huaraz', 'La Libertad', '020107'),
('Ancash', 'Huaraz', 'Olleros', '020108'),
('Ancash', 'Huaraz', 'Pampas Grande', '020109'),
('Ancash', 'Huaraz', 'Pariacoto', '020110'),
('Ancash', 'Huaraz', 'Pira', '020111'),
('Ancash', 'Huaraz', 'Tarica', '020112'),
('Ancash', 'Aija', '', '020200'),
('Ancash', 'Aija', 'Aija', '020201'),
('Ancash', 'Aija', 'Coris', '020202'),
('Ancash', 'Aija', 'Huacllan', '020203'),
('Ancash', 'Aija', 'La Merced', '020204'),
('Ancash', 'Aija', 'Succha', '020205'),
('Ancash', 'Antonio Raymondi', '', '020300'),
('Ancash', 'Antonio Raymondi', 'Llamellin', '020301'),
('Ancash', 'Antonio Raymondi', 'Aczo', '020302'),
('Ancash', 'Antonio Raymondi', 'Chaccho', '020303'),
('Ancash', 'Antonio Raymondi', 'Chingas', '020304'),
('Ancash', 'Antonio Raymondi', 'Mirgas', '020305'),
('Ancash', 'Antonio Raymondi', 'San Juan de Rontoy', '020306'),
('Ancash', 'AsunciOn', '', '020400'),
('Ancash', 'AsunciOn', 'Chacas', '020401'),
('Ancash', 'AsunciOn', 'Acochaca', '020402'),
('Ancash', 'Bolognesi', '', '020500'),
('Ancash', 'Bolognesi', 'Chiquian', '020501'),
('Ancash', 'Bolognesi', 'Abelardo Pardo Lezameta', '020502'),
('Ancash', 'Bolognesi', 'Antonio Raymondi', '020503'),
('Ancash', 'Bolognesi', 'Aquia', '020504'),
('Ancash', 'Bolognesi', 'Cajacay', '020505'),
('Ancash', 'Bolognesi', 'Canis', '020506'),
('Ancash', 'Bolognesi', 'Colquioc', '020507'),
('Ancash', 'Bolognesi', 'Huallanca', '020508'),
('Ancash', 'Bolognesi', 'Huasta', '020509'),
('Ancash', 'Bolognesi', 'Huayllacayan', '020510'),
('Ancash', 'Bolognesi', 'La Primavera', '020511'),
('Ancash', 'Bolognesi', 'Mangas', '020512'),
('Ancash', 'Bolognesi', 'Pacllon', '020513'),
('Ancash', 'Bolognesi', 'San Miguel de Corpanqui', '020514'),
('Ancash', 'Bolognesi', 'Ticllos', '020515'),
('Ancash', 'Carhuaz', '', '020600'),
('Ancash', 'Carhuaz', 'Carhuaz', '020601'),
('Ancash', 'Carhuaz', 'Acopampa', '020602'),
('Ancash', 'Carhuaz', 'Amashca', '020603'),
('Ancash', 'Carhuaz', 'Anta', '020604'),
('Ancash', 'Carhuaz', 'Ataquero', '020605'),
('Ancash', 'Carhuaz', 'Marcara', '020606'),
('Ancash', 'Carhuaz', 'Pariahuanca', '020607'),
('Ancash', 'Carhuaz', 'San Miguel de Aco', '020608'),
('Ancash', 'Carhuaz', 'Shilla', '020609'),
('Ancash', 'Carhuaz', 'Tinco', '020610'),
('Ancash', 'Carhuaz', 'Yungar', '020611'),
('Ancash', 'Carlos FermIn Fitzcarrald', '', '020700'),
('Ancash', 'Carlos FermIn Fitzcarrald', 'San Luis', '020701'),
('Ancash', 'Carlos FermIn Fitzcarrald', 'San NicolAs', '020702'),
('Ancash', 'Carlos FermIn Fitzcarrald', 'Yauya', '020703'),
('Ancash', 'Casma', '', '020800'),
('Ancash', 'Casma', 'Casma', '020801'),
('Ancash', 'Casma', 'Buena Vista Alta', '020802'),
('Ancash', 'Casma', 'Comandante Noel', '020803'),
('Ancash', 'Casma', 'Yautan', '020804'),
('Ancash', 'Corongo', '', '020900'),
('Ancash', 'Corongo', 'Corongo', '020901'),
('Ancash', 'Corongo', 'Aco', '020902'),
('Ancash', 'Corongo', 'Bambas', '020903'),
('Ancash', 'Corongo', 'Cusca', '020904'),
('Ancash', 'Corongo', 'La Pampa', '020905'),
('Ancash', 'Corongo', 'Yanac', '020906'),
('Ancash', 'Corongo', 'Yupan', '020907'),
('Ancash', 'Huari', '', '021000'),
('Ancash', 'Huari', 'Huari', '021001'),
('Ancash', 'Huari', 'Anra', '021002'),
('Ancash', 'Huari', 'Cajay', '021003'),
('Ancash', 'Huari', 'Chavin de Huantar', '021004'),
('Ancash', 'Huari', 'Huacachi', '021005'),
('Ancash', 'Huari', 'Huacchis', '021006'),
('Ancash', 'Huari', 'Huachis', '021007'),
('Ancash', 'Huari', 'Huantar', '021008'),
('Ancash', 'Huari', 'Masin', '021009'),
('Ancash', 'Huari', 'Paucas', '021010'),
('Ancash', 'Huari', 'Ponto', '021011'),
('Ancash', 'Huari', 'Rahuapampa', '021012'),
('Ancash', 'Huari', 'Rapayan', '021013'),
('Ancash', 'Huari', 'San Marcos', '021014'),
('Ancash', 'Huari', 'San Pedro de Chana', '021015'),
('Ancash', 'Huari', 'Uco', '021016'),
('Ancash', 'Huarmey', '', '021100'),
('Ancash', 'Huarmey', 'Huarmey', '021101'),
('Ancash', 'Huarmey', 'Cochapeti', '021102'),
('Ancash', 'Huarmey', 'Culebras', '021103'),
('Ancash', 'Huarmey', 'Huayan', '021104'),
('Ancash', 'Huarmey', 'Malvas', '021105'),
('Ancash', 'Huaylas', '', '021200'),
('Ancash', 'Huaylas', 'Caraz', '021201'),
('Ancash', 'Huaylas', 'Huallanca', '021202'),
('Ancash', 'Huaylas', 'Huata', '021203'),
('Ancash', 'Huaylas', 'Huaylas', '021204'),
('Ancash', 'Huaylas', 'Mato', '021205'),
('Ancash', 'Huaylas', 'Pamparomas', '021206'),
('Ancash', 'Huaylas', 'Pueblo Libre', '021207'),
('Ancash', 'Huaylas', 'Santa Cruz', '021208'),
('Ancash', 'Huaylas', 'Santo Toribio', '021209'),
('Ancash', 'Huaylas', 'Yuracmarca', '021210'),
('Ancash', 'Mariscal Luzuriaga', '', '021300'),
('Ancash', 'Mariscal Luzuriaga', 'Piscobamba', '021301'),
('Ancash', 'Mariscal Luzuriaga', 'Casca', '021302'),
('Ancash', 'Mariscal Luzuriaga', 'Eleazar GuzmAn Barron', '021303'),
('Ancash', 'Mariscal Luzuriaga', 'Fidel Olivas Escudero', '021304'),
('Ancash', 'Mariscal Luzuriaga', 'Llama', '021305'),
('Ancash', 'Mariscal Luzuriaga', 'Llumpa', '021306'),
('Ancash', 'Mariscal Luzuriaga', 'Lucma', '021307'),
('Ancash', 'Mariscal Luzuriaga', 'Musga', '021308'),
('Ancash', 'Ocros', '', '021400'),
('Ancash', 'Ocros', 'Ocros', '021401'),
('Ancash', 'Ocros', 'Acas', '021402'),
('Ancash', 'Ocros', 'Cajamarquilla', '021403'),
('Ancash', 'Ocros', 'Carhuapampa', '021404'),
('Ancash', 'Ocros', 'Cochas', '021405'),
('Ancash', 'Ocros', 'Congas', '021406'),
('Ancash', 'Ocros', 'Llipa', '021407'),
('Ancash', 'Ocros', 'San CristObal de Rajan', '021408'),
('Ancash', 'Ocros', 'San Pedro', '021409'),
('Ancash', 'Ocros', 'Santiago de Chilcas', '021410'),
('Ancash', 'Pallasca', '', '021500'),
('Ancash', 'Pallasca', 'Cabana', '021501'),
('Ancash', 'Pallasca', 'Bolognesi', '021502'),
('Ancash', 'Pallasca', 'Conchucos', '021503'),
('Ancash', 'Pallasca', 'Huacaschuque', '021504'),
('Ancash', 'Pallasca', 'Huandoval', '021505'),
('Ancash', 'Pallasca', 'Lacabamba', '021506'),
('Ancash', 'Pallasca', 'Llapo', '021507'),
('Ancash', 'Pallasca', 'Pallasca', '021508'),
('Ancash', 'Pallasca', 'Pampas', '021509'),
('Ancash', 'Pallasca', 'Santa Rosa', '021510'),
('Ancash', 'Pallasca', 'Tauca', '021511'),
('Ancash', 'Pomabamba', '', '021600'),
('Ancash', 'Pomabamba', 'Pomabamba', '021601'),
('Ancash', 'Pomabamba', 'Huayllan', '021602'),
('Ancash', 'Pomabamba', 'Parobamba', '021603'),
('Ancash', 'Pomabamba', 'Quinuabamba', '021604'),
('Ancash', 'Recuay', '', '021700'),
('Ancash', 'Recuay', 'Recuay', '021701'),
('Ancash', 'Recuay', 'Catac', '021702'),
('Ancash', 'Recuay', 'Cotaparaco', '021703'),
('Ancash', 'Recuay', 'Huayllapampa', '021704'),
('Ancash', 'Recuay', 'Llacllin', '021705'),
('Ancash', 'Recuay', 'Marca', '021706'),
('Ancash', 'Recuay', 'Pampas Chico', '021707'),
('Ancash', 'Recuay', 'Pararin', '021708'),
('Ancash', 'Recuay', 'Tapacocha', '021709'),
('Ancash', 'Recuay', 'Ticapampa', '021710'),
('Ancash', 'Santa', '', '021800'),
('Ancash', 'Santa', 'Chimbote', '021801'),
('Ancash', 'Santa', 'CAceres del PerU', '021802'),
('Ancash', 'Santa', 'Coishco', '021803'),
('Ancash', 'Santa', 'Macate', '021804'),
('Ancash', 'Santa', 'Moro', '021805'),
('Ancash', 'Santa', 'Nepeña', '021806'),
('Ancash', 'Santa', 'Samanco', '021807'),
('Ancash', 'Santa', 'Santa', '021808'),
('Ancash', 'Santa', 'Nuevo Chimbote', '021809'),
('Ancash', 'Sihuas', '', '021900'),
('Ancash', 'Sihuas', 'Sihuas', '021901'),
('Ancash', 'Sihuas', 'Acobamba', '021902'),
('Ancash', 'Sihuas', 'Alfonso Ugarte', '021903'),
('Ancash', 'Sihuas', 'Cashapampa', '021904'),
('Ancash', 'Sihuas', 'Chingalpo', '021905'),
('Ancash', 'Sihuas', 'Huayllabamba', '021906'),
('Ancash', 'Sihuas', 'Quiches', '021907'),
('Ancash', 'Sihuas', 'Ragash', '021908'),
('Ancash', 'Sihuas', 'San Juan', '021909'),
('Ancash', 'Sihuas', 'Sicsibamba', '021910'),
('Ancash', 'Yungay', '', '022000'),
('Ancash', 'Yungay', 'Yungay', '022001'),
('Ancash', 'Yungay', 'Cascapara', '022002'),
('Ancash', 'Yungay', 'Mancos', '022003'),
('Ancash', 'Yungay', 'Matacoto', '022004'),
('Ancash', 'Yungay', 'Quillo', '022005'),
('Ancash', 'Yungay', 'Ranrahirca', '022006'),
('Ancash', 'Yungay', 'Shupluy', '022007'),
('Ancash', 'Yungay', 'Yanama', '022008'),
('ApurImac', '', '', '030000'),
('ApurImac', 'Abancay', '', '030100'),
('ApurImac', 'Abancay', 'Abancay', '030101'),
('ApurImac', 'Abancay', 'Chacoche', '030102'),
('ApurImac', 'Abancay', 'Circa', '030103'),
('ApurImac', 'Abancay', 'Curahuasi', '030104'),
('ApurImac', 'Abancay', 'Huanipaca', '030105'),
('ApurImac', 'Abancay', 'Lambrama', '030106'),
('ApurImac', 'Abancay', 'Pichirhua', '030107'),
('ApurImac', 'Abancay', 'San Pedro de Cachora', '030108'),
('ApurImac', 'Abancay', 'Tamburco', '030109'),
('ApurImac', 'Andahuaylas', '', '030200'),
('ApurImac', 'Andahuaylas', 'Andahuaylas', '030201'),
('ApurImac', 'Andahuaylas', 'Andarapa', '030202'),
('ApurImac', 'Andahuaylas', 'Chiara', '030203'),
('ApurImac', 'Andahuaylas', 'Huancarama', '030204'),
('ApurImac', 'Andahuaylas', 'Huancaray', '030205'),
('ApurImac', 'Andahuaylas', 'Huayana', '030206'),
('ApurImac', 'Andahuaylas', 'Kishuara', '030207'),
('ApurImac', 'Andahuaylas', 'Pacobamba', '030208'),
('ApurImac', 'Andahuaylas', 'Pacucha', '030209'),
('ApurImac', 'Andahuaylas', 'Pampachiri', '030210'),
('ApurImac', 'Andahuaylas', 'Pomacocha', '030211'),
('ApurImac', 'Andahuaylas', 'San Antonio de Cachi', '030212'),
('ApurImac', 'Andahuaylas', 'San JerOnimo', '030213'),
('ApurImac', 'Andahuaylas', 'San Miguel de Chaccrampa', '030214'),
('ApurImac', 'Andahuaylas', 'Santa MarIa de Chicmo', '030215'),
('ApurImac', 'Andahuaylas', 'Talavera', '030216'),
('ApurImac', 'Andahuaylas', 'Tumay Huaraca', '030217'),
('ApurImac', 'Andahuaylas', 'Turpo', '030218'),
('ApurImac', 'Andahuaylas', 'Kaquiabamba', '030219'),
('ApurImac', 'Andahuaylas', 'JosE MarIa Arguedas', '030220'),
('ApurImac', 'Antabamba', '', '030300'),
('ApurImac', 'Antabamba', 'Antabamba', '030301'),
('ApurImac', 'Antabamba', 'El Oro', '030302'),
('ApurImac', 'Antabamba', 'Huaquirca', '030303'),
('ApurImac', 'Antabamba', 'Juan Espinoza Medrano', '030304'),
('ApurImac', 'Antabamba', 'Oropesa', '030305'),
('ApurImac', 'Antabamba', 'Pachaconas', '030306'),
('ApurImac', 'Antabamba', 'Sabaino', '030307'),
('ApurImac', 'Aymaraes', '', '030400'),
('ApurImac', 'Aymaraes', 'Chalhuanca', '030401'),
('ApurImac', 'Aymaraes', 'Capaya', '030402'),
('ApurImac', 'Aymaraes', 'Caraybamba', '030403'),
('ApurImac', 'Aymaraes', 'Chapimarca', '030404'),
('ApurImac', 'Aymaraes', 'Colcabamba', '030405'),
('ApurImac', 'Aymaraes', 'Cotaruse', '030406'),
('ApurImac', 'Aymaraes', 'Huayllo', '030407'),
('ApurImac', 'Aymaraes', 'Justo Apu Sahuaraura', '030408'),
('ApurImac', 'Aymaraes', 'Lucre', '030409'),
('ApurImac', 'Aymaraes', 'Pocohuanca', '030410'),
('ApurImac', 'Aymaraes', 'San Juan de Chacña', '030411'),
('ApurImac', 'Aymaraes', 'Sañayca', '030412'),
('ApurImac', 'Aymaraes', 'Soraya', '030413'),
('ApurImac', 'Aymaraes', 'Tapairihua', '030414'),
('ApurImac', 'Aymaraes', 'Tintay', '030415'),
('ApurImac', 'Aymaraes', 'Toraya', '030416'),
('ApurImac', 'Aymaraes', 'Yanaca', '030417'),
('ApurImac', 'Cotabambas', '', '030500'),
('ApurImac', 'Cotabambas', 'Tambobamba', '030501'),
('ApurImac', 'Cotabambas', 'Cotabambas', '030502'),
('ApurImac', 'Cotabambas', 'Coyllurqui', '030503'),
('ApurImac', 'Cotabambas', 'Haquira', '030504'),
('ApurImac', 'Cotabambas', 'Mara', '030505'),
('ApurImac', 'Cotabambas', 'Challhuahuacho', '030506'),
('ApurImac', 'Chincheros', '', '030600'),
('ApurImac', 'Chincheros', 'Chincheros', '030601'),
('ApurImac', 'Chincheros', 'Anco_Huallo', '030602'),
('ApurImac', 'Chincheros', 'Cocharcas', '030603'),
('ApurImac', 'Chincheros', 'Huaccana', '030604'),
('ApurImac', 'Chincheros', 'Ocobamba', '030605'),
('ApurImac', 'Chincheros', 'Ongoy', '030606'),
('ApurImac', 'Chincheros', 'Uranmarca', '030607'),
('ApurImac', 'Chincheros', 'Ranracancha', '030608'),
('ApurImac', 'Grau', '', '030700'),
('ApurImac', 'Grau', 'Chuquibambilla', '030701'),
('ApurImac', 'Grau', 'Curpahuasi', '030702'),
('ApurImac', 'Grau', 'Gamarra', '030703'),
('ApurImac', 'Grau', 'Huayllati', '030704'),
('ApurImac', 'Grau', 'Mamara', '030705'),
('ApurImac', 'Grau', 'Micaela Bastidas', '030706'),
('ApurImac', 'Grau', 'Pataypampa', '030707'),
('ApurImac', 'Grau', 'Progreso', '030708'),
('ApurImac', 'Grau', 'San Antonio', '030709'),
('ApurImac', 'Grau', 'Santa Rosa', '030710'),
('ApurImac', 'Grau', 'Turpay', '030711'),
('ApurImac', 'Grau', 'Vilcabamba', '030712'),
('ApurImac', 'Grau', 'Virundo', '030713'),
('ApurImac', 'Grau', 'Curasco', '030714'),
('Arequipa', '', '', '040000'),
('Arequipa', 'Arequipa', '', '040100'),
('Arequipa', 'Arequipa', 'Arequipa', '040101'),
('Arequipa', 'Arequipa', 'Alto Selva Alegre', '040102'),
('Arequipa', 'Arequipa', 'Cayma', '040103'),
('Arequipa', 'Arequipa', 'Cerro Colorado', '040104'),
('Arequipa', 'Arequipa', 'Characato', '040105'),
('Arequipa', 'Arequipa', 'Chiguata', '040106'),
('Arequipa', 'Arequipa', 'Jacobo Hunter', '040107'),
('Arequipa', 'Arequipa', 'La Joya', '040108'),
('Arequipa', 'Arequipa', 'Mariano Melgar', '040109'),
('Arequipa', 'Arequipa', 'Miraflores', '040110'),
('Arequipa', 'Arequipa', 'Mollebaya', '040111'),
('Arequipa', 'Arequipa', 'Paucarpata', '040112'),
('Arequipa', 'Arequipa', 'Pocsi', '040113'),
('Arequipa', 'Arequipa', 'Polobaya', '040114'),
('Arequipa', 'Arequipa', 'Quequeña', '040115'),
('Arequipa', 'Arequipa', 'Sabandia', '040116'),
('Arequipa', 'Arequipa', 'Sachaca', '040117'),
('Arequipa', 'Arequipa', 'San Juan de Siguas', '040118'),
('Arequipa', 'Arequipa', 'San Juan de Tarucani', '040119'),
('Arequipa', 'Arequipa', 'Santa Isabel de Siguas', '040120'),
('Arequipa', 'Arequipa', 'Santa Rita de Siguas', '040121'),
('Arequipa', 'Arequipa', 'Socabaya', '040122'),
('Arequipa', 'Arequipa', 'Tiabaya', '040123'),
('Arequipa', 'Arequipa', 'Uchumayo', '040124'),
('Arequipa', 'Arequipa', 'Vitor', '040125'),
('Arequipa', 'Arequipa', 'Yanahuara', '040126'),
('Arequipa', 'Arequipa', 'Yarabamba', '040127'),
('Arequipa', 'Arequipa', 'Yura', '040128'),
('Arequipa', 'Arequipa', 'JosE Luis Bustamante Y Rivero', '040129'),
('Arequipa', 'CamanA', '', '040200'),
('Arequipa', 'CamanA', 'CamanA', '040201'),
('Arequipa', 'CamanA', 'JosE MarIa Quimper', '040202'),
('Arequipa', 'CamanA', 'Mariano NicolAs ValcArcel', '040203'),
('Arequipa', 'CamanA', 'Mariscal CAceres', '040204'),
('Arequipa', 'CamanA', 'NicolAs de Pierola', '040205'),
('Arequipa', 'CamanA', 'Ocoña', '040206'),
('Arequipa', 'CamanA', 'Quilca', '040207'),
('Arequipa', 'CamanA', 'Samuel Pastor', '040208'),
('Arequipa', 'CaravelI', '', '040300'),
('Arequipa', 'CaravelI', 'CaravelI', '040301'),
('Arequipa', 'CaravelI', 'AcarI', '040302'),
('Arequipa', 'CaravelI', 'Atico', '040303'),
('Arequipa', 'CaravelI', 'Atiquipa', '040304'),
('Arequipa', 'CaravelI', 'Bella UniOn', '040305'),
('Arequipa', 'CaravelI', 'Cahuacho', '040306'),
('Arequipa', 'CaravelI', 'Chala', '040307'),
('Arequipa', 'CaravelI', 'Chaparra', '040308'),
('Arequipa', 'CaravelI', 'Huanuhuanu', '040309'),
('Arequipa', 'CaravelI', 'Jaqui', '040310'),
('Arequipa', 'CaravelI', 'Lomas', '040311'),
('Arequipa', 'CaravelI', 'Quicacha', '040312'),
('Arequipa', 'CaravelI', 'Yauca', '040313'),
('Arequipa', 'Castilla', '', '040400'),
('Arequipa', 'Castilla', 'Aplao', '040401'),
('Arequipa', 'Castilla', 'Andagua', '040402'),
('Arequipa', 'Castilla', 'Ayo', '040403'),
('Arequipa', 'Castilla', 'Chachas', '040404'),
('Arequipa', 'Castilla', 'Chilcaymarca', '040405'),
('Arequipa', 'Castilla', 'Choco', '040406'),
('Arequipa', 'Castilla', 'Huancarqui', '040407'),
('Arequipa', 'Castilla', 'Machaguay', '040408'),
('Arequipa', 'Castilla', 'Orcopampa', '040409'),
('Arequipa', 'Castilla', 'Pampacolca', '040410'),
('Arequipa', 'Castilla', 'Tipan', '040411'),
('Arequipa', 'Castilla', 'Uñon', '040412'),
('Arequipa', 'Castilla', 'Uraca', '040413'),
('Arequipa', 'Castilla', 'Viraco', '040414'),
('Arequipa', 'Caylloma', '', '040500'),
('Arequipa', 'Caylloma', 'Chivay', '040501'),
('Arequipa', 'Caylloma', 'Achoma', '040502'),
('Arequipa', 'Caylloma', 'Cabanaconde', '040503'),
('Arequipa', 'Caylloma', 'Callalli', '040504'),
('Arequipa', 'Caylloma', 'Caylloma', '040505'),
('Arequipa', 'Caylloma', 'Coporaque', '040506'),
('Arequipa', 'Caylloma', 'Huambo', '040507'),
('Arequipa', 'Caylloma', 'Huanca', '040508'),
('Arequipa', 'Caylloma', 'Ichupampa', '040509'),
('Arequipa', 'Caylloma', 'Lari', '040510'),
('Arequipa', 'Caylloma', 'Lluta', '040511'),
('Arequipa', 'Caylloma', 'Maca', '040512'),
('Arequipa', 'Caylloma', 'Madrigal', '040513'),
('Arequipa', 'Caylloma', 'San Antonio de Chuca', '040514'),
('Arequipa', 'Caylloma', 'Sibayo', '040515'),
('Arequipa', 'Caylloma', 'Tapay', '040516'),
('Arequipa', 'Caylloma', 'Tisco', '040517'),
('Arequipa', 'Caylloma', 'Tuti', '040518'),
('Arequipa', 'Caylloma', 'Yanque', '040519'),
('Arequipa', 'Caylloma', 'Majes', '040520'),
('Arequipa', 'Condesuyos', '', '040600'),
('Arequipa', 'Condesuyos', 'Chuquibamba', '040601'),
('Arequipa', 'Condesuyos', 'Andaray', '040602'),
('Arequipa', 'Condesuyos', 'Cayarani', '040603'),
('Arequipa', 'Condesuyos', 'Chichas', '040604'),
('Arequipa', 'Condesuyos', 'Iray', '040605'),
('Arequipa', 'Condesuyos', 'RIo Grande', '040606'),
('Arequipa', 'Condesuyos', 'Salamanca', '040607'),
('Arequipa', 'Condesuyos', 'Yanaquihua', '040608'),
('Arequipa', 'Islay', '', '040700'),
('Arequipa', 'Islay', 'Mollendo', '040701'),
('Arequipa', 'Islay', 'Cocachacra', '040702'),
('Arequipa', 'Islay', 'Dean Valdivia', '040703'),
('Arequipa', 'Islay', 'Islay', '040704'),
('Arequipa', 'Islay', 'Mejia', '040705'),
('Arequipa', 'Islay', 'Punta de BombOn', '040706'),
('Arequipa', 'La Unión', '', '040800'),
('Arequipa', 'La Unión', 'Cotahuasi', '040801'),
('Arequipa', 'La Unión', 'Alca', '040802'),
('Arequipa', 'La Unión', 'Charcana', '040803'),
('Arequipa', 'La Unión', 'Huaynacotas', '040804'),
('Arequipa', 'La Unión', 'Pampamarca', '040805'),
('Arequipa', 'La Unión', 'Puyca', '040806'),
('Arequipa', 'La Unión', 'Quechualla', '040807'),
('Arequipa', 'La Unión', 'Sayla', '040808'),
('Arequipa', 'La Unión', 'Tauria', '040809'),
('Arequipa', 'La Unión', 'Tomepampa', '040810'),
('Arequipa', 'La Unión', 'Toro', '040811'),
('Ayacucho', '', '', '050000'),
('Ayacucho', 'Huamanga', '', '050100'),
('Ayacucho', 'Huamanga', 'Ayacucho', '050101'),
('Ayacucho', 'Huamanga', 'Acocro', '050102'),
('Ayacucho', 'Huamanga', 'Acos Vinchos', '050103'),
('Ayacucho', 'Huamanga', 'Carmen Alto', '050104'),
('Ayacucho', 'Huamanga', 'Chiara', '050105'),
('Ayacucho', 'Huamanga', 'Ocros', '050106'),
('Ayacucho', 'Huamanga', 'Pacaycasa', '050107'),
('Ayacucho', 'Huamanga', 'Quinua', '050108'),
('Ayacucho', 'Huamanga', 'San JosE de Ticllas', '050109'),
('Ayacucho', 'Huamanga', 'San Juan Bautista', '050110'),
('Ayacucho', 'Huamanga', 'Santiago de Pischa', '050111'),
('Ayacucho', 'Huamanga', 'Socos', '050112'),
('Ayacucho', 'Huamanga', 'Tambillo', '050113'),
('Ayacucho', 'Huamanga', 'Vinchos', '050114'),
('Ayacucho', 'Huamanga', 'JesUs Nazareno', '050115'),
('Ayacucho', 'Huamanga', 'AndrEs Avelino CAceres Dorregaray', '050116'),
('Ayacucho', 'Cangallo', '', '050200'),
('Ayacucho', 'Cangallo', 'Cangallo', '050201'),
('Ayacucho', 'Cangallo', 'Chuschi', '050202'),
('Ayacucho', 'Cangallo', 'Los Morochucos', '050203'),
('Ayacucho', 'Cangallo', 'MarIa Parado de Bellido', '050204'),
('Ayacucho', 'Cangallo', 'Paras', '050205'),
('Ayacucho', 'Cangallo', 'Totos', '050206'),
('Ayacucho', 'Huanca Sancos', '', '050300'),
('Ayacucho', 'Huanca Sancos', 'Sancos', '050301'),
('Ayacucho', 'Huanca Sancos', 'Carapo', '050302'),
('Ayacucho', 'Huanca Sancos', 'Sacsamarca', '050303'),
('Ayacucho', 'Huanca Sancos', 'Santiago de Lucanamarca', '050304'),
('Ayacucho', 'Huanta', '', '050400'),
('Ayacucho', 'Huanta', 'Huanta', '050401'),
('Ayacucho', 'Huanta', 'Ayahuanco', '050402'),
('Ayacucho', 'Huanta', 'Huamanguilla', '050403'),
('Ayacucho', 'Huanta', 'Iguain', '050404'),
('Ayacucho', 'Huanta', 'Luricocha', '050405'),
('Ayacucho', 'Huanta', 'Santillana', '050406'),
('Ayacucho', 'Huanta', 'Sivia', '050407'),
('Ayacucho', 'Huanta', 'Llochegua', '050408'),
('Ayacucho', 'Huanta', 'Canayre', '050409'),
('Ayacucho', 'Huanta', 'Uchuraccay', '050410'),
('Ayacucho', 'Huanta', 'Pucacolpa', '050411'),
('Ayacucho', 'La Mar', '', '050500'),
('Ayacucho', 'La Mar', 'San Miguel', '050501'),
('Ayacucho', 'La Mar', 'Anco', '050502'),
('Ayacucho', 'La Mar', 'Ayna', '050503'),
('Ayacucho', 'La Mar', 'Chilcas', '050504'),
('Ayacucho', 'La Mar', 'Chungui', '050505'),
('Ayacucho', 'La Mar', 'Luis Carranza', '050506'),
('Ayacucho', 'La Mar', 'Santa Rosa', '050507'),
('Ayacucho', 'La Mar', 'Tambo', '050508'),
('Ayacucho', 'La Mar', 'Samugari', '050509'),
('Ayacucho', 'La Mar', 'Anchihuay', '050510'),
('Ayacucho', 'Lucanas', '', '050600'),
('Ayacucho', 'Lucanas', 'Puquio', '050601'),
('Ayacucho', 'Lucanas', 'Aucara', '050602'),
('Ayacucho', 'Lucanas', 'Cabana', '050603'),
('Ayacucho', 'Lucanas', 'Carmen Salcedo', '050604'),
('Ayacucho', 'Lucanas', 'Chaviña', '050605'),
('Ayacucho', 'Lucanas', 'Chipao', '050606'),
('Ayacucho', 'Lucanas', 'Huac-Huas', '050607'),
('Ayacucho', 'Lucanas', 'Laramate', '050608'),
('Ayacucho', 'Lucanas', 'Leoncio Prado', '050609'),
('Ayacucho', 'Lucanas', 'Llauta', '050610'),
('Ayacucho', 'Lucanas', 'Lucanas', '050611'),
('Ayacucho', 'Lucanas', 'Ocaña', '050612'),
('Ayacucho', 'Lucanas', 'Otoca', '050613'),
('Ayacucho', 'Lucanas', 'Saisa', '050614'),
('Ayacucho', 'Lucanas', 'San CristObal', '050615'),
('Ayacucho', 'Lucanas', 'San Juan', '050616'),
('Ayacucho', 'Lucanas', 'San Pedro', '050617'),
('Ayacucho', 'Lucanas', 'San Pedro de Palco', '050618'),
('Ayacucho', 'Lucanas', 'Sancos', '050619'),
('Ayacucho', 'Lucanas', 'Santa Ana de Huaycahuacho', '050620'),
('Ayacucho', 'Lucanas', 'Santa Lucia', '050621'),
('Ayacucho', 'Parinacochas', '', '050700'),
('Ayacucho', 'Parinacochas', 'Coracora', '050701'),
('Ayacucho', 'Parinacochas', 'Chumpi', '050702'),
('Ayacucho', 'Parinacochas', 'Coronel Castañeda', '050703'),
('Ayacucho', 'Parinacochas', 'Pacapausa', '050704'),
('Ayacucho', 'Parinacochas', 'Pullo', '050705'),
('Ayacucho', 'Parinacochas', 'Puyusca', '050706'),
('Ayacucho', 'Parinacochas', 'San Francisco de Ravacayco', '050707'),
('Ayacucho', 'Parinacochas', 'Upahuacho', '050708'),
('Ayacucho', 'Páucar del Sara Sara', '', '050800'),
('Ayacucho', 'Páucar del Sara Sara', 'Pausa', '050801'),
('Ayacucho', 'Páucar del Sara Sara', 'Colta', '050802'),
('Ayacucho', 'Páucar del Sara Sara', 'Corculla', '050803'),
('Ayacucho', 'Páucar del Sara Sara', 'Lampa', '050804'),
('Ayacucho', 'Páucar del Sara Sara', 'Marcabamba', '050805'),
('Ayacucho', 'Páucar del Sara Sara', 'Oyolo', '050806'),
('Ayacucho', 'Páucar del Sara Sara', 'Pararca', '050807'),
('Ayacucho', 'Páucar del Sara Sara', 'San Javier de Alpabamba', '050808'),
('Ayacucho', 'Páucar del Sara Sara', 'San JosE de Ushua', '050809'),
('Ayacucho', 'Páucar del Sara Sara', 'Sara Sara', '050810'),
('Ayacucho', 'Sucre', '', '050900'),
('Ayacucho', 'Sucre', 'Querobamba', '050901'),
('Ayacucho', 'Sucre', 'BelEn', '050902'),
('Ayacucho', 'Sucre', 'Chalcos', '050903'),
('Ayacucho', 'Sucre', 'Chilcayoc', '050904'),
('Ayacucho', 'Sucre', 'Huacaña', '050905'),
('Ayacucho', 'Sucre', 'Morcolla', '050906'),
('Ayacucho', 'Sucre', 'Paico', '050907'),
('Ayacucho', 'Sucre', 'San Pedro de Larcay', '050908'),
('Ayacucho', 'Sucre', 'San Salvador de Quije', '050909'),
('Ayacucho', 'Sucre', 'Santiago de Paucaray', '050910'),
('Ayacucho', 'Sucre', 'Soras', '050911'),
('Ayacucho', 'VIctor Fajardo', '', '051000'),
('Ayacucho', 'VIctor Fajardo', 'Huancapi', '051001'),
('Ayacucho', 'VIctor Fajardo', 'Alcamenca', '051002'),
('Ayacucho', 'VIctor Fajardo', 'Apongo', '051003'),
('Ayacucho', 'VIctor Fajardo', 'Asquipata', '051004'),
('Ayacucho', 'VIctor Fajardo', 'Canaria', '051005'),
('Ayacucho', 'VIctor Fajardo', 'Cayara', '051006'),
('Ayacucho', 'VIctor Fajardo', 'Colca', '051007'),
('Ayacucho', 'VIctor Fajardo', 'Huamanquiquia', '051008'),
('Ayacucho', 'VIctor Fajardo', 'Huancaraylla', '051009'),
('Ayacucho', 'VIctor Fajardo', 'Huaya', '051010'),
('Ayacucho', 'VIctor Fajardo', 'Sarhua', '051011'),
('Ayacucho', 'VIctor Fajardo', 'Vilcanchos', '051012'),
('Ayacucho', 'Vilcas HuamAn', '', '051100'),
('Ayacucho', 'Vilcas HuamAn', 'Vilcas Huaman', '051101'),
('Ayacucho', 'Vilcas HuamAn', 'Accomarca', '051102'),
('Ayacucho', 'Vilcas HuamAn', 'Carhuanca', '051103'),
('Ayacucho', 'Vilcas HuamAn', 'ConcepciOn', '051104'),
('Ayacucho', 'Vilcas HuamAn', 'Huambalpa', '051105'),
('Ayacucho', 'Vilcas HuamAn', 'Independencia', '051106'),
('Ayacucho', 'Vilcas HuamAn', 'Saurama', '051107'),
('Ayacucho', 'Vilcas HuamAn', 'Vischongo', '051108'),
('Cajamarca', '', '', '060000'),
('Cajamarca', 'Cajamarca', '', '060100'),
('Cajamarca', 'Cajamarca', 'Cajamarca', '060101'),
('Cajamarca', 'Cajamarca', 'AsunciOn', '060102'),
('Cajamarca', 'Cajamarca', 'Chetilla', '060103'),
('Cajamarca', 'Cajamarca', 'Cospan', '060104'),
('Cajamarca', 'Cajamarca', 'Encañada', '060105'),
('Cajamarca', 'Cajamarca', 'JesUs', '060106'),
('Cajamarca', 'Cajamarca', 'Llacanora', '060107'),
('Cajamarca', 'Cajamarca', 'Los Baños del Inca', '060108'),
('Cajamarca', 'Cajamarca', 'Magdalena', '060109'),
('Cajamarca', 'Cajamarca', 'Matara', '060110'),
('Cajamarca', 'Cajamarca', 'Namora', '060111'),
('Cajamarca', 'Cajamarca', 'San Juan', '060112'),
('Cajamarca', 'Cajabamba', '', '060200'),
('Cajamarca', 'Cajabamba', 'Cajabamba', '060201'),
('Cajamarca', 'Cajabamba', 'Cachachi', '060202'),
('Cajamarca', 'Cajabamba', 'Condebamba', '060203'),
('Cajamarca', 'Cajabamba', 'Sitacocha', '060204'),
('Cajamarca', 'CelendIn', '', '060300'),
('Cajamarca', 'CelendIn', 'CelendIn', '060301'),
('Cajamarca', 'CelendIn', 'Chumuch', '060302'),
('Cajamarca', 'CelendIn', 'Cortegana', '060303'),
('Cajamarca', 'CelendIn', 'Huasmin', '060304'),
('Cajamarca', 'CelendIn', 'Jorge ChAvez', '060305'),
('Cajamarca', 'CelendIn', 'JosE GAlvez', '060306'),
('Cajamarca', 'CelendIn', 'Miguel Iglesias', '060307'),
('Cajamarca', 'CelendIn', 'Oxamarca', '060308'),
('Cajamarca', 'CelendIn', 'Sorochuco', '060309'),
('Cajamarca', 'CelendIn', 'Sucre', '060310'),
('Cajamarca', 'CelendIn', 'Utco', '060311'),
('Cajamarca', 'CelendIn', 'La Libertad de Pallan', '060312'),
('Cajamarca', 'Chota', '', '060400'),
('Cajamarca', 'Chota', 'Chota', '060401'),
('Cajamarca', 'Chota', 'Anguia', '060402'),
('Cajamarca', 'Chota', 'Chadin', '060403'),
('Cajamarca', 'Chota', 'Chiguirip', '060404'),
('Cajamarca', 'Chota', 'Chimban', '060405'),
('Cajamarca', 'Chota', 'Choropampa', '060406'),
('Cajamarca', 'Chota', 'Cochabamba', '060407'),
('Cajamarca', 'Chota', 'Conchan', '060408'),
('Cajamarca', 'Chota', 'Huambos', '060409'),
('Cajamarca', 'Chota', 'Lajas', '060410'),
('Cajamarca', 'Chota', 'Llama', '060411'),
('Cajamarca', 'Chota', 'Miracosta', '060412'),
('Cajamarca', 'Chota', 'Paccha', '060413'),
('Cajamarca', 'Chota', 'Pion', '060414'),
('Cajamarca', 'Chota', 'Querocoto', '060415'),
('Cajamarca', 'Chota', 'San Juan de Licupis', '060416'),
('Cajamarca', 'Chota', 'Tacabamba', '060417'),
('Cajamarca', 'Chota', 'Tocmoche', '060418'),
('Cajamarca', 'Chota', 'Chalamarca', '060419'),
('Cajamarca', 'ContumazA', '', '060500'),
('Cajamarca', 'ContumazA', 'Contumaza', '060501'),
('Cajamarca', 'ContumazA', 'Chilete', '060502'),
('Cajamarca', 'ContumazA', 'Cupisnique', '060503'),
('Cajamarca', 'ContumazA', 'Guzmango', '060504'),
('Cajamarca', 'ContumazA', 'San Benito', '060505'),
('Cajamarca', 'ContumazA', 'Santa Cruz de Toledo', '060506'),
('Cajamarca', 'ContumazA', 'Tantarica', '060507'),
('Cajamarca', 'ContumazA', 'Yonan', '060508'),
('Cajamarca', 'Cutervo', '', '060600'),
('Cajamarca', 'Cutervo', 'Cutervo', '060601'),
('Cajamarca', 'Cutervo', 'Callayuc', '060602'),
('Cajamarca', 'Cutervo', 'Choros', '060603'),
('Cajamarca', 'Cutervo', 'Cujillo', '060604'),
('Cajamarca', 'Cutervo', 'La Ramada', '060605'),
('Cajamarca', 'Cutervo', 'Pimpingos', '060606'),
('Cajamarca', 'Cutervo', 'Querocotillo', '060607'),
('Cajamarca', 'Cutervo', 'San AndrEs de Cutervo', '060608'),
('Cajamarca', 'Cutervo', 'San Juan de Cutervo', '060609'),
('Cajamarca', 'Cutervo', 'San Luis de Lucma', '060610'),
('Cajamarca', 'Cutervo', 'Santa Cruz', '060611'),
('Cajamarca', 'Cutervo', 'Santo Domingo de la Capilla', '060612'),
('Cajamarca', 'Cutervo', 'Santo Tomas', '060613'),
('Cajamarca', 'Cutervo', 'Socota', '060614'),
('Cajamarca', 'Cutervo', 'Toribio Casanova', '060615'),
('Cajamarca', 'Hualgayoc', '', '060700'),
('Cajamarca', 'Hualgayoc', 'Bambamarca', '060701'),
('Cajamarca', 'Hualgayoc', 'Chugur', '060702'),
('Cajamarca', 'Hualgayoc', 'Hualgayoc', '060703'),
('Cajamarca', 'JaEn', '', '060800'),
('Cajamarca', 'JaEn', 'JaEn', '060801'),
('Cajamarca', 'JaEn', 'Bellavista', '060802'),
('Cajamarca', 'JaEn', 'Chontali', '060803'),
('Cajamarca', 'JaEn', 'Colasay', '060804'),
('Cajamarca', 'JaEn', 'Huabal', '060805'),
('Cajamarca', 'JaEn', 'Las Pirias', '060806'),
('Cajamarca', 'JaEn', 'Pomahuaca', '060807'),
('Cajamarca', 'JaEn', 'Pucara', '060808'),
('Cajamarca', 'JaEn', 'Sallique', '060809'),
('Cajamarca', 'JaEn', 'San Felipe', '060810'),
('Cajamarca', 'JaEn', 'San JosE del Alto', '060811'),
('Cajamarca', 'JaEn', 'Santa Rosa', '060812'),
('Cajamarca', 'San Ignacio', '', '060900'),
('Cajamarca', 'San Ignacio', 'San Ignacio', '060901'),
('Cajamarca', 'San Ignacio', 'Chirinos', '060902'),
('Cajamarca', 'San Ignacio', 'Huarango', '060903'),
('Cajamarca', 'San Ignacio', 'La Coipa', '060904'),
('Cajamarca', 'San Ignacio', 'Namballe', '060905'),
('Cajamarca', 'San Ignacio', 'San JosE de Lourdes', '060906'),
('Cajamarca', 'San Ignacio', 'Tabaconas', '060907'),
('Cajamarca', 'San Marcos', '', '061000'),
('Cajamarca', 'San Marcos', 'Pedro GAlvez', '061001'),
('Cajamarca', 'San Marcos', 'Chancay', '061002'),
('Cajamarca', 'San Marcos', 'Eduardo Villanueva', '061003'),
('Cajamarca', 'San Marcos', 'Gregorio Pita', '061004'),
('Cajamarca', 'San Marcos', 'Ichocan', '061005'),
('Cajamarca', 'San Marcos', 'JosE Manuel Quiroz', '061006'),
('Cajamarca', 'San Marcos', 'JosE Sabogal', '061007'),
('Cajamarca', 'San Miguel', '', '061100'),
('Cajamarca', 'San Miguel', 'San Miguel', '061101'),
('Cajamarca', 'San Miguel', 'BolIvar', '061102'),
('Cajamarca', 'San Miguel', 'Calquis', '061103'),
('Cajamarca', 'San Miguel', 'Catilluc', '061104'),
('Cajamarca', 'San Miguel', 'El Prado', '061105'),
('Cajamarca', 'San Miguel', 'La Florida', '061106'),
('Cajamarca', 'San Miguel', 'Llapa', '061107'),
('Cajamarca', 'San Miguel', 'Nanchoc', '061108'),
('Cajamarca', 'San Miguel', 'Niepos', '061109'),
('Cajamarca', 'San Miguel', 'San Gregorio', '061110'),
('Cajamarca', 'San Miguel', 'San Silvestre de Cochan', '061111'),
('Cajamarca', 'San Miguel', 'Tongod', '061112'),
('Cajamarca', 'San Miguel', 'UniOn Agua Blanca', '061113'),
('Cajamarca', 'San Pablo', '', '061200'),
('Cajamarca', 'San Pablo', 'San Pablo', '061201'),
('Cajamarca', 'San Pablo', 'San Bernardino', '061202'),
('Cajamarca', 'San Pablo', 'San Luis', '061203'),
('Cajamarca', 'San Pablo', 'Tumbaden', '061204'),
('Cajamarca', 'Santa Cruz', '', '061300'),
('Cajamarca', 'Santa Cruz', 'Santa Cruz', '061301'),
('Cajamarca', 'Santa Cruz', 'Andabamba', '061302'),
('Cajamarca', 'Santa Cruz', 'Catache', '061303'),
('Cajamarca', 'Santa Cruz', 'Chancaybaños', '061304'),
('Cajamarca', 'Santa Cruz', 'La Esperanza', '061305'),
('Cajamarca', 'Santa Cruz', 'Ninabamba', '061306'),
('Cajamarca', 'Santa Cruz', 'Pulan', '061307'),
('Cajamarca', 'Santa Cruz', 'Saucepampa', '061308'),
('Cajamarca', 'Santa Cruz', 'Sexi', '061309'),
('Cajamarca', 'Santa Cruz', 'Uticyacu', '061310'),
('Cajamarca', 'Santa Cruz', 'Yauyucan', '061311'),
('Callao', '', '', '070000'),
('Callao', 'Prov. Const. del Callao', '', '070100'),
('Callao', 'Prov. Const. del Callao', 'Callao', '070101'),
('Callao', 'Prov. Const. del Callao', 'Bellavista', '070102'),
('Callao', 'Prov. Const. del Callao', 'Carmen de la Legua Reynoso', '070103'),
('Callao', 'Prov. Const. del Callao', 'La Perla', '070104'),
('Callao', 'Prov. Const. del Callao', 'La Punta', '070105'),
('Callao', 'Prov. Const. del Callao', 'Ventanilla', '070106'),
('Callao', 'Prov. Const. del Callao', 'Mi PerU', '070107'),
('Cusco', '', '', '080000'),
('Cusco', 'Cusco', '', '080100'),
('Cusco', 'Cusco', 'Cusco', '080101'),
('Cusco', 'Cusco', 'Ccorca', '080102'),
('Cusco', 'Cusco', 'Poroy', '080103'),
('Cusco', 'Cusco', 'San JerOnimo', '080104'),
('Cusco', 'Cusco', 'San Sebastian', '080105'),
('Cusco', 'Cusco', 'Santiago', '080106'),
('Cusco', 'Cusco', 'Saylla', '080107'),
('Cusco', 'Cusco', 'Wanchaq', '080108'),
('Cusco', 'Acomayo', '', '080200'),
('Cusco', 'Acomayo', 'Acomayo', '080201'),
('Cusco', 'Acomayo', 'Acopia', '080202'),
('Cusco', 'Acomayo', 'Acos', '080203'),
('Cusco', 'Acomayo', 'Mosoc Llacta', '080204'),
('Cusco', 'Acomayo', 'Pomacanchi', '080205'),
('Cusco', 'Acomayo', 'Rondocan', '080206'),
('Cusco', 'Acomayo', 'Sangarara', '080207'),
('Cusco', 'Anta', '', '080300'),
('Cusco', 'Anta', 'Anta', '080301'),
('Cusco', 'Anta', 'Ancahuasi', '080302'),
('Cusco', 'Anta', 'Cachimayo', '080303'),
('Cusco', 'Anta', 'Chinchaypujio', '080304'),
('Cusco', 'Anta', 'Huarocondo', '080305'),
('Cusco', 'Anta', 'Limatambo', '080306'),
('Cusco', 'Anta', 'Mollepata', '080307'),
('Cusco', 'Anta', 'Pucyura', '080308'),
('Cusco', 'Anta', 'Zurite', '080309'),
('Cusco', 'Calca', '', '080400'),
('Cusco', 'Calca', 'Calca', '080401'),
('Cusco', 'Calca', 'Coya', '080402'),
('Cusco', 'Calca', 'Lamay', '080403'),
('Cusco', 'Calca', 'Lares', '080404'),
('Cusco', 'Calca', 'Pisac', '080405'),
('Cusco', 'Calca', 'San Salvador', '080406'),
('Cusco', 'Calca', 'Taray', '080407'),
('Cusco', 'Calca', 'Yanatile', '080408'),
('Cusco', 'Canas', '', '080500'),
('Cusco', 'Canas', 'Yanaoca', '080501'),
('Cusco', 'Canas', 'Checca', '080502'),
('Cusco', 'Canas', 'Kunturkanki', '080503'),
('Cusco', 'Canas', 'Langui', '080504'),
('Cusco', 'Canas', 'Layo', '080505'),
('Cusco', 'Canas', 'Pampamarca', '080506'),
('Cusco', 'Canas', 'Quehue', '080507'),
('Cusco', 'Canas', 'Tupac Amaru', '080508'),
('Cusco', 'Canchis', '', '080600'),
('Cusco', 'Canchis', 'Sicuani', '080601'),
('Cusco', 'Canchis', 'Checacupe', '080602'),
('Cusco', 'Canchis', 'Combapata', '080603'),
('Cusco', 'Canchis', 'Marangani', '080604'),
('Cusco', 'Canchis', 'Pitumarca', '080605'),
('Cusco', 'Canchis', 'San Pablo', '080606'),
('Cusco', 'Canchis', 'San Pedro', '080607'),
('Cusco', 'Canchis', 'Tinta', '080608'),
('Cusco', 'Chumbivilcas', '', '080700'),
('Cusco', 'Chumbivilcas', 'Santo Tomas', '080701'),
('Cusco', 'Chumbivilcas', 'Capacmarca', '080702'),
('Cusco', 'Chumbivilcas', 'Chamaca', '080703'),
('Cusco', 'Chumbivilcas', 'Colquemarca', '080704'),
('Cusco', 'Chumbivilcas', 'Livitaca', '080705'),
('Cusco', 'Chumbivilcas', 'Llusco', '080706'),
('Cusco', 'Chumbivilcas', 'Quiñota', '080707'),
('Cusco', 'Chumbivilcas', 'Velille', '080708'),
('Cusco', 'Espinar', '', '080800'),
('Cusco', 'Espinar', 'Espinar', '080801'),
('Cusco', 'Espinar', 'Condoroma', '080802'),
('Cusco', 'Espinar', 'Coporaque', '080803'),
('Cusco', 'Espinar', 'Ocoruro', '080804'),
('Cusco', 'Espinar', 'Pallpata', '080805'),
('Cusco', 'Espinar', 'Pichigua', '080806'),
('Cusco', 'Espinar', 'Suyckutambo', '080807'),
('Cusco', 'Espinar', 'Alto Pichigua', '080808'),
('Cusco', 'La ConvenciOn', '', '080900'),
('Cusco', 'La ConvenciOn', 'Santa Ana', '080901'),
('Cusco', 'La ConvenciOn', 'Echarate', '080902'),
('Cusco', 'La ConvenciOn', 'Huayopata', '080903'),
('Cusco', 'La ConvenciOn', 'Maranura', '080904'),
('Cusco', 'La ConvenciOn', 'Ocobamba', '080905'),
('Cusco', 'La ConvenciOn', 'Quellouno', '080906'),
('Cusco', 'La ConvenciOn', 'Kimbiri', '080907'),
('Cusco', 'La ConvenciOn', 'Santa Teresa', '080908'),
('Cusco', 'La ConvenciOn', 'Vilcabamba', '080909'),
('Cusco', 'La ConvenciOn', 'Pichari', '080910'),
('Cusco', 'La ConvenciOn', 'Inkawasi', '080911'),
('Cusco', 'La ConvenciOn', 'Villa Virgen', '080912'),
('Cusco', 'La ConvenciOn', 'Villa Kintiarina', '080913'),
('Cusco', 'Paruro', '', '081000'),
('Cusco', 'Paruro', 'Paruro', '081001'),
('Cusco', 'Paruro', 'Accha', '081002'),
('Cusco', 'Paruro', 'Ccapi', '081003'),
('Cusco', 'Paruro', 'Colcha', '081004'),
('Cusco', 'Paruro', 'Huanoquite', '081005'),
('Cusco', 'Paruro', 'Omacha', '081006'),
('Cusco', 'Paruro', 'Paccaritambo', '081007'),
('Cusco', 'Paruro', 'Pillpinto', '081008'),
('Cusco', 'Paruro', 'Yaurisque', '081009'),
('Cusco', 'Paucartambo', '', '081100'),
('Cusco', 'Paucartambo', 'Paucartambo', '081101'),
('Cusco', 'Paucartambo', 'Caicay', '081102'),
('Cusco', 'Paucartambo', 'Challabamba', '081103'),
('Cusco', 'Paucartambo', 'Colquepata', '081104'),
('Cusco', 'Paucartambo', 'Huancarani', '081105'),
('Cusco', 'Paucartambo', 'Kosñipata', '081106'),
('Cusco', 'Quispicanchi', '', '081200'),
('Cusco', 'Quispicanchi', 'Urcos', '081201'),
('Cusco', 'Quispicanchi', 'Andahuaylillas', '081202'),
('Cusco', 'Quispicanchi', 'Camanti', '081203'),
('Cusco', 'Quispicanchi', 'Ccarhuayo', '081204'),
('Cusco', 'Quispicanchi', 'Ccatca', '081205'),
('Cusco', 'Quispicanchi', 'Cusipata', '081206'),
('Cusco', 'Quispicanchi', 'Huaro', '081207'),
('Cusco', 'Quispicanchi', 'Lucre', '081208'),
('Cusco', 'Quispicanchi', 'Marcapata', '081209'),
('Cusco', 'Quispicanchi', 'Ocongate', '081210'),
('Cusco', 'Quispicanchi', 'Oropesa', '081211'),
('Cusco', 'Quispicanchi', 'Quiquijana', '081212'),
('Cusco', 'Urubamba', '', '081300'),
('Cusco', 'Urubamba', 'Urubamba', '081301'),
('Cusco', 'Urubamba', 'Chinchero', '081302'),
('Cusco', 'Urubamba', 'Huayllabamba', '081303'),
('Cusco', 'Urubamba', 'Machupicchu', '081304'),
('Cusco', 'Urubamba', 'Maras', '081305'),
('Cusco', 'Urubamba', 'Ollantaytambo', '081306'),
('Cusco', 'Urubamba', 'Yucay', '081307'),
('Huancavelica', '', '', '090000'),
('Huancavelica', 'Huancavelica', '', '090100'),
('Huancavelica', 'Huancavelica', 'Huancavelica', '090101'),
('Huancavelica', 'Huancavelica', 'Acobambilla', '090102'),
('Huancavelica', 'Huancavelica', 'Acoria', '090103'),
('Huancavelica', 'Huancavelica', 'Conayca', '090104'),
('Huancavelica', 'Huancavelica', 'Cuenca', '090105'),
('Huancavelica', 'Huancavelica', 'Huachocolpa', '090106'),
('Huancavelica', 'Huancavelica', 'Huayllahuara', '090107'),
('Huancavelica', 'Huancavelica', 'Izcuchaca', '090108'),
('Huancavelica', 'Huancavelica', 'Laria', '090109'),
('Huancavelica', 'Huancavelica', 'Manta', '090110'),
('Huancavelica', 'Huancavelica', 'Mariscal CAceres', '090111'),
('Huancavelica', 'Huancavelica', 'Moya', '090112'),
('Huancavelica', 'Huancavelica', 'Nuevo Occoro', '090113'),
('Huancavelica', 'Huancavelica', 'Palca', '090114'),
('Huancavelica', 'Huancavelica', 'Pilchaca', '090115'),
('Huancavelica', 'Huancavelica', 'Vilca', '090116'),
('Huancavelica', 'Huancavelica', 'Yauli', '090117'),
('Huancavelica', 'Huancavelica', 'AscensiOn', '090118'),
('Huancavelica', 'Huancavelica', 'Huando', '090119'),
('Huancavelica', 'Acobamba', '', '090200'),
('Huancavelica', 'Acobamba', 'Acobamba', '090201'),
('Huancavelica', 'Acobamba', 'Andabamba', '090202'),
('Huancavelica', 'Acobamba', 'Anta', '090203'),
('Huancavelica', 'Acobamba', 'Caja', '090204'),
('Huancavelica', 'Acobamba', 'Marcas', '090205'),
('Huancavelica', 'Acobamba', 'Paucara', '090206'),
('Huancavelica', 'Acobamba', 'Pomacocha', '090207'),
('Huancavelica', 'Acobamba', 'Rosario', '090208'),
('Huancavelica', 'Angaraes', '', '090300'),
('Huancavelica', 'Angaraes', 'Lircay', '090301'),
('Huancavelica', 'Angaraes', 'Anchonga', '090302'),
('Huancavelica', 'Angaraes', 'Callanmarca', '090303'),
('Huancavelica', 'Angaraes', 'Ccochaccasa', '090304'),
('Huancavelica', 'Angaraes', 'Chincho', '090305'),
('Huancavelica', 'Angaraes', 'Congalla', '090306'),
('Huancavelica', 'Angaraes', 'Huanca-Huanca', '090307'),
('Huancavelica', 'Angaraes', 'Huayllay Grande', '090308'),
('Huancavelica', 'Angaraes', 'Julcamarca', '090309'),
('Huancavelica', 'Angaraes', 'San Antonio de Antaparco', '090310'),
('Huancavelica', 'Angaraes', 'Santo Tomas de Pata', '090311'),
('Huancavelica', 'Angaraes', 'Secclla', '090312'),
('Huancavelica', 'Castrovirreyna', '', '090400'),
('Huancavelica', 'Castrovirreyna', 'Castrovirreyna', '090401'),
('Huancavelica', 'Castrovirreyna', 'Arma', '090402'),
('Huancavelica', 'Castrovirreyna', 'Aurahua', '090403'),
('Huancavelica', 'Castrovirreyna', 'Capillas', '090404'),
('Huancavelica', 'Castrovirreyna', 'Chupamarca', '090405'),
('Huancavelica', 'Castrovirreyna', 'Cocas', '090406'),
('Huancavelica', 'Castrovirreyna', 'Huachos', '090407'),
('Huancavelica', 'Castrovirreyna', 'Huamatambo', '090408'),
('Huancavelica', 'Castrovirreyna', 'Mollepampa', '090409'),
('Huancavelica', 'Castrovirreyna', 'San Juan', '090410'),
('Huancavelica', 'Castrovirreyna', 'Santa Ana', '090411'),
('Huancavelica', 'Castrovirreyna', 'Tantara', '090412'),
('Huancavelica', 'Castrovirreyna', 'Ticrapo', '090413'),
('Huancavelica', 'Churcampa', '', '090500'),
('Huancavelica', 'Churcampa', 'Churcampa', '090501'),
('Huancavelica', 'Churcampa', 'Anco', '090502'),
('Huancavelica', 'Churcampa', 'Chinchihuasi', '090503'),
('Huancavelica', 'Churcampa', 'El Carmen', '090504'),
('Huancavelica', 'Churcampa', 'La Merced', '090505'),
('Huancavelica', 'Churcampa', 'Locroja', '090506'),
('Huancavelica', 'Churcampa', 'Paucarbamba', '090507'),
('Huancavelica', 'Churcampa', 'San Miguel de Mayocc', '090508'),
('Huancavelica', 'Churcampa', 'San Pedro de Coris', '090509'),
('Huancavelica', 'Churcampa', 'Pachamarca', '090510'),
('Huancavelica', 'Churcampa', 'Cosme', '090511'),
('Huancavelica', 'HuaytarA', '', '090600'),
('Huancavelica', 'HuaytarA', 'Huaytara', '090601'),
('Huancavelica', 'HuaytarA', 'Ayavi', '090602'),
('Huancavelica', 'HuaytarA', 'COrdova', '090603'),
('Huancavelica', 'HuaytarA', 'Huayacundo Arma', '090604'),
('Huancavelica', 'HuaytarA', 'Laramarca', '090605'),
('Huancavelica', 'HuaytarA', 'Ocoyo', '090606'),
('Huancavelica', 'HuaytarA', 'Pilpichaca', '090607'),
('Huancavelica', 'HuaytarA', 'Querco', '090608'),
('Huancavelica', 'HuaytarA', 'Quito-Arma', '090609'),
('Huancavelica', 'HuaytarA', 'San Antonio de Cusicancha', '090610'),
('Huancavelica', 'HuaytarA', 'San Francisco de Sangayaico', '090611'),
('Huancavelica', 'HuaytarA', 'San Isidro', '090612'),
('Huancavelica', 'HuaytarA', 'Santiago de Chocorvos', '090613'),
('Huancavelica', 'HuaytarA', 'Santiago de Quirahuara', '090614'),
('Huancavelica', 'HuaytarA', 'Santo Domingo de Capillas', '090615'),
('Huancavelica', 'HuaytarA', 'Tambo', '090616'),
('Huancavelica', 'Tayacaja', '', '090700'),
('Huancavelica', 'Tayacaja', 'Pampas', '090701'),
('Huancavelica', 'Tayacaja', 'Acostambo', '090702'),
('Huancavelica', 'Tayacaja', 'Acraquia', '090703'),
('Huancavelica', 'Tayacaja', 'Ahuaycha', '090704'),
('Huancavelica', 'Tayacaja', 'Colcabamba', '090705'),
('Huancavelica', 'Tayacaja', 'Daniel HernAndez', '090706'),
('Huancavelica', 'Tayacaja', 'Huachocolpa', '090707'),
('Huancavelica', 'Tayacaja', 'Huaribamba', '090709'),
('Huancavelica', 'Tayacaja', 'Ñahuimpuquio', '090710'),
('Huancavelica', 'Tayacaja', 'Pazos', '090711'),
('Huancavelica', 'Tayacaja', 'Quishuar', '090713'),
('Huancavelica', 'Tayacaja', 'Salcabamba', '090714'),
('Huancavelica', 'Tayacaja', 'Salcahuasi', '090715'),
('Huancavelica', 'Tayacaja', 'San Marcos de Rocchac', '090716'),
('Huancavelica', 'Tayacaja', 'Surcubamba', '090717'),
('Huancavelica', 'Tayacaja', 'Tintay Puncu', '090718'),
('Huancavelica', 'Tayacaja', 'Quichuas', '090719'),
('Huancavelica', 'Tayacaja', 'Andaymarca', '090720'),
('HuAnuco', '', '', '100000'),
('HuAnuco', 'HuAnuco', '', '100100'),
('HuAnuco', 'HuAnuco', 'Huanuco', '100101'),
('HuAnuco', 'HuAnuco', 'Amarilis', '100102'),
('HuAnuco', 'HuAnuco', 'Chinchao', '100103'),
('HuAnuco', 'HuAnuco', 'Churubamba', '100104'),
('HuAnuco', 'HuAnuco', 'Margos', '100105'),
('HuAnuco', 'HuAnuco', 'Quisqui (Kichki),', '100106'),
('HuAnuco', 'HuAnuco', 'San Francisco de Cayran', '100107'),
('HuAnuco', 'HuAnuco', 'San Pedro de Chaulan', '100108'),
('HuAnuco', 'HuAnuco', 'Santa MarIa del Valle', '100109'),
('HuAnuco', 'HuAnuco', 'Yarumayo', '100110'),
('HuAnuco', 'HuAnuco', 'Pillco Marca', '100111'),
('HuAnuco', 'HuAnuco', 'Yacus', '100112'),
('HuAnuco', 'Ambo', '', '100200'),
('HuAnuco', 'Ambo', 'Ambo', '100201'),
('HuAnuco', 'Ambo', 'Cayna', '100202'),
('HuAnuco', 'Ambo', 'Colpas', '100203'),
('HuAnuco', 'Ambo', 'Conchamarca', '100204'),
('HuAnuco', 'Ambo', 'Huacar', '100205'),
('HuAnuco', 'Ambo', 'San Francisco', '100206'),
('HuAnuco', 'Ambo', 'San Rafael', '100207'),
('HuAnuco', 'Ambo', 'Tomay Kichwa', '100208'),
('HuAnuco', 'Dos de Mayo', '', '100300'),
('HuAnuco', 'Dos de Mayo', 'La UniOn', '100301'),
('HuAnuco', 'Dos de Mayo', 'Chuquis', '100307'),
('HuAnuco', 'Dos de Mayo', 'MarIas', '100311'),
('HuAnuco', 'Dos de Mayo', 'Pachas', '100313'),
('HuAnuco', 'Dos de Mayo', 'Quivilla', '100316'),
('HuAnuco', 'Dos de Mayo', 'Ripan', '100317'),
('HuAnuco', 'Dos de Mayo', 'Shunqui', '100321'),
('HuAnuco', 'Dos de Mayo', 'Sillapata', '100322'),
('HuAnuco', 'Dos de Mayo', 'Yanas', '100323'),
('HuAnuco', 'Huacaybamba', '', '100400'),
('HuAnuco', 'Huacaybamba', 'Huacaybamba', '100401'),
('HuAnuco', 'Huacaybamba', 'Canchabamba', '100402'),
('HuAnuco', 'Huacaybamba', 'Cochabamba', '100403'),
('HuAnuco', 'Huacaybamba', 'Pinra', '100404'),
('HuAnuco', 'HuamalIes', '', '100500'),
('HuAnuco', 'HuamalIes', 'Llata', '100501'),
('HuAnuco', 'HuamalIes', 'Arancay', '100502'),
('HuAnuco', 'HuamalIes', 'ChavIn de Pariarca', '100503'),
('HuAnuco', 'HuamalIes', 'Jacas Grande', '100504'),
('HuAnuco', 'HuamalIes', 'Jircan', '100505'),
('HuAnuco', 'HuamalIes', 'Miraflores', '100506'),
('HuAnuco', 'HuamalIes', 'MonzOn', '100507'),
('HuAnuco', 'HuamalIes', 'Punchao', '100508'),
('HuAnuco', 'HuamalIes', 'Puños', '100509'),
('HuAnuco', 'HuamalIes', 'Singa', '100510'),
('HuAnuco', 'HuamalIes', 'Tantamayo', '100511'),
('HuAnuco', 'Leoncio Prado', '', '100600'),
('HuAnuco', 'Leoncio Prado', 'Rupa-Rupa', '100601'),
('HuAnuco', 'Leoncio Prado', 'Daniel AlomIa Robles', '100602'),
('HuAnuco', 'Leoncio Prado', 'HermIlio Valdizan', '100603'),
('HuAnuco', 'Leoncio Prado', 'JosE Crespo y Castillo', '100604'),
('HuAnuco', 'Leoncio Prado', 'Luyando', '100605'),
('HuAnuco', 'Leoncio Prado', 'Mariano Damaso Beraun', '100606'),
('HuAnuco', 'Marañón', '', '100700'),
('HuAnuco', 'Marañón', 'Huacrachuco', '100701'),
('HuAnuco', 'Marañón', 'Cholon', '100702'),
('HuAnuco', 'Marañón', 'San Buenaventura', '100703'),
('HuAnuco', 'Pachitea', '', '100800'),
('HuAnuco', 'Pachitea', 'Panao', '100801'),
('HuAnuco', 'Pachitea', 'Chaglla', '100802'),
('HuAnuco', 'Pachitea', 'Molino', '100803'),
('HuAnuco', 'Pachitea', 'Umari', '100804'),
('HuAnuco', 'Puerto Inca', '', '100900'),
('HuAnuco', 'Puerto Inca', 'Puerto Inca', '100901'),
('HuAnuco', 'Puerto Inca', 'Codo del Pozuzo', '100902'),
('HuAnuco', 'Puerto Inca', 'Honoria', '100903'),
('HuAnuco', 'Puerto Inca', 'Tournavista', '100904'),
('HuAnuco', 'Puerto Inca', 'Yuyapichis', '100905'),
('HuAnuco', 'Lauricocha ', '', '101000'),
('HuAnuco', 'Lauricocha ', 'JesUs', '101001'),
('HuAnuco', 'Lauricocha ', 'Baños', '101002'),
('HuAnuco', 'Lauricocha ', 'Jivia', '101003'),
('HuAnuco', 'Lauricocha ', 'Queropalca', '101004'),
('HuAnuco', 'Lauricocha ', 'Rondos', '101005');
INSERT INTO `ubigeo` (`Departamento`, `Provincia`, `Distrito`, `id_ubigeo`) VALUES
('HuAnuco', 'Lauricocha ', 'San Francisco de AsIs', '101006'),
('HuAnuco', 'Lauricocha ', 'San Miguel de Cauri', '101007'),
('HuAnuco', 'Yarowilca ', '', '101100'),
('HuAnuco', 'Yarowilca ', 'Chavinillo', '101101'),
('HuAnuco', 'Yarowilca ', 'Cahuac', '101102'),
('HuAnuco', 'Yarowilca ', 'Chacabamba', '101103'),
('HuAnuco', 'Yarowilca ', 'Aparicio Pomares', '101104'),
('HuAnuco', 'Yarowilca ', 'Jacas Chico', '101105'),
('HuAnuco', 'Yarowilca ', 'Obas', '101106'),
('HuAnuco', 'Yarowilca ', 'Pampamarca', '101107'),
('HuAnuco', 'Yarowilca ', 'Choras', '101108'),
('Ica', '', '', '110000'),
('Ica', 'Ica ', '', '110100'),
('Ica', 'Ica ', 'Ica', '110101'),
('Ica', 'Ica ', 'La Tinguiña', '110102'),
('Ica', 'Ica ', 'Los Aquijes', '110103'),
('Ica', 'Ica ', 'Ocucaje', '110104'),
('Ica', 'Ica ', 'Pachacutec', '110105'),
('Ica', 'Ica ', 'Parcona', '110106'),
('Ica', 'Ica ', 'Pueblo Nuevo', '110107'),
('Ica', 'Ica ', 'Salas', '110108'),
('Ica', 'Ica ', 'San JosE de Los Molinos', '110109'),
('Ica', 'Ica ', 'San Juan Bautista', '110110'),
('Ica', 'Ica ', 'Santiago', '110111'),
('Ica', 'Ica ', 'Subtanjalla', '110112'),
('Ica', 'Ica ', 'Tate', '110113'),
('Ica', 'Ica ', 'Yauca del Rosario', '110114'),
('Ica', 'Chincha ', '', '110200'),
('Ica', 'Chincha ', 'Chincha Alta', '110201'),
('Ica', 'Chincha ', 'Alto Laran', '110202'),
('Ica', 'Chincha ', 'Chavin', '110203'),
('Ica', 'Chincha ', 'Chincha Baja', '110204'),
('Ica', 'Chincha ', 'El Carmen', '110205'),
('Ica', 'Chincha ', 'Grocio Prado', '110206'),
('Ica', 'Chincha ', 'Pueblo Nuevo', '110207'),
('Ica', 'Chincha ', 'San Juan de Yanac', '110208'),
('Ica', 'Chincha ', 'San Pedro de Huacarpana', '110209'),
('Ica', 'Chincha ', 'Sunampe', '110210'),
('Ica', 'Chincha ', 'Tambo de Mora', '110211'),
('Ica', 'Nazca ', '', '110300'),
('Ica', 'Nazca ', 'Nasca', '110301'),
('Ica', 'Nazca ', 'Changuillo', '110302'),
('Ica', 'Nazca ', 'El Ingenio', '110303'),
('Ica', 'Nazca ', 'Marcona', '110304'),
('Ica', 'Nazca ', 'Vista Alegre', '110305'),
('Ica', 'Palpa ', '', '110400'),
('Ica', 'Palpa ', 'Palpa', '110401'),
('Ica', 'Palpa ', 'Llipata', '110402'),
('Ica', 'Palpa ', 'RIo Grande', '110403'),
('Ica', 'Palpa ', 'Santa Cruz', '110404'),
('Ica', 'Palpa ', 'Tibillo', '110405'),
('Ica', 'Pisco ', '', '110500'),
('Ica', 'Pisco ', 'Pisco', '110501'),
('Ica', 'Pisco ', 'Huancano', '110502'),
('Ica', 'Pisco ', 'Humay', '110503'),
('Ica', 'Pisco ', 'Independencia', '110504'),
('Ica', 'Pisco ', 'Paracas', '110505'),
('Ica', 'Pisco ', 'San AndrEs', '110506'),
('Ica', 'Pisco ', 'San Clemente', '110507'),
('Ica', 'Pisco ', 'Tupac Amaru Inca', '110508'),
('JunIn', '', '', '120000'),
('JunIn', 'Huancayo ', '', '120100'),
('JunIn', 'Huancayo ', 'Huancayo', '120101'),
('JunIn', 'Huancayo ', 'Carhuacallanga', '120104'),
('JunIn', 'Huancayo ', 'Chacapampa', '120105'),
('JunIn', 'Huancayo ', 'Chicche', '120106'),
('JunIn', 'Huancayo ', 'Chilca', '120107'),
('JunIn', 'Huancayo ', 'Chongos Alto', '120108'),
('JunIn', 'Huancayo ', 'Chupuro', '120111'),
('JunIn', 'Huancayo ', 'Colca', '120112'),
('JunIn', 'Huancayo ', 'Cullhuas', '120113'),
('JunIn', 'Huancayo ', 'El Tambo', '120114'),
('JunIn', 'Huancayo ', 'Huacrapuquio', '120116'),
('JunIn', 'Huancayo ', 'Hualhuas', '120117'),
('JunIn', 'Huancayo ', 'Huancan', '120119'),
('JunIn', 'Huancayo ', 'Huasicancha', '120120'),
('JunIn', 'Huancayo ', 'Huayucachi', '120121'),
('JunIn', 'Huancayo ', 'Ingenio', '120122'),
('JunIn', 'Huancayo ', 'Pariahuanca', '120124'),
('JunIn', 'Huancayo ', 'Pilcomayo', '120125'),
('JunIn', 'Huancayo ', 'Pucara', '120126'),
('JunIn', 'Huancayo ', 'Quichuay', '120127'),
('JunIn', 'Huancayo ', 'Quilcas', '120128'),
('JunIn', 'Huancayo ', 'San AgustIn', '120129'),
('JunIn', 'Huancayo ', 'San JerOnimo de Tunan', '120130'),
('JunIn', 'Huancayo ', 'Saño', '120132'),
('JunIn', 'Huancayo ', 'Sapallanga', '120133'),
('JunIn', 'Huancayo ', 'Sicaya', '120134'),
('JunIn', 'Huancayo ', 'Santo Domingo de Acobamba', '120135'),
('JunIn', 'Huancayo ', 'Viques', '120136'),
('JunIn', 'ConcepciOn ', '', '120200'),
('JunIn', 'ConcepciOn ', 'ConcepciOn', '120201'),
('JunIn', 'ConcepciOn ', 'Aco', '120202'),
('JunIn', 'ConcepciOn ', 'Andamarca', '120203'),
('JunIn', 'ConcepciOn ', 'Chambara', '120204'),
('JunIn', 'ConcepciOn ', 'Cochas', '120205'),
('JunIn', 'ConcepciOn ', 'Comas', '120206'),
('JunIn', 'ConcepciOn ', 'HeroInas Toledo', '120207'),
('JunIn', 'ConcepciOn ', 'Manzanares', '120208'),
('JunIn', 'ConcepciOn ', 'Mariscal Castilla', '120209'),
('JunIn', 'ConcepciOn ', 'Matahuasi', '120210'),
('JunIn', 'ConcepciOn ', 'Mito', '120211'),
('JunIn', 'ConcepciOn ', 'Nueve de Julio', '120212'),
('JunIn', 'ConcepciOn ', 'Orcotuna', '120213'),
('JunIn', 'ConcepciOn ', 'San JosE de Quero', '120214'),
('JunIn', 'ConcepciOn ', 'Santa Rosa de Ocopa', '120215'),
('JunIn', 'Chanchamayo ', '', '120300'),
('JunIn', 'Chanchamayo ', 'Chanchamayo', '120301'),
('JunIn', 'Chanchamayo ', 'Perene', '120302'),
('JunIn', 'Chanchamayo ', 'Pichanaqui', '120303'),
('JunIn', 'Chanchamayo ', 'San Luis de Shuaro', '120304'),
('JunIn', 'Chanchamayo ', 'San RamOn', '120305'),
('JunIn', 'Chanchamayo ', 'Vitoc', '120306'),
('JunIn', 'Jauja ', '', '120400'),
('JunIn', 'Jauja ', 'Jauja', '120401'),
('JunIn', 'Jauja ', 'Acolla', '120402'),
('JunIn', 'Jauja ', 'Apata', '120403'),
('JunIn', 'Jauja ', 'Ataura', '120404'),
('JunIn', 'Jauja ', 'Canchayllo', '120405'),
('JunIn', 'Jauja ', 'Curicaca', '120406'),
('JunIn', 'Jauja ', 'El Mantaro', '120407'),
('JunIn', 'Jauja ', 'Huamali', '120408'),
('JunIn', 'Jauja ', 'Huaripampa', '120409'),
('JunIn', 'Jauja ', 'Huertas', '120410'),
('JunIn', 'Jauja ', 'Janjaillo', '120411'),
('JunIn', 'Jauja ', 'JulcAn', '120412'),
('JunIn', 'Jauja ', 'Leonor OrdOñez', '120413'),
('JunIn', 'Jauja ', 'Llocllapampa', '120414'),
('JunIn', 'Jauja ', 'Marco', '120415'),
('JunIn', 'Jauja ', 'Masma', '120416'),
('JunIn', 'Jauja ', 'Masma Chicche', '120417'),
('JunIn', 'Jauja ', 'Molinos', '120418'),
('JunIn', 'Jauja ', 'Monobamba', '120419'),
('JunIn', 'Jauja ', 'Muqui', '120420'),
('JunIn', 'Jauja ', 'Muquiyauyo', '120421'),
('JunIn', 'Jauja ', 'Paca', '120422'),
('JunIn', 'Jauja ', 'Paccha', '120423'),
('JunIn', 'Jauja ', 'Pancan', '120424'),
('JunIn', 'Jauja ', 'Parco', '120425'),
('JunIn', 'Jauja ', 'Pomacancha', '120426'),
('JunIn', 'Jauja ', 'Ricran', '120427'),
('JunIn', 'Jauja ', 'San Lorenzo', '120428'),
('JunIn', 'Jauja ', 'San Pedro de Chunan', '120429'),
('JunIn', 'Jauja ', 'Sausa', '120430'),
('JunIn', 'Jauja ', 'Sincos', '120431'),
('JunIn', 'Jauja ', 'Tunan Marca', '120432'),
('JunIn', 'Jauja ', 'Yauli', '120433'),
('JunIn', 'Jauja ', 'Yauyos', '120434'),
('JunIn', 'JunIn ', '', '120500'),
('JunIn', 'JunIn ', 'Junin', '120501'),
('JunIn', 'JunIn ', 'Carhuamayo', '120502'),
('JunIn', 'JunIn ', 'Ondores', '120503'),
('JunIn', 'JunIn ', 'Ulcumayo', '120504'),
('JunIn', 'Satipo ', '', '120600'),
('JunIn', 'Satipo ', 'Satipo', '120601'),
('JunIn', 'Satipo ', 'Coviriali', '120602'),
('JunIn', 'Satipo ', 'Llaylla', '120603'),
('JunIn', 'Satipo ', 'Mazamari', '120604'),
('JunIn', 'Satipo ', 'Pampa Hermosa', '120605'),
('JunIn', 'Satipo ', 'Pangoa', '120606'),
('JunIn', 'Satipo ', 'RIo Negro', '120607'),
('JunIn', 'Satipo ', 'RIo Tambo', '120608'),
('JunIn', 'Satipo ', 'Vizcatan del Ene', '120609'),
('JunIn', 'Tarma ', '', '120700'),
('JunIn', 'Tarma ', 'Tarma', '120701'),
('JunIn', 'Tarma ', 'Acobamba', '120702'),
('JunIn', 'Tarma ', 'Huaricolca', '120703'),
('JunIn', 'Tarma ', 'Huasahuasi', '120704'),
('JunIn', 'Tarma ', 'La UniOn', '120705'),
('JunIn', 'Tarma ', 'Palca', '120706'),
('JunIn', 'Tarma ', 'Palcamayo', '120707'),
('JunIn', 'Tarma ', 'San Pedro de Cajas', '120708'),
('JunIn', 'Tarma ', 'Tapo', '120709'),
('JunIn', 'Yauli ', '', '120800'),
('JunIn', 'Yauli ', 'La Oroya', '120801'),
('JunIn', 'Yauli ', 'Chacapalpa', '120802'),
('JunIn', 'Yauli ', 'Huay-Huay', '120803'),
('JunIn', 'Yauli ', 'Marcapomacocha', '120804'),
('JunIn', 'Yauli ', 'Morococha', '120805'),
('JunIn', 'Yauli ', 'Paccha', '120806'),
('JunIn', 'Yauli ', 'Santa BArbara de Carhuacayan', '120807'),
('JunIn', 'Yauli ', 'Santa Rosa de Sacco', '120808'),
('JunIn', 'Yauli ', 'Suitucancha', '120809'),
('JunIn', 'Yauli ', 'Yauli', '120810'),
('JunIn', 'Chupaca ', '', '120900'),
('JunIn', 'Chupaca ', 'Chupaca', '120901'),
('JunIn', 'Chupaca ', 'Ahuac', '120902'),
('JunIn', 'Chupaca ', 'Chongos Bajo', '120903'),
('JunIn', 'Chupaca ', 'Huachac', '120904'),
('JunIn', 'Chupaca ', 'Huamancaca Chico', '120905'),
('JunIn', 'Chupaca ', 'San Juan de Iscos', '120906'),
('JunIn', 'Chupaca ', 'San Juan de Jarpa', '120907'),
('JunIn', 'Chupaca ', 'Tres de Diciembre', '120908'),
('JunIn', 'Chupaca ', 'Yanacancha', '120909'),
('La Libertad', '', '', '130000'),
('La Libertad', 'Trujillo ', '', '130100'),
('La Libertad', 'Trujillo ', 'Trujillo', '130101'),
('La Libertad', 'Trujillo ', 'El Porvenir', '130102'),
('La Libertad', 'Trujillo ', 'Florencia de Mora', '130103'),
('La Libertad', 'Trujillo ', 'Huanchaco', '130104'),
('La Libertad', 'Trujillo ', 'La Esperanza', '130105'),
('La Libertad', 'Trujillo ', 'Laredo', '130106'),
('La Libertad', 'Trujillo ', 'Moche', '130107'),
('La Libertad', 'Trujillo ', 'Poroto', '130108'),
('La Libertad', 'Trujillo ', 'Salaverry', '130109'),
('La Libertad', 'Trujillo ', 'Simbal', '130110'),
('La Libertad', 'Trujillo ', 'Victor Larco Herrera', '130111'),
('La Libertad', 'Ascope ', '', '130200'),
('La Libertad', 'Ascope ', 'Ascope', '130201'),
('La Libertad', 'Ascope ', 'Chicama', '130202'),
('La Libertad', 'Ascope ', 'Chocope', '130203'),
('La Libertad', 'Ascope ', 'Magdalena de Cao', '130204'),
('La Libertad', 'Ascope ', 'Paijan', '130205'),
('La Libertad', 'Ascope ', 'RAzuri', '130206'),
('La Libertad', 'Ascope ', 'Santiago de Cao', '130207'),
('La Libertad', 'Ascope ', 'Casa Grande', '130208'),
('La Libertad', 'BolIvar ', '', '130300'),
('La Libertad', 'BolIvar ', 'BolIvar', '130301'),
('La Libertad', 'BolIvar ', 'Bambamarca', '130302'),
('La Libertad', 'BolIvar ', 'Condormarca', '130303'),
('La Libertad', 'BolIvar ', 'Longotea', '130304'),
('La Libertad', 'BolIvar ', 'Uchumarca', '130305'),
('La Libertad', 'BolIvar ', 'Ucuncha', '130306'),
('La Libertad', 'ChepEn ', '', '130400'),
('La Libertad', 'ChepEn ', 'Chepen', '130401'),
('La Libertad', 'ChepEn ', 'Pacanga', '130402'),
('La Libertad', 'ChepEn ', 'Pueblo Nuevo', '130403'),
('La Libertad', 'JulcAn ', '', '130500'),
('La Libertad', 'JulcAn ', 'Julcan', '130501'),
('La Libertad', 'JulcAn ', 'Calamarca', '130502'),
('La Libertad', 'JulcAn ', 'Carabamba', '130503'),
('La Libertad', 'JulcAn ', 'Huaso', '130504'),
('La Libertad', 'Otuzco ', '', '130600'),
('La Libertad', 'Otuzco ', 'Otuzco', '130601'),
('La Libertad', 'Otuzco ', 'Agallpampa', '130602'),
('La Libertad', 'Otuzco ', 'Charat', '130604'),
('La Libertad', 'Otuzco ', 'Huaranchal', '130605'),
('La Libertad', 'Otuzco ', 'La Cuesta', '130606'),
('La Libertad', 'Otuzco ', 'Mache', '130608'),
('La Libertad', 'Otuzco ', 'Paranday', '130610'),
('La Libertad', 'Otuzco ', 'Salpo', '130611'),
('La Libertad', 'Otuzco ', 'Sinsicap', '130613'),
('La Libertad', 'Otuzco ', 'Usquil', '130614'),
('La Libertad', 'Pacasmayo ', '', '130700'),
('La Libertad', 'Pacasmayo ', 'San Pedro de Lloc', '130701'),
('La Libertad', 'Pacasmayo ', 'Guadalupe', '130702'),
('La Libertad', 'Pacasmayo ', 'Jequetepeque', '130703'),
('La Libertad', 'Pacasmayo ', 'Pacasmayo', '130704'),
('La Libertad', 'Pacasmayo ', 'San JosE', '130705'),
('La Libertad', 'Pataz ', '', '130800'),
('La Libertad', 'Pataz ', 'Tayabamba', '130801'),
('La Libertad', 'Pataz ', 'Buldibuyo', '130802'),
('La Libertad', 'Pataz ', 'Chillia', '130803'),
('La Libertad', 'Pataz ', 'Huancaspata', '130804'),
('La Libertad', 'Pataz ', 'Huaylillas', '130805'),
('La Libertad', 'Pataz ', 'Huayo', '130806'),
('La Libertad', 'Pataz ', 'Ongon', '130807'),
('La Libertad', 'Pataz ', 'Parcoy', '130808'),
('La Libertad', 'Pataz ', 'Pataz', '130809'),
('La Libertad', 'Pataz ', 'Pias', '130810'),
('La Libertad', 'Pataz ', 'Santiago de Challas', '130811'),
('La Libertad', 'Pataz ', 'Taurija', '130812'),
('La Libertad', 'Pataz ', 'Urpay', '130813'),
('La Libertad', 'Sánchez Carrión', '', '130900'),
('La Libertad', 'Sánchez Carrión', 'Huamachuco', '130901'),
('La Libertad', 'Sánchez Carrión', 'Chugay', '130902'),
('La Libertad', 'Sánchez Carrión', 'Cochorco', '130903'),
('La Libertad', 'Sánchez Carrión', 'Curgos', '130904'),
('La Libertad', 'Sánchez Carrión', 'Marcabal', '130905'),
('La Libertad', 'Sánchez Carrión', 'Sanagoran', '130906'),
('La Libertad', 'Sánchez Carrión', 'Sarin', '130907'),
('La Libertad', 'Sánchez Carrión', 'Sartimbamba', '130908'),
('La Libertad', 'Santiago de Chuco ', '', '131000'),
('La Libertad', 'Santiago de Chuco ', 'Santiago de Chuco', '131001'),
('La Libertad', 'Santiago de Chuco ', 'Angasmarca', '131002'),
('La Libertad', 'Santiago de Chuco ', 'Cachicadan', '131003'),
('La Libertad', 'Santiago de Chuco ', 'Mollebamba', '131004'),
('La Libertad', 'Santiago de Chuco ', 'Mollepata', '131005'),
('La Libertad', 'Santiago de Chuco ', 'Quiruvilca', '131006'),
('La Libertad', 'Santiago de Chuco ', 'Santa Cruz de Chuca', '131007'),
('La Libertad', 'Santiago de Chuco ', 'Sitabamba', '131008'),
('La Libertad', 'Gran ChimU ', '', '131100'),
('La Libertad', 'Gran ChimU ', 'Cascas', '131101'),
('La Libertad', 'Gran ChimU ', 'Lucma', '131102'),
('La Libertad', 'Gran ChimU ', 'Marmot', '131103'),
('La Libertad', 'Gran ChimU ', 'Sayapullo', '131104'),
('La Libertad', 'VirU ', '', '131200'),
('La Libertad', 'VirU ', 'Viru', '131201'),
('La Libertad', 'VirU ', 'Chao', '131202'),
('La Libertad', 'VirU ', 'Guadalupito', '131203'),
('Lambayeque', '', '', '140000'),
('Lambayeque', 'Chiclayo ', '', '140100'),
('Lambayeque', 'Chiclayo ', 'Chiclayo', '140101'),
('Lambayeque', 'Chiclayo ', 'Chongoyape', '140102'),
('Lambayeque', 'Chiclayo ', 'Eten', '140103'),
('Lambayeque', 'Chiclayo ', 'Eten Puerto', '140104'),
('Lambayeque', 'Chiclayo ', 'JosE Leonardo Ortiz', '140105'),
('Lambayeque', 'Chiclayo ', 'La Victoria', '140106'),
('Lambayeque', 'Chiclayo ', 'Lagunas', '140107'),
('Lambayeque', 'Chiclayo ', 'Monsefu', '140108'),
('Lambayeque', 'Chiclayo ', 'Nueva Arica', '140109'),
('Lambayeque', 'Chiclayo ', 'Oyotun', '140110'),
('Lambayeque', 'Chiclayo ', 'Picsi', '140111'),
('Lambayeque', 'Chiclayo ', 'Pimentel', '140112'),
('Lambayeque', 'Chiclayo ', 'Reque', '140113'),
('Lambayeque', 'Chiclayo ', 'Santa Rosa', '140114'),
('Lambayeque', 'Chiclayo ', 'Saña', '140115'),
('Lambayeque', 'Chiclayo ', 'Cayalti', '140116'),
('Lambayeque', 'Chiclayo ', 'Patapo', '140117'),
('Lambayeque', 'Chiclayo ', 'Pomalca', '140118'),
('Lambayeque', 'Chiclayo ', 'Pucala', '140119'),
('Lambayeque', 'Chiclayo ', 'Tuman', '140120'),
('Lambayeque', 'Ferreñafe ', '', '140200'),
('Lambayeque', 'Ferreñafe ', 'Ferreñafe', '140201'),
('Lambayeque', 'Ferreñafe ', 'Cañaris', '140202'),
('Lambayeque', 'Ferreñafe ', 'Incahuasi', '140203'),
('Lambayeque', 'Ferreñafe ', 'Manuel Antonio Mesones Muro', '140204'),
('Lambayeque', 'Ferreñafe ', 'Pitipo', '140205'),
('Lambayeque', 'Ferreñafe ', 'Pueblo Nuevo', '140206'),
('Lambayeque', 'Lambayeque ', '', '140300'),
('Lambayeque', 'Lambayeque ', 'Lambayeque', '140301'),
('Lambayeque', 'Lambayeque ', 'Chochope', '140302'),
('Lambayeque', 'Lambayeque ', 'Illimo', '140303'),
('Lambayeque', 'Lambayeque ', 'Jayanca', '140304'),
('Lambayeque', 'Lambayeque ', 'Mochumi', '140305'),
('Lambayeque', 'Lambayeque ', 'Morrope', '140306'),
('Lambayeque', 'Lambayeque ', 'Motupe', '140307'),
('Lambayeque', 'Lambayeque ', 'Olmos', '140308'),
('Lambayeque', 'Lambayeque ', 'Pacora', '140309'),
('Lambayeque', 'Lambayeque ', 'Salas', '140310'),
('Lambayeque', 'Lambayeque ', 'San JosE', '140311'),
('Lambayeque', 'Lambayeque ', 'Tucume', '140312'),
('Lima', '', '', '150000'),
('Lima', 'Lima ', '', '150100'),
('Lima', 'Lima ', 'Lima', '150101'),
('Lima', 'Lima ', 'AncOn', '150102'),
('Lima', 'Lima ', 'Ate', '150103'),
('Lima', 'Lima ', 'Barranco', '150104'),
('Lima', 'Lima ', 'Breña', '150105'),
('Lima', 'Lima ', 'Carabayllo', '150106'),
('Lima', 'Lima ', 'Chaclacayo', '150107'),
('Lima', 'Lima ', 'Chorrillos', '150108'),
('Lima', 'Lima ', 'Cieneguilla', '150109'),
('Lima', 'Lima ', 'Comas', '150110'),
('Lima', 'Lima ', 'El Agustino', '150111'),
('Lima', 'Lima ', 'Independencia', '150112'),
('Lima', 'Lima ', 'JesUs MarIa', '150113'),
('Lima', 'Lima ', 'La Molina', '150114'),
('Lima', 'Lima ', 'La Victoria', '150115'),
('Lima', 'Lima ', 'Lince', '150116'),
('Lima', 'Lima ', 'Los Olivos', '150117'),
('Lima', 'Lima ', 'Lurigancho', '150118'),
('Lima', 'Lima ', 'Lurin', '150119'),
('Lima', 'Lima ', 'Magdalena del Mar', '150120'),
('Lima', 'Lima ', 'Pueblo Libre', '150121'),
('Lima', 'Lima ', 'Miraflores', '150122'),
('Lima', 'Lima ', 'Pachacamac', '150123'),
('Lima', 'Lima ', 'Pucusana', '150124'),
('Lima', 'Lima ', 'Puente Piedra', '150125'),
('Lima', 'Lima ', 'Punta Hermosa', '150126'),
('Lima', 'Lima ', 'Punta Negra', '150127'),
('Lima', 'Lima ', 'RImac', '150128'),
('Lima', 'Lima ', 'San Bartolo', '150129'),
('Lima', 'Lima ', 'San Borja', '150130'),
('Lima', 'Lima ', 'San Isidro', '150131'),
('Lima', 'Lima ', 'San Juan de Lurigancho', '150132'),
('Lima', 'Lima ', 'San Juan de Miraflores', '150133'),
('Lima', 'Lima ', 'San Luis', '150134'),
('Lima', 'Lima ', 'San MartIn de Porres', '150135'),
('Lima', 'Lima ', 'San Miguel', '150136'),
('Lima', 'Lima ', 'Santa Anita', '150137'),
('Lima', 'Lima ', 'Santa MarIa del Mar', '150138'),
('Lima', 'Lima ', 'Santa Rosa', '150139'),
('Lima', 'Lima ', 'Santiago de Surco', '150140'),
('Lima', 'Lima ', 'Surquillo', '150141'),
('Lima', 'Lima ', 'Villa El Salvador', '150142'),
('Lima', 'Lima ', 'Villa MarIa del Triunfo', '150143'),
('Lima', 'Barranca ', '', '150200'),
('Lima', 'Barranca ', 'Barranca', '150201'),
('Lima', 'Barranca ', 'Paramonga', '150202'),
('Lima', 'Barranca ', 'Pativilca', '150203'),
('Lima', 'Barranca ', 'Supe', '150204'),
('Lima', 'Barranca ', 'Supe Puerto', '150205'),
('Lima', 'Cajatambo ', '', '150300'),
('Lima', 'Cajatambo ', 'Cajatambo', '150301'),
('Lima', 'Cajatambo ', 'Copa', '150302'),
('Lima', 'Cajatambo ', 'Gorgor', '150303'),
('Lima', 'Cajatambo ', 'Huancapon', '150304'),
('Lima', 'Cajatambo ', 'Manas', '150305'),
('Lima', 'Canta ', '', '150400'),
('Lima', 'Canta ', 'Canta', '150401'),
('Lima', 'Canta ', 'Arahuay', '150402'),
('Lima', 'Canta ', 'Huamantanga', '150403'),
('Lima', 'Canta ', 'Huaros', '150404'),
('Lima', 'Canta ', 'Lachaqui', '150405'),
('Lima', 'Canta ', 'San Buenaventura', '150406'),
('Lima', 'Canta ', 'Santa Rosa de Quives', '150407'),
('Lima', 'Cañete', '', '150500'),
('Lima', 'Cañete', 'San Vicente de Cañete', '150501'),
('Lima', 'Cañete', 'Asia', '150502'),
('Lima', 'Cañete', 'Calango', '150503'),
('Lima', 'Cañete', 'Cerro Azul', '150504'),
('Lima', 'Cañete', 'Chilca', '150505'),
('Lima', 'Cañete', 'Coayllo', '150506'),
('Lima', 'Cañete', 'Imperial', '150507'),
('Lima', 'Cañete', 'Lunahuana', '150508'),
('Lima', 'Cañete', 'Mala', '150509'),
('Lima', 'Cañete', 'Nuevo Imperial', '150510'),
('Lima', 'Cañete', 'Pacaran', '150511'),
('Lima', 'Cañete', 'Quilmana', '150512'),
('Lima', 'Cañete', 'San Antonio', '150513'),
('Lima', 'Cañete', 'San Luis', '150514'),
('Lima', 'Cañete', 'Santa Cruz de Flores', '150515'),
('Lima', 'Cañete', 'ZUñiga', '150516'),
('Lima', 'Huaral ', '', '150600'),
('Lima', 'Huaral ', 'Huaral', '150601'),
('Lima', 'Huaral ', 'Atavillos Alto', '150602'),
('Lima', 'Huaral ', 'Atavillos Bajo', '150603'),
('Lima', 'Huaral ', 'Aucallama', '150604'),
('Lima', 'Huaral ', 'Chancay', '150605'),
('Lima', 'Huaral ', 'Ihuari', '150606'),
('Lima', 'Huaral ', 'Lampian', '150607'),
('Lima', 'Huaral ', 'Pacaraos', '150608'),
('Lima', 'Huaral ', 'San Miguel de Acos', '150609'),
('Lima', 'Huaral ', 'Santa Cruz de Andamarca', '150610'),
('Lima', 'Huaral ', 'Sumbilca', '150611'),
('Lima', 'Huaral ', 'Veintisiete de Noviembre', '150612'),
('Lima', 'HuarochirI ', '', '150700'),
('Lima', 'HuarochirI ', 'Matucana', '150701'),
('Lima', 'HuarochirI ', 'Antioquia', '150702'),
('Lima', 'HuarochirI ', 'Callahuanca', '150703'),
('Lima', 'HuarochirI ', 'Carampoma', '150704'),
('Lima', 'HuarochirI ', 'Chicla', '150705'),
('Lima', 'HuarochirI ', 'Cuenca', '150706'),
('Lima', 'HuarochirI ', 'Huachupampa', '150707'),
('Lima', 'HuarochirI ', 'Huanza', '150708'),
('Lima', 'HuarochirI ', 'Huarochiri', '150709'),
('Lima', 'HuarochirI ', 'Lahuaytambo', '150710'),
('Lima', 'HuarochirI ', 'Langa', '150711'),
('Lima', 'HuarochirI ', 'Laraos', '150712'),
('Lima', 'HuarochirI ', 'Mariatana', '150713'),
('Lima', 'HuarochirI ', 'Ricardo Palma', '150714'),
('Lima', 'HuarochirI ', 'San AndrEs de Tupicocha', '150715'),
('Lima', 'HuarochirI ', 'San Antonio', '150716'),
('Lima', 'HuarochirI ', 'San BartolomE', '150717'),
('Lima', 'HuarochirI ', 'San Damian', '150718'),
('Lima', 'HuarochirI ', 'San Juan de Iris', '150719'),
('Lima', 'HuarochirI ', 'San Juan de Tantaranche', '150720'),
('Lima', 'HuarochirI ', 'San Lorenzo de Quinti', '150721'),
('Lima', 'HuarochirI ', 'San Mateo', '150722'),
('Lima', 'HuarochirI ', 'San Mateo de Otao', '150723'),
('Lima', 'HuarochirI ', 'San Pedro de Casta', '150724'),
('Lima', 'HuarochirI ', 'San Pedro de Huancayre', '150725'),
('Lima', 'HuarochirI ', 'Sangallaya', '150726'),
('Lima', 'HuarochirI ', 'Santa Cruz de Cocachacra', '150727'),
('Lima', 'HuarochirI ', 'Santa Eulalia', '150728'),
('Lima', 'HuarochirI ', 'Santiago de Anchucaya', '150729'),
('Lima', 'HuarochirI ', 'Santiago de Tuna', '150730'),
('Lima', 'HuarochirI ', 'Santo Domingo de Los Olleros', '150731'),
('Lima', 'HuarochirI ', 'Surco', '150732'),
('Lima', 'Huaura ', '', '150800'),
('Lima', 'Huaura ', 'Huacho', '150801'),
('Lima', 'Huaura ', 'Ambar', '150802'),
('Lima', 'Huaura ', 'Caleta de Carquin', '150803'),
('Lima', 'Huaura ', 'Checras', '150804'),
('Lima', 'Huaura ', 'Hualmay', '150805'),
('Lima', 'Huaura ', 'Huaura', '150806'),
('Lima', 'Huaura ', 'Leoncio Prado', '150807'),
('Lima', 'Huaura ', 'Paccho', '150808'),
('Lima', 'Huaura ', 'Santa Leonor', '150809'),
('Lima', 'Huaura ', 'Santa MarIa', '150810'),
('Lima', 'Huaura ', 'Sayan', '150811'),
('Lima', 'Huaura ', 'Vegueta', '150812'),
('Lima', 'OyOn ', '', '150900'),
('Lima', 'OyOn ', 'Oyon', '150901'),
('Lima', 'OyOn ', 'Andajes', '150902'),
('Lima', 'OyOn ', 'Caujul', '150903'),
('Lima', 'OyOn ', 'Cochamarca', '150904'),
('Lima', 'OyOn ', 'Navan', '150905'),
('Lima', 'OyOn ', 'Pachangara', '150906'),
('Lima', 'Yauyos ', '', '151000'),
('Lima', 'Yauyos ', 'Yauyos', '151001'),
('Lima', 'Yauyos ', 'Alis', '151002'),
('Lima', 'Yauyos ', 'Allauca', '151003'),
('Lima', 'Yauyos ', 'Ayaviri', '151004'),
('Lima', 'Yauyos ', 'AzAngaro', '151005'),
('Lima', 'Yauyos ', 'Cacra', '151006'),
('Lima', 'Yauyos ', 'Carania', '151007'),
('Lima', 'Yauyos ', 'Catahuasi', '151008'),
('Lima', 'Yauyos ', 'Chocos', '151009'),
('Lima', 'Yauyos ', 'Cochas', '151010'),
('Lima', 'Yauyos ', 'Colonia', '151011'),
('Lima', 'Yauyos ', 'Hongos', '151012'),
('Lima', 'Yauyos ', 'Huampara', '151013'),
('Lima', 'Yauyos ', 'Huancaya', '151014'),
('Lima', 'Yauyos ', 'Huangascar', '151015'),
('Lima', 'Yauyos ', 'Huantan', '151016'),
('Lima', 'Yauyos ', 'Huañec', '151017'),
('Lima', 'Yauyos ', 'Laraos', '151018'),
('Lima', 'Yauyos ', 'Lincha', '151019'),
('Lima', 'Yauyos ', 'Madean', '151020'),
('Lima', 'Yauyos ', 'Miraflores', '151021'),
('Lima', 'Yauyos ', 'Omas', '151022'),
('Lima', 'Yauyos ', 'Putinza', '151023'),
('Lima', 'Yauyos ', 'Quinches', '151024'),
('Lima', 'Yauyos ', 'Quinocay', '151025'),
('Lima', 'Yauyos ', 'San JoaquIn', '151026'),
('Lima', 'Yauyos ', 'San Pedro de Pilas', '151027'),
('Lima', 'Yauyos ', 'Tanta', '151028'),
('Lima', 'Yauyos ', 'Tauripampa', '151029'),
('Lima', 'Yauyos ', 'Tomas', '151030'),
('Lima', 'Yauyos ', 'Tupe', '151031'),
('Lima', 'Yauyos ', 'Viñac', '151032'),
('Lima', 'Yauyos ', 'Vitis', '151033'),
('Loreto', '', '', '160000'),
('Loreto', 'Maynas ', '', '160100'),
('Loreto', 'Maynas ', 'Iquitos', '160101'),
('Loreto', 'Maynas ', 'Alto Nanay', '160102'),
('Loreto', 'Maynas ', 'Fernando Lores', '160103'),
('Loreto', 'Maynas ', 'Indiana', '160104'),
('Loreto', 'Maynas ', 'Las Amazonas', '160105'),
('Loreto', 'Maynas ', 'Mazan', '160106'),
('Loreto', 'Maynas ', 'Napo', '160107'),
('Loreto', 'Maynas ', 'Punchana', '160108'),
('Loreto', 'Maynas ', 'Torres Causana', '160110'),
('Loreto', 'Maynas ', 'BelEn', '160112'),
('Loreto', 'Maynas ', 'San Juan Bautista', '160113'),
('Loreto', 'Alto Amazonas ', '', '160200'),
('Loreto', 'Alto Amazonas ', 'Yurimaguas', '160201'),
('Loreto', 'Alto Amazonas ', 'Balsapuerto', '160202'),
('Loreto', 'Alto Amazonas ', 'Jeberos', '160205'),
('Loreto', 'Alto Amazonas ', 'Lagunas', '160206'),
('Loreto', 'Alto Amazonas ', 'Santa Cruz', '160210'),
('Loreto', 'Alto Amazonas ', 'Teniente Cesar LOpez Rojas', '160211'),
('Loreto', 'Loreto ', '', '160300'),
('Loreto', 'Loreto ', 'Nauta', '160301'),
('Loreto', 'Loreto ', 'Parinari', '160302'),
('Loreto', 'Loreto ', 'Tigre', '160303'),
('Loreto', 'Loreto ', 'Trompeteros', '160304'),
('Loreto', 'Loreto ', 'Urarinas', '160305'),
('Loreto', 'Mariscal RamOn Castilla ', '', '160400'),
('Loreto', 'Mariscal RamOn Castilla ', 'RamOn Castilla', '160401'),
('Loreto', 'Mariscal RamOn Castilla ', 'Pebas', '160402'),
('Loreto', 'Mariscal RamOn Castilla ', 'Yavari', '160403'),
('Loreto', 'Mariscal RamOn Castilla ', 'San Pablo', '160404'),
('Loreto', 'Requena ', '', '160500'),
('Loreto', 'Requena ', 'Requena', '160501'),
('Loreto', 'Requena ', 'Alto Tapiche', '160502'),
('Loreto', 'Requena ', 'Capelo', '160503'),
('Loreto', 'Requena ', 'Emilio San MartIn', '160504'),
('Loreto', 'Requena ', 'Maquia', '160505'),
('Loreto', 'Requena ', 'Puinahua', '160506'),
('Loreto', 'Requena ', 'Saquena', '160507'),
('Loreto', 'Requena ', 'Soplin', '160508'),
('Loreto', 'Requena ', 'Tapiche', '160509'),
('Loreto', 'Requena ', 'Jenaro Herrera', '160510'),
('Loreto', 'Requena ', 'Yaquerana', '160511'),
('Loreto', 'Ucayali ', '', '160600'),
('Loreto', 'Ucayali ', 'Contamana', '160601'),
('Loreto', 'Ucayali ', 'Inahuaya', '160602'),
('Loreto', 'Ucayali ', 'Padre MArquez', '160603'),
('Loreto', 'Ucayali ', 'Pampa Hermosa', '160604'),
('Loreto', 'Ucayali ', 'Sarayacu', '160605'),
('Loreto', 'Ucayali ', 'Vargas Guerra', '160606'),
('Loreto', 'Datem del Marañón', '', '160700'),
('Loreto', 'Datem del Marañón ', 'Barranca', '160701'),
('Loreto', 'Datem del Marañón', 'Cahuapanas', '160702'),
('Loreto', 'Datem del Marañón', 'Manseriche', '160703'),
('Loreto', 'Datem del Marañón', 'Morona', '160704'),
('Loreto', 'Datem del Marañón', 'Pastaza', '160705'),
('Loreto', 'Datem del Marañón', 'Andoas', '160706'),
('Loreto', 'Putumayo', '', '160800'),
('Loreto', 'Putumayo', 'Putumayo', '160801'),
('Loreto', 'Putumayo', 'Rosa Panduro', '160802'),
('Loreto', 'Putumayo', 'Teniente Manuel Clavero', '160803'),
('Loreto', 'Putumayo', 'Yaguas', '160804'),
('Madre de Dios', '', '', '170000'),
('Madre de Dios', 'Tambopata ', '', '170100'),
('Madre de Dios', 'Tambopata ', 'Tambopata', '170101'),
('Madre de Dios', 'Tambopata ', 'Inambari', '170102'),
('Madre de Dios', 'Tambopata ', 'Las Piedras', '170103'),
('Madre de Dios', 'Tambopata ', 'Laberinto', '170104'),
('Madre de Dios', 'Manu ', '', '170200'),
('Madre de Dios', 'Manu ', 'Manu', '170201'),
('Madre de Dios', 'Manu ', 'Fitzcarrald', '170202'),
('Madre de Dios', 'Manu ', 'Madre de Dios', '170203'),
('Madre de Dios', 'Manu ', 'Huepetuhe', '170204'),
('Madre de Dios', 'Tahuamanu ', '', '170300'),
('Madre de Dios', 'Tahuamanu ', 'Iñapari', '170301'),
('Madre de Dios', 'Tahuamanu ', 'Iberia', '170302'),
('Madre de Dios', 'Tahuamanu ', 'Tahuamanu', '170303'),
('Moquegua', '', '', '180000'),
('Moquegua', 'Mariscal Nieto ', '', '180100'),
('Moquegua', 'Mariscal Nieto ', 'Moquegua', '180101'),
('Moquegua', 'Mariscal Nieto ', 'Carumas', '180102'),
('Moquegua', 'Mariscal Nieto ', 'Cuchumbaya', '180103'),
('Moquegua', 'Mariscal Nieto ', 'Samegua', '180104'),
('Moquegua', 'Mariscal Nieto ', 'San CristObal', '180105'),
('Moquegua', 'Mariscal Nieto ', 'Torata', '180106'),
('Moquegua', 'General SAnchez Cerro ', '', '180200'),
('Moquegua', 'General SAnchez Cerro ', 'Omate', '180201'),
('Moquegua', 'General SAnchez Cerro ', 'Chojata', '180202'),
('Moquegua', 'General SAnchez Cerro ', 'Coalaque', '180203'),
('Moquegua', 'General SAnchez Cerro ', 'Ichuña', '180204'),
('Moquegua', 'General SAnchez Cerro ', 'La Capilla', '180205'),
('Moquegua', 'General SAnchez Cerro ', 'Lloque', '180206'),
('Moquegua', 'General SAnchez Cerro ', 'Matalaque', '180207'),
('Moquegua', 'General SAnchez Cerro ', 'Puquina', '180208'),
('Moquegua', 'General SAnchez Cerro ', 'Quinistaquillas', '180209'),
('Moquegua', 'General SAnchez Cerro ', 'Ubinas', '180210'),
('Moquegua', 'General SAnchez Cerro ', 'Yunga', '180211'),
('Moquegua', 'Ilo ', '', '180300'),
('Moquegua', 'Ilo ', 'Ilo', '180301'),
('Moquegua', 'Ilo ', 'El Algarrobal', '180302'),
('Moquegua', 'Ilo ', 'Pacocha', '180303'),
('Pasco', '', '', '190000'),
('Pasco', 'Pasco ', '', '190100'),
('Pasco', 'Pasco ', 'Chaupimarca', '190101'),
('Pasco', 'Pasco ', 'Huachon', '190102'),
('Pasco', 'Pasco ', 'Huariaca', '190103'),
('Pasco', 'Pasco ', 'Huayllay', '190104'),
('Pasco', 'Pasco ', 'Ninacaca', '190105'),
('Pasco', 'Pasco ', 'Pallanchacra', '190106'),
('Pasco', 'Pasco ', 'Paucartambo', '190107'),
('Pasco', 'Pasco ', 'San Francisco de AsIs de Yarusyacan', '190108'),
('Pasco', 'Pasco ', 'Simon BolIvar', '190109'),
('Pasco', 'Pasco ', 'Ticlacayan', '190110'),
('Pasco', 'Pasco ', 'Tinyahuarco', '190111'),
('Pasco', 'Pasco ', 'Vicco', '190112'),
('Pasco', 'Pasco ', 'Yanacancha', '190113'),
('Pasco', 'Daniel Alcides CarriOn ', '', '190200'),
('Pasco', 'Daniel Alcides CarriOn ', 'Yanahuanca', '190201'),
('Pasco', 'Daniel Alcides CarriOn ', 'Chacayan', '190202'),
('Pasco', 'Daniel Alcides CarriOn ', 'Goyllarisquizga', '190203'),
('Pasco', 'Daniel Alcides CarriOn ', 'Paucar', '190204'),
('Pasco', 'Daniel Alcides CarriOn ', 'San Pedro de Pillao', '190205'),
('Pasco', 'Daniel Alcides CarriOn ', 'Santa Ana de Tusi', '190206'),
('Pasco', 'Daniel Alcides CarriOn ', 'Tapuc', '190207'),
('Pasco', 'Daniel Alcides CarriOn ', 'Vilcabamba', '190208'),
('Pasco', 'Oxapampa ', '', '190300'),
('Pasco', 'Oxapampa ', 'Oxapampa', '190301'),
('Pasco', 'Oxapampa ', 'Chontabamba', '190302'),
('Pasco', 'Oxapampa ', 'Huancabamba', '190303'),
('Pasco', 'Oxapampa ', 'Palcazu', '190304'),
('Pasco', 'Oxapampa ', 'Pozuzo', '190305'),
('Pasco', 'Oxapampa ', 'Puerto BermUdez', '190306'),
('Pasco', 'Oxapampa ', 'Villa Rica', '190307'),
('Pasco', 'Oxapampa ', 'ConstituciOn', '190308'),
('Piura', '', '', '200000'),
('Piura', 'Piura ', '', '200100'),
('Piura', 'Piura ', 'Piura', '200101'),
('Piura', 'Piura ', 'Castilla', '200104'),
('Piura', 'Piura ', 'Atacaos', '200105'),
('Piura', 'Piura ', 'Cura Mori', '200107'),
('Piura', 'Piura ', 'El Tallan', '200108'),
('Piura', 'Piura ', 'La Arena', '200109'),
('Piura', 'Piura ', 'La UniOn', '200110'),
('Piura', 'Piura ', 'Las Lomas', '200111'),
('Piura', 'Piura ', 'Tambo Grande', '200114'),
('Piura', 'Piura ', 'Veintiseis de Octubre', '200115'),
('Piura', 'Ayabaca ', '', '200200'),
('Piura', 'Ayabaca ', 'Ayabaca', '200201'),
('Piura', 'Ayabaca ', 'Frias', '200202'),
('Piura', 'Ayabaca ', 'Jilili', '200203'),
('Piura', 'Ayabaca ', 'Lagunas', '200204'),
('Piura', 'Ayabaca ', 'Montero', '200205'),
('Piura', 'Ayabaca ', 'Pacaipampa', '200206'),
('Piura', 'Ayabaca ', 'Paimas', '200207'),
('Piura', 'Ayabaca ', 'Sapillica', '200208'),
('Piura', 'Ayabaca ', 'Sicchez', '200209'),
('Piura', 'Ayabaca ', 'Suyo', '200210'),
('Piura', 'Huancabamba ', '', '200300'),
('Piura', 'Huancabamba ', 'Huancabamba', '200301'),
('Piura', 'Huancabamba ', 'Canchaque', '200302'),
('Piura', 'Huancabamba ', 'El Carmen de la Frontera', '200303'),
('Piura', 'Huancabamba ', 'Huarmaca', '200304'),
('Piura', 'Huancabamba ', 'Lalaquiz', '200305'),
('Piura', 'Huancabamba ', 'San Miguel de El Faique', '200306'),
('Piura', 'Huancabamba ', 'Sondor', '200307'),
('Piura', 'Huancabamba ', 'Sondorillo', '200308'),
('Piura', 'MorropOn ', '', '200400'),
('Piura', 'MorropOn ', 'Chulucanas', '200401'),
('Piura', 'MorropOn ', 'Buenos Aires', '200402'),
('Piura', 'MorropOn ', 'Chalaco', '200403'),
('Piura', 'MorropOn ', 'La Matanza', '200404'),
('Piura', 'MorropOn ', 'Morropon', '200405'),
('Piura', 'MorropOn ', 'Salitral', '200406'),
('Piura', 'MorropOn ', 'San Juan de Bigote', '200407'),
('Piura', 'MorropOn ', 'Santa Catalina de Mossa', '200408'),
('Piura', 'MorropOn ', 'Santo Domingo', '200409'),
('Piura', 'MorropOn ', 'Yamango', '200410'),
('Piura', 'Paita ', '', '200500'),
('Piura', 'Paita ', 'Paita', '200501'),
('Piura', 'Paita ', 'Amotape', '200502'),
('Piura', 'Paita ', 'Arenal', '200503'),
('Piura', 'Paita ', 'Colan', '200504'),
('Piura', 'Paita ', 'La Huaca', '200505'),
('Piura', 'Paita ', 'Tamarindo', '200506'),
('Piura', 'Paita ', 'Vichayal', '200507'),
('Piura', 'Sullana ', '', '200600'),
('Piura', 'Sullana ', 'Sullana', '200601'),
('Piura', 'Sullana ', 'Bellavista', '200602'),
('Piura', 'Sullana ', 'Ignacio Escudero', '200603'),
('Piura', 'Sullana ', 'Lancones', '200604'),
('Piura', 'Sullana ', 'Marcavelica', '200605'),
('Piura', 'Sullana ', 'Miguel Checa', '200606'),
('Piura', 'Sullana ', 'Querecotillo', '200607'),
('Piura', 'Sullana ', 'Salitral', '200608'),
('Piura', 'Talara ', '', '200700'),
('Piura', 'Talara ', 'Pariñas', '200701'),
('Piura', 'Talara ', 'El Alto', '200702'),
('Piura', 'Talara ', 'La Brea', '200703'),
('Piura', 'Talara ', 'Lobitos', '200704'),
('Piura', 'Talara ', 'Los Organos', '200705'),
('Piura', 'Talara ', 'Mancora', '200706'),
('Piura', 'Sechura ', '', '200800'),
('Piura', 'Sechura ', 'Sechura', '200801'),
('Piura', 'Sechura ', 'Bellavista de la UniOn', '200802'),
('Piura', 'Sechura ', 'Bernal', '200803'),
('Piura', 'Sechura ', 'Cristo Nos Valga', '200804'),
('Piura', 'Sechura ', 'Vice', '200805'),
('Piura', 'Sechura ', 'Rinconada Llicuar', '200806'),
('Puno', '', '', '210000'),
('Puno', 'Puno ', '', '210100'),
('Puno', 'Puno ', 'Puno', '210101'),
('Puno', 'Puno ', 'Acora', '210102'),
('Puno', 'Puno ', 'Amantani', '210103'),
('Puno', 'Puno ', 'Atuncolla', '210104'),
('Puno', 'Puno ', 'Capachica', '210105'),
('Puno', 'Puno ', 'Chucuito', '210106'),
('Puno', 'Puno ', 'Coata', '210107'),
('Puno', 'Puno ', 'Huata', '210108'),
('Puno', 'Puno ', 'Mañazo', '210109'),
('Puno', 'Puno ', 'Paucarcolla', '210110'),
('Puno', 'Puno ', 'Pichacani', '210111'),
('Puno', 'Puno ', 'Plateria', '210112'),
('Puno', 'Puno ', 'San Antonio', '210113'),
('Puno', 'Puno ', 'Tiquillaca', '210114'),
('Puno', 'Puno ', 'Vilque', '210115'),
('Puno', 'AzAngaro ', '', '210200'),
('Puno', 'AzAngaro ', 'AzAngaro', '210201'),
('Puno', 'AzAngaro ', 'Achaya', '210202'),
('Puno', 'AzAngaro ', 'Arapa', '210203'),
('Puno', 'AzAngaro ', 'Asillo', '210204'),
('Puno', 'AzAngaro ', 'Caminaca', '210205'),
('Puno', 'AzAngaro ', 'Chupa', '210206'),
('Puno', 'AzAngaro ', 'JosE Domingo Choquehuanca', '210207'),
('Puno', 'AzAngaro ', 'Muñani', '210208'),
('Puno', 'AzAngaro ', 'Potoni', '210209'),
('Puno', 'AzAngaro ', 'Saman', '210210'),
('Puno', 'AzAngaro ', 'San Anton', '210211'),
('Puno', 'AzAngaro ', 'San JosE', '210212'),
('Puno', 'AzAngaro ', 'San Juan de Salinas', '210213'),
('Puno', 'AzAngaro ', 'Santiago de Pupuja', '210214'),
('Puno', 'AzAngaro ', 'Tirapata', '210215'),
('Puno', 'Carabaya ', '', '210300'),
('Puno', 'Carabaya ', 'Macusani', '210301'),
('Puno', 'Carabaya ', 'Ajoyani', '210302'),
('Puno', 'Carabaya ', 'Ayapata', '210303'),
('Puno', 'Carabaya ', 'Coasa', '210304'),
('Puno', 'Carabaya ', 'Corani', '210305'),
('Puno', 'Carabaya ', 'Crucero', '210306'),
('Puno', 'Carabaya ', 'Ituata', '210307'),
('Puno', 'Carabaya ', 'Ollachea', '210308'),
('Puno', 'Carabaya ', 'San Gaban', '210309'),
('Puno', 'Carabaya ', 'Usicayos', '210310'),
('Puno', 'Chucuito ', '', '210400'),
('Puno', 'Chucuito ', 'Juli', '210401'),
('Puno', 'Chucuito ', 'Desaguadero', '210402'),
('Puno', 'Chucuito ', 'Huacullani', '210403'),
('Puno', 'Chucuito ', 'Kelluyo', '210404'),
('Puno', 'Chucuito ', 'Pisacoma', '210405'),
('Puno', 'Chucuito ', 'Pomata', '210406'),
('Puno', 'Chucuito ', 'Zepita', '210407'),
('Puno', 'El Collao ', '', '210500'),
('Puno', 'El Collao ', 'Ilave', '210501'),
('Puno', 'El Collao ', 'Capazo', '210502'),
('Puno', 'El Collao ', 'Pilcuyo', '210503'),
('Puno', 'El Collao ', 'Santa Rosa', '210504'),
('Puno', 'El Collao ', 'Conduriri', '210505'),
('Puno', 'HuancanE ', '', '210600'),
('Puno', 'HuancanE ', 'Huancane', '210601'),
('Puno', 'HuancanE ', 'Cojata', '210602'),
('Puno', 'HuancanE ', 'Huatasani', '210603'),
('Puno', 'HuancanE ', 'Inchupalla', '210604'),
('Puno', 'HuancanE ', 'Pusi', '210605'),
('Puno', 'HuancanE ', 'Rosaspata', '210606'),
('Puno', 'HuancanE ', 'Taraco', '210607'),
('Puno', 'HuancanE ', 'Vilque Chico', '210608'),
('Puno', 'Lampa ', '', '210700'),
('Puno', 'Lampa ', 'Lampa', '210701'),
('Puno', 'Lampa ', 'Cabanilla', '210702'),
('Puno', 'Lampa ', 'Calapuja', '210703'),
('Puno', 'Lampa ', 'Nicasio', '210704'),
('Puno', 'Lampa ', 'Ocuviri', '210705'),
('Puno', 'Lampa ', 'Palca', '210706'),
('Puno', 'Lampa ', 'Paratia', '210707'),
('Puno', 'Lampa ', 'Pucara', '210708'),
('Puno', 'Lampa ', 'Santa Lucia', '210709'),
('Puno', 'Lampa ', 'Vilavila', '210710'),
('Puno', 'Melgar ', '', '210800'),
('Puno', 'Melgar ', 'Ayaviri', '210801'),
('Puno', 'Melgar ', 'Antauta', '210802'),
('Puno', 'Melgar ', 'Cupi', '210803'),
('Puno', 'Melgar ', 'Llalli', '210804'),
('Puno', 'Melgar ', 'Macari', '210805'),
('Puno', 'Melgar ', 'Nuñoa', '210806'),
('Puno', 'Melgar ', 'Orurillo', '210807'),
('Puno', 'Melgar ', 'Santa Rosa', '210808'),
('Puno', 'Melgar ', 'Umachiri', '210809'),
('Puno', 'Moho ', '', '210900'),
('Puno', 'Moho ', 'Moho', '210901'),
('Puno', 'Moho ', 'Conima', '210902'),
('Puno', 'Moho ', 'Huayrapata', '210903'),
('Puno', 'Moho ', 'Tilali', '210904'),
('Puno', 'San Antonio de Putina ', '', '211000'),
('Puno', 'San Antonio de Putina ', 'Putina', '211001'),
('Puno', 'San Antonio de Putina ', 'Ananea', '211002'),
('Puno', 'San Antonio de Putina ', 'Pedro Vilca Apaza', '211003'),
('Puno', 'San Antonio de Putina ', 'Quilcapuncu', '211004'),
('Puno', 'San Antonio de Putina ', 'Sina', '211005'),
('Puno', 'San RomAn ', '', '211100'),
('Puno', 'San RomAn ', 'Juliaca', '211101'),
('Puno', 'San RomAn ', 'Cabana', '211102'),
('Puno', 'San RomAn ', 'Cabanillas', '211103'),
('Puno', 'San RomAn ', 'Caracoto', '211104'),
('Puno', 'Sandia ', '', '211200'),
('Puno', 'Sandia ', 'Sandia', '211201'),
('Puno', 'Sandia ', 'Cuyocuyo', '211202'),
('Puno', 'Sandia ', 'Limbani', '211203'),
('Puno', 'Sandia ', 'Patambuco', '211204'),
('Puno', 'Sandia ', 'Phara', '211205'),
('Puno', 'Sandia ', 'Quiaca', '211206'),
('Puno', 'Sandia ', 'San Juan del Oro', '211207'),
('Puno', 'Sandia ', 'Yanahuaya', '211208'),
('Puno', 'Sandia ', 'Alto Inambari', '211209'),
('Puno', 'Sandia ', 'San Pedro de Putina Punco', '211210'),
('Puno', 'Yunguyo ', '', '211300'),
('Puno', 'Yunguyo ', 'Yunguyo', '211301'),
('Puno', 'Yunguyo ', 'Anapia', '211302'),
('Puno', 'Yunguyo ', 'Copani', '211303'),
('Puno', 'Yunguyo ', 'Cuturapi', '211304'),
('Puno', 'Yunguyo ', 'Ollaraya', '211305'),
('Puno', 'Yunguyo ', 'Tinicachi', '211306'),
('Puno', 'Yunguyo ', 'Unicachi', '211307'),
('San MartIn', '', '', '220000'),
('San MartIn', 'Moyobamba ', '', '220100'),
('San MartIn', 'Moyobamba ', 'Moyobamba', '220101'),
('San MartIn', 'Moyobamba ', 'Calzada', '220102'),
('San MartIn', 'Moyobamba ', 'Habana', '220103'),
('San MartIn', 'Moyobamba ', 'Jepelacio', '220104'),
('San MartIn', 'Moyobamba ', 'Soritor', '220105'),
('San MartIn', 'Moyobamba ', 'Yantalo', '220106'),
('San MartIn', 'Bellavista ', '', '220200'),
('San MartIn', 'Bellavista ', 'Bellavista', '220201'),
('San MartIn', 'Bellavista ', 'Alto Biavo', '220202'),
('San MartIn', 'Bellavista ', 'Bajo Biavo', '220203'),
('San MartIn', 'Bellavista ', 'Huallaga', '220204'),
('San MartIn', 'Bellavista ', 'San Pablo', '220205'),
('San MartIn', 'Bellavista ', 'San Rafael', '220206'),
('San MartIn', 'El Dorado ', '', '220300'),
('San MartIn', 'El Dorado ', 'San JosE de Sisa', '220301'),
('San MartIn', 'El Dorado ', 'Agua Blanca', '220302'),
('San MartIn', 'El Dorado ', 'San MartIn', '220303'),
('San MartIn', 'El Dorado ', 'Santa Rosa', '220304'),
('San MartIn', 'El Dorado ', 'Shatoja', '220305'),
('San MartIn', 'Huallaga ', '', '220400'),
('San MartIn', 'Huallaga ', 'Saposoa', '220401'),
('San MartIn', 'Huallaga ', 'Alto Saposoa', '220402'),
('San MartIn', 'Huallaga ', 'El EslabOn', '220403'),
('San MartIn', 'Huallaga ', 'Piscoyacu', '220404'),
('San MartIn', 'Huallaga ', 'Sacanche', '220405'),
('San MartIn', 'Huallaga ', 'Tingo de Saposoa', '220406'),
('San MartIn', 'Lamas ', '', '220500'),
('San MartIn', 'Lamas ', 'Lamas', '220501'),
('San MartIn', 'Lamas ', 'Alonso de Alvarado', '220502'),
('San MartIn', 'Lamas ', 'Barranquita', '220503'),
('San MartIn', 'Lamas ', 'Caynarachi', '220504'),
('San MartIn', 'Lamas ', 'Cuñumbuqui', '220505'),
('San MartIn', 'Lamas ', 'Pinto Recodo', '220506'),
('San MartIn', 'Lamas ', 'Rumisapa', '220507'),
('San MartIn', 'Lamas ', 'San Roque de Cumbaza', '220508'),
('San MartIn', 'Lamas ', 'Shanao', '220509'),
('San MartIn', 'Lamas ', 'Tabalosos', '220510'),
('San MartIn', 'Lamas ', 'Zapatero', '220511'),
('San MartIn', 'Mariscal CAceres ', '', '220600'),
('San MartIn', 'Mariscal CAceres ', 'JuanjuI', '220601'),
('San MartIn', 'Mariscal CAceres ', 'Campanilla', '220602'),
('San MartIn', 'Mariscal CAceres ', 'Huicungo', '220603'),
('San MartIn', 'Mariscal CAceres ', 'Pachiza', '220604'),
('San MartIn', 'Mariscal CAceres ', 'Pajarillo', '220605'),
('San MartIn', 'Picota ', '', '220700'),
('San MartIn', 'Picota ', 'Picota', '220701'),
('San MartIn', 'Picota ', 'Buenos Aires', '220702'),
('San MartIn', 'Picota ', 'Caspisapa', '220703'),
('San MartIn', 'Picota ', 'Pilluana', '220704'),
('San MartIn', 'Picota ', 'Pucacaca', '220705'),
('San MartIn', 'Picota ', 'San CristObal', '220706'),
('San MartIn', 'Picota ', 'San HilariOn', '220707'),
('San MartIn', 'Picota ', 'Shamboyacu', '220708'),
('San MartIn', 'Picota ', 'Tingo de Ponasa', '220709'),
('San MartIn', 'Picota ', 'Tres Unidos', '220710'),
('San MartIn', 'Rioja ', '', '220800'),
('San MartIn', 'Rioja ', 'Rioja', '220801'),
('San MartIn', 'Rioja ', 'Awajun', '220802'),
('San MartIn', 'Rioja ', 'ElIas Soplin Vargas', '220803'),
('San MartIn', 'Rioja ', 'Nueva Cajamarca', '220804'),
('San MartIn', 'Rioja ', 'Pardo Miguel', '220805'),
('San MartIn', 'Rioja ', 'Posic', '220806'),
('San MartIn', 'Rioja ', 'San Fernando', '220807'),
('San MartIn', 'Rioja ', 'Yorongos', '220808'),
('San MartIn', 'Rioja ', 'Yuracyacu', '220809'),
('San MartIn', 'San MartIn ', '', '220900'),
('San MartIn', 'San MartIn ', 'Tarapoto', '220901'),
('San MartIn', 'San MartIn ', 'Alberto Leveau', '220902'),
('San MartIn', 'San MartIn ', 'Cacatachi', '220903'),
('San MartIn', 'San MartIn ', 'Chazuta', '220904'),
('San MartIn', 'San MartIn ', 'Chipurana', '220905'),
('San MartIn', 'San MartIn ', 'El Porvenir', '220906'),
('San MartIn', 'San MartIn ', 'Huimbayoc', '220907'),
('San MartIn', 'San MartIn ', 'Juan Guerra', '220908'),
('San MartIn', 'San MartIn ', 'La Banda de Shilcayo', '220909'),
('San MartIn', 'San MartIn ', 'Morales', '220910'),
('San MartIn', 'San MartIn ', 'Papaplaya', '220911'),
('San MartIn', 'San MartIn ', 'San Antonio', '220912'),
('San MartIn', 'San MartIn ', 'Sauce', '220913'),
('San MartIn', 'San MartIn ', 'Shapaja', '220914'),
('San MartIn', 'Tocache ', '', '221000'),
('San MartIn', 'Tocache ', 'Tocache', '221001'),
('San MartIn', 'Tocache ', 'Nuevo Progreso', '221002'),
('San MartIn', 'Tocache ', 'Polvora', '221003'),
('San MartIn', 'Tocache ', 'Shunte', '221004'),
('San MartIn', 'Tocache ', 'Uchiza', '221005'),
('Tacna', '', '', '230000'),
('Tacna', 'Tacna ', '', '230100'),
('Tacna', 'Tacna ', 'Tacna', '230101'),
('Tacna', 'Tacna ', 'Alto de la Alianza', '230102'),
('Tacna', 'Tacna ', 'Calana', '230103'),
('Tacna', 'Tacna ', 'Ciudad Nueva', '230104'),
('Tacna', 'Tacna ', 'Inclan', '230105'),
('Tacna', 'Tacna ', 'Pachia', '230106'),
('Tacna', 'Tacna ', 'Palca', '230107'),
('Tacna', 'Tacna ', 'Pocollay', '230108'),
('Tacna', 'Tacna ', 'Sama', '230109'),
('Tacna', 'Tacna ', 'Coronel Gregorio AlbarracIn Lanchipa', '230110'),
('Tacna', 'Candarave ', '', '230200'),
('Tacna', 'Candarave ', 'Candarave', '230201'),
('Tacna', 'Candarave ', 'Cairani', '230202'),
('Tacna', 'Candarave ', 'Camilaca', '230203'),
('Tacna', 'Candarave ', 'Curibaya', '230204'),
('Tacna', 'Candarave ', 'Huanuara', '230205'),
('Tacna', 'Candarave ', 'Quilahuani', '230206'),
('Tacna', 'Jorge Basadre ', '', '230300'),
('Tacna', 'Jorge Basadre ', 'Locumba', '230301'),
('Tacna', 'Jorge Basadre ', 'Ilabaya', '230302'),
('Tacna', 'Jorge Basadre ', 'Ite', '230303'),
('Tacna', 'Tarata ', '', '230400'),
('Tacna', 'Tarata ', 'Tarata', '230401'),
('Tacna', 'Tarata ', 'HEroes AlbarracIn', '230402'),
('Tacna', 'Tarata ', 'Estique', '230403'),
('Tacna', 'Tarata ', 'Estique-Pampa', '230404'),
('Tacna', 'Tarata ', 'Sitajara', '230405'),
('Tacna', 'Tarata ', 'Susapaya', '230406'),
('Tacna', 'Tarata ', 'Tarucachi', '230407'),
('Tacna', 'Tarata ', 'Ticaco', '230408'),
('Tumbes', '', '', '240000'),
('Tumbes', 'Tumbes ', '', '240100'),
('Tumbes', 'Tumbes ', 'Tumbes', '240101'),
('Tumbes', 'Tumbes ', 'Corrales', '240102'),
('Tumbes', 'Tumbes ', 'La Cruz', '240103'),
('Tumbes', 'Tumbes ', 'Pampas de Hospital', '240104'),
('Tumbes', 'Tumbes ', 'San Jacinto', '240105'),
('Tumbes', 'Tumbes ', 'San Juan de la Virgen', '240106'),
('Tumbes', 'Contralmirante Villar ', '', '240200'),
('Tumbes', 'Contralmirante Villar ', 'Zorritos', '240201'),
('Tumbes', 'Contralmirante Villar ', 'Casitas', '240202'),
('Tumbes', 'Contralmirante Villar ', 'Canoas de Punta Sal', '240203'),
('Tumbes', 'Zarumilla ', '', '240300'),
('Tumbes', 'Zarumilla ', 'Zarumilla', '240301'),
('Tumbes', 'Zarumilla ', 'Aguas Verdes', '240302'),
('Tumbes', 'Zarumilla ', 'Matapalo', '240303'),
('Tumbes', 'Zarumilla ', 'Papayal', '240304'),
('Ucayali', '', '', '250000'),
('Ucayali', 'Coronel Portillo ', '', '250100'),
('Ucayali', 'Coronel Portillo ', 'Calleria', '250101'),
('Ucayali', 'Coronel Portillo ', 'Campoverde', '250102'),
('Ucayali', 'Coronel Portillo ', 'Iparia', '250103'),
('Ucayali', 'Coronel Portillo ', 'Masisea', '250104'),
('Ucayali', 'Coronel Portillo ', 'Yarinacocha', '250105'),
('Ucayali', 'Coronel Portillo ', 'Nueva Requena', '250106'),
('Ucayali', 'Coronel Portillo ', 'Manantay', '250107'),
('Ucayali', 'Atalaya ', '', '250200'),
('Ucayali', 'Atalaya ', 'Raymondi', '250201'),
('Ucayali', 'Atalaya ', 'Sepahua', '250202'),
('Ucayali', 'Atalaya ', 'Tahuania', '250203'),
('Ucayali', 'Atalaya ', 'Yurua', '250204'),
('Ucayali', 'Padre Abad ', '', '250300'),
('Ucayali', 'Padre Abad ', 'Padre Abad', '250301'),
('Ucayali', 'Padre Abad ', 'Irazola', '250302'),
('Ucayali', 'Padre Abad ', 'Curimana', '250303'),
('Ucayali', 'Padre Abad ', 'Neshuya', '250304'),
('Ucayali', 'Padre Abad ', 'Alexander Von Humboldt', '250305'),
('Ucayali', 'PurUs', '', '250400'),
('Ucayali', 'PurUs', 'Purus', '250401');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(70) NOT NULL,
  `password` text NOT NULL,
  `telefono` varchar(11) DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `habilitado` bit(1) NOT NULL DEFAULT b'1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombres`, `apellidos`, `email`, `username`, `password`, `telefono`, `photo`, `fecha_creacion`, `habilitado`) VALUES
(1, 'ang', 'ang', 'ang@email.com', 'juan', '$2y$10$1fTQj6o8xkUbGRzQrKjK1OlCsYFO3nyFzol1ewSF0qYT.0wCILEfK', NULL, NULL, '2021-01-20 00:09:53', b'1'),
(2, 'Joel', 'Cotrina', 'joelcotrina@luckimotors.com', 'adm1', '$2y$10$G5UZhCxomDN3uc9BFM2J/eOvS7pJmjAm121GGIe1VWqEhtzRibHUu', NULL, '', '2021-10-29 10:01:51', b'1'),
(3, 'admin', 'Cotrina', 'eliascotrina@luckimotors.com', 'admin', '$2y$10$f7ya5gr4o8aXs63AnMPYLeY9IVYbyb3Pnx6J4/RYsKJ52xQVP33Da', NULL, '', '2021-10-29 10:03:59', b'1'),
(4, 'Tanya', 'Castillo', 'tanyacastillo@luckimotors.com', 'adm3', '$2a$07$usesomesillystringforeUkgqJKRF.59I1R.B/KPdAr3HDbJR4qO', NULL, '', '2021-10-29 10:12:39', b'1'),
(14, 'Karla', 'Alvarado', 'karla.alvarado@luckimotors.com', 'kalva', '$2y$10$E2yahmzRB07aZiYYBnYDjegQs8QeP3lL23jZDFCOeQNT0yzfzHKYi', NULL, '', '2021-10-30 08:55:37', b'1'),
(15, 'jhamis', 'ruiz castillo', 'jruiz@email.com', 'jhamis', '$2y$10$naGfJAhR5ZUPkz7IA1WEh.g7UbOQfig3jfgJ05bzHwZy/6eU6MokO', '987452123', NULL, '2022-02-25 15:17:39', b'1'),
(18, 'nuevo usuario', 'prueba', 'emaail@meail.com', 'prueba123', '$2y$10$qiluRVxlZe.anXAygn/D3.K16Ca0GTTDikwwMEgluwx5vbfQfq7Fq', '987456321', NULL, '2022-03-19 11:10:48', b'1');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUbigeo` (`idUbigeo`),
  ADD KEY `idSucursal` (`idSucursal`) USING BTREE;

--
-- Indices de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_empleado` (`id_empleado`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_number` (`document_number`);

--
-- Indices de la tabla `documento_electronico`
--
ALTER TABLE `documento_electronico`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni_ruc` (`dni_ruc`),
  ADD KEY `dni_ruc_2` (`dni_ruc`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_documento` (`numero_documento`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_componente`
--
ALTER TABLE `menu_componente`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `menu_submenu`
--
ALTER TABLE `menu_submenu`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCategoria` (`idCategoria`),
  ADD KEY `idUmedida` (`idUmedida`),
  ADD KEY `idAlmacen` (`idAlmacen`),
  ADD KEY `id_marca` (`id_marca`),
  ADD KEY `idInfraestructura` (`idInfraestructura`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_documento` (`id_documento`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `ubigeo` (`ubigeo`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- Indices de la tabla `sucursal_documento`
--
ALTER TABLE `sucursal_documento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_sucursal` (`id_sucursal`);

--
-- Indices de la tabla `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `ubigeo`
--
ALTER TABLE `ubigeo`
  ADD PRIMARY KEY (`id_ubigeo`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE,
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacen`
--
ALTER TABLE `almacen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `asistencia`
--
ALTER TABLE `asistencia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `documento_electronico`
--
ALTER TABLE `documento_electronico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `menu_componente`
--
ALTER TABLE `menu_componente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `menu_submenu`
--
ALTER TABLE `menu_submenu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(22) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `sucursal_documento`
--
ALTER TABLE `sucursal_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asistencia`
--
ALTER TABLE `asistencia`
  ADD CONSTRAINT `fk_id_orden_empleado_asistencia` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id`) ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `fk_id_ubigeo` FOREIGN KEY (`ubigeo`) REFERENCES `ubigeo` (`id_ubigeo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sucursal_documento`
--
ALTER TABLE `sucursal_documento`
  ADD CONSTRAINT `fk_id_sucursa` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
