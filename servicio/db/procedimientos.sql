
-------------------------------------------
sp_chequear_loguin
-------------------------------------------

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
    SET puedeAdministrar_out = 0;
    SET puedePublicar_out = 0;
	SET error_out = 0;
    
    SELECT u.id, u.nick, u.nombre, u.email, u.semillaSesion, u.estaBloqueado, u.fechaAlta, u.fechaUltimoLogueo, u.fechaUltimaAccion, u.novedadesMensaje, u.fechaNacimiento, tu.nombre, tu.puedeAdministrar, tu.puedePublicar, s.hashSesion INTO id_out, nick_out, nombre_out, email_out, @semillaSesion, @estaBloqueado, fechaAlta_out, fechaUltimoLogueo_out, fechaUltimaAccion_out, novedadesMensaje_out, fechaNacimiento_out, tipoUsuario_out, puedeAdministrar_out, puedePublicar_out, @hashSesion FROM sesioniniciada s INNER JOIN usuario u ON s.id = idSesion_in AND s.hashId = hashId_in AND s.usuario = u.id INNER JOIN tipousuario tu ON u.tipoUsuario = tu.id;
    
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
			SET puedeAdministrar_out = 0;
			SET puedePublicar_out = 0;
			SET error_out = 123;
		END IF;
	END IF;
END



-------------------------------------------
sp_agregar_megusta
-------------------------------------------


BEGIN
	DECLARE CONTINUE HANDLER FOR SQLSTATE '23000' BEGIN
    	SET error_out = 1;
    END;
	
	SET error_out = 0;

	INSERT INTO megusta (usuario, publicacion) VALUES (usuario_in, publicacion_in);
	
	IF error_out = 0 THEN
		UPDATE publicacion SET meGusta = (SELECT meGusta FROM publicacion WHERE id = publicacion_in) + 1 WHERE id = publicacion_in;

		SELECT meGusta, metaMeGusta INTO @meGusta, @metaMeGusta FROM publicacion WHERE id = publicacion_in;

		IF @meGusta >= @metaMeGusta THEN
			UPDATE publicacion SET metaMeGusta = @metaMeGusta * 3, fechaModificacion = fecha_in WHERE id = publicacion_in;
		END IF;
	END IF;
END



