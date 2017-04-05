-- phpMyAdmin SQL Dump
-- version 3.4.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 30-01-2017 a las 05:36:07
-- Versión del servidor: 5.1.73
-- Versión de PHP: 5.4.45

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `wi061609_penca`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`wi061609`@`%` PROCEDURE `jogo_terminado`(IN s_id_match bigint, 
IN res1 integer, IN res2 integer, IN s_id_team1 bigint, IN s_id_team2 bigint, IN s_id_champ bigint )
BEGIN

	
	DECLARE done INT DEFAULT 0;
	DECLARE jogadores_ganadores integer;
	DECLARE acumulado_jogo decimal(20,2);
	DECLARE dinero_para_jogadores decimal(20,2);
	DECLARE id_jogador_ganador bigint;
	DECLARE dinero_actual_ganador decimal(20,2);

	DECLARE id_team1 bigint;
	DECLARE points_tm1 bigint;
	DECLARE jogados_tm1 bigint;

	DECLARE id_team2 bigint;
	DECLARE points_tm2 bigint;
	DECLARE jogados_tm2 bigint;

	DECLARE curs1 CURSOR FOR SELECT rs_iduser FROM result
		WHERE rs_idmatch = s_id_match AND rs_res1 = res1 AND rs_res2 = res2;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;	
	
        UPDATE wi061609_penca.match SET mt_played=1, mt_goal1 = res1, mt_goal2 = res2 WHERE mt_id = s_id_match;

	SELECT mt_idteam1, mt_idteam2 INTO id_team1, id_team2 FROM wi061609_penca.match WHERE mt_id = s_id_match;

	SELECT tm_points, tm_played INTO points_tm1, jogados_tm1 FROM team WHERE tm_id = id_team1;
	

	IF (res1 > res2) then
		SET points_tm1 = points_tm1 + 3;	
	ELSEIF (res1 = res2) then
		SET points_tm1 = points_tm1 + 1;
	ELSE
		SET points_tm1 = points_tm1;
	END IF;

	SET jogados_tm1 = jogados_tm1 + 1;
	UPDATE team SET tm_points = points_tm1, tm_played = jogados_tm1 WHERE tm_id = id_team1;

	SELECT tm_points, tm_played INTO points_tm2, jogados_tm2 FROM team WHERE tm_id = id_team2;

	IF (res2 > res1) then
		SET points_tm2 = points_tm2 + 3;
	ELSEIF (res1 = res2) then
		SET points_tm2 = points_tm2 + 1;
	ELSE
		SET points_tm2 = points_tm2;
	END IF;

	SET jogados_tm2 = jogados_tm2 + 1;
	UPDATE team SET tm_points = points_tm2, tm_played = jogados_tm2 WHERE tm_id = id_team2;

	SELECT count(*) INTO jogadores_ganadores FROM result 
		WHERE rs_idmatch = s_id_match AND rs_res1 = res1 AND rs_res2 = res2;

	SELECT mt_acumulado INTO acumulado_jogo FROM wi061609_penca.match WHERE mt_id = s_id_match;
	
	IF (jogadores_ganadores > 0) then
		
		SET dinero_para_jogadores = acumulado_jogo / jogadores_ganadores;

		OPEN curs1;
		read_loop: loop

			FETCH curs1 INTO id_jogador_ganador;

			IF done THEN
			  LEAVE read_loop;
			END IF;

			SELECT us_cash INTO dinero_actual_ganador FROM user WHERE us_id = id_jogador_ganador;
			
			SELECT dinero_actual_ganador, dinero_para_jogadores;

			SET dinero_actual_ganador = dinero_actual_ganador + dinero_para_jogadores;
			UPDATE user SET us_cash = dinero_actual_ganador WHERE us_id = id_jogador_ganador;

			UPDATE result SET rs_result = 1, rs_points = 5 WHERE rs_idmatch = s_id_match AND
					rs_iduser = id_jogador_ganador;

			INSERT INTO transaction(tr_valortransaccion, tr_valorcampeonato, tr_iduser, tr_valorrodada,
				tr_valorjogo, tr_idcampeonato, tr_idmatch, tr_res_ch_acumulado, tr_res_rd_acumulado,
				tr_res_mt_acumulado, tr_res_us_cash, tr_tipo, tr_date, tr_motivo) 
				VALUES(dinero_para_jogadores, 0, id_jogador_ganador, 0, 0, s_id_champ, s_id_match, 
				0,0,0,dinero_actual_ganador,'CREDITO', now(), 'JOGO');

		END LOOP read_loop;
		CLOSE curs1;
		

	END IF;



END$$

CREATE DEFINER=`wi061609`@`%` PROCEDURE `rankings_championships`(IN s_user_id bigint)
BEGIN

	SET @rank=0;
SELECT @rank:=@rank+1 AS position, vwr.* FROM vwranking_championship vwr WHERE us_id = s_user_id;

END$$

CREATE DEFINER=`wi061609`@`%` PROCEDURE `update_palpites`(IN dpalpite decimal(20,2), 
IN dchamp decimal(20,2), s_ch_id bigint, s_us_id bigint, IN drodada decimal(20,2), s_rd_id bigint,
IN djogo decimal(20,2), s_mt_id bigint, IN s_result_id bigint )
BEGIN

	declare s_ch_acumulado decimal(20,2);
	declare s_rd_acumulado decimal(20,2);
	declare s_mt_acumulado decimal(20,2);
	declare s_us_cash decimal(20,2);
	declare id bigint;

	IF (s_result_id IS NOT NULL) then
		delete from result where rs_id = s_result_id;
	END IF;

	select ch_acumulado into s_ch_acumulado from championship where ch_id = s_ch_id;
	set s_ch_acumulado = s_ch_acumulado + dchamp;
	update championship set ch_acumulado = s_ch_acumulado where ch_id = s_ch_id;

	select mt_acumulado into s_mt_acumulado from wi061609_penca.match where mt_id = s_mt_id;
	set s_mt_acumulado = s_mt_acumulado + djogo;	
	update wi061609_penca.match set mt_acumulado = s_mt_acumulado where mt_id = s_mt_id;

	select rd_acumulado into s_rd_acumulado from round where rd_id = s_rd_id and rd_idchampionship = s_ch_id;
	set s_rd_acumulado = s_rd_acumulado + drodada;
	update round set rd_acumulado = s_rd_acumulado where rd_id = s_rd_id and rd_idchampionship = s_ch_id;

	select us_cash into s_us_cash from user where us_id = s_us_id;
	set s_us_cash = s_us_cash + dpalpite;
	update user set us_cash = s_us_cash where us_id = s_us_id;
	
	insert into transaction(tr_valortransaccion, tr_valorcampeonato, tr_iduser, 
		tr_valorrodada, tr_valorjogo, tr_idcampeonato, tr_idmatch, tr_res_ch_acumulado,
		tr_res_rd_acumulado, tr_res_mt_acumulado, tr_res_us_cash)
		values(dpalpite, dchamp, s_us_id, drodada, djogo, s_ch_id, s_mt_id, s_ch_acumulado,
			s_rd_acumulado, s_mt_acumulado, s_us_cash);

	set id = last_insert_id();
	
	select * from wi061609_penca.transaction where tr_id = id;

END$$

CREATE DEFINER=`wi061609`@`%` PROCEDURE `vaciar_base`()
BEGIN

	
	delete from team;
	delete from result;
	delete from wi061609_penca.match;
	delete from transaction;
	delete from round;
	delete from championship;

	
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `championship`
--

