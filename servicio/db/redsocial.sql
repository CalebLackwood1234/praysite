-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-02-2023 a las 22:10:17
-- Versión del servidor: 10.1.30-MariaDB
-- Versión de PHP: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `redsocial`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_agregar_megusta` (IN `publicacion_in` BIGINT, IN `usuario_in` BIGINT, IN `fecha_in` TIMESTAMP, OUT `error_out` INT)  MODIFIES SQL DATA
BEGIN
	DECLARE CONTINUE HANDLER FOR SQLSTATE '23000' BEGIN
    	SET error_out = 1;
    END;
	
	SET error_out = 0;

	INSERT INTO megusta (usuario, publicacion) VALUES (usuario_in, publicacion_in);
	
	IF error_out = 0 THEN
		SELECT meGusta INTO @meGusta2 FROM publicacion WHERE id = publicacion_in;

		UPDATE publicacion SET meGusta = @meGusta2 + 1 WHERE id = publicacion_in;
		
        SELECT meGusta, metaMeGusta INTO @meGusta, @metaMeGusta FROM publicacion WHERE id = publicacion_in;
        
        IF @meGusta >= @metaMeGusta THEN
			UPDATE publicacion SET metaMeGusta = @metaMeGusta * 3, fechaModificacion = fecha_in WHERE id = publicacion_in;
		END IF;
        
	END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_chequear_loguin` (IN `idSesion_in` BIGINT, IN `passSesion_in` VARCHAR(35), IN `hashId_in` VARCHAR(35), IN `fecha_in` TIMESTAMP, OUT `id_out` BIGINT, OUT `nick_out` VARCHAR(50), OUT `nombre_out` VARCHAR(80), OUT `email_out` VARCHAR(80), OUT `fechaAlta_out` TIMESTAMP, OUT `fechaUltimoLogueo_out` TIMESTAMP, OUT `fechaUltimaAccion_out` TIMESTAMP, OUT `novedadesMensaje_out` BOOLEAN, OUT `fechaNacimiento_out` TIMESTAMP, OUT `tipoUsuario_out` VARCHAR(30), OUT `urlImagen_out` VARCHAR(200), OUT `puedeAdministrar_out` BOOLEAN, OUT `puedePublicar_out` BOOLEAN, OUT `error_out` INT)  MODIFIES SQL DATA
BEGIN
	DECLARE CONTINUE HANDLER FOR NOT FOUND BEGIN
		SET error_out = 123;
	END;
    
	SET id_out = 0;
    SET nick_out = NULL;
    SET nombre_out = NULL;
    SET email_out = NULL;
    SET fechaAlta_out = NULL;
    SET fechaUltimoLogueo_out = NULL;
    SET fechaUltimaAccion_out = NULL;
    SET novedadesMensaje_out = 0;
    SET fechaNacimiento_out = NULL;
    SET tipoUsuario_out = NULL;
    SET urlImagen_out = NULL;
    SET puedeAdministrar_out = 0;
    SET puedePublicar_out = 0;
	SET error_out = 0;
    
    SELECT u.id, u.nick, u.nombre, u.email, u.semillaSesion, u.estaBloqueado, u.fechaAlta, u.fechaUltimoLogueo, u.fechaUltimaAccion, u.novedadesMensaje, u.fechaNacimiento, tu.nombre, tu.puedeAdministrar, tu.puedePublicar, s.hashSesion, u.urtImagen INTO id_out, nick_out, nombre_out, email_out, @semillaSesion, @estaBloqueado, fechaAlta_out, fechaUltimoLogueo_out, fechaUltimaAccion_out, novedadesMensaje_out, fechaNacimiento_out, tipoUsuario_out, puedeAdministrar_out, puedePublicar_out, @hashSesion, urlImagen_out FROM sesioniniciada s INNER JOIN usuario u ON s.id = idSesion_in AND s.hashId = hashId_in AND s.usuario = u.id INNER JOIN tipousuario tu ON u.tipoUsuario = tu.id;
    
	IF error_out = 0 THEN
		SET @hasCalculado = md5(CONCAT(md5(md5( CONCAT(passSesion_in, @semillaSesion, passSesion_in, @semillaSesion) )) , passSesion_in, @semillaSesion));
		
		IF STRCMP(@hasCalculado, CONVERT(@hashSesion USING utf8)) = 0 THEN
			UPDATE usuario SET fechaUltimaAccion = fecha_in WHERE id = id_out;
			UPDATE sesioniniciada SET fechaUltimaAccion = fecha_in WHERE id = idSesion_in;
		ELSE
			DELETE FROM sesioniniciada WHERE id = idSesion_in;
			SET id_out = 0;
			SET nick_out = NULL;
			SET nombre_out = NULL;
			SET email_out = NULL;
			SET fechaAlta_out = NULL;
			SET fechaUltimoLogueo_out = NULL;
			SET fechaUltimaAccion_out = NULL;
			SET novedadesMensaje_out = 0;
			SET fechaNacimiento_out = NULL;
			SET tipoUsuario_out = NULL;
            SET urlImagen_out = NULL;
			SET puedeAdministrar_out = 0;
			SET puedePublicar_out = 0;
			SET error_out = 123;
		END IF;
	END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_quitar_megusta` (IN `usuario_in` BIGINT, IN `publicacion_in` BIGINT, OUT `error_out` INT)  MODIFIES SQL DATA
BEGIN
	SET error_out = 0;

	DELETE FROM megusta WHERE usuario = usuario_in AND publicacion= publicacion_in;
    
	SELECT meGusta INTO @meGusta FROM publicacion WHERE id = publicacion_in;

	UPDATE publicacion SET meGusta = @meGusta - 1 WHERE id = publicacion_in;
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat`
--