CREATE TABLE IF NOT EXISTS `championship` (
  `ch_id` int(11) NOT NULL AUTO_INCREMENT,
  `ch_nome` varchar(100) NOT NULL,
  `ch_idfixture` int(11) DEFAULT NULL,
  `ch_started` tinyint(1) DEFAULT '0',
  `ch_atualround` int(4) DEFAULT '1',
  `ch_sec1_ini` int(2) DEFAULT NULL,
  `ch_sec1_fin` int(2) DEFAULT NULL,
  `ch_sec2_ini` int(2) DEFAULT NULL,
  `ch_sec2_fin` int(2) DEFAULT NULL,
  `ch_sec3_ini` int(2) DEFAULT NULL,
  `ch_sec3_fin` int(2) DEFAULT NULL,
  `ch_sec1_desc` varchar(100) DEFAULT NULL,
  `ch_sec2_desc` varchar(100) DEFAULT NULL,
  `ch_sec3_desc` varchar(100) DEFAULT NULL,
  `ch_acumulado` decimal(19,2) DEFAULT '0.00',
  `ch_dchamp` decimal(19,2) DEFAULT '0.00',
  `ch_djogo` decimal(19,2) DEFAULT '0.00',
  `ch_drodada` decimal(19,2) DEFAULT '0.00',
  `ch_dpalpite` decimal(19,2) DEFAULT '0.00',
  `ch_ativo` tinyint(1) DEFAULT '1',
  `ch_descricao` text,
  `ch_data_inicio` date DEFAULT NULL,
  `ch_data_termino` date DEFAULT NULL,
  `ch_es` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ch_id`),
  UNIQUE KEY `ch_nome_UNIQUE` (`ch_nome`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

--
-- Volcado de datos para la tabla `championship`
--

INSERT INTO `championship` (`ch_id`, `ch_nome`, `ch_idfixture`, `ch_started`, `ch_atualround`, `ch_sec1_ini`, `ch_sec1_fin`, `ch_sec2_ini`, `ch_sec2_fin`, `ch_sec3_ini`, `ch_sec3_fin`, `ch_sec1_desc`, `ch_sec2_desc`, `ch_sec3_desc`, `ch_acumulado`, `ch_dchamp`, `ch_djogo`, `ch_drodada`, `ch_dpalpite`, `ch_ativo`, `ch_descricao`, `ch_data_inicio`, `ch_data_termino`, `ch_es`) VALUES
(5, 'Copa Pre-Libertadores', NULL, 0, 8, 0, 0, 0, 0, 0, 0, '', '', '', '0.00', '0.00', '0.00', '0.00', '0.00', 1, 'Copa Bridgestone Pre-Libertadores de América 2017', '2017-01-23', NULL, NULL),
(6, 'Campeonato Paulista 2017', NULL, 0, 10, 1, 2, 0, 0, 0, 0, 'Próxima rodada', '', '', '0.00', '0.12', '2.00', '0.13', '2.50', 1, 'Campeonato Paulista 2017 - Paulistão Itaipava 2017', '2017-02-03', NULL, NULL),
(7, 'Copa Libertadores 2017', NULL, 0, 1, 1, 2, 0, 0, 0, 0, 'Próxima fase', '', '', '0.00', '0.12', '2.00', '0.13', '2.50', 1, 'Copa Bridgestone Libertadores de América 2017', '2017-03-07', NULL, NULL),
(8, 'Sul-Americano Sub-20', NULL, 0, 27, 1, 3, 0, 0, 0, 0, 'Próxima fase', '', '', '0.00', '0.00', '0.00', '0.00', '0.00', 0, 'XXVIII Campeonato Sul-Americano Sub-20', '2017-01-18', NULL, NULL),
(9, 'Florida Cup', NULL, 0, 32, 0, 0, 0, 0, 0, 0, '', '', '', '0.00', '0.00', '0.00', '0.00', '0.00', 0, NULL, NULL, NULL, NULL),
(10, 'Copa do Brasil 2017', NULL, 0, 81, 0, 0, 0, 0, 0, 0, '', '', '', '0.00', '0.00', '0.00', '0.00', '0.00', 1, 'XXIX Copa do Brasil de Futebol 2017', '2017-02-08', NULL, NULL),
(11, 'Sul-Americano Sub20 Fase final', NULL, 0, 82, 1, 4, 0, 0, 0, 0, 'Clasificacion mundial', '', '', '0.00', '0.00', '0.00', '0.00', '0.00', 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credito`
--

CREATE TABLE IF NOT EXISTS `credito` (
  `cr_id` int(11) NOT NULL,
  `cr_cash` decimal(12,2) DEFAULT '0.00',
  `cr_iduser` int(11) NOT NULL,
  `cr_concepto` varchar(45) DEFAULT 'CREDITO',
  `cr_data` datetime DEFAULT NULL,
  PRIMARY KEY (`cr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fixture`
--

CREATE TABLE IF NOT EXISTS `fixture` (
  `fx_id` int(11) NOT NULL AUTO_INCREMENT,
  `fx_match` int(11) NOT NULL,
  PRIMARY KEY (`fx_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linkedreferencia`
--

CREATE TABLE IF NOT EXISTS `linkedreferencia` (
  `lr_id` int(11) NOT NULL AUTO_INCREMENT,
  `lr_idcreador` int(11) DEFAULT NULL,
  `lr_iduso` int(11) DEFAULT NULL,
  PRIMARY KEY (`lr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `match`
--

CREATE TABLE IF NOT EXISTS `match` (
  `mt_id` int(11) NOT NULL AUTO_INCREMENT,
  `mt_idteam1` int(11) NOT NULL,
  `mt_idteam2` int(11) NOT NULL,
  `mt_date` timestamp NULL DEFAULT NULL,
  `mt_goal1` int(2) DEFAULT NULL,
  `mt_goal2` int(2) DEFAULT NULL,
  `mt_idchampionship` int(11) NOT NULL,
  `mt_played` tinyint(1) DEFAULT '0',
  `mt_acumulado` decimal(19,2) DEFAULT '0.00',
  `mt_idround` int(11) DEFAULT NULL,
  PRIMARY KEY (`mt_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=332 ;

--
-- Volcado de datos para la tabla `match`
--

INSERT INTO `match` (`mt_id`, `mt_idteam1`, `mt_idteam2`, `mt_date`, `mt_goal1`, `mt_goal2`, `mt_idchampionship`, `mt_played`, `mt_acumulado`, `mt_idround`) VALUES
(127, 96, 97, '2017-01-23 21:00:00', 3, 2, 5, 1, '0.00', 6),
(128, 98, 99, '2017-01-23 22:15:00', 1, 0, 5, 1, '0.00', 6),
(129, 100, 101, '2017-01-23 22:15:00', 0, 1, 5, 1, '0.00', 6),
(130, 97, 96, '2017-01-27 22:15:00', 5, 2, 5, 1, '0.00', 7),
(131, 101, 100, '2017-01-27 22:15:00', 2, 2, 5, 1, '0.00', 7),
(132, 99, 98, '2017-01-27 23:45:00', 0, 0, 5, 1, '0.00', 7),
(133, 102, 103, '2017-01-31 23:00:00', NULL, NULL, 5, 0, '0.00', 8),
(134, 104, 105, '2017-02-01 01:15:00', NULL, NULL, 5, 0, '0.00', 8),
(135, 106, 107, '2017-02-01 02:00:00', NULL, NULL, 5, 0, '0.00', 8),
(136, 108, 109, '2017-02-02 00:45:00', NULL, NULL, 5, 0, '0.00', 8),
(137, 110, 111, '2017-02-02 00:45:00', NULL, NULL, 5, 0, '0.00', 8),
(138, 105, 104, '2017-02-08 01:00:00', NULL, NULL, 5, 0, '0.00', 9),
(139, 103, 102, '2017-02-08 01:15:00', NULL, NULL, 5, 0, '0.00', 9),
(140, 107, 106, '2017-02-08 01:15:00', NULL, NULL, 5, 0, '0.00', 9),
(141, 109, 108, '2017-02-09 00:45:00', NULL, NULL, 5, 0, '0.00', 9),
(142, 111, 110, '2017-02-09 00:45:00', NULL, NULL, 5, 0, '0.00', 9),
(143, 124, 118, '2017-02-04 00:00:00', NULL, NULL, 6, 0, '0.00', 10),
(144, 125, 116, '2017-02-05 20:00:00', NULL, NULL, 6, 0, '0.00', 10),
(145, 126, 119, '2017-02-05 22:30:00', NULL, NULL, 6, 0, '0.00', 10),
(146, 120, 115, '2017-02-05 20:00:00', NULL, NULL, 6, 0, '0.00', 10),
(147, 113, 112, '2017-02-04 20:00:00', NULL, NULL, 6, 0, '0.00', 10),
(148, 127, 117, '2017-02-05 22:30:00', NULL, NULL, 6, 0, '0.00', 10),
(149, 122, 121, '2017-02-04 20:00:00', NULL, NULL, 6, 0, '0.00', 10),
(150, 123, 114, '2017-02-04 13:00:00', NULL, NULL, 6, 0, '0.00', 10),
(151, 117, 124, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(152, 115, 122, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(153, 116, 126, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(154, 112, 123, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(155, 113, 121, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(156, 114, 120, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(157, 119, 127, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(158, 118, 125, '2017-02-12 03:00:00', NULL, NULL, 6, 0, '0.00', 11),
(159, 115, 126, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(160, 112, 122, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(161, 119, 125, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(162, 114, 121, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(163, 127, 118, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(164, 120, 113, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(165, 117, 123, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(166, 124, 116, '2017-02-15 22:00:00', NULL, NULL, 6, 0, '0.00', 12),
(167, 125, 112, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(168, 118, 120, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(169, 122, 114, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(170, 126, 117, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(171, 123, 113, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(172, 124, 119, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(173, 121, 115, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(174, 116, 127, '2017-02-19 22:00:00', NULL, NULL, 6, 0, '0.00', 14),
(175, 115, 125, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(176, 112, 120, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(177, 119, 123, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(178, 114, 124, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(179, 118, 126, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(180, 117, 122, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(181, 113, 127, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(182, 116, 121, '2017-02-22 21:00:00', NULL, NULL, 6, 0, '0.00', 15),
(183, 125, 114, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(184, 127, 112, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(185, 122, 116, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(186, 120, 119, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(187, 126, 113, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(188, 123, 118, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(189, 124, 115, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(190, 121, 117, '2017-02-25 21:00:00', NULL, NULL, 6, 0, '0.00', 16),
(191, 115, 127, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(192, 112, 124, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(193, 119, 121, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(194, 114, 126, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(195, 118, 122, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(196, 117, 120, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(197, 113, 125, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(198, 116, 123, '2017-03-05 21:00:00', NULL, NULL, 6, 0, '0.00', 17),
(199, 125, 117, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(200, 127, 114, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(201, 122, 119, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(202, 120, 116, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(203, 126, 112, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(204, 123, 115, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(205, 121, 118, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(206, 113, 124, '2017-03-12 21:00:00', NULL, NULL, 6, 0, '0.00', 18),
(207, 125, 121, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(208, 119, 112, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(209, 118, 115, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(210, 127, 123, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(211, 126, 122, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(212, 117, 113, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(213, 124, 120, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(214, 116, 114, '2017-03-19 21:00:00', NULL, NULL, 6, 0, '0.00', 19),
(215, 115, 116, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(216, 112, 117, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(217, 114, 118, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(218, 122, 125, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(219, 120, 127, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(220, 123, 126, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(221, 121, 124, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(222, 113, 119, '2017-03-22 21:00:00', NULL, NULL, 6, 0, '0.00', 20),
(223, 119, 114, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(224, 118, 113, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(225, 122, 127, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(226, 120, 125, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(227, 117, 115, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(228, 123, 124, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(229, 121, 126, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(230, 116, 112, '2017-03-26 20:00:00', NULL, NULL, 6, 0, '0.00', 21),
(231, 125, 123, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(232, 115, 119, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(233, 112, 118, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(234, 114, 117, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(235, 127, 121, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(236, 126, 120, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(237, 124, 122, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(238, 113, 116, '2017-03-29 20:00:00', NULL, NULL, 6, 0, '0.00', 22),
(239, 157, 159, '2017-01-18 23:00:00', 1, 1, 8, 1, '0.00', 23),
(240, 156, 158, '2017-01-19 01:15:00', 0, 1, 8, 1, '0.00', 23),
(241, 162, 164, '2017-01-19 23:00:00', 0, 0, 8, 1, '0.00', 23),
(242, 161, 163, '2017-01-20 01:15:00', 1, 1, 8, 1, '0.00', 23),
(243, 158, 160, '2017-01-20 23:00:00', 0, 0, 8, 1, '0.00', 24),
(244, 156, 157, '2017-01-21 01:15:00', 4, 3, 8, 1, '0.00', 24),
(245, 163, 165, '2017-01-21 23:00:00', 0, 2, 8, 1, '0.00', 24),
(246, 161, 162, '2017-01-22 01:15:00', 3, 3, 8, 1, '0.00', 24),
(247, 158, 159, '2017-01-22 23:00:00', 3, 2, 8, 1, '0.00', 25),
(248, 156, 160, '2017-01-23 01:15:00', 1, 1, 8, 1, '0.00', 25),
(249, 163, 164, '2017-01-23 23:00:00', 1, 1, 8, 1, '0.00', 25),
(250, 161, 165, '2017-01-24 01:15:00', 5, 1, 8, 1, '0.00', 25),
(251, 159, 160, '2017-01-24 23:00:00', 2, 1, 8, 1, '0.00', 26),
(252, 157, 158, '2017-01-25 01:15:00', 1, 0, 8, 1, '0.00', 26),
(253, 164, 165, '2017-01-25 23:00:00', 0, 0, 8, 1, '0.00', 26),
(254, 162, 163, '2017-01-26 01:15:00', 2, 0, 8, 1, '0.00', 26),
(255, 157, 160, '2017-01-26 23:00:00', 1, 0, 8, 1, '0.00', 27),
(256, 156, 159, '2017-01-27 01:15:00', 2, 1, 8, 1, '0.00', 27),
(257, 162, 165, '2017-01-27 23:00:00', 3, 0, 8, 1, '0.00', 27),
(258, 161, 164, '2017-01-28 01:15:00', 0, 0, 8, 1, '0.00', 27),
(259, 166, 167, '2017-01-15 20:00:00', 2, 1, 9, 1, '0.00', 28),
(260, 168, 170, '2017-01-15 20:00:00', 1, 0, 9, 1, '0.00', 28),
(261, 166, 171, '2017-01-19 00:00:00', 1, 4, 9, 1, '0.00', 31),
(262, 169, 168, '2017-01-20 00:00:00', 8, 7, 9, 1, '0.00', 31),
(263, 167, 170, '2017-01-18 21:30:00', 0, 1, 9, 1, '0.00', 29),
(269, 172, 173, '2017-02-08 22:30:00', NULL, NULL, 10, 0, '0.00', 81),
(270, 174, 175, '2017-02-08 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(271, 176, 177, '2017-02-08 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(272, 190, 191, '2017-02-08 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(273, 192, 193, '2017-02-08 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(274, 188, 189, '2017-02-08 23:15:00', NULL, NULL, 10, 0, '0.00', 81),
(275, 178, 179, '2017-02-08 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(276, 194, 195, '2017-02-09 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(277, 180, 181, '2017-02-09 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(278, 182, 183, '2017-02-09 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(279, 196, 197, '2017-02-09 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(280, 198, 199, '2017-02-09 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(281, 184, 185, '2017-02-09 01:45:00', NULL, NULL, 10, 0, '0.00', 81),
(282, 200, 201, '2017-02-09 02:30:00', NULL, NULL, 10, 0, '0.00', 81),
(283, 186, 187, '2017-02-09 02:30:00', NULL, NULL, 10, 0, '0.00', 81),
(284, 202, 203, '2017-02-09 02:30:00', NULL, NULL, 10, 0, '0.00', 81),
(285, 204, 205, '2017-02-09 05:30:00', NULL, NULL, 10, 0, '0.00', 81),
(286, 208, 209, '2017-02-09 22:15:00', NULL, NULL, 10, 0, '0.00', 81),
(287, 206, 207, '2017-02-10 00:00:00', NULL, NULL, 10, 0, '0.00', 81),
(288, 210, 211, '2017-02-10 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(289, 212, 213, '2017-02-15 22:15:00', NULL, NULL, 10, 0, '0.00', 81),
(290, 228, 229, '2017-02-15 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(291, 214, 215, '2017-02-15 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(292, 230, 231, '2017-02-15 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(293, 216, 217, '2017-02-15 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(294, 232, 233, '2017-02-15 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(295, 218, 219, '2017-02-15 23:30:00', NULL, NULL, 10, 0, '0.00', 81),
(296, 234, 235, '2017-02-16 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(297, 220, 221, '2017-02-16 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(298, 236, 237, '2017-02-16 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(299, 222, 223, '2017-02-16 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(300, 238, 239, '2017-02-16 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(301, 224, 225, '2017-02-16 00:45:00', NULL, NULL, 10, 0, '0.00', 81),
(302, 240, 241, '2017-02-16 00:45:00', NULL, NULL, 10, 0, '0.00', 81),
(303, 226, 227, '2017-02-16 02:30:00', NULL, NULL, 10, 0, '0.00', 81),
(304, 242, 243, '2017-02-16 02:45:00', NULL, NULL, 10, 0, '0.00', 81),
(305, 244, 245, '2017-02-16 05:30:00', NULL, NULL, 10, 0, '0.00', 81),
(306, 248, 249, '2017-02-16 22:15:00', NULL, NULL, 10, 0, '0.00', 81),
(307, 246, 247, '2017-02-17 00:30:00', NULL, NULL, 10, 0, '0.00', 81),
(308, 250, 251, '2017-02-17 03:30:00', NULL, NULL, 10, 0, '0.00', 81),
(309, 166, 168, '2017-01-21 21:15:00', 1, 0, 9, 1, '0.00', 30),
(310, 171, 169, '2017-01-22 00:00:00', 3, 4, 9, 1, '0.00', 32),
(311, 252, 253, '2017-01-30 22:00:00', NULL, NULL, 11, 0, '0.00', 82),
(312, 256, 257, '2017-01-31 00:15:00', NULL, NULL, 11, 0, '0.00', 82),
(313, 254, 255, '2017-01-31 02:30:00', NULL, NULL, 11, 0, '0.00', 82),
(314, 252, 257, '2017-02-02 22:00:00', NULL, NULL, 11, 0, '0.00', 83),
(315, 256, 255, '2017-02-03 00:15:00', NULL, NULL, 11, 0, '0.00', 83),
(316, 254, 253, '2017-02-03 02:30:00', NULL, NULL, 11, 0, '0.00', 83),
(317, 255, 253, '2017-02-05 22:00:00', NULL, NULL, 11, 0, '0.00', 84),
(318, 256, 252, '2017-02-06 00:15:00', NULL, NULL, 11, 0, '0.00', 84),
(319, 254, 257, '2017-02-06 02:30:00', NULL, NULL, 11, 0, '0.00', 84),
(320, 254, 252, '2017-02-08 22:00:00', NULL, NULL, 11, 0, '0.00', 85),
(321, 256, 253, '2017-02-09 00:15:00', NULL, NULL, 11, 0, '0.00', 85),
(322, 255, 257, '2017-02-09 02:30:00', NULL, NULL, 11, 0, '0.00', 85),
(323, 257, 253, '2017-02-11 21:30:00', NULL, NULL, 11, 0, '0.00', 86),
(324, 252, 255, '2017-02-11 23:15:00', NULL, NULL, 11, 0, '0.00', 86),
(325, 254, 256, '2017-02-12 02:00:00', NULL, NULL, 11, 0, '0.00', 86),
(326, 98, 260, '2017-02-02 23:00:00', NULL, NULL, 5, 0, '0.00', 8),
(327, 97, 258, '2017-02-02 23:15:00', NULL, NULL, 5, 0, '0.00', 8),
(328, 101, 259, '2017-02-03 01:00:00', NULL, NULL, 5, 0, '0.00', 8),
(329, 258, 97, '2017-02-09 23:15:00', NULL, NULL, 5, 0, '0.00', 9),
(330, 259, 101, '2017-02-10 00:15:00', NULL, NULL, 5, 0, '0.00', 9),
(331, 260, 98, '2017-02-10 01:15:00', NULL, NULL, 5, 0, '0.00', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `penca`
--

CREATE TABLE IF NOT EXISTS `penca` (
  `pn_id` int(11) NOT NULL AUTO_INCREMENT,
  `pn_name` varchar(255) NOT NULL,
  `pn_value` decimal(3,0) NOT NULL,
  `pn_iduser` int(11) DEFAULT NULL,
  `pn_valueaccumulated` decimal(3,2) NOT NULL,
  `pn_idchampionship` int(11) NOT NULL,
  `pn_justfriends` binary(1) DEFAULT '0',
  `pn_password` varchar(45) DEFAULT NULL,
  `pn_icone` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`pn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provisorio`
--

CREATE TABLE IF NOT EXISTS `provisorio` (
  `prov_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `prov_username` varchar(20) DEFAULT NULL,
  `prov_password` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`prov_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Volcado de datos para la tabla `provisorio`
--

INSERT INTO `provisorio` (`prov_id`, `prov_username`, `prov_password`) VALUES
(34, 'xxx', 'pppp'),
(33, 'xxx', 'pppp'),
(32, 'test3', 'test4'),
(31, 'test3', 'test4'),
(30, 'test1', 'test2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntuacionchamp`
--

CREATE TABLE IF NOT EXISTS `puntuacionchamp` (
  `pc_id` int(11) NOT NULL AUTO_INCREMENT,
  `pc_idchampionship` int(11) NOT NULL,
  `pc_iduser` int(11) NOT NULL,
  `pc_points` int(5) DEFAULT '0',
  PRIMARY KEY (`pc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntuacionrodada`
--

CREATE TABLE IF NOT EXISTS `puntuacionrodada` (
  `pr_id` int(11) NOT NULL AUTO_INCREMENT,
  `pr_idround` int(11) NOT NULL,
  `pr_iduser` int(11) NOT NULL,
  `pr_points` int(5) DEFAULT '0',
  PRIMARY KEY (`pr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `ranking`
--
CREATE TABLE IF NOT EXISTS `ranking` (
`points` decimal(32,0)
,`rk_iduser` int(11)
,`rk_username` varchar(155)
,`rk_idchamp` int(11)
);
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `result`
--

CREATE TABLE IF NOT EXISTS `result` (
  `rs_id` int(11) NOT NULL AUTO_INCREMENT,
  `rs_idmatch` int(11) NOT NULL,
  `rs_res1` int(11) NOT NULL,
  `rs_res2` int(11) NOT NULL,
  `rs_date` datetime NOT NULL,
  `rs_idpenca` int(11) NOT NULL,
  `rs_iduser` int(11) NOT NULL,
  `rs_round` int(3) NOT NULL,
  `rs_result` varchar(100) DEFAULT NULL,
  `rs_points` int(3) DEFAULT '0',
  PRIMARY KEY (`rs_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=129 ;

--
-- Volcado de datos para la tabla `result`
--

INSERT INTO `result` (`rs_id`, `rs_idmatch`, `rs_res1`, `rs_res2`, `rs_date`, `rs_idpenca`, `rs_iduser`, `rs_round`, `rs_result`, `rs_points`) VALUES
(1, 127, 2, 1, '0000-00-00 00:00:00', 0, 27, 6, NULL, 0),
(2, 128, 1, 2, '0000-00-00 00:00:00', 0, 27, 6, NULL, 0),
(3, 129, 0, 2, '0000-00-00 00:00:00', 0, 27, 6, NULL, 0),
(4, 131, 2, 0, '0000-00-00 00:00:00', 0, 27, 7, NULL, 0),
(5, 130, 1, 1, '0000-00-00 00:00:00', 0, 27, 7, NULL, 0),
(6, 132, 2, 1, '0000-00-00 00:00:00', 0, 27, 7, NULL, 0),
(7, 133, 1, 1, '0000-00-00 00:00:00', 0, 27, 8, NULL, 0),
(8, 134, 1, 0, '0000-00-00 00:00:00', 0, 27, 8, NULL, 0),
(9, 135, 2, 2, '0000-00-00 00:00:00', 0, 27, 8, NULL, 0),
(10, 137, 3, 2, '0000-00-00 00:00:00', 0, 27, 8, NULL, 0),
(11, 136, 2, 1, '0000-00-00 00:00:00', 0, 27, 8, NULL, 0),
(12, 138, 2, 0, '0000-00-00 00:00:00', 0, 27, 9, NULL, 0),
(13, 140, 1, 1, '0000-00-00 00:00:00', 0, 27, 9, NULL, 0),
(14, 139, 2, 2, '0000-00-00 00:00:00', 0, 27, 9, NULL, 0),
(15, 141, 2, 0, '0000-00-00 00:00:00', 0, 27, 9, NULL, 0),
(16, 142, 2, 1, '0000-00-00 00:00:00', 0, 27, 9, NULL, 0),
(17, 127, 2, 1, '0000-00-00 00:00:00', 0, 31, 6, NULL, 0),
(18, 128, 2, 1, '0000-00-00 00:00:00', 0, 31, 6, NULL, 0),
(19, 129, 1, 1, '0000-00-00 00:00:00', 0, 31, 6, NULL, 0),
(20, 130, 2, 0, '0000-00-00 00:00:00', 0, 31, 7, NULL, 0),
(21, 131, 2, 0, '0000-00-00 00:00:00', 0, 31, 7, NULL, 0),
(22, 132, 3, 1, '0000-00-00 00:00:00', 0, 31, 7, NULL, 0),
(23, 127, 2, 1, '0000-00-00 00:00:00', 0, 28, 6, NULL, 0),
(24, 128, 3, 1, '0000-00-00 00:00:00', 0, 28, 6, NULL, 0),
(25, 129, 4, 1, '0000-00-00 00:00:00', 0, 28, 6, NULL, 0),
(26, 131, 5, 0, '0000-00-00 00:00:00', 0, 28, 7, NULL, 0),
(27, 130, 3, 2, '0000-00-00 00:00:00', 0, 28, 7, NULL, 0),
(28, 128, 1, 2, '0000-00-00 00:00:00', 0, 37, 6, NULL, 0),
(29, 127, 1, 1, '0000-00-00 00:00:00', 0, 37, 6, NULL, 0),
(30, 129, 0, 2, '0000-00-00 00:00:00', 0, 37, 6, NULL, 0),
(31, 127, 2, 1, '0000-00-00 00:00:00', 0, 19, 6, NULL, 0),
(32, 129, 4, 3, '0000-00-00 00:00:00', 0, 19, 6, NULL, 0),
(33, 128, 1, 0, '0000-00-00 00:00:00', 0, 19, 6, '1', 5),
(34, 127, 0, 0, '0000-00-00 00:00:00', 0, 48, 6, NULL, 0),
(35, 129, 0, 1, '0000-00-00 00:00:00', 0, 48, 6, '1', 5),
(36, 128, 0, 0, '0000-00-00 00:00:00', 0, 48, 6, NULL, 0),
(37, 128, 2, 1, '0000-00-00 00:00:00', 0, 49, 6, NULL, 0),
(38, 127, 2, 1, '0000-00-00 00:00:00', 0, 49, 6, NULL, 0),
(39, 129, 0, 3, '0000-00-00 00:00:00', 0, 49, 6, NULL, 0),
(40, 130, 3, 0, '0000-00-00 00:00:00', 0, 51, 7, NULL, 0),
(41, 131, 4, 1, '0000-00-00 00:00:00', 0, 51, 7, NULL, 0),
(42, 127, 1, 0, '0000-00-00 00:00:00', 0, 51, 6, NULL, 0),
(43, 129, 0, 1, '0000-00-00 00:00:00', 0, 51, 6, '1', 5),
(44, 128, 0, 0, '0000-00-00 00:00:00', 0, 51, 6, NULL, 0),
(45, 240, 1, 3, '0000-00-00 00:00:00', 0, 59, 23, NULL, 0),
(46, 239, 2, 0, '0000-00-00 00:00:00', 0, 59, 23, NULL, 0),
(47, 239, 3, 1, '0000-00-00 00:00:00', 0, 19, 23, NULL, 0),
(48, 240, 0, 3, '0000-00-00 00:00:00', 0, 19, 23, NULL, 0),
(49, 241, 4, 2, '0000-00-00 00:00:00', 0, 19, 23, NULL, 0),
(50, 242, 2, 1, '0000-00-00 00:00:00', 0, 19, 23, NULL, 0),
(51, 259, 0, 2, '0000-00-00 00:00:00', 0, 62, 28, NULL, 0),
(52, 260, 3, 1, '0000-00-00 00:00:00', 0, 62, 28, NULL, 0),
(53, 259, 1, 2, '0000-00-00 00:00:00', 0, 19, 28, NULL, 0),
(54, 263, 2, 1, '0000-00-00 00:00:00', 0, 63, 29, NULL, 0),
(55, 262, 2, 1, '0000-00-00 00:00:00', 0, 63, 31, NULL, 0),
(56, 261, 1, 4, '0000-00-00 00:00:00', 0, 19, 31, '1', 5),
(57, 262, 3, 2, '0000-00-00 00:00:00', 0, 19, 31, NULL, 0),
(58, 263, 2, 1, '0000-00-00 00:00:00', 0, 19, 29, NULL, 0),
(59, 242, 3, 0, '0000-00-00 00:00:00', 0, 3, 23, NULL, 0),
(60, 309, 1, 2, '0000-00-00 00:00:00', 0, 63, 30, NULL, 0),
(61, 310, 1, 3, '0000-00-00 00:00:00', 0, 63, 32, NULL, 0),
(62, 245, 1, 1, '0000-00-00 00:00:00', 0, 63, 24, NULL, 0),
(63, 246, 1, 1, '0000-00-00 00:00:00', 0, 63, 24, NULL, 0),
(64, 250, 1, 1, '0000-00-00 00:00:00', 0, 63, 25, NULL, 0),
(65, 247, 2, 0, '0000-00-00 00:00:00', 0, 63, 25, NULL, 0),
(66, 248, 2, 2, '0000-00-00 00:00:00', 0, 63, 25, NULL, 0),
(67, 249, 1, 1, '0000-00-00 00:00:00', 0, 63, 25, '1', 5),
(68, 127, 2, 1, '0000-00-00 00:00:00', 0, 63, 6, NULL, 0),
(69, 129, 2, 0, '0000-00-00 00:00:00', 0, 63, 6, NULL, 0),
(70, 128, 3, 1, '0000-00-00 00:00:00', 0, 63, 6, NULL, 0),
(71, 251, 2, 3, '0000-00-00 00:00:00', 0, 55, 26, NULL, 0),
(72, 254, 2, 0, '0000-00-00 00:00:00', 0, 55, 26, '1', 5),
(73, 252, 0, 2, '0000-00-00 00:00:00', 0, 55, 26, NULL, 0),
(74, 253, 2, 0, '0000-00-00 00:00:00', 0, 55, 26, NULL, 0),
(75, 249, 0, 1, '0000-00-00 00:00:00', 0, 55, 25, NULL, 0),
(76, 250, 2, 0, '0000-00-00 00:00:00', 0, 55, 25, NULL, 0),
(77, 255, 2, 2, '0000-00-00 00:00:00', 0, 55, 27, NULL, 0),
(78, 256, 2, 1, '0000-00-00 00:00:00', 0, 55, 27, '1', 5),
(79, 257, 3, 1, '0000-00-00 00:00:00', 0, 55, 27, NULL, 0),
(80, 258, 2, 1, '0000-00-00 00:00:00', 0, 55, 27, NULL, 0),
(81, 127, 0, 1, '0000-00-00 00:00:00', 0, 55, 6, NULL, 0),
(82, 129, 2, 2, '0000-00-00 00:00:00', 0, 55, 6, NULL, 0),
(83, 128, 1, 2, '0000-00-00 00:00:00', 0, 55, 6, NULL, 0),
(84, 251, 3, 1, '0000-00-00 00:00:00', 0, 63, 26, NULL, 0),
(85, 252, 0, 2, '0000-00-00 00:00:00', 0, 63, 26, NULL, 0),
(86, 253, 2, 1, '0000-00-00 00:00:00', 0, 63, 26, NULL, 0),
(87, 254, 1, 1, '0000-00-00 00:00:00', 0, 63, 26, NULL, 0),
(88, 131, 1, 0, '0000-00-00 00:00:00', 0, 63, 7, NULL, 0),
(89, 132, 2, 1, '0000-00-00 00:00:00', 0, 63, 7, NULL, 0),
(90, 130, 2, 1, '0000-00-00 00:00:00', 0, 63, 7, NULL, 0),
(91, 255, 1, 2, '0000-00-00 00:00:00', 0, 63, 27, NULL, 0),
(92, 256, 2, 0, '0000-00-00 00:00:00', 0, 63, 27, NULL, 0),
(93, 257, 1, 1, '0000-00-00 00:00:00', 0, 63, 27, NULL, 0),
(94, 258, 2, 0, '0000-00-00 00:00:00', 0, 63, 27, NULL, 0),
(95, 130, 2, 0, '0000-00-00 00:00:00', 0, 3, 7, NULL, 0),
(96, 130, 2, 1, '0000-00-00 00:00:00', 0, 55, 7, NULL, 0),
(97, 131, 2, 0, '0000-00-00 00:00:00', 0, 55, 7, NULL, 0),
(98, 132, 2, 0, '0000-00-00 00:00:00', 0, 55, 7, NULL, 0),
(99, 133, 1, 0, '0000-00-00 00:00:00', 0, 55, 8, NULL, 0),
(100, 134, 2, 1, '0000-00-00 00:00:00', 0, 55, 8, NULL, 0),
(101, 135, 2, 1, '0000-00-00 00:00:00', 0, 55, 8, NULL, 0),
(102, 137, 3, 2, '0000-00-00 00:00:00', 0, 55, 8, NULL, 0),
(103, 131, 1, 0, '0000-00-00 00:00:00', 0, 3, 7, NULL, 0),
(104, 132, 1, 1, '0000-00-00 00:00:00', 0, 3, 7, NULL, 0),
(105, 136, 2, 0, '0000-00-00 00:00:00', 0, 55, 8, NULL, 0),
(106, 257, 3, 1, '0000-00-00 00:00:00', 0, 3, 27, NULL, 0),
(107, 138, 0, 3, '0000-00-00 00:00:00', 0, 55, 9, NULL, 0),
(108, 258, 2, 0, '0000-00-00 00:00:00', 0, 3, 27, NULL, 0),
(109, 140, 2, 1, '0000-00-00 00:00:00', 0, 55, 9, NULL, 0),
(110, 139, 1, 1, '0000-00-00 00:00:00', 0, 55, 9, NULL, 0),
(111, 141, 2, 2, '0000-00-00 00:00:00', 0, 55, 9, NULL, 0),
(112, 142, 2, 3, '0000-00-00 00:00:00', 0, 55, 9, NULL, 0),
(113, 133, 2, 0, '0000-00-00 00:00:00', 0, 63, 8, NULL, 0),
(114, 136, 2, 1, '0000-00-00 00:00:00', 0, 63, 8, NULL, 0),
(115, 137, 2, 1, '0000-00-00 00:00:00', 0, 63, 8, NULL, 0),
(116, 134, 1, 0, '0000-00-00 00:00:00', 0, 63, 8, NULL, 0),
(117, 135, 1, 0, '0000-00-00 00:00:00', 0, 63, 8, NULL, 0),
(118, 133, 2, 0, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(119, 134, 1, 0, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(120, 137, 1, 1, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(121, 136, 0, 1, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(122, 327, 3, 0, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(123, 328, 0, 2, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(124, 135, 1, 0, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(125, 326, 2, 1, '0000-00-00 00:00:00', 0, 3, 8, NULL, 0),
(126, 311, 2, 1, '0000-00-00 00:00:00', 0, 3, 82, NULL, 0),
(127, 312, 2, 1, '0000-00-00 00:00:00', 0, 3, 82, NULL, 0),
(128, 313, 1, 2, '0000-00-00 00:00:00', 0, 3, 82, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `round`
--

CREATE TABLE IF NOT EXISTS `round` (
  `rd_id` int(11) NOT NULL AUTO_INCREMENT,
  `rd_round` varchar(40) NOT NULL,
  `rd_idchampionship` int(11) NOT NULL,
  `rd_acumulado` decimal(19,2) DEFAULT '0.00',
  PRIMARY KEY (`rd_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=87 ;

--
-- Volcado de datos para la tabla `round`
--

INSERT INTO `round` (`rd_id`, `rd_round`, `rd_idchampionship`, `rd_acumulado`) VALUES
(6, 'FASE 1 (a)', 5, '0.00'),
(7, 'FASE 1 (b)', 5, '0.00'),
(8, 'FASE 2 (a)', 5, '0.00'),
(9, 'FASE 2 (b)', 5, '0.00'),
(10, '1', 6, '0.00'),
(11, '2', 6, '0.00'),
(12, '3', 6, '0.00'),
(14, '4', 6, '0.00'),
(15, '5', 6, '0.00'),
(16, '6', 6, '0.00'),
(17, '7', 6, '0.00'),
(18, '8', 6, '0.00'),
(19, '9', 6, '0.00'),
(20, '10', 6, '0.00'),
(21, '11', 6, '0.00'),
(22, '12', 6, '0.00'),
(23, '1', 8, '0.00'),
(24, '2', 8, '0.00'),
(25, '3', 8, '0.00'),
(26, '4', 8, '0.00'),
(27, '5', 8, '0.00'),
(28, 'QUARTERFINALS', 9, '0.00'),
(31, 'SEMIFINALS', 9, '0.00'),
(30, '3RD PLACE', 9, '0.00'),
(29, 'CONSOLATION', 9, '0.00'),
(32, 'FINAL', 9, '0.00'),
(81, '1', 10, '0.00'),
(82, '1', 11, '0.00'),
(83, '2', 11, '0.00'),
(84, '3', 11, '0.00'),
(85, '4', 11, '0.00'),
(86, '5', 11, '0.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saques`
--

CREATE TABLE IF NOT EXISTS `saques` (
  `sq_id` int(11) NOT NULL AUTO_INCREMENT,
  `sq_iduser` int(11) NOT NULL,
  `sq_cash` decimal(12,2) DEFAULT '0.00',
  `sq_date` datetime DEFAULT NULL,
  `sq_concluido` bit(1) DEFAULT b'0',
  PRIMARY KEY (`sq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `team`
--

CREATE TABLE IF NOT EXISTS `team` (
  `tm_id` int(11) NOT NULL AUTO_INCREMENT,
  `tm_name` varchar(100) NOT NULL,
  `tm_idchampionship` int(11) NOT NULL,
  `tm_points` int(11) DEFAULT '0',
  `tm_played` int(3) DEFAULT '0',
  `tm_logo` varchar(255) DEFAULT NULL,
  `tm_grupo` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`tm_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=261 ;

--
-- Volcado de datos para la tabla `team`
--

INSERT INTO `team` (`tm_id`, `tm_name`, `tm_idchampionship`, `tm_points`, `tm_played`, `tm_logo`, `tm_grupo`) VALUES
(96, 'U. Sucre', 5, 3, 2, '/assets/img/prelibertadores/unsucre.png', 'E1'),
(97, 'Wanderers', 5, 3, 2, '/assets/img/prelibertadores/wanderers.png', 'E1'),
(98, 'Dep. Capiata', 5, 4, 2, '/assets/img/prelibertadores/depcapiata.png', 'E3'),
(99, 'Dep. Tachira', 5, 1, 2, '/assets/img/prelibertadores/deptachira.png', 'E3'),
(100, 'Dep. Municipal', 5, 1, 2, '/assets/img/prelibertadores/depmunicipal.png', 'E2'),
(101, 'Indep. del Valle', 5, 4, 2, '/assets/img/prelibertadores/indepdelvalle.png', 'E2'),
(102, 'Cerro', 5, 0, 0, '/assets/img/prelibertadores/cerro.png', 'C3'),
(103, 'Unión Española', 5, 0, 0, '/assets/img/prelibertadores/unionesp.png', 'C3'),
(104, 'Carabobo', 5, 0, 0, '/assets/img/prelibertadores/carabobo.png', 'C4'),
(105, 'Junior', 5, 0, 0, '/assets/img/prelibertadores/junior.png', 'C4'),
(106, 'Atl. Tucumán', 5, 0, 0, '/assets/img/prelibertadores/tucuman.png', 'C5'),
(107, 'El Nacional', 5, 0, 0, '/assets/img/prelibertadores/elnacional.png', 'C5'),
(108, 'Atl. Paranaense', 5, 0, 0, '/assets/img/prelibertadores/atlparanaense.png', 'C1'),
(109, 'Millonarios', 5, 0, 0, '/assets/img/prelibertadores/millonarios.png', 'C1'),
(110, 'Botafogo', 5, 0, 0, '/assets/img/prelibertadores/botafogo.png', 'C2'),
(111, 'Colo colo', 5, 0, 0, '/assets/img/prelibertadores/colocolo.png', 'C2'),
(112, 'Corinthians', 6, 0, 0, '/assets/img/campeonatopaulista2017/corinthians.png', 'A'),
(113, 'São Bernardo', 6, 0, 0, '/assets/img/campeonatopaulista2017/saobernardo.png', 'A'),
(114, 'Ituano', 6, 0, 0, '/assets/img/campeonatopaulista2017/ituano.png', 'A'),
(115, 'Botafogo', 6, 0, 0, '/assets/img/campeonatopaulista2017/botafogosp.png', 'A'),
(116, 'São Paulo', 6, 0, 0, '/assets/img/campeonatopaulista2017/sao_paulo.png', 'B'),
(117, 'Red Bull Brasil', 6, 0, 0, '/assets/img/campeonatopaulista2017/rbBrasil.png', 'B'),
(118, 'Linense', 6, 0, 0, '/assets/img/campeonatopaulista2017/linense.png', 'B'),
(119, 'Ferroviaria', 6, 0, 0, '/assets/img/campeonatopaulista2017/ferroviaria.png', 'B'),
(120, 'Palmeiras', 6, 0, 0, '/assets/img/campeonatopaulista2017/palmeiras.png', 'C'),
(121, 'São Bento', 6, 0, 0, '/assets/img/campeonatopaulista2017/saobento.png', 'C'),
(122, 'Novorizontino', 6, 0, 0, '/assets/img/campeonatopaulista2017/novohorizontino.png', 'C'),
(123, 'Santo André', 6, 0, 0, '/assets/img/campeonatopaulista2017/stoandre.png', 'C'),
(124, 'Santos', 6, 0, 0, '/assets/img/campeonatopaulista2017/santos.png', 'D'),
(125, 'Audax', 6, 0, 0, '/assets/img/campeonatopaulista2017/audax.png', 'D'),
(126, 'Ponte Preta', 6, 0, 0, '/assets/img/campeonatopaulista2017/ponte_preta.png', 'D'),
(127, 'Mirassol', 6, 0, 0, '/assets/img/campeonatopaulista2017/mirassol.png', 'D'),
(128, 'Atlético Nacional', 7, 0, 0, '/assets/img/copalibertadores/atleticonacional.png', '1'),
(129, 'Estudiantes', 7, 0, 0, '/assets/img/copalibertadores/estudiantes.png', '1'),
(130, 'Barcelona', 7, 0, 0, '/assets/img/copalibertadores/barcelonasc.png', '1'),
(131, 'Santos', 7, 0, 0, '/assets/img/copalibertadores/santos.png', '2'),
(132, 'Santa Fe', 7, 0, 0, '/assets/img/copalibertadores/independientesantafe.png', '2'),
(133, 'Sporting Cristal', 7, 0, 0, '/assets/img/copalibertadores/sportingcristal.png', '2'),
(134, 'River Plate', 7, 0, 0, '/assets/img/copalibertadores/riverarg.png', '3'),
(135, 'Emelec', 7, 0, 0, '/assets/img/copalibertadores/emelec.png', '3'),
(136, 'Indep. Medellin', 7, 0, 0, '/assets/img/copalibertadores/dim.png', '3'),
(137, 'Melgar', 7, 0, 0, '/assets/img/copalibertadores/melgar.png', '3'),
(138, 'San Lorenzo', 7, 0, 0, '/assets/img/copalibertadores/sanlorenzo.png', '4'),
(139, 'U Católica', 7, 0, 0, '/assets/img/copalibertadores/ucatolica.png', '4'),
(140, 'Flamengo', 7, 0, 0, '/assets/img/copalibertadores/flamengo.png', '4'),
(141, 'Peñarol', 7, 0, 0, '/assets/img/copalibertadores/penarol.png', '5'),
(142, 'Palmeiras', 7, 0, 0, '/assets/img/copalibertadores/palmeiras.png', '5'),
(143, 'J Wilstermann', 7, 0, 0, '/assets/img/copalibertadores/wilstermann.png', '5'),
(144, 'Atlético Mineiro', 7, 0, 0, '/assets/img/copalibertadores/atleticomineiro.png', '6'),
(145, 'Libertad', 7, 0, 0, '/assets/img/copalibertadores/clublibertad.png', '6'),
(146, 'Godoy Cruz', 7, 0, 0, '/assets/img/copalibertadores/godoycruz.png', '6'),
(147, 'Sport Boys', 7, 0, 0, '/assets/img/copalibertadores/sportboys.png', '6'),
(148, 'Nacional', 7, 0, 0, '/assets/img/copalibertadores/nacional.png', '7'),
(149, 'Chapecoense', 7, 0, 0, '/assets/img/copalibertadores/chapecoense.png', '7'),
(150, 'Lanús', 7, 0, 0, '/assets/img/copalibertadores/lanus.png', '7'),
(151, 'Zulia', 7, 0, 0, '/assets/img/copalibertadores/zulia.png', '7'),
(152, 'Grêmio', 7, 0, 0, '/assets/img/copalibertadores/gremio.png', '8'),
(153, 'Guaraní', 7, 0, 0, '/assets/img/copalibertadores/guarani.png', '8'),
(154, 'Zamora', 7, 0, 0, '/assets/img/copalibertadores/zamora.png', '8'),
(155, 'Dep. Iquique', 7, 0, 0, '/assets/img/copalibertadores/deportesiquique.png', '8'),
(156, 'Ecuador', 8, 7, 4, '/assets/img/copasudamericanasub20/ecuador.gif', 'A'),
(157, 'Colombia', 8, 7, 4, '/assets/img/copasudamericanasub20/colombia.gif', 'A'),
(158, 'Brasil', 8, 7, 4, '/assets/img/copasudamericanasub20/brasil.gif', 'A'),
(159, 'Paraguay', 8, 4, 4, '/assets/img/copasudamericanasub20/paraguay.gif', 'A'),
(160, 'Chile', 8, 2, 4, '/assets/img/copasudamericanasub20/chile.gif', 'A'),
(161, 'Argentina', 8, 6, 4, '/assets/img/copasudamericanasub20/argentina.gif', 'B'),
(162, 'Uruguay', 8, 8, 4, '/assets/img/copasudamericanasub20/uruguay.gif', 'B'),
(163, 'Peru', 8, 2, 4, '/assets/img/copasudamericanasub20/peru.gif', 'B'),
(164, 'Venezuela', 8, 4, 4, '/assets/img/copasudamericanasub20/venezuela.gif', 'B'),
(165, 'Bolivia', 8, 4, 4, '/assets/img/copasudamericanasub20/bolivia.gif', 'B'),
(166, 'Vasco', 9, 6, 3, '/assets/img/floridacup/vasco.png', ''),
(167, 'Barcelona SC', 9, 0, 2, '/assets/img/floridacup/barcelonasc.png', ''),
(168, 'River Plate', 9, 3, 3, '/assets/img/floridacup/riverplate.png', ''),
(169, 'São Paulo', 9, 6, 2, '/assets/img/floridacup/saopaulo.png', ''),
(170, 'Millonarios FC', 9, 3, 2, '/assets/img/floridacup/millonarios.png', ''),
(171, 'Corinthians', 9, 3, 2, '/assets/img/floridacup/corinthians.png', ''),
(172, 'AA Luziânia', 10, 0, 0, '/assets/img/copadobrasil/luziania.png', ''),
(173, 'Vitória', 10, 0, 0, '/assets/img/copadobrasil/Vitoria.png', ''),
(174, 'PSTC', 10, 0, 0, '/assets/img/copadobrasil/PSTC.png', ''),
(175, 'Ypiranga FC', 10, 0, 0, '/assets/img/copadobrasil/Ypiranga.png', ''),
(176, 'Sao Bento', 10, 0, 0, '/assets/img/copadobrasil/SaoBento.png', ''),
(177, 'Paraná', 10, 0, 0, '/assets/img/copadobrasil/Parana.png', ''),
(178, 'Campinense', 10, 0, 0, '/assets/img/copadobrasil/Campinense.png', ''),
(179, 'Ponte Preta', 10, 0, 0, '/assets/img/copadobrasil/PontePreta.png', ''),
(180, 'Altos', 10, 0, 0, '/assets/img/copadobrasil/Altos.png', ''),
(181, 'CRB', 10, 0, 0, '/assets/img/copadobrasil/CRB.png', ''),
(182, 'Rondoniense', 10, 0, 0, '/assets/img/copadobrasil/Rondoniense.png', ''),
(183, 'Cuiabá', 10, 0, 0, '/assets/img/copadobrasil/Cuiaba.png', ''),
(184, 'CSA', 10, 0, 0, '/assets/img/copadobrasil/CSA.png', ''),
(185, 'Sport Recife', 10, 0, 0, '/assets/img/copadobrasil/SportRecife.png', ''),
(186, 'São Raimundo', 10, 0, 0, '/assets/img/copadobrasil/SaoRaimundo.png', ''),
(187, 'Boa', 10, 0, 0, '/assets/img/copadobrasil/Boa.png', ''),
(188, 'Vitoria Conquista', 10, 0, 0, '/assets/img/copadobrasil/VitoriaConquista.png', ''),
(189, 'Coritiba', 10, 0, 0, '/assets/img/copadobrasil/Coritiba.png', ''),
(190, 'Audax', 10, 0, 0, '/assets/img/copadobrasil/Audax .png', ''),
(191, 'América RN', 10, 0, 0, '/assets/img/copadobrasil/AmericaRN.png', ''),
(192, 'Ferroviária', 10, 0, 0, '/assets/img/copadobrasil/Ferroviaria.png', ''),
(193, 'ASA', 10, 0, 0, '/assets/img/copadobrasil/ASA.png', ''),
(194, 'Sao Francisco', 10, 0, 0, '/assets/img/copadobrasil/SaoFrancisco.png', ''),
(195, 'Botafogo PB', 10, 0, 0, '/assets/img/copadobrasil/BotafogoPB.png', ''),
(196, 'Gurupi', 10, 0, 0, '/assets/img/copadobrasil/Gurupi.png', ''),
(197, 'Londrina', 10, 0, 0, '/assets/img/copadobrasil/Londrina.png', ''),
(198, 'Caldense', 10, 0, 0, '/assets/img/copadobrasil/Caldense.png', ''),
(199, 'Corinthians', 10, 0, 0, '/assets/img/copadobrasil/Corinthians.png', ''),
(200, 'Sete de Dourados', 10, 0, 0, '/assets/img/copadobrasil/SetedeDourados.png', ''),
(201, 'Ríver AC', 10, 0, 0, '/assets/img/copadobrasil/Ríver.png', ''),
(202, 'Sinop', 10, 0, 0, '/assets/img/copadobrasil/Sinop.png', ''),
(203, 'Salgueiro', 10, 0, 0, '/assets/img/copadobrasil/Salgueiro.png', ''),
(204, 'Atlético Acreano', 10, 0, 0, '/assets/img/copadobrasil/AtleticoAcreano.png', ''),
(205, 'América-MG', 10, 0, 0, '/assets/img/copadobrasil/AmericaMG.png', ''),
(206, 'Santos AP', 10, 0, 0, '/assets/img/copadobrasil/SantosAP.png', ''),
(207, 'Vasco ', 10, 0, 0, '/assets/img/copadobrasil/Vasco.png', ''),
(208, 'São José', 10, 0, 0, '/assets/img/copadobrasil/SaoJose.png', ''),
(209, 'Sampaio Corrêa', 10, 0, 0, '/assets/img/copadobrasil/SampaioCorrea.png', ''),
(210, 'Moto Club MA', 10, 0, 0, '/assets/img/copadobrasil/motoclub.png', ''),
(211, 'São Paulo', 10, 0, 0, '/assets/img/copadobrasil/SaoPaulo.png', ''),
(212, 'Desportiva Ferroviária', 10, 0, 0, '/assets/img/copadobrasil/DesportivaFerroviaria.png', ''),
(213, 'Avaí', 10, 0, 0, '/assets/img/copadobrasil/Avai.png', ''),
(214, 'Friburguense', 10, 0, 0, '/assets/img/copadobrasil/Friburguense.png', ''),
(215, 'Oeste', 10, 0, 0, '/assets/img/copadobrasil/Oeste.png', ''),
(216, 'Ceilandia', 10, 0, 0, '/assets/img/copadobrasil/Ceilandia.png', ''),
(217, 'ABC', 10, 0, 0, '/assets/img/copadobrasil/ABC.png', ''),
(218, 'Brusque', 10, 0, 0, '/assets/img/copadobrasil/Brusque.png', ''),
(219, 'Remo', 10, 0, 0, '/assets/img/copadobrasil/Remo.png', ''),
(220, 'Murici', 10, 0, 0, '/assets/img/copadobrasil/Murici.png', ''),
(221, 'Juventude', 10, 0, 0, '/assets/img/copadobrasil/Juventude.png', ''),
(222, 'Itabaiana', 10, 0, 0, '/assets/img/copadobrasil/Itabaiana.png', ''),
(223, 'Goiás', 10, 0, 0, '/assets/img/copadobrasil/Goias.png', ''),
(224, 'Globo FC', 10, 0, 0, '/assets/img/copadobrasil/GloboFC.png', ''),
(225, 'Fluminense', 10, 0, 0, '/assets/img/copadobrasil/Fluminense.png', ''),
(226, 'Comercial MS', 10, 0, 0, '/assets/img/copadobrasil/ComercialMS.png', ''),
(227, 'Joinville', 10, 0, 0, '/assets/img/copadobrasil/Joinville.png', ''),
(228, 'Anapolis', 10, 0, 0, '/assets/img/copadobrasil/Anapolis.png', ''),
(229, 'Bragantino', 10, 0, 0, '/assets/img/copadobrasil/Bragantino.png', ''),
(230, 'Boavista', 10, 0, 0, '/assets/img/copadobrasil/Boavista.png', ''),
(231, 'Ceará', 10, 0, 0, '/assets/img/copadobrasil/Ceara.png', ''),
(232, 'URT', 10, 0, 0, '/assets/img/copadobrasil/URT.png', ''),
(233, 'Luverdense', 10, 0, 0, '/assets/img/copadobrasil/Luverdense.png', ''),
(234, 'Sao Raimundo (PA)', 10, 0, 0, '/assets/img/copadobrasil/SaoRaimundoPA.png', ''),
(235, 'Fortaleza', 10, 0, 0, '/assets/img/copadobrasil/Fortaleza.png', ''),
(236, 'Uniclinic', 10, 0, 0, '/assets/img/copadobrasil/Uniclinic.png', ''),
(237, 'Portuguesa', 10, 0, 0, '/assets/img/copadobrasil/Portuguesa.png', ''),
(238, 'Guarani de Juazeiro', 10, 0, 0, '/assets/img/copadobrasil/GuaranideJuazeiro.png', ''),
(239, 'Náutico', 10, 0, 0, '/assets/img/copadobrasil/Nautico.png', ''),
(240, 'Volta Redonda', 10, 0, 0, '/assets/img/copadobrasil/VoltaRedonda.png', ''),
(241, 'Cruzeiro', 10, 0, 0, '/assets/img/copadobrasil/Cruzeiro.png', ''),
(242, 'Princesa Solimões', 10, 0, 0, '/assets/img/copadobrasil/PrincesaSolimoes.png', ''),
(243, 'Internacional', 10, 0, 0, '/assets/img/copadobrasil/Internacional.png', ''),
(244, 'Rio Branco', 10, 0, 0, '/assets/img/copadobrasil/RioBranco.png', ''),
(245, 'Figueirense', 10, 0, 0, '/assets/img/copadobrasil/Figueirense.png', ''),
(246, 'Sergipe', 10, 0, 0, '/assets/img/copadobrasil/Sergipe.png', ''),
(247, 'Bahía', 10, 0, 0, '/assets/img/copadobrasil/Bahia.png', ''),
(248, 'Santo Andre', 10, 0, 0, '/assets/img/copadobrasil/SantoAndre.png', ''),
(249, 'Criciúma', 10, 0, 0, '/assets/img/copadobrasil/Criciuma.png', ''),
(250, 'Fast Clube', 10, 0, 0, '/assets/img/copadobrasil/FastClube.png', ''),
(251, 'Vila Nova', 10, 0, 0, '/assets/img/copadobrasil/VilaNova.png', ''),
(252, 'Colombia', 11, 0, 0, '/assets/img/copasudamericanasub20/colombia.gif', ''),
(253, 'Venezuela', 11, 0, 0, '/assets/img/copasudamericanasub20/venezuela.gif', ''),
(254, 'Ecuador', 11, 0, 0, '/assets/img/copasudamericanasub20/ecuador.gif', ''),
(255, 'Brasil', 11, 0, 0, '/assets/img/copasudamericanasub20/brasil.gif', ''),
(256, 'Uruguay', 11, 0, 0, '/assets/img/copasudamericanasub20/uruguay.gif', ''),
(257, 'Argentina', 11, 0, 0, '/assets/img/copasudamericanasub20/argentina.gif', ''),
(258, 'The Strongest', 5, 0, 0, '/assets/img/prelibertadores/strongest.png', ''),
(259, 'Olimpia', 5, 0, 0, '/assets/img/prelibertadores/olimpia.png', ''),
(260, 'Universitario', 5, 0, 0, '/assets/img/prelibertadores/universitario.png', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testdate`
--

CREATE TABLE IF NOT EXISTS `testdate` (
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `testdate`
--

INSERT INTO `testdate` (`date`) VALUES
('2016-11-05 03:00:00'),
('2016-11-04 08:55:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `tr_id` int(11) NOT NULL AUTO_INCREMENT,
  `tr_valortransaccion` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tr_valorcampeonato` decimal(20,2) DEFAULT '0.00',
  `tr_iduser` bigint(20) NOT NULL,
  `tr_valorrodada` decimal(20,2) DEFAULT '0.00',
  `tr_valorjogo` decimal(20,2) DEFAULT '0.00',
  `tr_idcampeonato` int(11) NOT NULL,
  `tr_idmatch` int(11) NOT NULL,
  `tr_res_ch_acumulado` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tr_res_rd_acumulado` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tr_res_mt_acumulado` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tr_res_us_cash` decimal(20,2) NOT NULL DEFAULT '0.00',
  `tr_tipo` varchar(45) DEFAULT 'JOGO',
  `tr_date` datetime DEFAULT NULL,
  `tr_motivo` varchar(45) DEFAULT NULL,
  `tr_idrodada` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`tr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=157 ;

--
-- Volcado de datos para la tabla `transaction`
--

INSERT INTO `transaction` (`tr_id`, `tr_valortransaccion`, `tr_valorcampeonato`, `tr_iduser`, `tr_valorrodada`, `tr_valorjogo`, `tr_idcampeonato`, `tr_idmatch`, `tr_res_ch_acumulado`, `tr_res_rd_acumulado`, `tr_res_mt_acumulado`, `tr_res_us_cash`, `tr_tipo`, `tr_date`, `tr_motivo`, `tr_idrodada`) VALUES
(1, '0.00', '0.00', 27, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(2, '0.00', '0.00', 27, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(3, '0.00', '0.00', 27, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(4, '0.00', '0.00', 27, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(5, '0.00', '0.00', 27, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(6, '0.00', '0.00', 27, '0.00', '0.00', 5, 132, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(7, '0.00', '0.00', 27, '0.00', '0.00', 5, 133, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(8, '0.00', '0.00', 27, '0.00', '0.00', 5, 134, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(9, '0.00', '0.00', 27, '0.00', '0.00', 5, 135, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(10, '0.00', '0.00', 27, '0.00', '0.00', 5, 137, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(11, '0.00', '0.00', 27, '0.00', '0.00', 5, 136, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(12, '0.00', '0.00', 27, '0.00', '0.00', 5, 138, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(13, '0.00', '0.00', 27, '0.00', '0.00', 5, 140, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(14, '0.00', '0.00', 27, '0.00', '0.00', 5, 139, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(15, '0.00', '0.00', 27, '0.00', '0.00', 5, 141, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(16, '0.00', '0.00', 27, '0.00', '0.00', 5, 142, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(17, '0.00', '0.00', 31, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(18, '0.00', '0.00', 31, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(19, '0.00', '0.00', 31, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(20, '0.00', '0.00', 31, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(21, '0.00', '0.00', 31, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(22, '0.00', '0.00', 31, '0.00', '0.00', 5, 132, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(23, '0.00', '0.00', 28, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(24, '0.00', '0.00', 28, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(25, '0.00', '0.00', 28, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(26, '0.00', '0.00', 28, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(27, '0.00', '0.00', 28, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(28, '0.00', '0.00', 37, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(29, '0.00', '0.00', 37, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(30, '0.00', '0.00', 37, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(31, '0.00', '0.00', 19, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(32, '0.00', '0.00', 19, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(33, '0.00', '0.00', 19, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(34, '0.00', '0.00', 48, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(35, '0.00', '0.00', 48, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(36, '0.00', '0.00', 48, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(37, '0.00', '0.00', 49, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(38, '0.00', '0.00', 49, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(39, '0.00', '0.00', 49, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(40, '0.00', '0.00', 51, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(41, '0.00', '0.00', 51, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(42, '0.00', '0.00', 51, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(43, '0.00', '0.00', 51, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(44, '0.00', '0.00', 51, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(45, '0.00', '0.12', 59, '0.00', '0.00', 8, 240, '0.12', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(46, '0.00', '0.12', 59, '0.00', '0.00', 8, 239, '0.24', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(47, '0.00', '0.12', 19, '0.00', '0.00', 8, 239, '0.36', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(48, '0.00', '0.12', 19, '0.00', '0.00', 8, 240, '0.48', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(49, '0.00', '0.12', 19, '0.00', '0.00', 8, 241, '0.60', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(50, '0.00', '0.12', 19, '0.00', '0.00', 8, 242, '0.72', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(51, '0.00', '0.00', 62, '0.00', '0.00', 9, 259, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(52, '0.00', '0.00', 62, '0.00', '0.00', 9, 260, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(53, '0.00', '0.00', 19, '0.00', '0.00', 9, 259, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(54, '0.00', '0.00', 62, '0.00', '0.00', 9, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-15 18:21:19', 'RODADA', 28),
(55, '0.00', '0.00', 19, '0.00', '0.00', 9, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-15 18:21:19', 'RODADA', 28),
(56, '0.00', '0.00', 63, '0.00', '0.00', 9, 263, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(57, '0.00', '0.00', 63, '0.00', '0.00', 9, 262, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(58, '0.00', '0.00', 19, '0.00', '0.00', 9, 261, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(59, '0.00', '0.00', 19, '0.00', '0.00', 9, 262, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(60, '0.00', '0.00', 19, '0.00', '0.00', 9, 263, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(61, '0.00', '0.00', 19, '0.00', '0.00', 9, 261, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-19 05:27:35', 'JOGO', NULL),
(62, '0.00', '0.12', 3, '0.00', '0.00', 8, 242, '0.84', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(63, '0.00', '0.00', 19, '0.00', '0.00', 9, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-20 05:27:06', 'RODADA', 31),
(64, '0.00', '0.00', 59, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-20 05:31:01', 'RODADA', 23),
(65, '0.00', '0.00', 19, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-20 05:31:01', 'RODADA', 23),
(66, '0.00', '0.00', 3, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '46.20', 'CREDITO', '2017-01-20 05:31:01', 'RODADA', 23),
(67, '0.00', '0.00', 63, '0.00', '0.00', 9, 309, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(68, '0.00', '0.00', 63, '0.00', '0.00', 9, 310, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(69, '0.00', '0.00', 63, '0.00', '0.00', 8, 245, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(70, '0.00', '0.00', 63, '0.00', '0.00', 8, 246, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(71, '0.00', '0.00', 63, '0.00', '0.00', 8, 250, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(72, '0.00', '0.00', 63, '0.00', '0.00', 8, 247, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(73, '0.00', '0.00', 63, '0.00', '0.00', 8, 248, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(74, '0.00', '0.00', 63, '0.00', '0.00', 8, 249, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(75, '0.00', '0.00', 63, '0.00', '0.00', 9, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-21 20:02:04', 'RODADA', 30),
(76, '0.00', '0.00', 63, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-21 23:34:12', 'RODADA', 24),
(77, '0.00', '0.00', 63, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(78, '0.00', '0.00', 63, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(79, '0.00', '0.00', 63, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(80, '0.00', '0.00', 55, '0.00', '0.00', 8, 251, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(81, '0.00', '0.00', 55, '0.00', '0.00', 8, 254, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(82, '0.00', '0.00', 55, '0.00', '0.00', 8, 252, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(83, '0.00', '0.00', 55, '0.00', '0.00', 8, 253, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(84, '0.00', '0.00', 55, '0.00', '0.00', 8, 249, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(85, '0.00', '0.00', 55, '0.00', '0.00', 8, 250, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(86, '0.00', '0.00', 55, '0.00', '0.00', 8, 255, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(87, '0.00', '0.00', 55, '0.00', '0.00', 8, 256, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(88, '0.00', '0.00', 55, '0.00', '0.00', 8, 257, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(89, '0.00', '0.00', 55, '0.00', '0.00', 8, 258, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(90, '0.00', '0.00', 55, '0.00', '0.00', 5, 127, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(91, '0.00', '0.00', 55, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(92, '0.00', '0.00', 55, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(93, '0.00', '0.00', 63, '0.00', '0.00', 8, 251, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(94, '0.00', '0.00', 63, '0.00', '0.00', 8, 252, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(95, '0.00', '0.00', 63, '0.00', '0.00', 8, 253, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(96, '0.00', '0.00', 63, '0.00', '0.00', 8, 254, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(97, '0.00', '0.00', 19, '0.00', '0.00', 5, 128, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:20:31', 'JOGO', NULL),
(98, '0.00', '0.00', 48, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:20:41', 'JOGO', NULL),
(99, '0.00', '0.00', 51, '0.00', '0.00', 5, 129, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:20:41', 'JOGO', NULL),
(100, '0.00', '0.00', 19, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:12:26', 'RODADA', 6),
(101, '0.00', '0.00', 48, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:12:26', 'RODADA', 6),
(102, '0.00', '0.00', 51, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:12:26', 'RODADA', 6),
(103, '0.00', '0.00', 63, '0.00', '0.00', 8, 249, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:22:00', 'JOGO', NULL),
(104, '0.00', '0.00', 63, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-24 14:13:53', 'RODADA', 25),
(105, '0.00', '0.00', 63, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(106, '0.00', '0.00', 63, '0.00', '0.00', 5, 132, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(107, '0.00', '0.00', 63, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(108, '0.00', '0.00', 63, '0.00', '0.00', 8, 255, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(109, '0.00', '0.00', 63, '0.00', '0.00', 8, 256, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(110, '0.00', '0.00', 63, '0.00', '0.00', 8, 257, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(111, '0.00', '0.00', 63, '0.00', '0.00', 8, 258, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(112, '0.00', '0.00', 55, '0.00', '0.00', 8, 254, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-26 06:42:51', 'JOGO', NULL),
(113, '0.00', '0.00', 55, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-26 06:37:07', 'RODADA', 26),
(114, '0.00', '0.00', 55, '0.00', '0.00', 8, 256, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-27 05:43:53', 'JOGO', NULL),
(115, '0.00', '0.00', 3, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(116, '0.00', '0.00', 55, '0.00', '0.00', 5, 130, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(117, '0.00', '0.00', 55, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(118, '0.00', '0.00', 55, '0.00', '0.00', 5, 132, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(119, '0.00', '0.00', 55, '0.00', '0.00', 5, 133, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(120, '0.00', '0.00', 55, '0.00', '0.00', 5, 134, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(121, '0.00', '0.00', 55, '0.00', '0.00', 5, 135, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(122, '0.00', '0.00', 55, '0.00', '0.00', 5, 137, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(123, '0.00', '0.00', 3, '0.00', '0.00', 5, 131, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(124, '0.00', '0.00', 3, '0.00', '0.00', 5, 132, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(125, '0.00', '0.00', 55, '0.00', '0.00', 5, 136, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(126, '0.00', '0.00', 3, '0.00', '0.00', 8, 257, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(127, '0.00', '0.00', 55, '0.00', '0.00', 5, 138, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(128, '0.00', '0.00', 3, '0.00', '0.00', 8, 258, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(129, '0.00', '0.00', 55, '0.00', '0.00', 5, 140, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(130, '0.00', '0.00', 55, '0.00', '0.00', 5, 139, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(131, '0.00', '0.00', 55, '0.00', '0.00', 5, 141, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(132, '0.00', '0.00', 55, '0.00', '0.00', 5, 142, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(133, '0.00', '0.00', 63, '0.00', '0.00', 5, 133, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(134, '0.00', '0.00', 63, '0.00', '0.00', 5, 136, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(135, '0.00', '0.00', 63, '0.00', '0.00', 5, 137, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(136, '0.00', '0.00', 63, '0.00', '0.00', 5, 134, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(137, '0.00', '0.00', 63, '0.00', '0.00', 5, 135, '0.00', '0.00', '0.00', '0.00', 'JOGO', NULL, NULL, NULL),
(138, '0.00', '0.00', 55, '0.00', '0.00', 8, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:03:52', 'RODADA', 27),
(139, '0.00', '0.00', 27, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(140, '0.00', '0.00', 31, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(141, '0.00', '0.00', 28, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(142, '0.00', '0.00', 51, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(143, '0.00', '0.00', 63, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(144, '0.00', '0.00', 3, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '46.20', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(145, '0.00', '0.00', 55, '0.00', '0.00', 5, 0, '0.00', '0.00', '0.00', '0.00', 'CREDITO', '2017-01-28 20:05:09', 'RODADA', 7),
(146, '0.00', '0.00', 3, '0.00', '0.00', 5, 133, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(147, '0.00', '0.00', 3, '0.00', '0.00', 5, 134, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(148, '0.00', '0.00', 3, '0.00', '0.00', 5, 137, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(149, '0.00', '0.00', 3, '0.00', '0.00', 5, 136, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(150, '0.00', '0.00', 3, '0.00', '0.00', 5, 327, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(151, '0.00', '0.00', 3, '0.00', '0.00', 5, 328, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(152, '0.00', '0.00', 3, '0.00', '0.00', 5, 135, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(153, '0.00', '0.00', 3, '0.00', '0.00', 5, 326, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(154, '0.00', '0.00', 3, '0.00', '0.00', 11, 311, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(155, '0.00', '0.00', 3, '0.00', '0.00', 11, 312, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL),
(156, '0.00', '0.00', 3, '0.00', '0.00', 11, 313, '0.00', '0.00', '0.00', '46.20', 'JOGO', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `us_id` int(11) NOT NULL AUTO_INCREMENT,
  `us_username` varchar(155) NOT NULL,
  `us_password` varchar(45) NOT NULL,
  `us_cash` double(19,2) DEFAULT '0.00',
  `us_admin` bit(1) DEFAULT b'0',
  `us_team` int(11) DEFAULT NULL,
  `us_teamname` varchar(45) DEFAULT NULL,
  `us_palppublicos` bit(1) DEFAULT b'1',
  `us_puntpublica` bit(1) DEFAULT b'1',
  `us_logo` varchar(255) DEFAULT NULL,
  `us_idfacebook` varchar(45) DEFAULT NULL,
  `us_userbyfaceseted` bit(1) DEFAULT NULL,
  `us_nome` varchar(100) DEFAULT NULL,
  `us_sobenome` varchar(100) DEFAULT NULL,
  `us_cpf` varchar(12) DEFAULT NULL,
  `us_email` varchar(100) DEFAULT NULL,
  `us_telefone` varchar(40) DEFAULT NULL,
  `us_cep` varchar(8) DEFAULT NULL,
  `us_dia_niver` int(2) DEFAULT NULL,
  `us_mes_niver` int(2) DEFAULT NULL,
  `us_anio_niver` int(4) DEFAULT NULL,
  `us_linkreferencia` varchar(200) DEFAULT NULL,
  `us_codverificacion` varchar(100) DEFAULT NULL,
  `us_base` varchar(45) DEFAULT NULL,
  `us_idioma` varchar(10) NOT NULL DEFAULT 'pt',
  PRIMARY KEY (`us_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=69 ;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`us_id`, `us_username`, `us_password`, `us_cash`, `us_admin`, `us_team`, `us_teamname`, `us_palppublicos`, `us_puntpublica`, `us_logo`, `us_idfacebook`, `us_userbyfaceseted`, `us_nome`, `us_sobenome`, `us_cpf`, `us_email`, `us_telefone`, `us_cep`, `us_dia_niver`, `us_mes_niver`, `us_anio_niver`, `us_linkreferencia`, `us_codverificacion`, `us_base`, `us_idioma`) VALUES
(3, 'mdymen', '3345531', 46.20, b'1', 71, 'Santos', b'1', b'1', NULL, NULL, NULL, 'sa', NULL, '068.429.301-', 's@s.com', '26041722', NULL, 19, 2, 1982, NULL, NULL, 'wi061609_penca', 'es'),
(9, 'mdymen1', '3345531', 699.20, b'0', 70, 'Flamengo', b'1', b'1', NULL, NULL, NULL, 'Martin Dymenstein', NULL, '068.429.301-', 'msn@dymenstein.com', '26041722', NULL, 19, 1, 1982, NULL, NULL, NULL, 'pt'),
(10, 'mdymen2', '3345531', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(19, 'riquerubim', 'Rique123.', 0.00, b'1', 69, 'Palmeiras', b'1', b'1', NULL, NULL, NULL, 'Henrique Rubim Fernandes', NULL, '39807538831', 'henrique_rubim@hotmail.com', '19981524667', NULL, 31, 7, 1995, NULL, '', 'wi061609_penca', 'pt'),
(20, 'hyakka', 'hzkchupapinto', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Rian Ferreira', NULL, '34448280985', 'linnfity@gmail.com', '4898416516', NULL, 18, 12, 1996, NULL, '', NULL, 'pt'),
(21, 'Bira gomes', 'bira7777', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Ubiratan Gomes', NULL, '22718170808', 'biragomes777@gmail.com', '11959829889', NULL, 25, 2, 1982, NULL, '', NULL, 'pt'),
(22, 'Giovani', 'gico013013', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Giovani Pravato', NULL, '73233471904', 'gicovan@hotmail.com', '47 999329220', NULL, 14, 7, 1969, NULL, '571', NULL, 'pt'),
(23, 'genield@gmail.com', 'brasil2012', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Geniel da Silva Ferreira', NULL, '06351279380', 'genield@gmail.com', '88981536227', NULL, 18, 2, 1996, NULL, '', NULL, 'pt'),
(24, 'Vinicius 27', 'lanysilva', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Vinícius de Araújo farias', NULL, '06101762424', 'vd.farias@hotmt.com', '83 991859252', NULL, 20, 11, 1987, NULL, '24984', NULL, 'pt'),
(25, 'lucasps14@hotmail.co', 'esqueci1', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Lucas Pereira Silva ', NULL, '06023816657', 'lucasps14@hotmail.com', '9 99720312', NULL, 26, 3, 1990, NULL, '3076', NULL, 'pt'),
(26, 'tetos', 'tet', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(27, 'Kaio', 'pedrohenrique', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(28, 'henrique', 'Rique123.', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Henrique Rubim Fernandes', NULL, '398.075.388-', 'henrique_rubim@hotmail.com', '19981524667', NULL, 31, 7, 1995, NULL, NULL, NULL, 'pt'),
(29, 'Everton', 'teteebia', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(30, 'GraziRibeiro', 'graziele22', 0.00, b'0', 69, 'Palmeiras', b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(31, 'Nick Dias', 'nicollas', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(32, 'SCHMEIER ', '19schmeier83', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(33, 'matheusalves88@hotmail.com', '034869matheus', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(34, 'Isabele', 'Rique123.', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(35, 'Wagner', 'juba7290', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(36, 'Richard', '1974', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(37, 'Paulo Roberto ', '22545454pr', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(38, 'Alan Recife', 'jogafacil10', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(39, 'marcioliveira', '15300607', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(40, 'Mairon Carnellosso', 'maironfx5500', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(41, 'fernandescecel@gmail.com', 'golcelio', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(43, 'mdymenTesting', '3345531', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(44, 'Luis Henrique ', '070981', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(45, 'james dos santos', 'vidanova', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'james dos santos', NULL, '358.943.438.', 'jamesdossantos@gmail.com', '(13)33178436', NULL, 1, 7, 1988, NULL, NULL, NULL, 'pt'),
(46, 'Del cunha', 'tricolor2015', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(47, 'Ivan', 'careca28', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Ivanildo dos santos', NULL, '02069871371', 'ivanildorobsom@gmail.com', '(85)988901857', NULL, 1, 2, 1985, NULL, NULL, NULL, 'pt'),
(48, 'Rossales', '2825511a', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Sérgio Luís Rossales Viana ', NULL, '00484685090', 'sergiorossales@hotmail.com', '54996545790', NULL, 15, 1, 1985, NULL, NULL, NULL, 'pt'),
(49, 'Flaviodossantos026@gmail.com', 'flavio1995', 0.00, b'0', 69, 'Palmeiras', b'1', b'1', NULL, NULL, NULL, 'Flavio dos santos silva', NULL, '44472840812', 'flaviodossantos026@gmail.com', '13997679737', NULL, 26, 3, 1995, NULL, NULL, NULL, 'pt'),
(50, 'Júnior Farias ', 'celio123', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(51, 'giltastico', 'gilmar27', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Gilmar Ramos Teodoro ', NULL, '10316076767', 'giltastico@gmail.com', '27999226794', NULL, 15, 3, 1985, NULL, NULL, NULL, 'pt'),
(52, 'darley almeida', '201010', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'darlei almeida silva', NULL, '11383814627', 'darley.bretas@gmail.com', '31988248743', NULL, 12, 5, 1991, NULL, NULL, NULL, 'pt'),
(53, 'tirsogonzalez', '653872aw', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Tirso Fernandes Gonzalez', NULL, '08514547712', 'tirsogonzalez32@gmail.com', '(21)970420192', NULL, 17, 6, 1981, NULL, NULL, NULL, 'pt'),
(54, 'jeferson', 'penelope2016', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(55, 'jfdf12', 'jfdf1234', 0.00, b'1', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Fernando Da Silva', NULL, '06830142910', 'jfdf2011@hotmail.com', '', NULL, 13, 4, 1985, NULL, NULL, NULL, 'es'),
(56, 'Alex', '123456789', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(57, 'Leomaxbezerradecarvalhodasilva@gmail.com', '19057611', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Leomaxbezerradecarvalhodasilva', NULL, '76131688320', 'antileomax@gmail.com', '21969954618', NULL, 19, 5, 1976, NULL, NULL, NULL, 'pt'),
(58, 'Everpao', 'anabia', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Everton o de moura', NULL, '01932190503', 'evertonoliveirademoura@hotmail.com', '7598293658', NULL, 28, 3, 1984, NULL, NULL, NULL, 'pt'),
(59, 'Carlinho Lima ', '21006690', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Antonio carlos pinheiro lima ', NULL, '04448139344', 'antoniocarloslima26@gmail.com', '98981792779', NULL, 27, 10, 1991, NULL, NULL, NULL, 'pt'),
(60, 'Alexander dá Silva ', '192009vilma', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(61, 'Saulofortevermelho88', 'baku2018qatar2022', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(62, 'RenatoCP', 'dadinho86', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Renato Cavalcante Feldmann', NULL, '36188806852', 'renato.cavalcante09@yahoo.com.br', '11972041221', NULL, 8, 7, 1986, NULL, NULL, NULL, 'pt'),
(63, 'diegoshow86@hotmail.com', '161286', 0.00, b'0', 71, 'Santos', b'1', b'1', NULL, NULL, NULL, 'Diego Novaes Violin', NULL, '36573967801', 'diegoshow86@hotmail.com', '11975696156', NULL, 16, 12, 1986, NULL, NULL, NULL, 'pt'),
(64, 'Felipe', '1234578', 0.00, b'0', 72, 'Corinthians', b'1', b'1', NULL, NULL, NULL, 'Felipe Ferreira da Silva', NULL, '46074027889', 'Felipe.facebook1533@hotmail.cpm', '25477821', NULL, 9, 3, 1995, NULL, NULL, NULL, 'pt'),
(65, 'Junior', 'estela123', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt'),
(66, 'Renatooliveira73', 'renato1976', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Renato batista de Oliveira', NULL, '86317075620', 'garapa.77@hotmail.com', '34997680992', NULL, 5, 4, 1976, NULL, NULL, NULL, 'pt'),
(67, 'alex.luiz68', 'fusca68', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, 'Alex Sandro Luiz de Araújo', NULL, '21269488899', 'alex.luiz68@yahoo.com.br', '1126770264', NULL, 27, 2, 1980, NULL, NULL, NULL, 'pt'),
(68, 'Andresel87@hotmail. Com', 'mayonesa2000', 0.00, b'0', NULL, NULL, b'1', b'1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pt');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_penca`
--

CREATE TABLE IF NOT EXISTS `user_penca` (
  `up_id` int(11) NOT NULL AUTO_INCREMENT,
  `up_idpenca` int(11) NOT NULL,
  `up_iduser` int(11) NOT NULL,
  `up_puntagem` int(11) DEFAULT NULL,
  PRIMARY KEY (`up_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vwmatchsresult`
--
CREATE TABLE IF NOT EXISTS `vwmatchsresult` (
`mt_id` int(11)
,`mt_idteam1` int(11)
,`mt_idteam2` int(11)
,`mt_date` timestamp
,`mt_goal1` int(2)
,`mt_goal2` int(2)
,`mt_idchampionship` int(11)
,`mt_played` tinyint(1)
,`mt_idround` int(11)
,`mt_acumulado` decimal(19,2)
,`rs_id` int(11)
,`rs_idmatch` int(11)
,`rs_res1` int(11)
,`rs_res2` int(11)
,`rs_date` datetime
,`rs_idpenca` int(11)
,`rs_iduser` int(11)
,`rs_round` int(3)
,`rs_result` varchar(100)
,`rs_points` int(3)
,`tm1_id` int(11)
,`t1nome` varchar(100)
,`tm1_idchampionship` int(11)
,`tm1_points` int(11)
,`tm1_played` int(3)
,`tm1_logo` varchar(255)
,`tm2_id` int(11)
,`t2nome` varchar(100)
,`tm2_idchampionship` int(11)
,`tm2_points` int(11)
,`tm2_played` int(3)
,`tm2_logo` varchar(255)
,`ch_id` int(11)
,`ch_nome` varchar(100)
,`ch_idfixture` int(11)
,`ch_started` tinyint(1)
,`ch_atualround` int(4)
,`ch_sec1_ini` int(2)
,`ch_sec1_fin` int(2)
,`ch_sec2_ini` int(2)
,`ch_sec2_fin` int(2)
,`ch_sec3_ini` int(2)
,`ch_sec3_fin` int(2)
,`ch_sec1_desc` varchar(100)
,`ch_sec2_desc` varchar(100)
,`ch_sec3_desc` varchar(100)
,`rd_id` int(11)
,`rd_round` varchar(40)
,`rd_idchampionship` int(11)
,`rd_acumulado` decimal(19,2)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vwmatchsteams`
--
CREATE TABLE IF NOT EXISTS `vwmatchsteams` (
`mt_id` int(11)
,`mt_idteam1` int(11)
,`mt_idteam2` int(11)
,`mt_date` timestamp
,`mt_goal1` int(2)
,`mt_goal2` int(2)
,`mt_idchampionship` int(11)
,`mt_played` tinyint(1)
,`mt_acumulado` decimal(19,2)
,`mt_idround` int(11)
,`tm1_id` int(11)
,`t1nome` varchar(100)
,`tm1_idchampionship` int(11)
,`tm1_points` int(11)
,`tm1_played` int(3)
,`tm1_logo` varchar(255)
,`tm2_id` int(11)
,`t2nome` varchar(100)
,`tm2_idchampionship` int(11)
,`tm2_points` int(11)
,`tm2_played` int(3)
,`tm2_logo` varchar(255)
,`rd_id` int(11)
,`rd_round` varchar(40)
,`rd_idchampionship` int(11)
,`rd_acumulado` decimal(19,2)
,`cantidad` bigint(21)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vwpalpites`
--
CREATE TABLE IF NOT EXISTS `vwpalpites` (
`cantidad` bigint(21)
,`rs_idmatch` int(11)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vwranking_championship`
--
CREATE TABLE IF NOT EXISTS `vwranking_championship` (
`points` decimal(32,0)
,`us_username` varchar(155)
,`us_id` int(11)
,`mt_idchampionship` int(11)
,`ch_acumulado` decimal(19,2)
,`ch_nome` varchar(100)
);
-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vwranking_round`
--
CREATE TABLE IF NOT EXISTS `vwranking_round` (
`points` decimal(32,0)
,`us_username` varchar(155)
,`us_id` int(11)
,`mt_idround` int(11)
,`mt_idchampionship` int(11)
,`rd_acumulado` decimal(19,2)
,`rd_id` int(11)
,`rd_round` varchar(40)
);
-- --------------------------------------------------------

--
-- Estructura para la vista `ranking`
--
DROP TABLE IF EXISTS `ranking`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wi061609`@`%` SQL SECURITY DEFINER VIEW `ranking` AS select sum(`r`.`rs_points`) AS `points`,`r`.`rs_iduser` AS `rk_iduser`,`u`.`us_username` AS `rk_username`,`m`.`mt_idchampionship` AS `rk_idchamp` from ((`user` `u` join `result` `r` on((`u`.`us_id` = `r`.`rs_iduser`))) join `match` `m` on((`m`.`mt_id` = `r`.`rs_idmatch`))) group by `r`.`rs_iduser` order by sum(`r`.`rs_points`) desc;

-- --------------------------------------------------------

--
-- Estructura para la vista `vwmatchsresult`
--
DROP TABLE IF EXISTS `vwmatchsresult`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wi061609`@`%` SQL SECURITY DEFINER VIEW `vwmatchsresult` AS select distinct `m`.`mt_id` AS `mt_id`,`m`.`mt_idteam1` AS `mt_idteam1`,`m`.`mt_idteam2` AS `mt_idteam2`,`m`.`mt_date` AS `mt_date`,`m`.`mt_goal1` AS `mt_goal1`,`m`.`mt_goal2` AS `mt_goal2`,`m`.`mt_idchampionship` AS `mt_idchampionship`,`m`.`mt_played` AS `mt_played`,`m`.`mt_idround` AS `mt_idround`,`m`.`mt_acumulado` AS `mt_acumulado`,`r`.`rs_id` AS `rs_id`,`r`.`rs_idmatch` AS `rs_idmatch`,`r`.`rs_res1` AS `rs_res1`,`r`.`rs_res2` AS `rs_res2`,`r`.`rs_date` AS `rs_date`,`r`.`rs_idpenca` AS `rs_idpenca`,`r`.`rs_iduser` AS `rs_iduser`,`r`.`rs_round` AS `rs_round`,`r`.`rs_result` AS `rs_result`,`r`.`rs_points` AS `rs_points`,`t1`.`tm_id` AS `tm1_id`,`t1`.`tm_name` AS `t1nome`,`t1`.`tm_idchampionship` AS `tm1_idchampionship`,`t1`.`tm_points` AS `tm1_points`,`t1`.`tm_played` AS `tm1_played`,`t1`.`tm_logo` AS `tm1_logo`,`t2`.`tm_id` AS `tm2_id`,`t2`.`tm_name` AS `t2nome`,`t2`.`tm_idchampionship` AS `tm2_idchampionship`,`t2`.`tm_points` AS `tm2_points`,`t2`.`tm_played` AS `tm2_played`,`t2`.`tm_logo` AS `tm2_logo`,`c`.`ch_id` AS `ch_id`,`c`.`ch_nome` AS `ch_nome`,`c`.`ch_idfixture` AS `ch_idfixture`,`c`.`ch_started` AS `ch_started`,`c`.`ch_atualround` AS `ch_atualround`,`c`.`ch_sec1_ini` AS `ch_sec1_ini`,`c`.`ch_sec1_fin` AS `ch_sec1_fin`,`c`.`ch_sec2_ini` AS `ch_sec2_ini`,`c`.`ch_sec2_fin` AS `ch_sec2_fin`,`c`.`ch_sec3_ini` AS `ch_sec3_ini`,`c`.`ch_sec3_fin` AS `ch_sec3_fin`,`c`.`ch_sec1_desc` AS `ch_sec1_desc`,`c`.`ch_sec2_desc` AS `ch_sec2_desc`,`c`.`ch_sec3_desc` AS `ch_sec3_desc`,`round`.`rd_id` AS `rd_id`,`round`.`rd_round` AS `rd_round`,`round`.`rd_idchampionship` AS `rd_idchampionship`,`round`.`rd_acumulado` AS `rd_acumulado` from (((((`match` `m` left join `result` `r` on((`m`.`mt_id` = `r`.`rs_idmatch`))) join `team` `t1` on((`t1`.`tm_id` = `m`.`mt_idteam1`))) join `team` `t2` on((`t2`.`tm_id` = `m`.`mt_idteam2`))) join `championship` `c` on((`c`.`ch_id` = `m`.`mt_idchampionship`))) join `round` on((`round`.`rd_id` = `m`.`mt_idround`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `vwmatchsteams`
--
DROP TABLE IF EXISTS `vwmatchsteams`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wi061609`@`%` SQL SECURITY DEFINER VIEW `vwmatchsteams` AS select distinct `m`.`mt_id` AS `mt_id`,`m`.`mt_idteam1` AS `mt_idteam1`,`m`.`mt_idteam2` AS `mt_idteam2`,`m`.`mt_date` AS `mt_date`,`m`.`mt_goal1` AS `mt_goal1`,`m`.`mt_goal2` AS `mt_goal2`,`m`.`mt_idchampionship` AS `mt_idchampionship`,`m`.`mt_played` AS `mt_played`,`m`.`mt_acumulado` AS `mt_acumulado`,`m`.`mt_idround` AS `mt_idround`,`t1`.`tm_id` AS `tm1_id`,`t1`.`tm_name` AS `t1nome`,`t1`.`tm_idchampionship` AS `tm1_idchampionship`,`t1`.`tm_points` AS `tm1_points`,`t1`.`tm_played` AS `tm1_played`,`t1`.`tm_logo` AS `tm1_logo`,`t2`.`tm_id` AS `tm2_id`,`t2`.`tm_name` AS `t2nome`,`t2`.`tm_idchampionship` AS `tm2_idchampionship`,`t2`.`tm_points` AS `tm2_points`,`t2`.`tm_played` AS `tm2_played`,`t2`.`tm_logo` AS `tm2_logo`,`r`.`rd_id` AS `rd_id`,`r`.`rd_round` AS `rd_round`,`r`.`rd_idchampionship` AS `rd_idchampionship`,`r`.`rd_acumulado` AS `rd_acumulado`,`p`.`cantidad` AS `cantidad` from ((((`match` `m` join `team` `t1` on((`t1`.`tm_id` = `m`.`mt_idteam1`))) join `team` `t2` on((`t2`.`tm_id` = `m`.`mt_idteam2`))) join `round` `r` on((`m`.`mt_idround` = `r`.`rd_id`))) join `vwpalpites` `p` on((`p`.`rs_idmatch` = `m`.`mt_id`)));

-- --------------------------------------------------------

--
-- Estructura para la vista `vwpalpites`
--
DROP TABLE IF EXISTS `vwpalpites`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wi061609`@`%` SQL SECURITY DEFINER VIEW `vwpalpites` AS select count(`result`.`rs_id`) AS `cantidad`,`result`.`rs_idmatch` AS `rs_idmatch` from `result` group by `result`.`rs_idmatch`;

-- --------------------------------------------------------

--
-- Estructura para la vista `vwranking_championship`
--
DROP TABLE IF EXISTS `vwranking_championship`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wi061609`@`%` SQL SECURITY DEFINER VIEW `vwranking_championship` AS select sum(`result`.`rs_points`) AS `points`,`user`.`us_username` AS `us_username`,`user`.`us_id` AS `us_id`,`m`.`mt_idchampionship` AS `mt_idchampionship`,`championship`.`ch_acumulado` AS `ch_acumulado`,`championship`.`ch_nome` AS `ch_nome` from (((`user` join `result` on((`user`.`us_id` = `result`.`rs_iduser`))) join `match` `m` on((`m`.`mt_id` = `result`.`rs_idmatch`))) join `championship` on((`championship`.`ch_id` = `m`.`mt_idchampionship`))) group by `m`.`mt_idchampionship`,`user`.`us_username`,`user`.`us_id` order by sum(`result`.`rs_points`) desc;

-- --------------------------------------------------------

--
-- Estructura para la vista `vwranking_round`
--
DROP TABLE IF EXISTS `vwranking_round`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wi061609`@`%` SQL SECURITY DEFINER VIEW `vwranking_round` AS select sum(`result`.`rs_points`) AS `points`,`user`.`us_username` AS `us_username`,`user`.`us_id` AS `us_id`,`m`.`mt_idround` AS `mt_idround`,`m`.`mt_idchampionship` AS `mt_idchampionship`,`round`.`rd_acumulado` AS `rd_acumulado`,`round`.`rd_id` AS `rd_id`,`round`.`rd_round` AS `rd_round` from (((`user` join `result` on((`user`.`us_id` = `result`.`rs_iduser`))) join `match` `m` on((`m`.`mt_id` = `result`.`rs_idmatch`))) join `round` on((`round`.`rd_id` = `m`.`mt_idround`))) group by `m`.`mt_idround`,`m`.`mt_idchampionship`,`user`.`us_username`,`user`.`us_id`,`round`.`rd_round` order by sum(`result`.`rs_points`) desc;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