CREATE TABLE `chat` (
  `id` bigint(20) NOT NULL,
  `usuarioIdMenor` bigint(20) NOT NULL,
  `usuarioIdMayor` bigint(20) NOT NULL,
  `fueVisto` tinyint(1) NOT NULL,
  `cantidadMensajes` int(11) NOT NULL,
  `fechaUltimaActualizacion` timestamp NULL DEFAULT NULL,
  `previewUltimoMensaje` varchar(50) DEFAULT NULL,
  `previewUltimoMensajeUsuarioId` bigint(20) DEFAULT NULL,
  `estadoChatIdMenor` int(11) NOT NULL,
  `estadoChatIdMayor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `chat`
--

INSERT INTO `chat` (`id`, `usuarioIdMenor`, `usuarioIdMayor`, `fueVisto`, `cantidadMensajes`, `fechaUltimaActualizacion`, `previewUltimoMensaje`, `previewUltimoMensajeUsuarioId`, `estadoChatIdMenor`, `estadoChatIdMayor`) VALUES
(1, 93, 95, 1, 21, '2023-01-25 02:54:51', 'Este mensaje se va a enviar', 93, 1, 1),
(2, 93, 96, 0, 58, '2023-01-25 02:32:58', 'as', 93, 1, 1),
(3, 91, 93, 1, 33, '2023-02-09 02:10:54', 'Hola', 91, 1, 1),
(5, 92, 95, 1, 1, '2023-01-25 03:04:24', 'Vamos a tener un mensaje', 92, 1, 1),
(6, 91, 95, 0, 1, '2023-02-09 02:49:33', 'Hola', 95, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denuncia`
--

CREATE TABLE `denuncia` (
  `id` int(11) NOT NULL,
  `denunciante` bigint(20) NOT NULL,
  `denunciado` bigint(20) NOT NULL,
  `resolvio` bigint(20) DEFAULT NULL,
  `fechaResolucion` timestamp NULL DEFAULT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mensaje` varchar(2000) NOT NULL,
  `respuesta` varchar(2000) DEFAULT NULL,
  `estado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `denuncia`
--

INSERT INTO `denuncia` (`id`, `denunciante`, `denunciado`, `resolvio`, `fechaResolucion`, `fechaAlta`, `mensaje`, `respuesta`, `estado`) VALUES
(1, 93, 96, NULL, NULL, '2022-05-25 03:08:50', 'Me dijo algo feo', NULL, 1),
(2, 93, 96, NULL, NULL, '2022-05-25 03:08:52', 'Me dijo algo feo', NULL, 1),
(3, 93, 96, NULL, NULL, '2022-05-25 03:08:53', 'Me dijo algo feo', NULL, 1),
(4, 93, 96, NULL, NULL, '2022-05-25 03:08:54', 'Me dijo algo feo', NULL, 1),
(5, 93, 96, NULL, NULL, '2022-05-25 03:08:54', 'Me dijo algo feo', NULL, 1),
(6, 93, 96, NULL, NULL, '2022-05-25 03:08:55', 'Me dijo algo feo', NULL, 1),
(7, 93, 91, NULL, NULL, '2022-05-25 03:26:25', 'Puso algo feo', NULL, 1),
(8, 93, 91, NULL, NULL, '2022-05-25 03:26:27', 'Puso algo feo', NULL, 1),
(9, 93, 91, NULL, NULL, '2022-05-25 03:26:28', 'Puso algo feo', NULL, 1),
(10, 93, 91, NULL, NULL, '2022-05-25 03:26:28', 'Puso algo feo', NULL, 1),
(11, 93, 91, NULL, NULL, '2022-05-25 03:36:15', 'Puso algo feo1', NULL, 1),
(12, 93, 91, NULL, NULL, '2022-05-25 03:36:17', 'Puso algo feo1', NULL, 1),
(13, 93, 91, NULL, NULL, '2022-05-25 03:36:18', 'Puso algo feo12', NULL, 1),
(14, 93, 91, NULL, NULL, '2022-05-25 03:36:19', 'Puso algo feo12', NULL, 1),
(15, 93, 91, NULL, NULL, '2022-05-25 03:36:20', 'Puso algo feo123', NULL, 1),
(16, 93, 91, NULL, NULL, '2022-05-25 03:36:21', 'Puso algo feo123', NULL, 1),
(17, 93, 91, NULL, NULL, '2022-05-25 03:36:22', 'Puso algo feo1234', NULL, 1),
(18, 93, 91, NULL, NULL, '2022-05-25 03:36:23', 'Puso algo feo1234', NULL, 1),
(19, 93, 91, NULL, NULL, '2022-05-25 03:36:24', 'Puso algo feo12345', NULL, 1),
(20, 93, 91, NULL, NULL, '2022-05-25 03:36:25', 'Puso algo feo12345', NULL, 1),
(21, 93, 91, NULL, NULL, '2022-05-25 03:36:27', 'Puso algo feo123456', NULL, 1),
(22, 93, 91, NULL, NULL, '2022-05-25 03:36:28', 'Puso algo feo123456', NULL, 1),
(23, 93, 91, NULL, NULL, '2022-05-25 03:36:29', 'Puso algo feo1234567', NULL, 1),
(24, 93, 91, NULL, NULL, '2022-05-25 03:36:30', 'Puso algo feo1234567', NULL, 1),
(25, 93, 91, NULL, NULL, '2022-05-25 03:36:32', 'Puso algo feo12345678', NULL, 1),
(26, 93, 91, NULL, NULL, '2022-05-25 03:36:33', 'Puso algo feo12345678', NULL, 1),
(27, 93, 91, NULL, NULL, '2022-05-25 03:36:35', 'Puso algo feo123456789', NULL, 1),
(28, 93, 91, NULL, NULL, '2022-05-25 03:36:35', 'Puso algo feo123456789', NULL, 1),
(29, 93, 91, NULL, NULL, '2022-05-25 03:36:39', 'Puso algo feo1234567891', NULL, 1),
(30, 93, 91, NULL, NULL, '2022-05-25 03:36:39', 'Puso algo feo1234567891', NULL, 1),
(31, 93, 91, NULL, NULL, '2022-05-25 03:36:42', 'Puso algo feo12345678912', NULL, 1),
(32, 95, 93, 86, '2022-05-27 03:15:50', '2022-05-25 03:58:42', 'Me dijo algo feo', 'no me parece bien lo que planteas', 4),
(33, 92, 91, 86, '2022-06-08 03:49:21', '2022-06-08 02:48:55', 'Puso algo feo12345678912', 'me parece bien lo que planteas', 3),
(34, 96, 92, 86, '2022-06-08 03:48:07', '2022-06-08 03:27:22', 'Me dijo algo feo', 'me parece bien lo que planteas', 3),
(35, 96, 91, NULL, NULL, '2022-06-15 03:32:43', 'Puso algo feo12345678912', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadochat`
--

CREATE TABLE `estadochat` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `bloqueado` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estadochat`
--

INSERT INTO `estadochat` (`id`, `nombre`, `bloqueado`) VALUES
(1, 'Normal', 0),
(2, 'Bloqueado', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadodenuncia`
--

CREATE TABLE `estadodenuncia` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `leida` tinyint(1) NOT NULL,
  `aceptada` tinyint(1) NOT NULL,
  `rechazada` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `estadodenuncia`
--

INSERT INTO `estadodenuncia` (`id`, `nombre`, `leida`, `aceptada`, `rechazada`) VALUES
(1, 'Nueva', 0, 0, 0),
(2, 'Leida', 1, 0, 0),
(3, 'Aceptada', 1, 1, 0),
(4, 'Rechazada', 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lider`
--

CREATE TABLE `lider` (
  `id` bigint(20) NOT NULL,
  `biografia` text NOT NULL,
  `habilitado` tinyint(1) NOT NULL,
  `tipoRelacionChats` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `lider`
--

INSERT INTO `lider` (`id`, `biografia`, `habilitado`, `tipoRelacionChats`) VALUES
(91, 'Nuevo biografia del nuevo lider, esto es para que lo conozcan mejor', 0, 2),
(92, 'Nuevo Lider', 1, 2),
(95, 'Nuevo Lider - Datos de biografia', 1, 2),
(96, 'Nuevo Lider', 1, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mediodepago`
--

CREATE TABLE `mediodepago` (
  `id` bigint(20) NOT NULL,
  `usuario` bigint(20) NOT NULL,
  `tipo` int(11) NOT NULL,
  `datosNecesarios` varchar(1000) NOT NULL,
  `favorito` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `megusta`
--

CREATE TABLE `megusta` (
  `usuario` bigint(20) NOT NULL,
  `publicacion` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `megusta`
--

INSERT INTO `megusta` (`usuario`, `publicacion`) VALUES
(91, 31),
(93, 65),
(93, 78),
(93, 82),
(93, 85),
(95, 31),
(96, 31);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje`
--

CREATE TABLE `mensaje` (
  `id` bigint(20) NOT NULL,
  `chat` bigint(20) NOT NULL,
  `usuario` bigint(20) NOT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mensaje` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `mensaje`
--

INSERT INTO `mensaje` (`id`, `chat`, `usuario`, `fechaAlta`, `mensaje`) VALUES
(2, 2, 96, '2022-05-13 02:53:54', 'Buenos dias'),
(3, 2, 96, '2022-05-13 02:56:45', 'Buenos dias'),
(4, 2, 96, '2022-05-13 02:57:58', 'Buenos dias'),
(5, 2, 96, '2022-05-13 02:58:48', 'Cuerto intento la vencida'),
(6, 2, 93, '2022-05-13 02:59:52', 'Respuesta a lo anterior'),
(7, 2, 93, '2022-05-13 03:00:40', 'Respuesta a lo anterior'),
(8, 2, 93, '2022-05-13 03:00:40', 'Respuesta a lo anterior'),
(9, 2, 93, '2022-05-13 03:00:42', 'Respuesta a lo anterior'),
(10, 2, 93, '2022-05-13 03:00:43', 'Respuesta a lo anterior'),
(11, 2, 93, '2022-05-13 03:00:43', 'Respuesta a lo anterior'),
(12, 2, 93, '2022-05-13 03:00:44', 'Respuesta a lo anterior'),
(13, 2, 93, '2022-05-13 03:00:44', 'Respuesta a lo anterior'),
(14, 2, 93, '2022-05-13 03:00:45', 'Respuesta a lo anterior'),
(15, 2, 93, '2022-05-13 03:00:46', 'Respuesta a lo anterior'),
(16, 2, 93, '2022-05-13 03:00:47', 'Respuesta a lo anterior'),
(17, 2, 93, '2022-05-13 03:00:47', 'Respuesta a lo anterior'),
(18, 2, 93, '2022-05-13 03:00:48', 'Respuesta a lo anterior'),
(19, 2, 93, '2022-05-13 03:00:48', 'Respuesta a lo anterior'),
(20, 2, 93, '2022-05-13 03:00:49', 'Respuesta a lo anterior'),
(21, 2, 93, '2022-05-13 03:00:49', 'Respuesta a lo anterior'),
(22, 2, 96, '2022-05-13 03:01:13', 'Hoooo'),
(23, 2, 93, '2022-05-13 03:01:25', 'Oka'),
(24, 2, 93, '2022-05-13 03:02:35', 'Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo Texto muy largo'),
(25, 1, 93, '2022-05-13 03:04:56', 'Hola gente nueva'),
(26, 3, 93, '2022-05-13 03:05:01', 'Hola gente nueva'),
(27, 3, 91, '2022-05-13 03:06:56', 'Hola todo bien?'),
(28, 2, 96, '2022-05-13 03:20:44', 'Hola todo bien?'),
(29, 2, 96, '2022-05-13 03:39:06', 'Hola todo bien?'),
(30, 2, 93, '2022-05-20 03:51:00', 'Hola todo bien?'),
(31, 2, 96, '2022-05-20 03:59:26', 'Hola todo bien?'),
(32, 3, 93, '2022-10-05 02:34:54', 'Hola todo bien?'),
(33, 3, 93, '2022-10-05 02:35:07', 'Este es un mensaje diferente'),
(34, 3, 93, '2022-10-05 02:35:20', 'Este es u'),
(35, 3, 93, '2022-10-05 02:35:43', 'Este es u'),
(36, 3, 93, '2022-10-05 02:36:56', 'Este es u'),
(37, 3, 93, '2022-10-05 02:37:46', 'Este es u'),
(38, 3, 93, '2022-10-05 02:39:03', 'Hola todo bien?'),
(39, 3, 93, '2022-10-05 02:40:11', 'Hola todo bien?'),
(40, 3, 93, '2022-10-05 02:40:18', 'Hola todo bien?'),
(41, 3, 93, '2022-10-05 02:40:28', 'Hola todo bien?'),
(42, 3, 93, '2022-10-05 02:43:36', 'Hola todo bien?'),
(43, 3, 93, '2022-10-05 02:45:44', 'Hola todo bien?'),
(44, 3, 93, '2022-10-05 02:47:05', 'Hola todo bien?'),
(45, 3, 93, '2022-10-05 02:47:08', 'Hola todo bien?'),
(46, 3, 93, '2022-10-05 02:47:41', 'Hola todo bien?'),
(47, 3, 93, '2022-10-05 02:48:02', 'Ultimo mensaje, arriba de la lista'),
(48, 3, 93, '2022-10-05 02:50:33', 'Ultimo mensaje, arriba de la listaUltimo mensaje, arriba de la listaUltimo mensaje, arriba de la listaUltimo mensaje, arriba de la listaUltimo mensaje, arriba de la listaUltimo mensaje, arriba de la listaUltimo mensaje, arriba de la listaUltimo mensaje, arriba de la lista'),
(49, 3, 91, '2022-10-05 02:59:07', 'Ahora te respondo yo'),
(50, 2, 96, '2022-10-05 03:04:42', 'hola soy mica y te escribo'),
(51, 1, 95, '2022-10-05 03:49:37', 'hola 2'),
(52, 1, 95, '2022-10-05 03:52:03', 'che respondeme'),
(53, 1, 95, '2022-10-05 03:52:25', 'Hola'),
(54, 1, 95, '2022-10-05 03:52:33', 'estas ahi?'),
(55, 2, 96, '2022-10-05 03:59:50', 'estas ahi?'),
(56, 2, 96, '2022-10-05 04:00:02', 'aparezco primera'),
(57, 2, 93, '2022-10-05 04:06:12', 'Este mensaje es mio'),
(58, 2, 93, '2022-10-05 04:08:03', 'Nuevo'),
(59, 2, 93, '2022-10-05 04:08:11', 'Nuevo asdf'),
(60, 2, 93, '2022-10-05 04:08:19', 'qasdaa'),
(61, 1, 95, '2022-10-05 04:08:58', 'te respondo'),
(62, 1, 93, '2022-10-05 04:10:29', 'Te escribo'),
(63, 3, 91, '2022-10-12 03:26:51', 'Te escribo'),
(64, 3, 91, '2022-10-12 03:27:03', 'Hola'),
(65, 3, 91, '2022-10-14 04:09:19', 'ultimo mensaje'),
(66, 3, 93, '2022-10-28 02:57:01', 'dasfasda'),
(67, 3, 93, '2022-10-28 02:58:06', 'fsadfa'),
(68, 3, 93, '2022-10-28 02:58:45', 'fdsafsafas'),
(69, 3, 93, '2022-10-28 02:59:48', 'fasdfasf'),
(70, 3, 93, '2022-10-28 03:00:53', 'fsdagasdf'),
(71, 3, 93, '2022-10-28 03:01:00', 'as'),
(72, 3, 93, '2022-10-28 03:01:03', 'fwqerewt'),
(73, 3, 93, '2022-10-28 03:01:10', 'hola'),
(74, 3, 93, '2022-10-28 03:01:23', 'Como estas'),
(75, 1, 93, '2022-10-28 03:03:11', 'te sigo escribiendo'),
(76, 2, 93, '2022-10-28 03:03:43', 'Hola de nuevo'),
(77, 2, 93, '2022-10-28 03:16:25', 'hola'),
(78, 2, 93, '2022-10-28 03:16:31', 'todo bien'),
(79, 2, 96, '2022-11-25 02:42:42', 'Hola todo bien?'),
(80, 2, 96, '2022-11-25 02:43:06', 'Hola todo bien nuevo'),
(81, 2, 93, '2022-11-25 03:06:47', 'hola'),
(82, 2, 93, '2022-11-30 01:27:12', 'as'),
(83, 2, 93, '2022-11-30 01:30:23', 'dsafasfasdas'),
(84, 2, 93, '2022-11-30 01:30:54', 'asdfasdf'),
(85, 2, 93, '2022-11-30 01:30:59', 'qwe'),
(86, 2, 93, '2022-11-30 01:31:42', 'asdf'),
(87, 2, 93, '2022-11-30 01:31:45', 'qwerqw'),
(88, 2, 93, '2022-11-30 01:44:49', 'nuevo mensaje'),
(89, 2, 93, '2022-11-30 01:45:54', 'q'),
(90, 2, 93, '2022-11-30 01:47:43', 'asdasfasd'),
(91, 2, 93, '2022-11-30 01:47:47', 'asdasfasdsa'),
(92, 2, 93, '2022-11-30 01:47:52', 'asdfasfasf'),
(93, 2, 93, '2022-11-30 01:47:54', 'sadgdasfadsf'),
(94, 2, 93, '2022-11-30 01:48:02', 'asdfasdgasdfasd'),
(95, 1, 95, '2022-11-30 01:50:47', 'ultimo mensaje'),
(96, 1, 95, '2022-11-30 01:50:59', 'ultimo mensaje'),
(97, 1, 95, '2022-11-30 01:51:00', 'ultimo mensaje'),
(98, 1, 95, '2022-11-30 01:51:01', 'ultimo mensaje'),
(99, 1, 95, '2022-11-30 01:51:11', 'ultimo mensaje'),
(100, 1, 95, '2022-11-30 01:51:12', 'ultimo mensaje'),
(101, 1, 95, '2022-11-30 01:51:28', 'ultimo mensaje2'),
(102, 1, 95, '2022-11-30 01:58:31', 'ultimo mensaje3'),
(103, 1, 95, '2022-11-30 01:59:51', 'este es el ultimo que te va a a llegar'),
(104, 1, 95, '2022-11-30 02:00:14', 'este se va a quedar'),
(105, 2, 93, '2022-11-30 02:00:25', 'hola'),
(106, 2, 93, '2022-11-30 02:00:33', 'otro chat'),
(107, 1, 95, '2022-11-30 02:00:43', 'mirame'),
(108, 2, 93, '2022-11-30 02:00:49', 'no quiero'),
(109, 1, 95, '2022-11-30 02:00:59', 'mirame dale?'),
(110, 2, 93, '2023-01-04 01:50:21', 'sadfasdfads'),
(111, 2, 93, '2023-01-25 02:32:58', 'as'),
(112, 1, 93, '2023-01-25 02:54:51', 'Este mensaje se va a enviar'),
(114, 5, 92, '2023-01-25 03:04:24', 'Vamos a tener un mensaje'),
(115, 3, 91, '2023-02-09 02:10:54', 'Hola'),
(116, 6, 95, '2023-02-09 02:49:33', 'Hola');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina`
--

CREATE TABLE `pagina` (
  `id` bigint(20) NOT NULL,
  `lider` bigint(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `bloqueada` tinyint(1) NOT NULL,
  `suscripcionMinimo` int(11) NOT NULL,
  `nivelMinimo` int(11) NOT NULL,
  `ultimaActualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pagina`
--

INSERT INTO `pagina` (`id`, `lider`, `nombre`, `descripcion`, `bloqueada`, `suscripcionMinimo`, `nivelMinimo`, `ultimaActualizacion`) VALUES
(6, 91, 'Luz del dia', 'Pagina orientada a mis queridos debotos', 0, 3, 99, '2023-02-27 05:48:33'),
(8, 91, 'Luz del dia2', 'Pagina orientada a mis queridos debotos', 0, 1, 0, '2023-02-27 05:48:34'),
(9, 96, 'Pagina de micaela', 'Pagina orientada a mis queridos debotos de micaela', 0, 1, 0, '2022-11-11 02:30:44'),
(12, 95, 'Pagina de maria 2', 'Pagina orientada a mis queridos debotos de maria 2', 0, 1, 0, '2023-02-27 06:19:15'),
(13, 95, 'Pagina de maria 3', 'Otra vez sopa', 0, 1, 0, '2023-02-27 06:19:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pago`
--

CREATE TABLE `pago` (
  `id` bigint(20) NOT NULL,
  `usuario` bigint(20) NOT NULL,
  `lider` bigint(20) NOT NULL,
  `tipoSuscripcion` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `monto` double NOT NULL,
  `tipoMedioPago` int(11) NOT NULL,
  `detalle` text NOT NULL,
  `pagoExitoso` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pago`
--

INSERT INTO `pago` (`id`, `usuario`, `lider`, `tipoSuscripcion`, `fecha`, `monto`, `tipoMedioPago`, `detalle`, `pagoExitoso`) VALUES
(1, 93, 96, 2, '2022-04-07 22:37:53', 3, 1, 'Datos uitiles para el pago', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publicacion`
--

CREATE TABLE `publicacion` (
  `id` bigint(11) NOT NULL,
  `pagina` bigint(20) NOT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaModificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mensaje` text NOT NULL,
  `cantidadImagenes` int(11) NOT NULL,
  `nombreImagenes` varchar(450) NOT NULL,
  `meGusta` int(11) NOT NULL,
  `metaMeGusta` int(11) NOT NULL DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `publicacion`
--

INSERT INTO `publicacion` (`id`, `pagina`, `fechaAlta`, `fechaModificacion`, `mensaje`, `cantidadImagenes`, `nombreImagenes`, `meGusta`, `metaMeGusta`) VALUES
(3, 6, '2022-04-01 02:39:42', '2022-04-01 02:39:42', 'Hola a todos', 0, '', 0, 3),
(4, 6, '2022-04-01 02:40:21', '2022-04-01 02:40:21', 'Hola a todos', 0, '', 0, 3),
(5, 6, '2022-04-01 02:53:21', '2022-04-01 02:53:21', 'Hola a todos1', 0, '', 0, 3),
(6, 6, '2022-04-01 02:53:24', '2022-04-01 02:53:24', 'Hola a todos2', 0, '', 0, 3),
(7, 6, '2022-04-01 02:53:27', '2022-04-01 02:53:27', 'Hola a todos3', 0, '', 0, 3),
(8, 6, '2022-04-01 02:53:31', '2022-04-01 02:53:31', 'Hola a todos4', 0, '', 0, 3),
(9, 6, '2022-04-01 02:58:58', '2022-04-01 02:58:58', 'Hola a todos4', 0, '', 0, 3),
(10, 6, '2022-04-01 02:58:59', '2022-04-01 02:58:59', 'Hola a todos4', 0, '', 0, 3),
(11, 6, '2022-04-01 02:58:59', '2022-04-01 02:58:59', 'Hola a todos4', 0, '', 0, 3),
(12, 6, '2022-04-01 02:59:00', '2022-04-01 02:59:00', 'Hola a todos4', 0, '', 0, 3),
(13, 6, '2022-04-01 02:59:00', '2022-04-01 02:59:00', 'Hola a todos4', 0, '', 0, 3),
(14, 6, '2022-04-01 02:59:00', '2022-04-01 02:59:00', 'Hola a todos4', 0, '', 0, 3),
(15, 6, '2022-04-01 02:59:01', '2022-04-01 02:59:01', 'Hola a todos4', 0, '', 0, 3),
(16, 6, '2022-04-01 02:59:01', '2022-04-01 02:59:01', 'Hola a todos4', 0, '', 0, 3),
(17, 6, '2022-04-01 02:59:02', '2022-04-01 02:59:02', 'Hola a todos4', 0, '', 0, 3),
(19, 6, '2022-04-01 02:59:29', '2022-04-01 03:24:04', 'Hola a todos4 - modificado1', 0, '', 0, 3),
(20, 6, '2022-04-01 03:12:53', '2022-04-01 03:12:53', 'Hola a todos4', 0, '', 0, 3),
(21, 8, '2022-04-06 03:02:20', '2022-04-06 03:02:20', 'Pagina 8 - 1', 0, '', 0, 3),
(22, 8, '2022-04-06 03:02:24', '2022-04-06 03:02:24', 'Pagina 8 - 2', 0, '', 0, 3),
(23, 8, '2022-04-06 03:02:28', '2022-04-06 03:02:28', 'Pagina 8 - 3', 0, '', 0, 3),
(24, 8, '2022-04-06 03:02:31', '2022-04-06 03:02:31', 'Pagina 8 - 4', 0, '', 0, 3),
(25, 8, '2022-04-06 03:02:34', '2022-04-06 03:02:34', 'Pagina 8 - 5', 0, '', 0, 3),
(26, 8, '2022-04-06 03:02:36', '2022-04-06 03:02:36', 'Pagina 8 - 6', 0, '', 0, 3),
(27, 9, '2022-04-06 03:04:40', '2022-08-05 02:32:53', 'Pagina de miceaela 1 - Publicacion 1', 1, ';2022-08-042bb2ad2bd6242b71b02a40deb9d68287', 0, 3),
(28, 9, '2022-04-06 03:04:43', '2022-04-06 03:04:43', 'Pagina de miceaela 1 - Publicacion 2', 0, '', 0, 3),
(29, 9, '2022-04-06 03:04:46', '2022-04-06 03:04:46', 'Pagina de miceaela 1 - Publicacion 3', 0, '', 0, 3),
(30, 9, '2022-04-06 03:04:49', '2022-04-06 03:04:49', 'Pagina de miceaela 1 - Publicacion 4', 0, '', 0, 3),
(31, 9, '2022-04-06 03:04:52', '2022-04-27 04:00:44', 'Pagina de miceaela 1 - Publicacion 5', 0, '', 3, 9),
(40, 12, '2022-04-06 03:06:50', '2022-04-06 03:06:50', 'Pagina de maria 2 - Publicacion 1', 0, '', 0, 3),
(41, 12, '2022-04-06 03:06:54', '2022-04-06 03:06:54', 'Pagina de maria 2 - Publicacion 2', 0, '', 0, 3),
(42, 12, '2022-04-06 03:06:58', '2022-04-06 03:06:58', 'Pagina de maria 2 - Publicacion 3', 0, '', 0, 3),
(43, 12, '2022-04-06 03:07:01', '2022-04-06 03:07:01', 'Pagina de maria 2 - Publicacion 4', 0, '', 0, 3),
(44, 6, '2022-04-06 03:36:38', '2022-04-06 03:36:38', 'publicacion paga', 0, '', 0, 3),
(45, 8, '2022-04-06 03:37:02', '2022-04-06 03:37:02', 'publicacion gratis', 0, '', 0, 3),
(46, 6, '2022-04-06 03:48:37', '2022-04-06 03:48:37', 'publicacion gratis', 0, '', 0, 3),
(47, 8, '2022-04-06 03:52:23', '2022-04-06 03:52:23', 'publicacion gratis', 0, '', 0, 3),
(48, 6, '2022-04-22 03:14:40', '2022-04-22 03:14:40', 'ultima novedad de novedades', 0, '', 0, 3),
(49, 6, '2022-04-22 03:17:56', '2022-04-22 03:17:56', 'ultima novedad de novedades', 0, '', 0, 3),
(50, 6, '2022-04-22 03:17:56', '2022-04-22 03:17:56', 'ultima novedad de novedades', 0, '', 0, 3),
(51, 6, '2022-04-22 03:17:56', '2022-04-22 03:17:56', 'ultima novedad de novedades', 0, '', 0, 3),
(52, 6, '2022-04-22 03:17:57', '2022-04-22 03:17:57', 'ultima novedad de novedades', 0, '', 0, 3),
(53, 6, '2022-04-22 03:17:57', '2022-04-22 03:17:57', 'ultima novedad de novedades', 0, '', 0, 3),
(54, 6, '2022-04-22 03:17:58', '2022-04-22 03:17:58', 'ultima novedad de novedades', 0, '', 0, 3),
(55, 6, '2022-04-22 03:17:58', '2022-04-22 03:17:58', 'ultima novedad de novedades', 0, '', 0, 3),
(56, 6, '2022-04-22 03:21:28', '2022-04-22 03:21:28', 'ultima novedad de novedades', 0, '', 0, 3),
(57, 6, '2022-04-22 03:21:28', '2022-04-22 03:21:28', 'ultima novedad de novedades', 0, '', 0, 3),
(58, 6, '2022-04-22 03:24:18', '2022-04-22 03:24:18', 'ultima novedad de novedades', 0, '', 0, 3),
(61, 9, '2022-07-08 04:18:30', '2022-07-08 04:18:30', 'ultima novedad de novedades', 3, ';2022-07-08e57da1dd7cea043988b80f23b868c679;2022-07-08943fcd1244e687e29651d4f20f442c60;2022-07-08fe5c6e4c71687ea85893e7d7cd147d75', 0, 3),
(62, 9, '2022-07-13 02:39:09', '2022-11-01 22:52:58', 'ultima novedad de novedades', 3, ';2022-07-12038500eb30189fb597963ba0aca61014;2022-07-12c0cc70eeea8f6a156c3bf4b56931b984;2022-07-125413c816e1dd4f9596e33a7629e91dbc', 0, 9),
(63, 9, '2022-11-11 02:30:19', '2022-11-11 02:30:19', 'ultima novedad de novedades', 0, '', 0, 3),
(64, 9, '2022-11-11 02:30:25', '2022-11-11 02:30:25', 'ultima novedad de novedades', 0, '', 0, 3),
(65, 9, '2022-11-11 02:30:25', '2022-11-11 02:30:25', 'ultima novedad de novedades', 0, '', 1, 3),
(66, 9, '2022-11-11 02:30:26', '2022-11-11 02:30:26', 'ultima novedad de novedades', 0, '', 0, 3),
(67, 9, '2022-11-11 02:30:26', '2022-11-11 02:30:26', 'ultima novedad de novedades', 0, '', 0, 3),
(68, 9, '2022-11-11 02:30:27', '2022-11-11 02:30:27', 'ultima novedad de novedades', 0, '', 0, 3),
(69, 9, '2022-11-11 02:30:28', '2022-11-11 02:30:28', 'ultima novedad de novedades', 0, '', 0, 3),
(70, 9, '2022-11-11 02:30:28', '2022-11-11 02:30:28', 'ultima novedad de novedades', 0, '', 0, 3),
(71, 9, '2022-11-11 02:30:29', '2022-11-11 02:30:29', 'ultima novedad de novedades', 0, '', 0, 3),
(72, 9, '2022-11-11 02:30:29', '2022-11-11 02:30:29', 'ultima novedad de novedades', 0, '', 0, 3),
(73, 9, '2022-11-11 02:30:30', '2022-11-11 02:30:30', 'ultima novedad de novedades', 0, '', 0, 3),
(74, 9, '2022-11-11 02:30:30', '2022-11-11 02:30:30', 'ultima novedad de novedades', 0, '', 0, 3),
(75, 9, '2022-11-11 02:30:31', '2022-11-11 02:30:31', 'ultima novedad de novedades', 0, '', 0, 3),
(76, 9, '2022-11-11 02:30:31', '2022-11-11 02:30:31', 'ultima novedad de novedades', 0, '', 0, 3),
(77, 9, '2022-11-11 02:30:32', '2022-11-11 02:30:32', 'ultima novedad de novedades', 0, '', 0, 3),
(78, 9, '2022-11-11 02:30:40', '2022-11-11 02:30:40', 'ultima novedad de novedades', 0, '', 1, 3),
(79, 9, '2022-11-11 02:30:41', '2022-11-11 02:30:41', 'ultima novedad de novedades', 0, '', 0, 3),
(80, 9, '2022-11-11 02:30:42', '2022-11-11 02:30:42', 'ultima novedad de novedades', 0, '', 0, 3),
(81, 9, '2022-11-11 02:30:42', '2022-11-11 02:30:42', 'ultima novedad de novedades', 0, '', 0, 3),
(82, 9, '2022-11-11 02:30:43', '2022-11-11 02:30:43', 'ultima novedad de novedades', 0, '', 1, 3),
(83, 9, '2022-11-11 02:30:43', '2022-11-11 02:30:43', 'ultima novedad de novedades', 0, '', 0, 3),
(84, 9, '2022-11-11 02:30:44', '2022-11-11 02:30:44', 'ultima novedad de novedades', 0, '', 0, 3),
(85, 8, '2023-01-06 02:04:26', '2023-01-06 02:04:26', 'Juan publica imagenes', 3, ';2023-01-051e101ba3a96038d68ba157f0431ef821;2023-01-0537733fc095d26319d635b9e2d35fdb84;2023-01-0515b4ffb133d8fcc4a9d516e7384ed9fc', 1, 3),
(90, 13, '2023-02-16 02:04:21', '2023-02-16 02:04:21', 'Hola primera publicacion', 0, '', 0, 3),
(92, 13, '2023-02-16 03:22:57', '2023-02-16 03:22:57', 'dos imagenes iguales', 2, ';2023-02-16a64ac88d3e116c3a45011218b2340504;2023-02-16f6d1cdf5602e3cc8fb5d6ada871f1445', 0, 3),
(93, 13, '2023-02-16 03:23:11', '2023-02-16 03:23:11', 'una imagen', 1, ';2023-02-16808c9b27e9e47b9ef0fe3124cf7f684a', 0, 3),
(94, 13, '2023-02-16 03:47:54', '2023-02-16 03:47:54', 'asdf', 2, ';2023-02-16dd33910e678bb37c3ec0cd49738f4ab5;2023-02-162c7b871af2bef518affded5a4c46e8ac', 0, 3),
(95, 13, '2023-02-16 03:48:38', '2023-02-16 03:48:38', 'a', 1, ';2023-02-164215513709b563e872f15b69c93c4bef', 0, 3),
(96, 13, '2023-02-16 03:48:58', '2023-02-16 03:48:58', 'aaa', 1, ';2023-02-16622807fd9d46dc0481b3f88da957ef4b', 0, 3),
(97, 13, '2023-02-16 03:50:39', '2023-02-16 03:50:39', 'asdas', 2, ';2023-02-165f947a330b29ffee070b0484ba73c75e;2023-02-16bfd4d3acc8443ea8f033c4f3379eb9d3', 0, 3),
(98, 13, '2023-02-16 03:51:06', '2023-02-16 03:51:06', 'sasadas', 2, ';2023-02-1615d8b95f8ea38b2df12a0aa9f4452666;2023-02-1655366838af3b78c1251affba0f54edfa', 0, 3),
(99, 13, '2023-02-16 03:55:51', '2023-02-16 03:55:51', 'aaaaaa', 2, ';2023-02-16bec53c8cccc835c7d90840515ddd4311;2023-02-164f02598fd9e5ba125b42472e8392bc3d', 0, 3),
(100, 13, '2023-02-16 05:52:29', '2023-02-16 05:52:29', 'Vamos a ver que onda', 2, ';2023-02-16342d23967f64e4f5943f83b834594b19;2023-02-16fea060ba57dfd595c31d01ce398057b1', 0, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesioniniciada`
--

CREATE TABLE `sesioniniciada` (
  `id` bigint(20) NOT NULL,
  `usuario` bigint(20) NOT NULL,
  `hashId` varchar(35) NOT NULL,
  `hashSesion` varchar(35) NOT NULL,
  `fechaCreacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaUltimaAccion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `sesioniniciada`
--

INSERT INTO `sesioniniciada` (`id`, `usuario`, `hashId`, `hashSesion`, `fechaCreacion`, `fechaUltimaAccion`) VALUES
(52, 86, 'c21836e6406245598ad94c1b92fd5d29', '5d99f1b5ebe79f11b9be0f7d8192f2b7', '2022-02-18 02:34:26', '2022-02-18 02:59:12'),
(53, 86, 'f6e743a78a23c1a8a092f5910a23c226', '482407846ec455f8f88b8ea9b33f6a90', '2022-02-23 01:30:49', '2022-02-23 01:30:54'),
(54, 86, '538d0866dd3d9d3d7962609998816297', '9f78f6d28ccf095c4ce2b8317fe433bc', '2022-02-23 01:36:22', '2022-02-23 01:36:22'),
(55, 86, '20d36d64cd2600da88312ec3a7c13b80', '9ac208294e9ea32e6ee9190145591bc7', '2022-02-23 01:36:23', '2022-02-23 01:36:23'),
(56, 86, 'edbea22a3a768411fba1c3a0a6dad69e', '7e4a244bc69c81c4cad2cd9abc5c0697', '2022-02-23 01:36:24', '2022-02-23 01:36:24'),
(57, 86, 'd60aad1901d546a76820a64776a03034', '80d697801bef11baccfcab4e61745d9f', '2022-02-23 01:36:24', '2022-02-23 01:36:24'),
(58, 86, 'abfce83a280f8a2f56444d2abf156f0a', '6e6cc664dda595cd88cded6468fd3bac', '2022-02-23 01:36:25', '2022-02-23 01:36:25'),
(59, 86, '7652244682a7564f89c1204aebfbda5f', '211f501cc1c6bd4575a8055c96f899ce', '2022-02-23 01:36:25', '2022-02-23 01:36:25'),
(60, 86, 'e5d6d6bfd633882b48689776b2881a52', '8e6df21fe04b6879c1adaf94b97ba54b', '2022-02-23 01:36:25', '2022-02-23 01:36:25'),
(61, 86, '5efc1b5838b04084dc8e1478da424fcd', '915a2be48f6e98ce0ad2c86d8f460232', '2022-02-23 01:36:26', '2022-02-23 01:36:26'),
(62, 86, '51fd9e231d735f7b12c267bc258d6c16', '528e353bb5076f65509c60b28fb9616f', '2022-02-23 01:36:26', '2022-02-23 01:36:26'),
(63, 86, 'a927ec51a8f1a47ea94a7be557cc5bd8', '577346565360efd3c8fc2c2e8ae38d7d', '2022-02-23 01:36:27', '2022-02-23 01:36:27'),
(64, 86, '00c5d8de3c4197ff1b1dc34f34603419', '7e13f41fcc0ac85652dbf59228f89f00', '2022-02-23 01:36:27', '2022-02-23 01:36:27'),
(65, 86, 'df27856e514947ed314137c56d6bd06e', '5785761e5e5839ce8489b1e2c17445f7', '2022-02-23 01:36:28', '2022-02-23 01:36:28'),
(66, 86, 'f6412b3c1a67ea6281ff488a437b39a7', '1dc5992a47969e16c34f3027a7701262', '2022-02-23 01:36:28', '2022-02-23 01:36:28'),
(67, 86, '893322ad9b278d5d53a877e03eb0c93f', '6484c828b14e5f0965c60a892b4a22b0', '2022-02-23 01:36:29', '2022-02-23 01:36:29'),
(68, 86, '209e3791962753d2ffbdff4093a67072', 'c73bd51a61dda2fda4481a08ac6ad48e', '2022-02-23 01:36:29', '2022-02-23 01:36:29'),
(69, 86, '9d577c90d07f517afe9088a3ef3acf2a', 'fc58f56350121829908879899885ce2d', '2022-02-23 01:36:30', '2022-02-23 01:36:30'),
(70, 86, '054b51ae642e934a6032596c26d5e575', '6739fc27318967db63c3a80d09f7728f', '2022-02-23 01:36:30', '2022-02-23 01:36:30'),
(71, 86, 'a3a11ed6c3880c2e736e7f1192372d52', '3ec955396263bba1f44f0a887990d668', '2022-02-23 01:36:31', '2022-02-23 01:36:31'),
(72, 86, '0e02a774cfc79ec446a3b19c75164872', '77b8555c28239aa82b41e3834a74fe00', '2022-02-23 01:36:31', '2022-02-23 01:36:31'),
(73, 86, 'cd78e942e8325ca66df420cbc0efc7d8', '3b1e110b7235691cc9ba396e605ff939', '2022-02-23 01:36:32', '2022-02-23 01:36:32'),
(74, 86, '189a6d24084992f5ed08ea76b881a18b', '12c173a355eaadbc1c23c05da755c390', '2022-02-23 01:36:44', '2022-02-23 01:36:44'),
(75, 86, '49ebb41c25c764ee108612c1d93d179a', '842ceb347b33f71dcbc2d906d2fb27dc', '2022-02-23 01:36:44', '2022-02-23 01:36:44'),
(76, 86, '46aa3f87186ceebbaa7a6baa7b799214', 'fe3e8b9eae1a9bb13f8f6c8f4f87bb91', '2022-02-23 01:36:45', '2022-02-23 01:36:45'),
(77, 86, 'dc499d8db6a9826bc8561b7b85869f19', '4aa184233b7722f26de76d7a8f3e1b09', '2022-02-23 01:36:45', '2022-02-23 01:36:45'),
(78, 86, 'c902a5cdc28229a710b3a5e65d636d1e', 'ae6c3b425a41a8013fd81e5b7a495d74', '2022-02-23 01:36:46', '2022-02-23 01:36:46'),
(79, 86, '92abb785f537d05009c1d3f8602e4dbb', '432ff608895b7d25cdbec731c338e4bf', '2022-02-23 01:36:46', '2022-02-23 01:36:46'),
(80, 86, '68b8b3ddd58aa556ff8b7abdce6f263b', 'd908e595c2e1944f0f611d974d26b924', '2022-02-23 01:36:47', '2022-02-23 01:36:47'),
(81, 86, 'bc16935f8648071a641ff0b1b1016f91', '9b286e4e9f2561e03ee32bb412c2ac75', '2022-02-23 01:36:47', '2022-02-23 01:36:47'),
(82, 86, '3d5d0beef9a92ebc07a68198e8458e54', 'b977587a6a9cf07338b8857a7c91fec2', '2022-02-23 01:40:24', '2022-02-23 02:34:00'),
(83, 86, '684a91c9adb4873785802b8d3b8474f5', 'd286b85294e74340f0d19f5c1f04715a', '2022-02-23 02:43:11', '2022-02-23 03:06:01'),
(84, 86, '45a0e4c6bba4248adced939b7e9e7bbb', '1bfb734e6bc6bece74a81ff47d078240', '2022-02-25 01:16:42', '2022-02-25 02:04:22'),
(85, 86, '779fdffa66f728a7ea1c1953ab5fcf90', '78c5040c7bdb77a6bae8c1f16fc17e47', '2022-02-25 02:29:44', '2022-02-25 03:00:08'),
(86, 86, '54ebfb3ebe7421c632ff98746a303339', 'a914a94d9d027cdcbb29e230f6aeeb4d', '2022-03-02 02:02:05', '2022-03-02 02:56:32'),
(87, 86, '6abc416213d2676e382e86987a068463', 'a90b863a729a653ad1fcb34a5ca587a8', '2022-03-04 01:37:19', '2022-03-04 01:37:25'),
(88, 86, '2d9d7d2224c21619cbcad5ca4d79d91b', '5e4f6bad219975d497f6451b7a00becd', '2022-03-04 01:50:04', '2022-03-04 01:50:04'),
(89, 86, '899c2950a27cf957abeb842e44ca60e8', '8ea086d9fe27f12d6c5b9d41f3da13f6', '2022-03-04 01:50:22', '2022-03-04 02:00:14'),
(90, 86, '1d662abe6c1f4f6ede63b0faf020965b', 'a3c1d0081a14462cffaf697c32b51c81', '2022-03-04 02:00:21', '2022-03-04 02:00:21'),
(93, 86, '16cbfed64aa7b353e0b0e07ef5986fb6', '6c41725f3e7b57638a6ba90d073f86cf', '2022-03-04 02:05:06', '2022-03-04 02:05:12'),
(94, 91, '33fb596bff237249b1011217f3d86d46', '28602c1d28ffffb605b07c32941a640d', '2022-03-04 02:05:28', '2022-03-04 02:05:28'),
(95, 86, '6da5589e0b2e902b740575cc89f9a65c', 'd14a916b7209035d1e6526d30db90be5', '2022-03-04 02:06:13', '2022-03-04 02:45:30'),
(96, 91, '6d8c4479c7197d42198353444d45e78f', '01e856db33f990b8c829766c945860a7', '2022-03-04 02:45:45', '2022-03-04 03:28:47'),
(97, 86, '7db75213b67c1df2bf47e4790c389c12', 'a9dde98bd32e80e9ec2c3b7c47781462', '2022-03-09 01:34:02', '2022-03-09 01:35:00'),
(98, 91, '641b390bc546982c6d20f8871bd4b699', '71671df04a1ce10e48e14585550e54bc', '2022-03-09 01:35:35', '2022-03-09 01:46:12'),
(99, 86, '585fd16d530fc2bcaa4813fdebc045e8', '647e7e1bab3af57c5a3201c27729d2dd', '2022-03-09 01:46:17', '2022-03-09 01:46:20'),
(100, 86, 'c2492926c3bc18436bcabab483b225c5', '36e30a8c0ad49d218d23a2a8644c2e40', '2022-03-09 01:48:20', '2022-03-09 01:58:35'),
(101, 91, 'c82f3a487d5a3bc88af2687b1465ab4e', '0d8e02fc043006f8c45c4ffcd0a9451d', '2022-03-09 01:58:39', '2022-03-09 02:29:19'),
(102, 86, '11ed29146464664a25151206030544e7', '9836881e247070d5ea2ad162c4da645d', '2022-03-09 02:58:38', '2022-03-09 03:06:23'),
(103, 91, 'e6235c42b8f8c3c75cfb7330dafe367d', 'e70e128b870b46d63d9aee6770d4a252', '2022-03-09 03:06:25', '2022-03-09 03:09:21'),
(104, 86, '446669d8eebfa700e28b9a790289e95a', '5a16cec3b28ffb3f1ece51aa3960e76b', '2022-03-09 03:09:33', '2022-03-09 03:09:33'),
(105, 86, 'b757b8c7dae044bd762405572a2ab9f9', '2b6d237166d6e4026c85529223c89dbc', '2022-03-09 03:09:46', '2022-03-09 03:10:18'),
(106, 91, 'e1baa8aea0a584370355a87a088c2d04', 'd9ff997e84b78248ead82f9e65999a06', '2022-03-09 03:10:39', '2022-03-09 03:23:04'),
(107, 86, 'ff11fa802cf7eaaabf57d83411b951a8', '616195ccba2f95e1df1bbddbad2cf7db', '2022-03-18 01:09:37', '2022-03-18 01:52:24'),
(108, 86, '647d320e812beb67de0b9c3974545eaa', '9c31a5b04c1cbf3725891b14e07055fa', '2022-03-18 02:15:09', '2022-03-18 02:16:00'),
(109, 86, '6ff7fcfb58d9964a968015421984e22a', '7b3ed9811cb60fd36bd8bac38af1a3dd', '2022-03-18 02:16:14', '2022-03-18 02:19:34'),
(110, 86, 'c92063479f039a03c3d2c2e16a7ae833', 'faf9565c4d2e7438b11d4aa48344dfba', '2022-03-18 02:38:19', '2022-03-18 02:40:37'),
(111, 91, 'ffe7c924ed56651a926070dfd80d2633', 'ee9dd648430b7f0931ed063d9440de40', '2022-03-18 02:40:44', '2022-03-18 02:53:10'),
(112, 91, '96a4ee770a486d68219a5cdd172060f1', '5f8ea9da12e37bb4a4adba4182a779fd', '2022-03-23 01:14:39', '2022-03-23 02:14:17'),
(113, 91, '834072a58433285e9ea6274b58ab87f0', '18f93e447efe5669dac64bc5eb59d519', '2022-03-23 02:16:35', '2022-03-23 02:59:52'),
(114, 91, 'a8fdef1bfbf3530639041d0332fc13ae', 'c8925acf2e860351decd45404afa431a', '2022-03-25 01:09:42', '2022-03-25 01:55:42'),
(115, 86, '241de1b9a81839f49bc88424a3257d1f', 'b5bc95434d6b0a8c39970327f00db461', '2022-03-25 02:05:39', '2022-03-25 02:07:41'),
(116, 91, 'fbae6f43d06549cf74207b1d8523e24a', 'be13b00badb3dcb8dd8e75d085961bd4', '2022-03-25 02:08:19', '2022-03-25 02:08:55'),
(117, 86, '47d69f492e84f00b4f29360168ed735a', 'f9c245e31003c4da930ef6b1bcecd971', '2022-03-25 02:09:13', '2022-03-25 02:09:16'),
(118, 91, '43a3ce304279dca95440ad26709e467c', 'a2e3bdc96f797bd015664806097e4780', '2022-03-25 02:09:24', '2022-03-25 02:09:28'),
(119, 91, '5f298e1610b78212b337430fb034b75d', '00fa997e89368aa58d4b0e539a96a546', '2022-03-25 02:15:18', '2022-03-25 02:15:18'),
(120, 91, '4eec5126f5b0efc106d6b3ad59e424da', '5c9838b8a74e737d453694741c3a9da8', '2022-03-25 02:15:34', '2022-03-25 02:32:44'),
(121, 91, '483aa8b4d2ccb15b34092a092aebc77f', 'cf86e708f91a280f44891ce65bfeac59', '2022-04-01 02:27:39', '2022-04-01 03:24:04'),
(122, 91, 'ad884de32410019bed8d5d126edc2235', 'aa53e5686e9a8898b6b04fcdaeb2e4bc', '2022-04-01 03:29:01', '2022-04-01 03:29:51'),
(123, 86, '79113da6ce5eabdfc381ba7538cf1356', '81a029bd1f2c8da66f2eb52d5c3fdd01', '2022-04-01 03:32:41', '2022-04-01 03:47:56'),
(124, 86, '8d60f8ac4d97685d037856974802c462', '9430d9909ec935e4ae8aae95c1c9b073', '2022-04-06 02:23:29', '2022-04-06 02:46:36'),
(126, 95, 'b2fd74aebe836b7fbf9ec55a793f9d7d', 'a855ad9eeadab7e6aa6fd5dde6f0601e', '2022-04-06 02:58:20', '2022-04-06 02:58:20'),
(127, 93, '826f48b853dfa7e2039147615aa8f641', '732b087841be64b1ed399e553dc1aef3', '2022-04-06 02:59:31', '2022-04-06 02:59:31'),
(128, 86, '6dde96030bd9f3fc833af572d121b650', '717d04bb427b19847b9cc13827bdf73d', '2022-04-06 02:59:48', '2022-04-06 03:01:12'),
(129, 91, 'fe84c4e51e97f006d6f2836488be132f', '5deb3183ec342cdcba16024200b84a66', '2022-04-06 03:01:34', '2022-04-06 03:02:57'),
(131, 95, 'ea9f91725d2ee3e0e1ef8dd4cf3f6a46', '0b1b10e3d2ddc0635f342947b9993231', '2022-04-06 03:05:30', '2022-04-06 03:08:06'),
(132, 93, 'd98b9def9fac6cbed2fed94476f08d5f', '809be7f87e433230d7608881520e7ed7', '2022-04-06 03:08:50', '2022-04-06 03:08:50'),
(133, 91, 'e81481d51a478629f923429438d2051d', '262e5b35d2f96654fd5e0558cd48af19', '2022-04-06 03:34:48', '2022-04-06 03:52:23'),
(135, 93, 'b21967cc8fdbc624ceca6276916e0914', 'aad84dd5e3557609fa9480a050790319', '2022-04-08 02:56:09', '2022-04-08 02:56:13'),
(138, 93, '53a32aec51aafd1e91c6fd9af3b076e8', 'dc5e6bba5766368ccdaa3dee4de62a49', '2022-04-08 03:28:27', '2022-04-08 03:28:45'),
(140, 93, 'e2f42b88b67d27a2b3d2ece33eb80905', 'f782f75b9d0bb264d25a4a8e3e127070', '2022-04-08 03:32:54', '2022-04-08 03:37:09'),
(142, 93, '64c03ae22b1ac47d142c79a56612f5d1', '89d1833faf35cb6a4453908c71f23c2d', '2022-04-08 03:38:38', '2022-04-08 03:38:41'),
(144, 93, 'e654ba4592c6dc7e97d3b02c3aaf1122', '9b36aa071a94147b288b1c69af4f96e0', '2022-04-08 03:40:16', '2022-04-08 03:40:19'),
(145, 93, '0750b9ad28af20ef9ab9ef08a391e9da', '3b6add732d901285c81129c4db94a024', '2022-04-08 03:40:23', '2022-04-08 03:41:11'),
(147, 93, '9f6109a72b255b11f653f0bd37b059ef', '4f091d0711b87395ca497a46ce3e50fb', '2022-04-08 03:43:17', '2022-04-08 03:44:03'),
(151, 93, 'a041aed0eba5463c00eeeeab088a1990', '100311d1d92eac85f4b4485113d9ccf2', '2022-04-08 03:46:26', '2022-04-08 03:46:37'),
(155, 91, '037588ca97ce09e4597c2f50555a05f9', '1e6c914feac0a7a5467655f81728fcfe', '2022-04-08 03:52:09', '2022-04-08 03:52:12'),
(157, 93, 'b6576b2dd099688b515e1dce322a25cf', '954bf2b6090617f7aba225dada063332', '2022-04-08 03:52:23', '2022-04-08 03:56:43'),
(159, 93, '25b5afd17073df6768b58606c040678d', '3c77bfb27d2002fed6653be69d12dc59', '2022-04-08 03:58:14', '2022-04-08 04:00:18'),
(160, 95, 'a949dcf9ecbc13dc0865ee7b3ced5fc6', '59e7696674f0d87263ce84f10344d26f', '2022-04-08 04:00:22', '2022-04-08 04:00:27'),
(162, 95, '59d2f83d94d454811b86229042358d4c', 'd828ef02605ddb1887457089832d47cc', '2022-04-08 04:00:41', '2022-04-08 04:00:45'),
(163, 91, 'c26c11e1e4969b4b1c662748c08fafa7', 'bc714396fbf6b75704246acd48a8a74a', '2022-04-08 04:01:02', '2022-04-08 04:01:17'),
(164, 93, 'f71125eeec4717fdc28d4e2923f872df', '17651423783a0f7381c650956a39a377', '2022-04-08 04:03:38', '2022-04-08 04:03:42'),
(165, 91, 'a916b411d2e66fefd66633315ae6c908', '271cba538834386749a55ee9a418f39b', '2022-04-08 04:03:48', '2022-04-08 04:05:26'),
(166, 95, 'f134c227b49c21b81bd8a78e776a229a', '35e805a6867ed8b87fb2f17116db320f', '2022-04-08 04:05:35', '2022-04-08 04:05:37'),
(167, 93, '66c8914273a878cc6414117f0ef198aa', '62872dd8ced79b2e6c0fad6ec491cbab', '2022-04-08 04:06:02', '2022-04-08 04:06:05'),
(168, 95, '3a44f2dd999f92689c277bc3b4e7fa22', '7e1e2aa248dcb0729353c139b54ec27e', '2022-04-08 04:06:10', '2022-04-08 04:11:08'),
(169, 91, '9c6714cefee0443a0bc0bf1003ba0b32', 'a868df9f7b442c583ade728c215a7848', '2022-04-08 04:11:25', '2022-04-08 04:12:09'),
(171, 93, 'a75789b7ee93f7cf6c44d88815ab0440', '6e84ae13ae8afdb4aaf224812a61753b', '2022-04-08 04:12:43', '2022-04-08 04:12:46'),
(172, 91, '185568b94f6a9a17e5258a1bdcb2f95d', 'f825454c8677bb70538382ea29439e7c', '2022-04-08 04:12:54', '2022-04-08 04:12:57'),
(173, 93, '64b8dc35ed68ba62d99583f7e42f747d', '8f027378beb888e67114f66ff8a4179d', '2022-04-15 02:05:27', '2022-04-15 02:05:51'),
(174, 95, 'd90a7173f1028e4d461ff83c7e171617', 'cb0afb44446b44e1606302eb03689262', '2022-04-15 02:06:01', '2022-04-15 02:06:04'),
(175, 95, 'd207bb4455a338338040d545c2e2b219', '7f3ca8775843fa6d95299652d4a8e760', '2022-04-15 02:06:12', '2022-04-15 02:06:12'),
(176, 91, '462e5d3347f139752daed85adcc9e2fd', 'b9a2596ae51dc4f798b25b1cd0bf30ab', '2022-04-15 02:06:14', '2022-04-15 02:06:17'),
(177, 93, '83240c7f6c708eac4e0f09d71fbe30bf', '100e297a48a71cb2c21842ad8ae09631', '2022-04-15 02:08:01', '2022-04-15 03:06:17'),
(178, 93, 'e39835b3b3d4945fa81d9ee6d30b5264', '39e202586baf29323b4f9edea4f15bb6', '2022-04-15 03:08:17', '2022-04-15 03:09:33'),
(179, 95, '551ad5973fa2b4339eded60ebebca174', '0dca1417e3785050a8981a44a09fe27b', '2022-04-15 03:09:45', '2022-04-15 03:09:52'),
(181, 93, 'b407ff819fcfdb18c5dc692b83601154', '2fa88226aec9c3a2ff55ea7c5cf1b5b7', '2022-04-15 03:10:39', '2022-04-15 03:10:43'),
(182, 95, '4794b88f4fe2e92e323c3415550d671b', '975a2905c26e43935d413873210d4963', '2022-04-15 03:11:48', '2022-04-15 03:11:48'),
(183, 93, '64bae973aa1ee46e1e34986c13a2810b', 'bed8b309268261c45f245aadddb1dd1d', '2022-04-15 03:11:53', '2022-04-15 03:44:11'),
(184, 93, '30018df722e8c7d48d2935c7c9e9e613', '72a8afafc685ff3e9203346de0579122', '2022-04-15 03:44:44', '2022-04-15 04:00:05'),
(186, 95, '6e0ea95456ad36e752da14b8a9f0b8af', '65b7b3b69955580c6307b7581031a1f7', '2022-04-15 04:00:32', '2022-04-15 04:01:19'),
(187, 93, 'bdddf1d4f8122226ed11bb58cb2fb9f1', '5c537ab76a1718ccebbe7fcdf19f3965', '2022-04-15 04:01:24', '2022-04-15 04:01:55'),
(188, 93, '2f7aa44d074c93ac9c6f2a37f2d71d74', '5fc905b7c7db578c624ba217b038be5b', '2022-04-22 02:14:23', '2022-04-22 02:49:09'),
(189, 93, 'd7f0951207ec55fe2f9f697d396d1ebe', 'ebdcf8e4e9810752608d19b21b9a2c9c', '2022-04-22 02:49:25', '2022-04-22 03:13:48'),
(190, 91, 'd169bfee2bb1352e725f4814561abe72', '64286aefd5413fbb3d580ef749f647b8', '2022-04-22 03:14:08', '2022-04-22 03:14:40'),
(191, 93, '6ad91add50a09a91b166805d900262cb', '7fbe06da804a4bc59b34bccd75e249c1', '2022-04-22 03:14:45', '2022-04-22 03:17:31'),
(192, 91, '284dee3549aae66b471df5d67676ae67', '74bbe24de37990ccd2497a6e478ac7c4', '2022-04-22 03:17:42', '2022-04-22 03:17:58'),
(193, 93, '2ad66b691914f51995bb70739c9c7c12', 'c4b96d4003552e347a80d5278b05605d', '2022-04-22 03:18:02', '2022-04-22 03:19:48'),
(194, 93, '23e6a63f0824944a4b0edaf5e33c228c', 'f469bbc026e3db1c5303a2e30cb40a92', '2022-04-22 03:19:52', '2022-04-22 03:21:05'),
(195, 91, '1d4fe8ae944a196f0c07bb2368a5e0d5', '55b8a864587745c70d356db1a47a184e', '2022-04-22 03:21:19', '2022-04-22 03:21:28'),
(196, 93, 'b5d79b7f526ac1c31731364bd5dcacdd', '242f5be8c848f6ba4cdafa2479659cf6', '2022-04-22 03:21:36', '2022-04-22 03:21:49'),
(197, 91, '2b6f1594c5415459bba80bb021a7c3e8', '5a6fff8d9fac0b033bc4d30fd9bd2164', '2022-04-22 03:24:13', '2022-04-22 03:24:18'),
(198, 95, '704dc80b8a8e88b5c1afa05124d50a65', '4bb798e6e500b61701c43c85021607a9', '2022-04-22 03:34:03', '2022-04-22 03:34:54'),
(199, 93, '2048c1e95075a7e3efe242a71586f972', '3873421148fd474e45ba58a2a7de230b', '2022-04-22 03:35:01', '2022-04-22 03:35:05'),
(200, 93, 'cd64055e1b97e1efe65fb5b64cdd8510', 'bfba12b5c6f48011ecfce8ea7b836c95', '2022-04-22 03:35:47', '2022-04-22 03:37:12'),
(201, 93, '399be881bcf72138ffcb30e8e0995504', 'e1c4e5ff938662cc9f5a1c8276354fd2', '2022-04-22 03:37:46', '2022-04-22 03:38:32'),
(202, 93, '4c2847e6b07d87a28101165cf162356c', '2caa731aae245bfc6846a6e9aef25df0', '2022-04-22 03:40:13', '2022-04-22 03:40:29'),
(203, 91, 'c45226c979d83f0cc8c1485ce004e3f3', 'e1f127cbc2e48b57998d7e15b309326f', '2022-04-22 03:40:41', '2022-04-22 03:41:05'),
(204, 93, '6e81d330fadee1cfbbb541bdac4098b2', '06e8a19c546cc9e13f9cc9370b07f37e', '2022-04-22 03:41:16', '2022-04-22 03:41:43'),
(205, 91, '965bef67e0703093bd76b784763f9d6d', 'f24f7d5119d77b35a73a392d669c0ba6', '2022-04-22 03:41:55', '2022-04-22 03:57:21'),
(206, 91, '34166923d251ba475c191b5b71b20e42', '4bba7c6bbf97e9d9a82af8a0e3fe1736', '2022-04-22 03:59:07', '2022-04-22 03:59:22'),
(207, 93, 'e5a1b844e2fc059b3c5e3b1dfed9866a', '122c509975f6f5821206078fba8fbeee', '2022-04-22 03:59:28', '2022-04-22 03:59:30'),
(208, 95, '2b28061128490dcc14f1b96ddf0a0512', '1c4f3a726ca3a0128d6fcfec26112377', '2022-04-22 03:59:35', '2022-04-22 03:59:35'),
(210, 91, '18eb7ce790092cc52328decbc89ed04b', 'e563780ad3b423e07b57b76ab490fd02', '2022-04-22 03:59:46', '2022-04-22 04:07:14'),
(211, 93, '66e7ab9c38151b42fe82a44a603130e4', '0f79b5228ecc6c454515d305cf2e1966', '2022-04-22 04:07:18', '2022-04-22 04:34:54'),
(212, 91, '229d59537d3b24cd3cfab1d72f9da7ba', '916b4e5e42fb42d7bc0bae5b72501264', '2022-04-22 04:35:01', '2022-04-22 04:35:16'),
(213, 95, 'e56b410683fe99012df5656c1f464bc7', '6de7c9bf346390f2562818398cbc7fb3', '2022-04-22 04:35:22', '2022-04-22 04:35:34'),
(215, 91, 'a08b34f79c399a3cd0dd2f82d13730b6', '6ea7de64b32f885e6f253b8949ce91f6', '2022-04-27 03:06:33', '2022-04-27 03:08:02'),
(217, 95, 'c80d320b5ead47e3b9cfa9036693bafc', '591c972646c4d34fba6af54f0f389755', '2022-04-27 03:08:19', '2022-04-27 04:01:19'),
(218, 93, '76856d200379cee9b0a20486ade82b1e', '0650d94d39494d08d3550ea1bc56b3d1', '2022-05-04 02:43:47', '2022-05-04 03:28:09'),
(219, 93, '20849d9571a0deb02cfe206c80c2666b', '673ad025cd61e24d3e595098224c24b4', '2022-05-04 03:52:33', '2022-05-04 04:23:44'),
(220, 91, '40f1d9da43282513c8102951a50370a4', '9598990b8f48496402b68a0cb8f1cf5c', '2022-05-06 02:22:57', '2022-05-06 02:23:22'),
(221, 91, '19dbd3b627dd1554803631da759bb217', '75d24c7dae35cacf9fb81a137d6cc034', '2022-05-06 02:23:39', '2022-05-06 02:24:00'),
(222, 91, 'e879da6e4a6067d476315c88299fda10', '326d1e15bbf5952c233e37f9ab8f17bb', '2022-05-06 02:24:05', '2022-05-06 02:24:09'),
(223, 86, '77ebaa504d45b0b06356f74c8921f781', 'eb7994a31c2efea5fef302a28b408a92', '2022-05-06 02:24:15', '2022-05-06 02:24:20'),
(224, 95, '65510f90dadfb0977ad983620ab9df32', 'e0a5b087856ecdfdfcaa6141b3eae34d', '2022-05-06 02:24:24', '2022-05-06 02:24:30'),
(225, 93, 'b12eb1e318528d468b897c0cbd1b7953', '83b7866329759e79e0efc0b8e46277b7', '2022-05-06 02:26:42', '2022-05-06 03:22:32'),
(226, 93, '18c2b2c1cf8e6f098f04528727f80095', '2a359af942f1de39926368c130a12b8d', '2022-05-06 03:56:49', '2022-05-06 04:07:59'),
(228, 93, 'e14c68df90e4cad585b2a59d5fd0d237', 'a4a1cba5be7f795ffbf9c3ea13e7fd32', '2022-05-06 04:09:04', '2022-05-06 04:09:09'),
(229, 93, '5ecfdc2c2e965f9d7513f9cf90c59d45', '70945c0f6aa72920ff7a771d6d7c66a7', '2022-05-06 04:09:17', '2022-05-06 04:09:20'),
(230, 93, 'baf8d0395e8a6d42bac2ce18f463bab7', '1c692a9a0e23ed85001abfaadc1b3a68', '2022-05-06 04:09:23', '2022-05-06 04:09:46'),
(231, 93, '14580df7f1c17609f7beea86c2c32130', '222bd191820b492802b843689e799d4e', '2022-05-06 04:09:49', '2022-05-06 04:09:51'),
(232, 93, '2d824969ed633fbba8ad9747d703f665', '2b273d0255fce7c9c9b1231dbad26e3c', '2022-05-06 04:09:54', '2022-05-06 04:09:56'),
(233, 93, '8f698a59469dccb8d038554020a0ef7b', '645d09062bc274fcbcd33b3e285ce5e7', '2022-05-06 04:10:20', '2022-05-06 04:11:07'),
(234, 93, '169e5fc8825eb5d0456d208fbdbf60e4', 'dd16e872cd6b5491577be6ea1b86e365', '2022-05-11 02:15:11', '2022-05-11 02:57:14'),
(235, 95, 'bc171c51c65e130a5cda94ec073b1673', '356d47fec4b6c2b4cfcf6e222b630472', '2022-05-11 02:57:24', '2022-05-11 02:57:41'),
(237, 95, '9c7adc7a71be991f83e3443106171fe7', '95ccca9ebda94856aeaca89c5effb0f7', '2022-05-11 02:58:15', '2022-05-11 03:24:05'),
(238, 95, '831bf39911b8c72309573e5e97ffb830', '8581b9c94949a25b9e7ac9790b67873f', '2022-05-11 03:24:35', '2022-05-11 03:24:37'),
(240, 93, '32f566187569df4f656092a6d7fa6c99', 'ce673da9393074be8924cf05318246f1', '2022-05-11 03:46:32', '2022-05-11 04:04:47'),
(242, 93, 'e4cc600ae88ea342ac340fcde7174d70', 'e4292b2947ba33aa474ed77de7c082ed', '2022-05-11 04:09:45', '2022-05-11 04:10:31'),
(243, 95, '57996881f64b487b1a1a460b2aa5343d', '28b5f993d6bc8019ada6adb66a673d77', '2022-05-11 04:10:34', '2022-05-11 04:10:44'),
(245, 93, '5e80701ca76adf5ef2296915fee69782', '80b6d4b8d99897d21bd4b0cfc35546ae', '2022-05-13 02:59:37', '2022-05-13 03:00:49'),
(247, 93, 'b29cc3395a16d90988d0bac3b233ea3b', '463c2b04154bd0befafd41f4ea55567d', '2022-05-13 03:01:16', '2022-05-13 03:02:35'),
(248, 93, 'b65c46ba63f2013b6e5a0977d34e8dc9', 'b2477ef829a8a2281cc0cea451a61a7f', '2022-05-13 03:04:39', '2022-05-13 03:05:13'),
(249, 91, 'c9da37023f62855e5e459065f84a626c', 'a3b7007caecc8cabc32ba9b37536fd69', '2022-05-13 03:06:41', '2022-05-13 03:06:56'),
(250, 93, '92163700bcc6d85e7f20796db28ac803', '90dc356e25accd7df591731f767246db', '2022-05-13 03:07:09', '2022-05-13 03:20:16'),
(252, 93, '3741639b97c7a280dd75da56fedef911', 'b5224f90bc57ec5631a30ef3eced587c', '2022-05-13 03:20:51', '2022-05-13 03:38:54'),
(253, 93, 'b605755d9f66ba611f1671d23d64dbf3', 'c1da063e727361e339cffbe7f65d77af', '2022-05-13 03:38:58', '2022-05-13 03:38:58'),
(255, 93, '67ba5637e804c5bec93774c274fc8f67', '884b150c01ad38a0cd611342110c51ba', '2022-05-13 03:39:17', '2022-05-13 04:00:44'),
(256, 93, '0f02d10ba262ace0186ce2f5a5e2381c', '56056e6aae69fba3fd389c845056430c', '2022-05-20 02:15:13', '2022-05-20 03:15:05'),
(257, 93, 'dca3e8dc14312bff5f2e6ada186a797b', 'e6cc4b45de41830db98d4b680a45d778', '2022-05-20 03:17:06', '2022-05-20 03:51:32'),
(258, 95, '55303e2aa970274ca4e3e22f58b67291', '87def6564cce2cafaed0f36e460ce4ab', '2022-05-20 03:51:42', '2022-05-20 03:51:44'),
(260, 93, 'a63428b821be3f6c3aad8f960782da71', 'c7f5a4ae7e5777237ce349ae15697180', '2022-05-20 03:53:06', '2022-05-20 03:53:58'),
(262, 93, 'd8ecd016383343f8f2cd8fb04358e436', '030d1ae6cc2ca035c49432af321c2699', '2022-05-20 03:54:34', '2022-05-20 03:54:40'),
(264, 93, '9f42befefdaf1593b9d52ff58b5cc35e', '9f1a5f11dc9c66f1c59c09bb93085473', '2022-05-20 03:58:30', '2022-05-20 03:59:04'),
(266, 93, '6473cd5c21e4a3a8afe1f0c3eed11e79', '955facaffdec2e5f82d8f6c6e2f02ab4', '2022-05-20 03:59:50', '2022-05-20 04:00:01'),
(267, 95, 'c8e265a0ff28549a60157e584da37091', '7e4666532e1bb704f8ce85e182d8976b', '2022-05-25 02:30:11', '2022-05-25 02:30:16'),
(269, 93, '6ed0d61fca699369a7ef23c9fe679273', '8efce787e65f3956bede8f765ad74269', '2022-05-25 02:57:40', '2022-05-25 03:07:24'),
(271, 93, 'a878a799e4fcd23ef97c082daf464dad', '54a3901f05dcef066785f72efc476ce0', '2022-05-25 03:07:44', '2022-05-25 03:08:54'),
(272, 93, 'a93209b7ec702782a9da9eacfacc21cc', '4c78c4d16de4607c5b5dd5322897c329', '2022-05-25 03:19:22', '2022-05-25 03:36:42'),
(273, 86, '19aa016bc8007b134854aca8f6dad979', '29780ed68f61800649a68ce024519d70', '2022-05-25 03:38:18', '2022-05-25 03:58:31'),
(274, 95, '57f2cd9a89824dd712836f88d5d55dce', '929d9960f717368b47e2ba774a25c811', '2022-05-25 03:58:35', '2022-05-25 03:58:42'),
(275, 86, '9b7f181ee1f1505015edc98149478fac', 'd3300217a3a8556320b20e09779a41a4', '2022-05-25 03:58:46', '2022-05-25 04:00:37'),
(276, 86, '9c275e5c80971b855289d5ac28b3584f', 'e0ae3f725ce0631270f70d034a50dffe', '2022-05-27 02:12:19', '2022-05-27 02:59:08'),
(277, 86, '763ce824912d94ef0271902d9e983786', '46d674e5d1f429d2316f6aa4373e50cb', '2022-05-27 03:02:14', '2022-05-27 03:15:54'),
(278, 91, '6bfbac9c3a6cc1e01688edb073ab1fec', '33764984929e952c7532268d42867635', '2022-06-03 03:37:53', '2022-06-03 03:37:53'),
(280, 86, '28ea619021098e572e88078010dc8903', '1d5468fac1965ddbdf5e41efce340017', '2022-06-03 03:40:20', '2022-06-03 04:05:16'),
(282, 86, '017ab108016c7d91ee20009c7c4b8df5', 'f67ff780ed65a189370fadc6c12b44f5', '2022-06-08 02:37:22', '2022-06-08 02:45:18'),
(284, 86, '975960240493fb34217827159d8ec177', 'bb62e200f35729e1139682926bd1c7df', '2022-06-08 02:49:05', '2022-06-08 03:24:13'),
(286, 86, 'bb79fd52509323f262f14520091056c2', '8668c094c2ea201b62e422964ac23efa', '2022-06-08 03:28:11', '2022-06-08 03:49:21'),
(287, 86, 'd24ad18542a3aa46fe2d57ee97c345d6', 'e96c4d38b17cdeac03b76daa33b8b1b1', '2022-06-08 04:05:52', '2022-06-08 04:15:56'),
(288, 86, 'ca34171a7f05b0ab73f2216f6e280240', 'b11f638718d15de7ee51df016eef5838', '2022-06-15 03:01:42', '2022-06-15 03:20:12'),
(289, 95, 'a8b9422df495a594d574d16702199009', 'b35353d682a58f8e2be131cad986624a', '2022-06-15 03:31:56', '2022-06-15 03:31:56'),
(293, 96, 'd7ba8a8d7a16f31a4f6713f8ef405381', 'dbcd325c15ac4ec89da235c8a84f5668', '2022-06-15 04:06:11', '2022-06-15 04:06:11'),
(294, 93, 'b6a81e10ef6ab8aab9233ed75547fc82', 'e3d025267cc1409a1443351732776808', '2022-06-17 02:36:36', '2022-06-17 02:46:26'),
(295, 93, 'b7e4afef62c7aaaaa4b6d66aa5030d22', 'fe678e1d91386b7a779af65eb319d4ee', '2022-06-17 03:09:49', '2022-06-17 03:09:49'),
(296, 93, '9cce75c3e5cbdfe761648a6bc44a3536', '027c38c3b1259e886cdf654aef56f1fb', '2022-06-17 03:11:14', '2022-06-17 03:11:17'),
(297, 93, 'f04deb13b1dc3c9e39215565d42783e6', '8735639776f9ae38335106176758b430', '2022-06-17 03:11:43', '2022-06-17 03:11:58'),
(298, 93, 'eded50ff2ba0b5c69c32396f113ca99c', '888613fcc2dd1500990ed928bd1d8e39', '2022-06-17 03:12:18', '2022-06-17 03:15:25'),
(300, 93, '0762e4a3e65ebae894966d21d3a5c08b', 'c1187115014caa4aa2f231e69dd2df4c', '2022-06-17 03:18:12', '2022-06-17 03:19:08'),
(301, 93, '04af1650640a0ea0f22622c4f684701c', 'd42aa22f723258a735ec2561d3b07fa8', '2022-06-17 03:42:11', '2022-06-17 04:09:05'),
(303, 93, 'e25998ce887d18a4b0545ea81da24afb', '159947ab17aa929cf0d4d07186d63876', '2022-06-24 02:20:46', '2022-06-24 02:20:49'),
(307, 96, 'ab85b424d4ecf1191fb93c6e5ad61586', 'dfce3117dae1da768ddd2d8f20b17e76', '2022-06-29 03:15:49', '2022-06-29 03:16:47'),
(308, 93, 'c8f6e9aa41a726d946231020e390909d', '13534451854b01f169da211cc705ca71', '2022-06-29 03:17:08', '2022-06-29 03:17:08'),
(309, 86, '4336043505a6977e1b5c7dc279995878', '80595554d548ad67a9080d8d88247e2b', '2022-06-29 03:17:21', '2022-06-29 03:23:22'),
(310, 93, 'af794f15d2c872833c5ec91549d61524', 'd7b4669e36e7823c972f46058f271f14', '2022-06-29 03:23:30', '2022-06-29 03:23:30'),
(311, 96, 'c07f180a25a8a99ddc5fdc3ada4805c9', 'a2f652be52c9754136e792c8b6601d11', '2022-06-29 03:23:33', '2022-06-29 03:35:11'),
(312, 93, 'c1e453405f094fb15c3e711a6a53046e', '07594d403455023fa23ba32198424ebf', '2022-06-29 03:35:18', '2022-06-29 03:45:37'),
(313, 86, '8a7e85ba42455df5dfa7a32beb95fb66', '87ee697671f58c964125fcd3c8b7a835', '2022-06-29 03:45:45', '2022-06-29 03:46:50'),
(314, 93, 'b187da57105277380435d0aee2c138c9', '3138c405ac4f75df08b52ed3e7615484', '2022-06-29 03:46:57', '2022-06-29 03:46:57'),
(315, 96, '37f6798a753544bfc7f741d038db9760', '81f98542b9ec93caa96fc7b9239a354f', '2022-06-29 03:46:59', '2022-06-29 03:49:01'),
(317, 93, 'aa9979ff93290da866375aa7e82fcd1b', 'cda885be6c8cefe75ceb894fea8d6bd6', '2022-06-29 03:49:19', '2022-06-29 03:55:06'),
(318, 96, 'cd78fff6fde9cd910d3905adeafeefcf', '66af7c04f1c66526d3b7dcc88cf1f1df', '2022-06-29 03:55:20', '2022-06-29 03:55:20'),
(319, 95, '02d04fa2becb3874f9cad18c58140815', 'c7c5a219d5e671d30f10b4dd9527f0c9', '2022-06-29 03:55:25', '2022-06-29 03:57:04'),
(320, 96, '0c5273ffec229d15faacd2c157b1d836', '3cf1983918e406fbf858c84f259139ba', '2022-06-29 03:57:14', '2022-06-29 03:57:18'),
(321, 93, '373b0d8d965d21cfa9f7c98df72e93c9', 'c4968a5c67b92dfbf4d8d775d74bd179', '2022-06-29 03:57:23', '2022-06-29 04:11:46'),
(324, 93, '7a745913ed5c70fd6b6eb0fcce43efb6', 'f1e54958ed35555f55d3178ae7cbab08', '2022-07-06 03:10:05', '2022-07-06 03:11:46'),
(326, 93, '8f269a9fd52804942da61b614052614a', '7bb231e897a234fb2fd168dcb65a455f', '2022-07-06 03:12:33', '2022-07-06 03:13:56'),
(327, 93, '2e4fa0205312bf313b3ca3f7231434ea', 'e5a3d475dac2f89b130c65c59f61be79', '2022-07-06 03:14:07', '2022-07-06 03:14:12'),
(329, 93, '6a209d087d90e90b6e3fe4472bdf60ad', '2f23964b60c6dbcf9fbc619b6f6a1442', '2022-07-06 03:20:23', '2022-07-06 03:20:27'),
(330, 96, '7d2bcdb3c4901b350d70ce119c8964ce', 'c17c554b18a00a810947505110709a96', '2022-07-06 03:20:42', '2022-07-06 03:20:54'),
(331, 91, 'b8de614c723a721a1fa011f21b3c9a35', '51f6e55a34fa8028e36e4bca588ef9da', '2022-07-06 03:20:59', '2022-07-06 03:24:47'),
(332, 93, 'd602df8cd5eecd6314b76565c3680229', '75b54cf0aabd49828f11344896ed45ef', '2022-07-06 03:25:07', '2022-07-06 03:28:51'),
(333, 96, '2147eb00c29e8a996520e081f9abc054', '761719a3e2448e47eb5ece53c052d1a3', '2022-07-08 02:22:41', '2022-07-08 02:22:59'),
(334, 96, 'cc4aaa8661c734a5247743d5e98d56f6', '648bf37c23f51bf51a10bdac174462fb', '2022-07-08 02:54:40', '2022-07-08 03:53:56'),
(335, 96, 'a091ca85cfa917edb03a2e084566e65c', 'fdb8aca66584d8645a7dc1d3b8dffd34', '2022-07-08 03:55:14', '2022-07-08 04:19:01'),
(336, 96, '62249828e8eb3d33c56d44f8ac03884a', '87e1c47302995228862a864dccd6d54d', '2022-07-13 02:18:35', '2022-07-13 02:43:35'),
(337, 93, '20207351521268a43ade463b7502c8e2', '79707102d8a3d2409a45e68f11b29b3a', '2022-07-13 02:43:39', '2022-07-13 02:44:43'),
(338, 91, 'fbf3932c8329ec4fcfec9fff8bb8aa41', '456d8a22a7fb1854166acbc87867d88a', '2022-07-20 04:30:16', '2022-07-20 04:30:16'),
(339, 96, '4c43e86b4fa0f67fd774376eb9aa1df0', 'bcefbeeb7281a2aef14a55792ae50c3d', '2022-07-29 02:24:11', '2022-07-29 02:24:11'),
(340, 91, '63395377dbf2ffff004ebb5dcd34d009', 'e97b24f563001829e291267a858b6352', '2022-07-29 02:36:29', '2022-07-29 02:36:29'),
(341, 91, 'ecdf1f4649a7e2fd6acfa270cfa8b7ad', '047ed5b502cae755a3eb3dee98c880fd', '2022-07-29 02:38:53', '2022-07-29 02:38:53'),
(342, 91, '44c0997ae37334eecbfe0dd1dd852ca3', 'e0436378713b2cc278fbc1e94c02301f', '2022-07-29 02:40:32', '2022-07-29 02:40:32'),
(343, 91, '811be1da5c8a70e019c1f1f368703da4', '2d995071e05fc90150f194ba63b3d2a8', '2022-07-29 02:42:51', '2022-07-29 02:42:51'),
(344, 91, 'f408b854c6c8bd894d9f8c0d1bea06c9', '7d37391415f0b6aba76bd4d10a0d2b55', '2022-07-29 02:42:51', '2022-07-29 02:42:51'),
(345, 91, 'c66a1e8b5f87952b9ab17a8d3d893491', '3e5bcd3d6c2f0782fa9181c550f9cdc6', '2022-07-29 02:42:51', '2022-07-29 02:42:51'),
(346, 91, '925240e6f23e4d0a2b7b1ec49ff5802f', 'e47c1ad832a56363ea367deb8865c3b9', '2022-07-29 02:42:51', '2022-07-29 02:42:51'),
(347, 91, 'eaa2ba3d30600ca253d42f47bc649ec0', '8b7dc39eea89e56950b6b3096e9e6493', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(348, 91, '9c6b4d89cda71bc2e6c9b142d590115f', 'ff6dd8a83d8c8ec7c5e0c19c416c34c1', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(349, 91, 'e0449f936188cd1c62e7da56c32c4c43', 'd42a571dd22c2c0e0460ed1afefe4704', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(350, 91, 'cbb1c4cfe725f3523beaa3254d8973c6', 'c1a8e564d96e9eef46daf181f031a6eb', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(351, 91, '16490dcdbf4cb7142144ea1f4f9f537d', '95fdca60341868ff3c01867f7fbef87d', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(352, 91, 'cb690b943e01d7f91a3c6ffe9b08b7a1', 'ea900d78f48b507524950a97c6011d51', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(353, 91, '1d48c9f8a46670d63103c05487aa5896', '300baeeaa6f849a0f7a79be796093f72', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(354, 91, '5f86f58f7d3d4bfd0298fc707c7499b2', 'b38e94bbf535bdaae72bbce4247edb5f', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(355, 91, '04da1a7f05a7f4fb62457eeff3981129', 'e1a45a36fc399d0c5f5d6f5448ba2e5a', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(356, 91, '8efeff6c2f66fffd0b2f64fe9bfae589', 'e21de62565db69f031c2c16a1f195286', '2022-07-29 02:42:52', '2022-07-29 02:42:52'),
(357, 91, '6e6e5564006ac486b09b75327ce740b0', '772a5a8cee4aec9cf52ffb50590e654c', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(358, 91, '02d815f3b8965af8e28ea218c5b445d1', '2b66ec54256a63d8fed88cf87d1dcecb', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(359, 91, '495f0571bdb5b15b45cae551da23a870', '553ca507204f0f9c3377ae64e22bc0b4', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(360, 91, 'd563659bf3bfbe7f54954f04f19abafb', '23be51e20b4a7f774dc0e802c4801ac8', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(361, 91, '2b1f9c2ac99b968696d31e7988d7ad03', '5484788f2977ecb5cb91210ca0d70c17', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(362, 91, '717bcd70ef59592c75ff76f467f16814', '1b9246d148dd13479af12ac0a71138f1', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(363, 91, '73d319c06c658e36ab7d760f1c6957e3', '3e74f5bbe05d63bc1fd0bf6c47cd79e8', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(364, 91, '3dbab9c1343d544df8dd29caada56e97', '8b2fdf9b3ec343fc390118c9e8560c9b', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(365, 91, 'a9240d17457bc39dfd95e3c8fc0a7118', '4d51eb8e47793f569a6a5c56d1fb98e8', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(366, 91, 'd29455df69c17b07c57a2dd3ece17eee', '09ed2e6353a93510306485174c30bb82', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(367, 91, '670b1aede01424aeac020e58e2e54fc3', 'de39e0d60d2d454dfa36443394e51ded', '2022-07-29 02:42:53', '2022-07-29 02:42:53'),
(368, 91, '1a90ca22d40ddfa93bcd35d01fbcae9a', '63d032cbee99faae065414587e8d7114', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(369, 91, '922c2e63c19b342241f5f88098fc7681', 'bb412cf7dad1f16d8b8920f6f25321a7', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(370, 91, 'f2ad2cfd6b8631414dfa202304a6fe23', 'b49d3ff16199564eb67558bed68df503', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(371, 91, 'f4e9cf8ce10276130e882c6f5538fea8', '47f194ba9b95a0e4400741c883a614d0', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(372, 91, '17e293dfe07023beca8e42d3d65d7164', '3a4a8f93c26026e18aca823258cd0a84', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(373, 91, '27cd13699eb49bf6440238d26a097f38', '81e1c6ebbc3d66f610ebc0d062ab6b71', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(374, 91, '8285dbaf5b5cb39625ae99598faea28f', 'e5f480f39ef1eac3e413eda1c43dd46e', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(375, 91, '9d1e07bc279edb46ebf44e1f8b30ba20', '16ab91806f5fc3665ddf4395964b939c', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(376, 91, '14bf6f8d549f38a8cadb01479af2fdf6', 'a21cf874245119c7f4aa9b36bd74f8d6', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(377, 91, 'efb280fddcb7d9e349e32adcc8b6fb67', 'bb055e5310eaacead1fdeb9aa5c2bb5d', '2022-07-29 02:42:54', '2022-07-29 02:42:54'),
(378, 91, '4037f6d6a7f680a8701dfe0745eb7f3a', '00df2e70bdbf1bddc829d9cdb604dde9', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(379, 91, 'ddfd2b4fd0216151e45dd330f1a58489', '8eb244d0adf55eeb398a575c1da6537f', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(380, 91, '947285e313aa28b5b1204e99b8d4f58b', 'f81cfc6e84f7097a52c74a79020de9f7', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(381, 91, '1112b0ab4e450c5a9bf5954afc9323c2', 'edd58c33d93daab73b549e5264e276d4', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(382, 91, '7b2f1011f8b26c57bc946592c4d36435', '5201adacc677992d894eef916e96bf19', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(383, 91, '7ba72186c78654d618d7ca400c4a9e12', '9132f66195fd90d57bef8a31ec6222c9', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(384, 91, 'ab9ae5f453a68cf62ea4d515b48a69b5', 'c10712109bb9a19759b5ce68ed1f71f7', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(385, 91, 'b78092a13ed953ffa7b0b2168572ef89', 'edd6257d93fd88dc9ba2536e23daa939', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(386, 91, 'a0c426340842a1996358469cb0d59a30', 'f6c370fd9887e7f3a01305cca33c1d22', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(387, 91, 'ac576a69f498a39d673e35de63e0eb40', 'c176502ba38bce1068e4feaaf423320d', '2022-07-29 02:42:55', '2022-07-29 02:42:55'),
(388, 91, '3491c34bc03d9660ed0ae422b8a69bcd', 'a2801f967a5444eeaaeac2c64791cb0b', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(389, 91, 'ba16be3421f1141af75e96132a5af34d', '1e53464be1fb5753b94dd8de57fb52b4', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(390, 91, '8390ef9a86e4b247d1c0d08faac5a0bf', 'f04df1c860dc88654d291523dd9a302c', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(391, 91, '19c6577965c26ed44e8dd74159608336', 'dc5415ccb50d141d1ce456b5198f4bf5', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(392, 91, '8311f38f8d8c103d6a6c9658b7bde6b4', '122ce9dad188c7e766dc1975c3fc30e5', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(393, 91, 'a65a285e827ff538848505b10faf81a8', '4469d34b973b875c24d1105c35ad3a27', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(394, 91, 'c3fa4116f718c832629f691b1b037545', '1a4be2c1c55be09b4c75ef321944d8e8', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(395, 91, '3b3b3e800df2bbf599d9ccd06647eef6', '177d3e67c84812218d7e03f61daa8b4c', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(396, 91, '58db5c6ac3d0d6dc8cc85c38752cbed2', '9c5e7cc51ee8f90406fe5d1125661cb1', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(397, 91, '62acdbd6c7e2d3ffa800944c8f33811a', '54f4de08808856410143c8e8230f8782', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(398, 91, '1b132b2ba4a0a889f15eae7c500df8ea', '065ffe6bb57cef491d28cd19360ec410', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(399, 91, 'd3b3205e78d7cf9893b37471cfaa027d', '9b44c0999c0eff5404e85832d5fbbe15', '2022-07-29 02:42:56', '2022-07-29 02:42:56'),
(400, 91, '42be3d5d06e12bd754d0dd32bf4c8346', '22996ffe0704d889fe9d0e381b11ba9b', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(401, 91, 'a2715d76bae9dbc8cb286d169b7b2459', '1ca3abf565201439633683b0166b04fd', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(402, 91, '70f6c536f9091785e77115c253af63b1', '1cfb0749d36868ac7cce44f0d5f60ba5', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(403, 91, '4a4d5a1d297403427793fdeb6d0f647d', 'a1d757bc2f1c4111ec06a22b6f0fb092', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(404, 91, '564f49253f7ee1f7534acb648ed6b6db', '9ad5a3a109d29bab471b593ec8f41ccd', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(405, 91, 'f4262711af39061ddbd2d64f07efa856', 'a9a839619346d03f3823a2da43e97e0f', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(406, 91, '6a1b68d3d5bd6aada69baf9f122f9d97', '2d77c524bb02109808abe6579805cb27', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(407, 91, '6fd4fb2ce034c1a6adf0291766a30bb7', 'd4fcd897f114fc0f03e43f651d60f399', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(408, 91, '264e00ffe617ee2403dedae30598669c', '71cb685e7128b9306a9e5151e430321c', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(409, 91, 'ff23df3f627d330e55ace96839aa3a4b', '3717fe0fc8aaf86ba8f6f071fef00fda', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(410, 91, 'd85601d0bf366cd758ef745d1c5d5b9a', 'e2db55d8945c33e7450fdeb6966bed7d', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(411, 91, '8fec81e1e69e2d1ff5266ce75d61c659', '5da98e8cb0cee07272ae99bde3832a58', '2022-07-29 02:42:57', '2022-07-29 02:42:57'),
(412, 91, 'acb2226f2170db4ed1e8f4e5f12a9ed5', 'f0b526f73953c4635805ef95ff31ca51', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(413, 91, '7daa5d987b4308f78dd5738c27074c03', 'a32300facfdfd9113ee97dbc6a2bcd37', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(414, 91, '8f76dbfca0878317cc379ab74c9acdd8', 'e24b8b4cd9a448f908c1dbb9d346a443', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(415, 91, '51babf8465948704c7db106d94c04bfc', '275ade454926ed86359b654bc7d0e78d', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(416, 91, 'ac33b6bab292a6651bb0f633ef60cb99', 'a5c498461218bcc297591616fe279e1b', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(417, 91, 'c9d525701c1b77a6b49ebeef022f0227', 'e406d221c762a23b7619bd36679ff18c', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(418, 91, '838c0606578c3400f296fbdbed4b9f02', '2fe02d215947a675b147016e8ba18f53', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(419, 91, 'd1c2e772f23548958be013d010481dd1', '5d441b344e761419032c29a141a9a86e', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(420, 91, '5de74624f0b0c70e74b0411d968e12f2', 'cb7f940cf51596559a48b006758976e7', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(421, 91, '49dea33137c8ab9b779886f8d781408d', '80263cdd5a24acd2dff5377c276b0cb5', '2022-07-29 02:42:58', '2022-07-29 02:42:58'),
(422, 91, '4f70b6b4b26fca4d561cc0d927aefb83', '869f25a815964908398ec52beea81578', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(423, 91, '06a816ee8901037b63d2b9b6bbaf5ae6', 'a6306522315cb37afbe3adf2425e9f92', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(424, 91, 'ba984a8da4fc8a9661f83ca07e1cced7', '8a79b6b1fb2cf8f0eb6f56070fa76a77', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(425, 91, '258457db18fb081d1fe5822480848f3d', '2db498b72eee0bfb1dcda5aded25c06e', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(426, 91, '962136de28e72ad7ba9ca6f45ccf1cd8', 'e5465e027178c6f8c47e05dd47dea940', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(427, 91, '2e5d9dfb9d1afd98f942a028c81f9f9b', 'e8aa07dc7760fa2594a4d4a6c12ef1de', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(428, 91, 'ebfa9b7ed2ed90422d027b0ea7ea5e36', 'ff870353a6952c73204d1fccadc09eb4', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(429, 91, '45a7506920c026a5e3bc4913a0aa9825', '061b6abe62655bf3a23d140721c38677', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(430, 91, '62457c0ca57ca869133b544abf66647d', '31a5f39c35ef0acc79806fd892f1299a', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(431, 91, 'c1aa87fbbcb79c30095b2b0235ba355f', '4e8338fb3917f98d524d48b275d5e067', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(432, 91, '0f8eb2099493618087df440eb2791e17', 'b21362c1930fef4bd8cc567beb9a07d6', '2022-07-29 02:42:59', '2022-07-29 02:42:59'),
(433, 91, 'e7687ecb85c472f5f086036a6a526e6d', '62291840b6b9bb28317bb9bb1639d954', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(434, 91, '6827bfe3d3831fdf50aa9ab583ad0fcd', '60faf423fa8373d4ab8a919013f0d5bd', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(435, 91, 'f5afea644aa46b4e8eb386e68783ff45', '102b31af3053bb06aca77971fd0eba0a', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(436, 91, '3c6d031b7dedc7c58c959dfb90096236', '0e9fa72a9d2a721fcd3b6a0661bcea64', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(437, 91, 'f4f26be0e27eddaedbd9a234d0402100', '9437e5c54334d885feed643e44d91309', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(438, 91, 'b70f5f8bfbc632201b3409310341c405', '4f81e14f71943145140349cfe1d423d0', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(439, 91, '89b6edffb6a60a6f0bf87eb61ca0940c', '3d9936055f8a6c44d58d8bb824cec285', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(440, 91, 'f8f05f96f7dddf27df1a4650c5ce3b99', 'b734331bafb1ded104b3f004f66ed498', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(441, 91, '08068c0946b424bc9b7b5cba5689fb35', '94664d5941cad80e72dc3475e43a5ba5', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(442, 91, '65d056fec77a6e80c025e978b6133a97', 'f1cd5c9e975e3d236d521b81ad391ebe', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(443, 91, '819f1d4fd36a91eb5b8279bc9686d5e9', '51f7fb5b2932dbd04872b6d556c20790', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(444, 91, 'de2bc9436eb7ad2f1818e4f1b00a6b52', '7ed3eac6a10b00144ffae70b368ff537', '2022-07-29 02:43:00', '2022-07-29 02:43:00'),
(445, 91, 'a5ce9829188b6358ed94737a56990c65', 'f6faded0bafd9d82648fda46492adbb4', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(446, 91, '3e8a7ee69a071fe92cc027475cd3f33c', 'd77f7b1d67c113d201b8dafe7fad7164', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(447, 91, '708126519896f67f20f8a13e6e7bde41', '7c14a75fbf1cc5eb6a354eb5bc4bb9f4', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(448, 91, '44a59d6e36927b96b9268c254144a82d', '30d94882688fac5e41b5c75a050ebdfc', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(449, 91, '89e104c9cbd1b5fba7791f2c3b83e120', '5354b3fca9ebc898af885fea413fcb8e', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(450, 91, '3984ed157644bcaeaaf34c7e33de09aa', '675799e0007d7c20a7bdf83e8ef349f1', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(451, 91, '0adc5df5ef5514571163b1f6e8c51a85', '5a8ca266032d859c43b3b5ec2cee56b5', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(452, 91, '2dec9fc75671a726e247cca9b1dd30ec', '7f42e4a26d86e8cf02e1ac1f71d499a4', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(453, 91, '641892ed190ef794ea3190398f005663', '88a759106821711f73d6bca0b6e0070e', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(454, 91, '34c71ecbb6e76a4d951f3bdb00d2e61b', 'eeb1aabb70d8a074d1848350ef8902ed', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(455, 91, '72da4ffe65b293c1646b5497eb053c73', '6cae524a3b616a5e275bba475579b7be', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(456, 91, '512b3a9522c70b9c77dd38d1b8007288', '93fa2dd59081a58520438d7963761b11', '2022-07-29 02:43:01', '2022-07-29 02:43:01'),
(457, 91, '60ca1fc32b08cef235ec1f3e54b99ef1', '7c7ae36139224086c9c6c5b2114410dc', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(458, 91, 'fbc8c6d277ad0946bd08b2a4860ddf31', 'd510d1d71a261f5678035420ee50982b', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(459, 91, '0df34b6df151f678775cb77a69f61e35', '2a391f87025878f6731167a60e214861', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(460, 91, 'c877c33efe9626eb747ae34df60e8d08', '386d874ea3d17a43626a21cacfd01f4b', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(461, 91, '606210560a86d6654fd2d43efcda6b88', '8cd9d24ea17adbae947b286445cb5ae3', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(462, 91, '6697929201a3f6d5f1362f1025225323', '8e042c76b71806fc117dc6169b35e8b2', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(463, 91, 'dd78d52228fed664d244c948ea64db03', '6819b9e6021e08c7b6c22b8d07930f1a', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(464, 91, 'f6fc2dc3a478b8235130c237c9cd6eaf', '0e1a340e35c54359e7bc5da6cb0ccd85', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(465, 91, '156b4eec8d96d536b9b30b1e97139ccf', '36e16bc300ac616d58529be1d1420175', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(466, 91, 'f2a8544f000085a4566232492d8c62a5', 'e9fdda1c44017f432097422fbd855de4', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(467, 91, 'e7eea549c8199d70d0bf950741ecd88f', '2d0149b86ee3d750e5284025f187c74f', '2022-07-29 02:43:02', '2022-07-29 02:43:02'),
(468, 91, 'fd0ad7f54622e1da6dd5c099cc608a9b', '72010fa78d44693f22e6ffcea326426b', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(469, 91, '37d8c176ff486d6bfb26260b41e6581d', '92b89dd94d3450cc66cf1940383e0ad3', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(470, 91, '4cc9ec264be1520c52879714b6a5175f', '480b12e76b2c1904c36554a06a098dcb', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(471, 91, '70e0a09a41f56b4ee299ea221878d182', '413077e65620a645c3dfe51948e64a29', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(472, 91, 'b023f4d3cba569a5ca180f5e69ec4202', '3ff657382e057894edfcaf17d64ccc34', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(473, 91, 'ef049c4a1b0aabb62cab5d8444c77f49', 'a360ee260a153aa3b6b263c56734c7d7', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(474, 91, '583ebf7bb99e24fd2978dc2236d7178a', 'e95ef86e918d07bce565ad2355de0207', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(475, 91, 'b697a0aaefa46b06812f551a63a40ef4', '04c0f0eef95965a7d9090060cc355e6c', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(476, 91, '0b35eb82a7334e93eb31ba45fb8c1277', '6de7d6df0ceecd1b96acfc90cb05603e', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(477, 91, '02a962749b85874fa374c02db54a0836', 'a16df1c9138040bac6df2db8d11a4805', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(478, 91, 'aef7f8f92b03a4ca55148ac1a9fbda69', 'c17065e8f618b6e3da99d142362e2f55', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(479, 91, '444a8fc7e35686dfa9e6851107186108', '60b63dd80aecbd0303d4f8883e2656f4', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(480, 91, '72c8a40af8928669498ca7123145b773', '14335bcbaba2a12269ee0841057938ff', '2022-07-29 02:43:03', '2022-07-29 02:43:03'),
(481, 91, '9bd3e1b781de9ad587574d60adccd552', 'c8f320088e113524a983030fb28ae126', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(482, 91, 'c849f59eed6feb52921332172c19a9d8', '8fead15bb1ff21946c02c5a47f343a2c', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(483, 91, '8295441ed080c31ddc4587dd0d42a6a0', 'ea9909c9ad360f6cf7ec4ef54cc795d3', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(484, 91, '9ea15fc54449254d84deffa624c7d0e4', 'ebdefee5cb072d55a2f4a5ce4d5f3cc6', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(485, 91, '07a0ecbf702249e36f8f562770a76a0e', 'f11a7d95f847592ee7233c6a6e6b12b6', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(486, 91, 'a59a2adc36d98d86e85c77de8cdcc418', '241ac285f754dcb003651b730846e519', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(487, 91, 'd1dd711d047d4ebb34a7aa182c533136', '9d822a70616b8355cc2952cc66a7c8c5', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(488, 91, '00cf9e5ebcc4098fa5b933e79431f7d6', '9852fa92c3f0552839837a14b90ceee6', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(489, 91, '404ce4cb3c2ba59d08aa273a0efe2f9b', 'ee3fced33182ef1506dd8e0d34200bbd', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(490, 91, 'b1671952c7364a45626a7c47cd80dcbf', 'ab627377e6792f134f9f1c655a7f2629', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(491, 91, '31da696a121c26450f6492c5a7d537c4', 'aeb82628663ec8291388024dcb3d526a', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(492, 91, '229b62df50680c0fc68d9bd947b2efe9', '59a83a6e03c1b9943618c16b481d525a', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(493, 91, '935f74d880fc720793147060508e5ebe', '4624672422843c70963ea6b91dab93b8', '2022-07-29 02:43:04', '2022-07-29 02:43:04'),
(494, 91, 'd0ebd454f4b7524efb40e9cc9832eb69', 'e89f3f2506f6adcf23761b83343c7569', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(495, 91, '1a83cbe65e9e90cb1d41d34840d3cff2', 'a8291f55c4f69aba420552abb64e4efa', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(496, 91, '8e8e5f42acc04dfda350c57c1f638156', 'eb2ede9279287199e3fd73b7e67fcaf1', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(497, 91, 'de4c537592453d44a60e0e67a737586f', 'be16cfc3346fea50fe028d8e5e54147c', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(498, 91, '2a0ecb206d538d9ff08eb43a503b55d4', 'cf71f7829e2a4eb3ec394e8963f7f5ae', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(499, 91, '3a13a190d4f71a6ecb2e72b739d4e1b7', 'aac2d4d97df64d40a6fa3586260c6ed5', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(500, 91, 'fa0c26fc8d0ce475733258c4f1786caf', '9217041a87ee7a8afd6e49622b114b69', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(501, 91, '20654a03d0ddd64f51ac70c7e4e1e70a', '3eb7d61710112374e0a26fe89722a430', '2022-07-29 02:43:05', '2022-07-29 02:43:05');
INSERT INTO `sesioniniciada` (`id`, `usuario`, `hashId`, `hashSesion`, `fechaCreacion`, `fechaUltimaAccion`) VALUES
(502, 91, 'df43d822dc7e0d56b92674abcee1ebcb', '08a0bce780493e5c51e5489fc5f5d6a4', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(503, 91, 'd57783a439d358cb4ec3e8f14cbd2149', 'db0ee02209011e15881b7fb3415d2110', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(504, 91, 'ae50199491e24e49f38145a6549848a6', '333218d8925895b41fc214bfee2d1161', '2022-07-29 02:43:05', '2022-07-29 02:43:05'),
(505, 91, '7a31b327308e415cefb0229bbc6fc896', 'd368cf4051ebab9ae608ed58bf446aa0', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(506, 91, '0042b9a5032223ab7eb35ec3f0a332c7', 'e46ff0e19167e28d0cd28cc538261a53', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(507, 91, '1a0e01db7bddd51ccb3fb25b06cd2b2e', 'a9ba9611221e8f347a2bf248db3d2004', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(508, 91, '19c6b517b07ee73c66079443a351a0a8', 'd88b1ed4c24d2b3a5a95eac9c3f3b497', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(509, 91, 'd0a8c6398cfbfae70af898a4144d2089', '83fb19fad86aff6e6c650d168b9eeb49', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(510, 91, '6d215cb3b5073583007c5e55e69a9ae1', 'd0f57c8e1bee28513fa4e08482a28804', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(511, 91, '835d9b0c58e387b24e2e389ac910516f', 'c6f65003d8e4219786d81277fec9b3e8', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(512, 91, 'fb0f761dda9db2c83efc1531bcf8bc51', 'd10b679fc15e3abcdea27f210965b8b5', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(513, 91, 'ad533fa749bb3dc58149e1f4468e03f2', 'e542b5e8badcf2c8bf76777e5ef62512', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(514, 91, 'e8885a74d53d9996fde7d7d83bec4405', 'c2e9fd821dd09b8deb74893f50a957b0', '2022-07-29 02:43:06', '2022-07-29 02:43:06'),
(515, 96, '3e66aa2cc29861893cd83908080accc7', 'a4746b4029ad4e5f15759622ac6d8267', '2022-07-29 02:43:37', '2022-07-29 02:43:37'),
(517, 95, '5da6b95de00d6476b9ac42065730ae8a', 'bb98a5cf6a49a478206a36a678cd0dfd', '2022-07-29 02:52:28', '2022-07-29 02:52:28'),
(519, 95, '86e508902fd526e0cf95904dbc4c06d8', '7b2b508e318f85e49770d7955d84a130', '2022-08-03 03:43:53', '2022-08-03 04:13:15'),
(520, 95, '5bcd6e391897aa6bb4e26efa17c73ef0', 'ec0afc680c1830f452dbd5a81cfcb78c', '2022-08-05 02:27:30', '2022-08-05 02:32:11'),
(521, 96, '610fd1b7f60b7d81b3c8e22f34bf48ee', '800b34ceff309e4e7adc1ba3e1463b80', '2022-08-05 02:32:20', '2022-08-05 02:32:53'),
(522, 96, '563957f0837e7d65aa29b1fa0920d45c', '1ff3895d3ef290ec76157e8e5b15b431', '2022-08-12 04:03:49', '2022-08-12 04:04:27'),
(523, 95, '4a50b5d90d83bd904e4890b0bfe4bb3f', '5a58f4a4f44a01f89794da7de2cb5fe1', '2022-08-12 04:06:03', '2022-09-23 03:38:02'),
(524, 96, '804487ab35a1f833d715bf73446e3e2b', '97a6fb359b84a1dca9ec788365791299', '2022-08-24 04:13:25', '2022-08-24 04:13:25'),
(525, 96, '6577237fb90c4d832a78f5ede49db035', '92589a2c00ef6ef224a34bb8b105915d', '2022-08-26 02:32:02', '2022-08-26 02:36:57'),
(526, 96, '0f14cc3ed1061aa8700c9057a4a98618', 'd047d25778b31d2831b85bbd19cf58fe', '2022-08-26 04:20:31', '2022-08-26 04:23:28'),
(527, 96, '47fde096d60cd8da506e100ba3bffa86', 'e086eda6fdb6ab75a6f2df083ff6483f', '2022-08-26 04:23:52', '2022-08-26 04:24:29'),
(528, 96, '006007f861f809c130554b5a5560dda0', '70041ff6fe0c3cd8fdf905d87682d869', '2022-08-26 04:24:50', '2022-08-26 04:24:51'),
(529, 96, '946131d5615f2702d96cb7d280457997', 'e071feabd5ab3e6ff1a8c517837041eb', '2022-08-26 04:25:31', '2022-08-26 04:25:32'),
(530, 96, 'ed602ae40acadffd0bc98fd9e441c334', 'f79651b91857652905c8d730cbbc42b0', '2022-08-26 04:33:09', '2022-08-26 04:33:10'),
(531, 96, 'b6c27208eb5900adf5ecc3fa54308fd4', 'a7c70ffa1724458d9ec457db8e91d1a3', '2022-08-31 02:32:59', '2022-08-31 02:33:00'),
(533, 93, 'f4e6e58e8f7badc2173167ba40f5b165', '6691abae0d4da8d207172c1b7f99e29d', '2022-09-16 02:27:49', '2022-09-16 02:27:57'),
(553, 91, 'eff7b30db675bebb5f69898b8a984ef2', '0936bb5bb86d1092ab8f6d2ca6f5d3f0', '2022-09-23 03:51:36', '2022-09-23 03:53:59'),
(555, 91, '38b29366c4f9a019a897bbb37cb61cd9', '40482259a65bd34f38f43978de615769', '2022-09-23 03:54:05', '2022-09-23 03:54:09'),
(556, 96, '545c3e5f0de3395693a6fbcdb8a62be2', '8eacd5ed7962f431a945003a660c114d', '2022-09-23 03:54:13', '2022-09-23 03:54:13'),
(557, 95, '5b8af93e72f82dba2bb83083fad4cf6f', '0ee19ff09fcc78346ada6013c36e5fcb', '2022-09-23 03:54:17', '2022-09-23 03:54:18'),
(558, 93, 'cf63c5ccf2833971bb75f44ba19bcbd5', '8bc5d4f0c1dd7ed118af0c16e210eb2d', '2022-09-23 03:54:45', '2022-09-23 03:55:56'),
(563, 93, '00009b1c628d65a694d95d12ceb24721', '7e952a42df673cafb08cc5330bb3045c', '2022-09-30 03:50:44', '2022-09-30 03:51:02'),
(564, 95, '18c22a8e231c8f8a1f0c6b8867b52634', 'ae0f029233e612b3311bf296f9312318', '2022-09-30 03:51:07', '2022-09-30 03:51:10'),
(565, 93, 'fef1ae851d318abf6c2bf6cc65504a79', 'a1cf25d593df8948c391a2e63f5df335', '2022-09-30 03:54:16', '2022-09-30 03:57:56'),
(566, 95, 'eca4cab8b78549f6f07b32b0a11ea88f', '766377ccf78fe9a7b81747048146ba9a', '2022-09-30 04:02:33', '2022-09-30 04:03:19'),
(567, 93, 'f46aaf2f2f45fbbada4224dd9f872e58', '2212e58dbd395ab284584cee66d6bde7', '2022-10-05 02:34:00', '2022-10-05 02:34:00'),
(568, 91, 'e6fbb40bdc4f7916a928d4feaaf10679', '467e57bbe93df12b8aebad163b57a23e', '2022-10-05 02:34:22', '2022-10-05 02:34:30'),
(569, 93, '525411e42659819d7f1481e0f2f3a0f1', '90bece4bf429565389af6ffa2e531eb1', '2022-10-05 02:34:43', '2022-10-05 02:35:20'),
(570, 93, 'fd2dce9ab86b762116dbcc3d26ec32ab', 'd59cf810442ea4116a17ee801e4878bb', '2022-10-05 02:35:27', '2022-10-05 02:35:27'),
(571, 93, 'b65695f1b36ae011338bd4ace2df4ee7', 'a9f49c3d0dbde04b5cfacfbdf34dd169', '2022-10-05 02:35:35', '2022-10-05 02:35:43'),
(572, 93, 'b02336be548f90ce3c9d2a53afcf0482', 'b4f976ae312b3fe248b4b86c80b45afa', '2022-10-05 02:35:48', '2022-10-05 02:36:01'),
(573, 91, '57122f0415f8da13c997f62e8bd6aaf0', '7bea015fcb5282c1145059be4ce42f85', '2022-10-05 02:36:26', '2022-10-05 02:36:29'),
(574, 91, '3a8336561b80d69aa6c1ad5929d16619', 'df09896db8e779ae29b26d29c46be6a9', '2022-10-05 02:36:36', '2022-10-05 02:36:42'),
(575, 93, 'f8dcbf2f41d0420fcebc8d4be2abb523', '484b7622518cd9c7fc54020f1b01d10b', '2022-10-05 02:36:46', '2022-10-05 02:37:50'),
(576, 91, '97e1b84f064ecf69dde38327d129ef65', '09897d5ae7f28e5150dbd0ec6afb7c26', '2022-10-05 02:38:08', '2022-10-05 02:38:21'),
(577, 93, '0dc73c47eea548d3e3e7f4a46eec5180', '30a66be0a412ee40fd18a967573e8929', '2022-10-05 02:38:52', '2022-10-05 02:54:45'),
(578, 91, '0b89ed46ccf86ba8c9a906d21cf08979', '72aa8ef4f22d40b2c36a9f2f4ff6df55', '2022-10-05 02:58:26', '2022-10-05 03:02:47'),
(579, 93, '892feea6a5ec6941cf9d71442390d3f5', '0b048f6f3d274775d6871d3d2aa8696f', '2022-10-05 03:03:00', '2022-10-05 03:03:06'),
(580, 96, '2303cc1747e3cf2e90ea9234865caa51', '42239eb53a0d8fb6de43335ed24ce249', '2022-10-05 03:04:26', '2022-10-05 03:10:36'),
(581, 93, '0aa5c55c6e8d753d94d220f4d6afb260', '6b76ce7a84f624a01532199d90d74e3d', '2022-10-05 03:12:06', '2022-10-05 03:12:10'),
(582, 93, '15f3e41f99740e6db604200817b22360', '7a852eb2b169d7a39160c26c8afd5f22', '2022-10-05 03:42:23', '2022-10-05 03:49:16'),
(583, 95, 'f85f319e2757e4f68f129dd5f2daf034', '3bffacf204b2fb60ff57df4af054351b', '2022-10-05 03:49:25', '2022-10-05 03:54:10'),
(584, 93, '358bc4fdf65c9aca30e03b2645b529ed', '003f8bad2537fd5ae296bd687fb5d2c2', '2022-10-05 03:54:15', '2022-10-05 03:54:26'),
(585, 93, 'd2f04eacf7bad4ac67787a9f11322b4d', 'cd6adb47d017aa75e28ff12e4a65c4ac', '2022-10-05 03:54:50', '2022-10-05 03:55:16'),
(586, 96, '6e916a5ef8ec3c775e0b463a0a154ebe', '6b85ad3c8e5a23b7c9766b8b5c83caad', '2022-10-05 03:59:46', '2022-10-05 04:00:02'),
(587, 93, '531f38ca63b3ca75aede32fd3adfe984', 'c226e30fd8d566c5288245fb2ea51c02', '2022-10-05 04:00:13', '2022-10-05 04:08:19'),
(588, 95, '83c579962c5c974e7cd996e42f85c1d7', 'bc89e97cb6532c0c27cde7e68b670cd1', '2022-10-05 04:08:43', '2022-10-05 04:08:58'),
(589, 93, '0ec3f26e58e6eb58653d0a3fad78a0dc', 'b38d9324cd968948a863b665d22e4ed0', '2022-10-05 04:09:12', '2022-10-05 04:10:29'),
(590, 93, 'dfcd27a0e8f0ef4c3e9f2f5c3084e7e3', 'cb55d30db1776546f043a4c05632806b', '2022-10-12 03:14:48', '2022-10-12 03:20:05'),
(591, 91, '3f760549af494c4e65671091a6b3a149', 'f7bb97fd929f556f1fc25dbbf50295a8', '2022-10-12 03:26:31', '2022-10-12 03:27:03'),
(592, 91, '786b4470a9d1414cd89d7cc42ea5ff7f', '363467b9c88dbdfcfb7802b745b9eb6e', '2022-10-14 04:09:11', '2022-10-14 04:09:19'),
(593, 93, 'b04e292c0fba481a3da616c486da90b3', '681091811377ca45f64d8e05eeb6c493', '2022-10-21 02:09:18', '2022-10-28 03:40:14'),
(594, 93, 'd67a68ba1ec8d085574f3f102ed98fd8', '02d29eaed618bd276fc2cc3589439d79', '2022-11-02 02:08:47', '2022-11-09 01:38:18'),
(597, 93, 'f9f9b86b860d1bbb1a461bef892116c4', '08b51b834d45a86356401efbb573e2a3', '2022-11-11 01:59:34', '2022-11-11 02:15:36'),
(598, 96, '8f0941d5b78783d24911f49044246100', 'a089ed86f67abe12dba3b8e842035888', '2022-11-11 02:28:55', '2022-11-11 02:28:55'),
(599, 96, '7a93a840d8684bd8a31b59342dd826a5', 'f89ddebfe4753d4773025f490ae267bf', '2022-11-11 02:29:07', '2022-11-11 02:29:07'),
(600, 91, '8b9b96258bfbd81bc58028f5db9b9c05', '1fbe21f79ea1120632ace750675c259f', '2022-11-11 02:29:12', '2022-11-11 02:29:12'),
(601, 96, '8e37234972d9da0be15d760caea87c2f', 'ca456c02771af584161a292c342b86a8', '2022-11-11 02:29:18', '2022-11-25 02:43:06'),
(602, 91, 'fd4bd18f81371520629935136be7c2ce', '11862b986eedd348c952dded030e3ed8', '2022-11-29 04:16:39', '2022-11-29 04:16:39'),
(603, 95, '63d385511d8fcffd643728928be33ad4', '02e15b35f19a7644e1776d5f4927a89a', '2022-11-30 01:50:10', '2022-12-21 02:47:28'),
(605, 93, '4df7ea5864486833a61e810f66a83354', '5d4b66a5c08a51ea91ff00e4dddda5de', '2022-12-21 02:47:49', '2023-01-06 01:53:53'),
(606, 91, 'edd9ea9e5ca8937db4cf5587a656d0c8', 'b3b577ffafda41ee4135036beff5cbaa', '2023-01-06 02:03:35', '2023-01-06 02:04:25'),
(607, 95, '77d0a3c2e2ae12f2d1c674bfffa41d96', '88f28eb2963b91a42fca65d779277e51', '2023-01-06 02:05:03', '2023-02-09 02:02:06'),
(624, 86, 'bb0a46a737b1227645dd6f9ab887bfb3', 'cb420935db3b3255ec6d87b9cead406e', '2023-02-09 03:04:49', '2023-02-16 03:14:29'),
(629, 95, 'd5b6750c27e7b541d0f17ecc2e4687ff', 'aefb7054714fc6c736584a881af3564a', '2023-02-16 03:14:36', '2023-02-20 07:04:31'),
(630, 95, '515c69a5a394e578d7d327b2390449cf', 'f1f67b0a2953dd8d5f4f960c485b7e11', '2023-02-20 07:04:54', '2023-02-21 22:05:40'),
(631, 86, 'f6a4e1968a086f1f21bf7415792376ba', '18b82bea9585be643ef19a8ac62db250', '2023-02-22 07:24:47', '2023-02-24 07:06:09'),
(632, 86, 'c8dd98728f9925cb9c85c657602cedc9', '82472db63d81294acee48f92a928a519', '2023-02-22 07:55:51', '2023-02-27 10:21:04'),
(635, 92, 'b2334658a778a39444431573f4de7fb0', 'abf308d3cdbd96229915f32913af2aa5', '2023-02-27 07:31:12', '2023-02-27 07:31:12'),
(636, 93, 'a4eb24943eb3390597227747c9cf6d6f', '877e88496fe22fd53e9b6c1c08607eaa', '2023-02-27 09:53:14', '2023-02-27 10:19:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `siguiendo`
--

CREATE TABLE `siguiendo` (
  `usuario` bigint(20) NOT NULL,
  `lider` bigint(20) NOT NULL,
  `pagina` bigint(20) NOT NULL,
  `ultimoAcceso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `siguiendo`
--

INSERT INTO `siguiendo` (`usuario`, `lider`, `pagina`, `ultimoAcceso`) VALUES
(92, 95, 11, '2023-02-07 05:21:54'),
(93, 91, 8, '2023-01-25 01:34:00'),
(93, 96, 9, '2023-02-27 09:53:37'),
(93, 96, 10, '2022-05-04 03:25:59'),
(93, 95, 11, '2023-01-25 02:56:52'),
(93, 95, 12, '2023-02-27 10:19:24'),
(95, 91, 8, '2023-02-21 01:48:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripcion`
--

CREATE TABLE `suscripcion` (
  `id` bigint(20) NOT NULL,
  `usuario` bigint(20) NOT NULL,
  `lider` bigint(20) NOT NULL,
  `fechaProximoPago` timestamp NULL DEFAULT NULL,
  `fechaUltimoPago` timestamp NULL DEFAULT NULL,
  `suscripcion` int(11) NOT NULL,
  `suscripcionNivel` int(11) NOT NULL,
  `problemaDePago` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `suscripcion`
--

INSERT INTO `suscripcion` (`id`, `usuario`, `lider`, `fechaProximoPago`, `fechaUltimoPago`, `suscripcion`, `suscripcionNivel`, `problemaDePago`) VALUES
(1, 93, 96, '2022-04-29 22:35:35', '2022-04-07 22:35:35', 2, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suscripcionhabilitada`
--

CREATE TABLE `suscripcionhabilitada` (
  `lider` bigint(20) NOT NULL,
  `tipo` int(11) NOT NULL,
  `nombrePersonalizado` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `suscripcionhabilitada`
--

INSERT INTO `suscripcionhabilitada` (`lider`, `tipo`, `nombrePersonalizado`) VALUES
(91, 1, 'Gratis'),
(91, 3, 'Oculta'),
(92, 1, 'Gratis'),
(92, 3, 'Oculta'),
(95, 1, 'Gratis'),
(95, 2, 'Seccion Premium Maria'),
(95, 3, 'Oculta'),
(96, 1, 'Gratis'),
(96, 2, 'Seccion Premium'),
(96, 3, 'Oculta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipomediopago`
--

CREATE TABLE `tipomediopago` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `link` varchar(200) NOT NULL,
  `descripcion` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipomediopago`
--

INSERT INTO `tipomediopago` (`id`, `nombre`, `link`, `descripcion`) VALUES
(1, 'PayPal', 'www.paypal.com', 'Medio de pago PayPal');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiporelacionchats`
--

CREATE TABLE `tiporelacionchats` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `deshabilitado` tinyint(1) NOT NULL,
  `soloCreados` tinyint(1) NOT NULL,
  `suscripcionMinimaNivel` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tiporelacionchats`
--

INSERT INTO `tiporelacionchats` (`id`, `nombre`, `descripcion`, `deshabilitado`, `soloCreados`, `suscripcionMinimaNivel`) VALUES
(1, 'Deshabilitado', 'Las conversaciones entre el líder religioso y los devotos están deshabilitadas.', 1, 0, NULL),
(2, 'Habilitado', 'Las conversaciones entre el líder religioso y todos los devotos están habilitadas.', 0, 0, 0),
(3, 'Premium3', 'Las conversaciones entre el líder religioso y todos los devotos están habilitadas solo para los devotos asociados.', 0, 0, 3),
(4, 'Iniciadas', 'Las conversaciones entre el líder religioso y los devotos están deshabilitadas, solamente se puede continuar hablando con conversaciones ya inicidas.', 0, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tiposuscripcion`
--

CREATE TABLE `tiposuscripcion` (
  `id` int(11) NOT NULL,
  `nivel` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `precio` double NOT NULL,
  `descripcion` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tiposuscripcion`
--

INSERT INTO `tiposuscripcion` (`id`, `nivel`, `nombre`, `precio`, `descripcion`) VALUES
(1, 0, 'Gratis', 0, 'Suscripción gratis'),
(2, 3, 'Pago_3', 3, 'Suscripción paga de 3 dólares'),
(3, 99, 'Oculto', 0, 'Modo oculto de la página');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipousuario`
--

CREATE TABLE `tipousuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `puedeAdministrar` tinyint(1) NOT NULL,
  `puedePublicar` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipousuario`
--

INSERT INTO `tipousuario` (`id`, `nombre`, `puedeAdministrar`, `puedePublicar`) VALUES
(1, 'Administrador', 1, 0),
(2, 'Lider', 0, 1),
(3, 'Deboto', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tyc`
--

CREATE TABLE `tyc` (
  `id` int(11) NOT NULL,
  `tyc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `tyc`
--

INSERT INTO `tyc` (`id`, `tyc`) VALUES
(1, '<h1>Terms and Conditions</h1>\r\n<p>Last updated: February 22, 2023</p>\r\n<p>Please read these terms and conditions carefully before using Our Service.</p>\r\n<h1>Interpretation and Definitions</h1>\r\n<h2>Interpretation</h2>\r\n<p>The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>\r\n<h2>Definitions</h2>\r\n<p>For the purposes of these Terms and Conditions:</p>\r\n<ul>\r\n<li>\r\n<p><strong>Affiliate</strong> means an entity that controls, is controlled by or is under common control with a party, where &quot;control&quot; means ownership of 50% or more of the shares, equity interest or other securities entitled to vote for election of directors or other managing authority.</p>\r\n</li>\r\n<li>\r\n<p><strong>Country</strong> refers to: Mississippi,  United States</p>\r\n</li>\r\n<li>\r\n<p><strong>Company</strong> (referred to as either &quot;the Company&quot;, &quot;We&quot;, &quot;Us&quot; or &quot;Our&quot; in this Agreement) refers to praysite.</p>\r\n</li>\r\n<li>\r\n<p><strong>Device</strong> means any device that can access the Service such as a computer, a cellphone or a digital tablet.</p>\r\n</li>\r\n<li>\r\n<p><strong>Service</strong> refers to the Website.</p>\r\n</li>\r\n<li>\r\n<p><strong>Terms and Conditions</strong> (also referred as &quot;Terms&quot;) mean these Terms and Conditions that form the entire agreement between You and the Company regarding the use of the Service. This Terms and Conditions agreement has been created with the help of the <a href=\"https://www.privacypolicies.com/terms-conditions-generator/\" target=\"_blank\">Terms and Conditions Generator</a>.</p>\r\n</li>\r\n<li>\r\n<p><strong>Third-party Social Media Service</strong> means any services or content (including data, information, products or services) provided by a third-party that may be displayed, included or made available by the Service.</p>\r\n</li>\r\n<li>\r\n<p><strong>Website</strong> refers to praysite, accessible from <a href=\"www.praysite.com\" rel=\"external nofollow noopener\" target=\"_blank\">www.praysite.com</a></p>\r\n</li>\r\n<li>\r\n<p><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p>\r\n</li>\r\n</ul>\r\n<h1>Acknowledgment</h1>\r\n<p>These are the Terms and Conditions governing the use of this Service and the agreement that operates between You and the Company. These Terms and Conditions set out the rights and obligations of all users regarding the use of the Service.</p>\r\n<p>Your access to and use of the Service is conditioned on Your acceptance of and compliance with these Terms and Conditions. These Terms and Conditions apply to all visitors, users and others who access or use the Service.</p>\r\n<p>By accessing or using the Service You agree to be bound by these Terms and Conditions. If You disagree with any part of these Terms and Conditions then You may not access the Service.</p>\r\n<p>You represent that you are over the age of 18. The Company does not permit those under 18 to use the Service.</p>\r\n<p>Your access to and use of the Service is also conditioned on Your acceptance of and compliance with the Privacy Policy of the Company. Our Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your personal information when You use the Application or the Website and tells You about Your privacy rights and how the law protects You. Please read Our Privacy Policy carefully before using Our Service.</p>\r\n<h1>Links to Other Websites</h1>\r\n<p>Our Service may contain links to third-party web sites or services that are not owned or controlled by the Company.</p>\r\n<p>The Company has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that the Company shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with the use of or reliance on any such content, goods or services available on or through any such web sites or services.</p>\r\n<p>We strongly advise You to read the terms and conditions and privacy policies of any third-party web sites or services that You visit.</p>\r\n<h1>Termination</h1>\r\n<p>We may terminate or suspend Your access immediately, without prior notice or liability, for any reason whatsoever, including without limitation if You breach these Terms and Conditions.</p>\r\n<p>Upon termination, Your right to use the Service will cease immediately.</p>\r\n<h1>Limitation of Liability</h1>\r\n<p>Notwithstanding any damages that You might incur, the entire liability of the Company and any of its suppliers under any provision of this Terms and Your exclusive remedy for all of the foregoing shall be limited to the amount actually paid by You through the Service or 100 USD if You haven\'t purchased anything through the Service.</p>\r\n<p>To the maximum extent permitted by applicable law, in no event shall the Company or its suppliers be liable for any special, incidental, indirect, or consequential damages whatsoever (including, but not limited to, damages for loss of profits, loss of data or other information, for business interruption, for personal injury, loss of privacy arising out of or in any way related to the use of or inability to use the Service, third-party software and/or third-party hardware used with the Service, or otherwise in connection with any provision of this Terms), even if the Company or any supplier has been advised of the possibility of such damages and even if the remedy fails of its essential purpose.</p>\r\n<p>Some states do not allow the exclusion of implied warranties or limitation of liability for incidental or consequential damages, which means that some of the above limitations may not apply. In these states, each party\'s liability will be limited to the greatest extent permitted by law.</p>\r\n<h1>&quot;AS IS&quot; and &quot;AS AVAILABLE&quot; Disclaimer</h1>\r\n<p>The Service is provided to You &quot;AS IS&quot; and &quot;AS AVAILABLE&quot; and with all faults and defects without warranty of any kind. To the maximum extent permitted under applicable law, the Company, on its own behalf and on behalf of its Affiliates and its and their respective licensors and service providers, expressly disclaims all warranties, whether express, implied, statutory or otherwise, with respect to the Service, including all implied warranties of merchantability, fitness for a particular purpose, title and non-infringement, and warranties that may arise out of course of dealing, course of performance, usage or trade practice. Without limitation to the foregoing, the Company provides no warranty or undertaking, and makes no representation of any kind that the Service will meet Your requirements, achieve any intended results, be compatible or work with any other software, applications, systems or services, operate without interruption, meet any performance or reliability standards or be error free or that any errors or defects can or will be corrected.</p>\r\n<p>Without limiting the foregoing, neither the Company nor any of the company\'s provider makes any representation or warranty of any kind, express or implied: (i) as to the operation or availability of the Service, or the information, content, and materials or products included thereon; (ii) that the Service will be uninterrupted or error-free; (iii) as to the accuracy, reliability, or currency of any information or content provided through the Service; or (iv) that the Service, its servers, the content, or e-mails sent from or on behalf of the Company are free of viruses, scripts, trojan horses, worms, malware, timebombs or other harmful components.</p>\r\n<p>Some jurisdictions do not allow the exclusion of certain types of warranties or limitations on applicable statutory rights of a consumer, so some or all of the above exclusions and limitations may not apply to You. But in such a case the exclusions and limitations set forth in this section shall be applied to the greatest extent enforceable under applicable law.</p>\r\n<h1>Governing Law</h1>\r\n<p>The laws of the Country, excluding its conflicts of law rules, shall govern this Terms and Your use of the Service. Your use of the Application may also be subject to other local, state, national, or international laws.</p>\r\n<h1>Disputes Resolution</h1>\r\n<p>If You have any concern or dispute about the Service, You agree to first try to resolve the dispute informally by contacting the Company.</p>\r\n<h1>For European Union (EU) Users</h1>\r\n<p>If You are a European Union consumer, you will benefit from any mandatory provisions of the law of the country in which you are resident in.</p>\r\n<h1>United States Legal Compliance</h1>\r\n<p>You represent and warrant that (i) You are not located in a country that is subject to the United States government embargo, or that has been designated by the United States government as a &quot;terrorist supporting&quot; country, and (ii) You are not listed on any United States government list of prohibited or restricted parties.</p>\r\n<h1>Severability and Waiver</h1>\r\n<h2>Severability</h2>\r\n<p>If any provision of these Terms is held to be unenforceable or invalid, such provision will be changed and interpreted to accomplish the objectives of such provision to the greatest extent possible under applicable law and the remaining provisions will continue in full force and effect.</p>\r\n<h2>Waiver</h2>\r\n<p>Except as provided herein, the failure to exercise a right or to require performance of an obligation under these Terms shall not effect a party\'s ability to exercise such right or require such performance at any time thereafter nor shall the waiver of a breach constitute a waiver of any subsequent breach.</p>\r\n<h1>Translation Interpretation</h1>\r\n<p>These Terms and Conditions may have been translated if We have made them available to You on our Service.\r\nYou agree that the original English text shall prevail in the case of a dispute.</p>\r\n<h1>Changes to These Terms and Conditions</h1>\r\n<p>We reserve the right, at Our sole discretion, to modify or replace these Terms at any time. If a revision is material We will make reasonable efforts to provide at least 30 days\' notice prior to any new terms taking effect. What constitutes a material change will be determined at Our sole discretion.</p>\r\n<p>By continuing to access or use Our Service after those revisions become effective, You agree to be bound by the revised terms. If You do not agree to the new terms, in whole or in part, please stop using the website and the Service.</p>\r\n<h1>Contact Us</h1>\r\n<p>If you have any questions about these Terms and Conditions, You can contact us:</p>\r\n<ul>\r\n<li>By email: ppernigotti@gmail.com</li>\r\n</ul>');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` bigint(20) NOT NULL,
  `nick` varchar(50) NOT NULL,
  `nickSimplificado` varchar(50) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `nombreSimplificado` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `emailPropuesto` varchar(80) DEFAULT NULL,
  `hashPass` varchar(35) NOT NULL,
  `semillaPass` varchar(35) NOT NULL,
  `semillaSesion` varchar(35) NOT NULL,
  `codigoVerificacion` varchar(35) DEFAULT NULL,
  `tipoCodigoVerificacion` varchar(1) DEFAULT NULL,
  `estaVerificado` tinyint(1) NOT NULL,
  `estaBloqueado` tinyint(1) NOT NULL,
  `tipoUsuario` int(11) NOT NULL,
  `fechaAlta` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaUltimoLogueo` timestamp NULL DEFAULT NULL,
  `fechaUltimaAccion` timestamp NULL DEFAULT NULL,
  `novedadesMensaje` tinyint(1) NOT NULL DEFAULT '0',
  `fechaNacimiento` date NOT NULL,
  `fechaReinicioIntentos` timestamp NULL DEFAULT NULL,
  `intentos` int(11) NOT NULL,
  `urtImagen` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nick`, `nickSimplificado`, `nombre`, `nombreSimplificado`, `email`, `emailPropuesto`, `hashPass`, `semillaPass`, `semillaSesion`, `codigoVerificacion`, `tipoCodigoVerificacion`, `estaVerificado`, `estaBloqueado`, `tipoUsuario`, `fechaAlta`, `fechaUltimoLogueo`, `fechaUltimaAccion`, `novedadesMensaje`, `fechaNacimiento`, `fechaReinicioIntentos`, `intentos`, `urtImagen`) VALUES
(86, 'pehuen', 'peuem', 'pehuen pernigottti', 'peuempermigottti', 'ppernigotti_@gmail.com', NULL, 'ccbfcb950df44825469804e3f7c82419', 'c7869c4a2183bd6e28df5a73ae49901c', 'fe992aa3a0cb0b7f54349e12c784e58d', NULL, NULL, 1, 0, 1, '2022-02-04 21:24:43', '2023-02-22 07:55:51', '2023-02-27 10:21:04', 0, '1989-03-29', NULL, 10, NULL),
(91, 'juan', 'juam', 'juanpablo', 'juampablo', 'ppernigotti@gmail.com', NULL, 'abc242f0bb3283be5587422d7fdcf894', 'bb24ca7cfc3feb07d5c103c3f7f7aab4', '85a28d8ed304461f1da33e5bcf1568e9', NULL, NULL, 1, 0, 3, '2022-02-18 02:27:16', '2023-02-09 03:00:45', '2023-02-09 03:01:03', 0, '1989-03-29', NULL, 10, '2023-02-089553e0f50517bc7204f664b8d63ab31c'),
(92, 'juanpablo', 'juampablo', 'Juan Re carlos', 'juamrecarloc', 'ppernigotti3@gmail.com', NULL, 'f8b62b72a0895a80e83163e68d79dc8f', '5c9c0dfcc05a935d2b29e5ab857d2fa7', '3b8c46a9eef49d959a45c9e6f704ddf2', NULL, NULL, 1, 0, 2, '2022-03-02 01:17:56', '2023-02-27 07:31:12', '2023-02-27 07:31:12', 0, '1989-03-29', NULL, 10, '2023-02-0723b4f3b980202fbab9b3673cd484945a'),
(93, 'lucas', 'lucac', 'lucas', 'lucac', 'lucas@gmail.com', 'ppernigotti@gmail.com', 'eff25f6f319cd01dfb5ea7b4c24ab75c', '587350b11752ce7c1583771fc9b1a4b0', 'caa0b1f0cd9d58e0d348ec8a3f364bb6', NULL, NULL, 1, 0, 3, '2022-04-06 02:53:52', '2023-02-27 09:53:14', '2023-02-27 10:19:25', 0, '1989-03-29', NULL, 10, '2023-02-086b4aa5b63d636868f2a2152ea3da9fc3'),
(94, 'marcos', 'marcoc', 'marcos', 'marcoc', 'marcos@gmail.com', NULL, 'add92bb16843e378081763ff6c992ba6', '8b3767fe03b665a7019b7e55c62c1c6e', '0a1fc67b84fdd7a2afbe8208d1c3aa4f', NULL, NULL, 1, 0, 3, '2022-04-06 02:54:42', NULL, NULL, 0, '1989-03-29', NULL, 10, NULL),
(95, 'maria', 'maria', 'maria', 'maria', 'maria@gmail.com', 'marcos@gmail.com', '4d3c598ab2a0aee4b91a7f8bf2a24d69', 'db39d600170fd09a9e7082f6536d0f52', '8ed260b85ceb7c2aa33228c13a2a5b81', NULL, NULL, 1, 0, 2, '2022-04-06 02:56:01', '2023-02-20 07:04:54', '2023-02-22 06:05:52', 0, '1989-03-29', NULL, 10, '2023-02-0796eeeddb51a7b4350accde06bcf103b5'),
(96, 'micaela', 'micaela', 'Marcela Sarin', 'marcelacarim', 'micaela@gmail.com', NULL, '8a45cf584e095bcd2d0068ec1924a728', '0607dcf52e98f86253bc114723a0f8e0', '9d61d2b4393e146958272deca73fa129', NULL, NULL, 1, 0, 2, '2022-04-06 02:56:51', '2023-01-20 02:07:09', '2023-01-20 02:10:07', 1, '1989-03-29', NULL, 10, '2022-08-1294578610fdcc197ee29c1e4852ad93b7'),
(97, 'email', 'email', 'email', 'email', 'email@email.com', NULL, '58fe4623232e1367f373ebd3c6840f4f', '4e5a72167bbc9b080df32218167920a8', '165374703e8d6b1b4c81624e5d1dd73b', '9e9e8a4408bacde5d2a6763e86ad13c6', 'R', 0, 0, 3, '2023-02-23 06:57:15', NULL, NULL, 0, '2023-02-01', NULL, 20, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuarioIdMenor` (`usuarioIdMenor`,`usuarioIdMayor`),
  ADD KEY `chat_previewusuario` (`previewUltimoMensajeUsuarioId`),
  ADD KEY `chat_usuariomayor` (`usuarioIdMayor`),
  ADD KEY `chat_estadomenor` (`estadoChatIdMenor`),
  ADD KEY `chat_estadomayor` (`estadoChatIdMayor`);

--
-- Indices de la tabla `denuncia`
--
ALTER TABLE `denuncia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `denuncia_estadodenuncia` (`estado`),
  ADD KEY `denuncia_denunciado` (`denunciado`),
  ADD KEY `denuncia_denunciante` (`denunciante`),
  ADD KEY `denuncia_resolvio` (`resolvio`);

--
-- Indices de la tabla `estadochat`
--
ALTER TABLE `estadochat`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estadodenuncia`
--
ALTER TABLE `estadodenuncia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nombre_EstadoDenuncia` (`nombre`);

--
-- Indices de la tabla `lider`
--
ALTER TABLE `lider`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lider_tipoRelacionChats` (`tipoRelacionChats`);

--
-- Indices de la tabla `mediodepago`
--
ALTER TABLE `mediodepago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_mediodepago` (`usuario`),
  ADD KEY `mediodepago_tipodemediodepago` (`tipo`);

--
-- Indices de la tabla `megusta`
--
ALTER TABLE `megusta`
  ADD PRIMARY KEY (`usuario`,`publicacion`),
  ADD KEY `megusta_publicacion` (`publicacion`);

--
-- Indices de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_mensaje` (`chat`),
  ADD KEY `mensaje_usuario` (`usuario`);

--
-- Indices de la tabla `pagina`
--
ALTER TABLE `pagina`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lider` (`lider`,`nombre`),
  ADD KEY `pagina_tipoSuscripcion` (`suscripcionMinimo`);

--
-- Indices de la tabla `pago`
--
ALTER TABLE `pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pago_usuario` (`usuario`),
  ADD KEY `pago_lider` (`lider`),
  ADD KEY `pago_tipoMedioPago` (`tipoMedioPago`),
  ADD KEY `pago_tipoSuscripcion` (`tipoSuscripcion`);

--
-- Indices de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pagina_publicacion` (`pagina`),
  ADD KEY `fechaModificacion` (`fechaModificacion`);

--
-- Indices de la tabla `sesioniniciada`
--
ALTER TABLE `sesioniniciada`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sesioniniciada_usuario` (`usuario`);

--
-- Indices de la tabla `siguiendo`
--
ALTER TABLE `siguiendo`
  ADD PRIMARY KEY (`usuario`,`pagina`),
  ADD KEY `lider` (`lider`);

--
-- Indices de la tabla `suscripcion`
--
ALTER TABLE `suscripcion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `suscripcion_lider` (`lider`),
  ADD KEY `suscripcion_tiposucrcipcion` (`suscripcion`),
  ADD KEY `suscripcion_usuario` (`usuario`);

--
-- Indices de la tabla `suscripcionhabilitada`
--
ALTER TABLE `suscripcionhabilitada`
  ADD PRIMARY KEY (`lider`,`tipo`),
  ADD KEY `suscripcionhabilitada_tiposuscripcion` (`tipo`);

--
-- Indices de la tabla `tipomediopago`
--
ALTER TABLE `tipomediopago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tiporelacionchats`
--
ALTER TABLE `tiporelacionchats`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tiposuscripcion`
--
ALTER TABLE `tiposuscripcion`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nivel` (`nivel`),
  ADD KEY `nivel_2` (`nivel`);

--
-- Indices de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tyc`
--
ALTER TABLE `tyc`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nick` (`nick`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `usuario_tipousuario` (`tipoUsuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `chat`
--
ALTER TABLE `chat`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `denuncia`
--
ALTER TABLE `denuncia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `estadochat`
--
ALTER TABLE `estadochat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estadodenuncia`
--
ALTER TABLE `estadodenuncia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `mediodepago`
--
ALTER TABLE `mediodepago`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mensaje`
--
ALTER TABLE `mensaje`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT de la tabla `pagina`
--
ALTER TABLE `pagina`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `pago`
--
ALTER TABLE `pago`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `publicacion`
--
ALTER TABLE `publicacion`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `sesioniniciada`
--
ALTER TABLE `sesioniniciada`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=637;

--
-- AUTO_INCREMENT de la tabla `suscripcion`
--
ALTER TABLE `suscripcion`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tipomediopago`
--
ALTER TABLE `tipomediopago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `tiporelacionchats`
--
ALTER TABLE `tiporelacionchats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tiposuscripcion`
--
ALTER TABLE `tiposuscripcion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tipousuario`
--
ALTER TABLE `tipousuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `tyc`
--
ALTER TABLE `tyc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `chat`
--
ALTER TABLE `chat`
  ADD CONSTRAINT `chat_estadomayor` FOREIGN KEY (`estadoChatIdMayor`) REFERENCES `estadochat` (`id`),
  ADD CONSTRAINT `chat_estadomenor` FOREIGN KEY (`estadoChatIdMenor`) REFERENCES `estadochat` (`id`),
  ADD CONSTRAINT `chat_previewusuario` FOREIGN KEY (`previewUltimoMensajeUsuarioId`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_usuariomayor` FOREIGN KEY (`usuarioIdMayor`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_usuariomenor` FOREIGN KEY (`usuarioIdMenor`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `denuncia`
--
ALTER TABLE `denuncia`
  ADD CONSTRAINT `denuncia_denunciado` FOREIGN KEY (`denunciado`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `denuncia_denunciante` FOREIGN KEY (`denunciante`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `denuncia_estadodenuncia` FOREIGN KEY (`estado`) REFERENCES `estadodenuncia` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `denuncia_resolvio` FOREIGN KEY (`resolvio`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `lider`
--
ALTER TABLE `lider`
  ADD CONSTRAINT `lider_tipoRelacionChats` FOREIGN KEY (`tipoRelacionChats`) REFERENCES `tiporelacionchats` (`id`) ON DELETE NO ACTION,
  ADD CONSTRAINT `lider_usuario` FOREIGN KEY (`id`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `mediodepago`
--
ALTER TABLE `mediodepago`
  ADD CONSTRAINT `mediodepago_tipodemediodepago` FOREIGN KEY (`tipo`) REFERENCES `tipomediopago` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mediodepago_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `megusta`
--
ALTER TABLE `megusta`
  ADD CONSTRAINT `megusta_publicacion` FOREIGN KEY (`publicacion`) REFERENCES `publicacion` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `megusta_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mensaje`
--
ALTER TABLE `mensaje`
  ADD CONSTRAINT `mensaje_chat` FOREIGN KEY (`chat`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensaje_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagina`
--
ALTER TABLE `pagina`
  ADD CONSTRAINT `pagina_lider` FOREIGN KEY (`lider`) REFERENCES `lider` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pagina_tipoSuscripcion` FOREIGN KEY (`suscripcionMinimo`) REFERENCES `tiposuscripcion` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago`
--
ALTER TABLE `pago`
  ADD CONSTRAINT `pago_lider` FOREIGN KEY (`lider`) REFERENCES `lider` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_tipoMedioPago` FOREIGN KEY (`tipoMedioPago`) REFERENCES `tipomediopago` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_tipoSuscripcion` FOREIGN KEY (`tipoSuscripcion`) REFERENCES `tiposuscripcion` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pago_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Filtros para la tabla `publicacion`
--
ALTER TABLE `publicacion`
  ADD CONSTRAINT `publicacion_pagina` FOREIGN KEY (`pagina`) REFERENCES `pagina` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sesioniniciada`
--
ALTER TABLE `sesioniniciada`
  ADD CONSTRAINT `sesioniniciada_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `siguiendo`
--
ALTER TABLE `siguiendo`
  ADD CONSTRAINT `siguiendo_lider` FOREIGN KEY (`lider`) REFERENCES `lider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `siguiendo_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `suscripcion`
--
ALTER TABLE `suscripcion`
  ADD CONSTRAINT `suscripcion_lider` FOREIGN KEY (`lider`) REFERENCES `lider` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `suscripcion_tiposucrcipcion` FOREIGN KEY (`suscripcion`) REFERENCES `tiposuscripcion` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `suscripcion_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `suscripcionhabilitada`
--
ALTER TABLE `suscripcionhabilitada`
  ADD CONSTRAINT `suscripcionhabilitada_lider` FOREIGN KEY (`lider`) REFERENCES `lider` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suscripcionhabilitada_tiposuscripcion` FOREIGN KEY (`tipo`) REFERENCES `tiposuscripcion` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_tipousuario` FOREIGN KEY (`tipoUsuario`) REFERENCES `tipousuario` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
