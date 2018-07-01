<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of usuario
 *
 * @author Martin Dymenstein
 */
class Application_Model_Users extends Application_Model_Bd_Adapter
{

    protected $_name = 'user';
    
    public function users() {
        $db = $this->db;
        
        return $db->select()->from("user")->query()->fetchAll();
        
    }
    
    
    
    public function userspalpitaron() {
        $db = $this->db;
        
        return $db->select()->from("user")->distinct()
                ->joinInner("result", "result.rs_iduser = user.us_id")
                ->joinInner("match", "result.rs_idmatch = match.mt_id")
                ->joinInner("championship", "championship.ch_id = match.mt_idchampionship")
                ->joinInner("penca", "penca.pn_id = rs_idpenca")
                ->where("user.us_email <> ''")
                ->query()->fetchAll();
        
    }
    
    public function cambiar_idioma($i, $id) {
        $db = $this->db;
        
        $db->update("user",array("us_idioma" => $i), "us_id = ".$id);
    }
    
    
    public function save_provisorio($params) {
        $db = $this->db;
        
        $info = array(
            'prov_username'=>$params['username'],
            'prov_password' => $params['password'],
        );  
        
        $db->insert("provisorio", $info);
        return $db->lastInsertId();
    }
    
    public function cancomplete($id, $pass) { 
        $db = $this->db;
        
        $return = $db->select()->from("provisorio")
                ->where("prov_id = ?", $id)
                ->where("prov_password = ?", $pass);
        
        print_r($return->__toString());
        die(".");
        
//                ->query()
//                ->fetch();
//        
        return $return;
    }
    
    public function save_user($data) {
        $db = $this->db;
        $db->insert("user", $data);
        return $db->lastInsertId();
    }
    
    public function update_codeverification($user, $code) {
        $db = $this->db;
        $db->update("user", array('us_codverificacion' => $code),'us_username = '.$user);
    }
    
    public function load_userbyid($user) {
        $db = $this->db;
        
        $result =  $db->select()->from("user")
                ->where("us_id = ?", $user)
                ->query()
                ->fetch();
        
        return $result;
    }    
    
    public function update_grito($user_id, $grito) {
        $db = $this->db;
        
        $db->update("user", array('us_grito' => $grito),'us_id = '.$user_id);
    }    
    
    public function update_senha($user_id, $senha) {
        $db = $this->db;
        
        $db->update("user", array('us_password' => $senha),'us_id = '.$user_id);
        $db->update("resetarsenha", array('reset_ativa' => 0),'reset_iduser = '.$user_id);
    }
    
    public function verificar_trocasenha($token) {
        $db = $this->db;
        
        $result = $db->select()->from("resetarsenha")
                ->where("reset_token = ?", $token)
                ->where("reset_ativo = ?", 1);
        
        return $result->query();
    }
    
    public function update_cash($user, $cash) {
        $db = $this->db;
        
//        print_r($user);
//        print_r("-".$cash);
//        die("...");
        
        $db->update("user", array('us_cash' => $cash),'us_id = '.$user);
    }
    
    public function update_user($dados, $us_id) {
        $db = $this->db;
        
        $db->update("user", $dados, "us_id = ".$us_id);
    }
    
    public function load_user($user) {
        $db = $this->db;
        
        $result =  $db->select()->from("user")
                ->where("us_username = ?", $user)
                ->query()
                ->fetch();
        
        return $result;
    }
    
    public function salvar_token($usuario, $token) {
        $db = $this->db;
        
        $db->update("user", array("us_token" => $token), "us_id = ".$usuario);
    }
    
    public function load_user_by_token($usuario, $token) {
        $db = $this->db;
        
        return $db->select()->from("user")
                ->where("us_id = ?", $usuario)
                ->where("us_token = ?", $token)
                ->query()
                ->fetch();
        
        
    }
    
    public function adicionesgrana($id_user, $valor) {
        $db = $this->db;
        
        $db->insert("adicionesgrana", array('ag_iduser' => $id_user, 'ag_date' => date("Y-m-d H:i:s"), 
            'ag_valor' => $valor));
    }
    
    public function user_penca($penca) {
        $db = $this->db;
        
        $result = $db->select()->from('user_penca')
                ->joinInner('user', 'user_penca.up_iduser = user.us_id')
                ->where('user_penca.up_idpenca = ?', $penca)
                ->query()
                ->fetchAll();
        
        return $result;
    }
        
    public function setTeamCoracao($id, $name, $us_id) {
        $db = $this->db;
        
        $db->update("user", array("us_team" => $id, "us_teamname" => $name), "us_id = ".$us_id);
    }
    
    public function setNovaSenha($senha, $us_id) {
        $db = $this->db;
        $db->update("user", array("us_password" => $senha), "us_id = ".$us_id);
    }
    
    public function getWonMatches($id_user) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("count(*) as wonmatches"))
               ->where("rs_iduser = ?",$id_user)
               ->where("rs_date < ".date("m-d-Y"))
               ->where("rs_points = 1")
                ->orWhere("rs_points = 5")
                ->query()
                ->fetch();
        
        return $result['wonmatches'];
    }    
    
    public function getLostMatches($us_id) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("count(*) as losts"))
               ->where("rs_iduser = ?",$us_id)
                ->where("rs_date < ".date("m-d-Y"))
               ->where("rs_points = 0")
                ->query()
                ->fetch();
        
        return $result['losts'];        
    }
    
    public function getMatchesLostMatches($us_id) {
        $db = $this->db;
        
        $result = $db->select()->from("vwmatchsresult")
                ->where("rs_iduser = ?",$us_id)
                ->where("rs_points = 0")
                ->where("rs_result = 0")
                ->query()
                ->fetchAll();
        
        return $result;  

    }
    
    public function getMatchesWonMatches($us_id) {
        $db = $this->db;
        
        $query = $db->select()->from("match")
                ->joinInner(array('t1' => 'team'), 'match.mt_idteam1 = t1.tm_id', array('t1nome' => 't1.tm_name', 'tm1_logo' => 't1.tm_logo', 'tm1_id' => 't1.tm_id'))
                ->joinInner(array('t2' => 'team'), 'match.mt_idteam2 = t2.tm_id', array('t2nome' => 't2.tm_name', 'tm2_logo' => 't2.tm_logo', 'tm2_id' => 't2.tm_id'))
                ->joinInner("result", "match.mt_id = result.rs_idmatch")
                ->joinInner("championship","championship.ch_id = match.mt_idchampionship")
                ->where("result.rs_iduser = ?", + $us_id)
                ->where("rs_points = 5 or rs_points = 1");
        
//        print_r($query->__toString());
//                ->orWhere("rs_points = 1")
          $result = $query->query()
                ->fetchAll();
        
        return $result;  

    }
    
    public function getPlayedMatches($id_user) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("count(*) as played"))
               ->where("rs_iduser = ?",$id_user)
                ->query()
                ->fetch();
        
        return $result['played'];        
    }
    
    public function getPoints($id_user) {
        $db = $this->db;
        
        $result = $db->select()->from("result", array("sum(rs_points) as points"))
                ->where("rs_iduser = ?",$id_user)
                ->where("rs_date < " + date("m-d-Y"))
                ->query()
                ->fetch();
        
        return $result['points'];
    }
    
    public function posicao_por_campeonato($id_user) {
//        $db = Zend_Db_Table::getDefaultAdapter();
//        
//        $db->select()->
    }
    
    public function getPoisition($id_user) {
        $db = $this->db;
        
        $index = "@us_".$id_user;
        
        $db->query("SET ".$index."=0");
       
        $sql = "select ".$index.":=".$index." + 1 as position, sum(rs_points) as points, rs_iduser"
                . " from result where rs_iduser = ".$id_user." group by rs_iduser"
                . " order by rs_points DESC";

//        $result = $db->select()->from("result",array($index.":=".$index." + 1 as position", 
//            "sum(rs_points) as points","rs_iduser"))
//                ->where("rs_iduser = ?",$id_user)
//                ->order("rs_points DESC")
//                ->group("rs_iduser")
//                ->query()
//                ->fetch();
        
        $result = $db->query($sql)->fetch();
        
        return $result['position'];
    }
    
    public function save_opcoes($id_user, $array_opcoes) {
        $db = $this->db;
        
        
        //$db->update("user", array('us_palppublicos' => 0, 'us_puntpublica' => 0), "us_id = ".$id_user);
    }
    
    public function historico_palpites($us) {
        $db = $this->db;
        
        $result = $db->select()->from("match")  
            ->joinInner("round", "match.mt_idround = round.rd_id")
            ->joinInner("result", "result.rs_idmatch = match.mt_id")    
            ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' => 't1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
            ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' => 't2.tm_id', 'tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))
            ->joinInner("championship", "championship.ch_id = match.mt_idchampionship")
            ->where('result.rs_iduser = ?', $us)
            ->order('mt_date')
            ->query()
            ->fetchAll();
        
        return $result;
    }
    
    /**
     * Retorna un usuario registrado pelo facebook id
     * @param facebookid
     */
    public function isUserRegistered($facebookid) {
        $db = $this->db;
        
        $result = $db->select()->from("user")
                 ->where("us_idfacebook = ?", $facebookid)
                ->query()
                ->fetch();
        return $result;
    }
    
    public function facebookUserSave($id) {
        $db = $this->db;
        
        $datas = array("us_idfacebook" => $id, "us_userbyfaceseted" => false, "us_password" => $id, "us_username" => $id);
        
        $db->insert("user", $datas);
        
    }
    
    public function isCpfUsed($cpf) {
        $db = $this->db;       
        
        $result = $db->select()->from("user")
                ->where("us_cpf = ?",$cpf)
        
//        print_r($result->__toString());
//        die(".");
            ->query()
                ->fetchAll();
        
        return $result;        
    }
    
    public function isEmailUsed($email) {
        $db = $this->db;       
        
        $result = $db->select()->from("user")
                ->where("us_email = ?",$email)
        
//        print_r($result->__toString());
//        die(".");
            ->query()
                ->fetchAll();
        
        return $result;  
    }    
    
    public function adicionarPorLinkReferencia($creador, $uso) {
        $db = $this->db;
        
        $dados = array('lr_idcreador' => $creador, 'lr_iduso' => $uso);
        
        $db->update("linkedreferencia", $dados);
    }
    
    public function isUsersName($user) {
        $db = $this->db;       
        
        $result = $db->select()->from("user")
                ->where("us_username = ?",$user)
        
//        print_r($result->__toString());
//        die(".");
            ->query()
                ->fetch();
        
        return $result;
    }
    
    
    public function existUserName($user) {
        $db = $this->db;       
        
        $result = $db->select()->from("user")
                ->where("us_username = ?",$user)
        
//        print_r($result->__toString());
//        die(".");
            ->query()
                ->fetch();
        
        return !empty($result);
    }
    
    public function getEmailsUsuario() {
        $db = $this->db;
        
        $result = $db->select()->from("user", array('us_email'))
                ->where("us_email <> ''")
                ->query()->fetchAll();
        
        return $result;
    }
    
    public function save_esqueceu($user_id, $email, $token) {
        $db = $this->db;
        
        $db->insert("resetarsenha", array('reset_iduser' => $user_id,
            'reset_email' => $email, 
            'reset_token' => $token, 
            'reset_data' => date("Y-m-d H:i:s"),
            'reset_ativa' => 1));
    }
    
    /**
     * Retorna un usuario pelo email
     * @param email
     */
    public function load_userbyemail($email) {
        $db = $this->db;
        
        $return = $db->select()->from("user")->where("us_email = ?", $email);
        
        return $return->query()->fetch();
    }
    
    public function registerUsernameFacebook($username, $id) {
        $db = $this->db;
        
        $dados = array("us_username" => $username, "us_userbyfaceseted" => 1);
        
        $db->update("user", $dados, "us_id = ".$id );
    }
    
    public function user_bycod($cod) {
        $db = $this->db;
        
        $result = $db->select()->from("user")
                ->where("us_codverificacion = ?", $cod)
                ->query()
                ->fetch();
        
        return $result;
    }
    
    public function confirmaremail($data) {
        $db = $this->db;
        
        $db->update("user", array("us_emailconfirmado" => true, 'us_codverificacion' => ""), "us_id = ".$data);
    }
    
    public function getDinheiro($user_id) {
        $db = $this->db;
        
        $result = $db->select()->from("user",array('us_cash'))
                ->where("us_id = ?", $user_id);
                
        $return = $result->query()->fetch();
        
        return $return;
    } 
    
    public function trocar_base($nome, $id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        
        $db->update("user", array("us_base" => $nome), "us_id = ".$id);
    }
    
    public function addprovisorio($us, $pass) { 
        $db = $this->db;
        
        $db->insert("provisorio",array('prov_username' => $us, 'prov_password' => $pass));
    }
    
    public function login($us, $pass) {
        $db = $this->db;
        
        $result = $db->select()->from("user")
                ->where("us_username = ?", $us)
				->where("us_password = ?", $pass);
				
		$return = $result->query()->fetch();		
		
		return $return;
    }
	
	public function add_pagseguro_ini($id_user, $id_transacion, $email, $plano) {
		$db = $this->db;
		
		$db->insert("pagseguro",array("pg_iduser" => $id_user, 
			"pg_idtransacao" => $id_transacion,
			"pg_email" => $email,
			"pg_plano" => $plano,
			"pg_datahora" => date("Y-m-d H:i:s")));
	}
	
	public function getPagSeguro($code) {
		$db = $this->db;
		
		$query = $db->select()->from("pagseguro")->where("pg_code = ?", $code);
		$result = $query->query()->fetch();
		
		return $result;
	}
	
	public function getUserByEmail($email) {
		$db = $this->db;
		
		$query = $db->select()->from("user")->where("us_email = ?", $email);
		
		$result = $query->query()->fetch();
		
		return $result;
	}
	
	public function getPagSeguroByEmail($email, $plano) {
		$db = $this->db;
		
		$query = $db->select()->from("pagseguro")
		->where("pg_email = ?", $email)
		->where("pg_transactionstatus < 3")
		->where("pg_plano = ?", $plano)
		->order("pg_datahora DESC");
		
		$result = $query->query()->fetch();
		
	}
        
        
     public function criar_bolao($iduser, $nome, $valor, $privado, $idchamp, $primer, $segundo, $tercer) {
         $db = $this->db;
         
         $db->insert("penca",array("pn_name" => $nome, 
			"pn_value" => $valor,
			"pn_iduser" => $iduser,
			"pn_idchampionship" => $idchamp,
			"pn_justfriends" => $privado,
                        "pn_primerlugar" => $primer,
                        "pn_segundolugar" => $segundo,
                        "pn_tercerlugar" => $tercer));
         
         return $db->lastInsertId();
     }
     
     public function getPencasDisponiveisCampeonato($iduser, $campeonato) {
                 $db = $this->db;
        
        $sql = "SELECT * FROM penca p"
                . " INNER JOIN championship c ON p.pn_idchampionship = c.ch_id"
                . " LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id "
                . " WHERE p.pn_ativo = 1 AND (p.pn_justfriends = 0 OR p.pn_justfriends is null) AND p.pn_id NOT IN ("
                . " SELECT DISTINCT p.pn_id FROM penca p"
                . " INNER JOIN championship c ON p.pn_idchampionship = c.ch_id"
                . " INNER JOIN user_penca up ON up.up_idpenca = p.pn_id "
                . " LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id "
                . " WHERE up.up_iduser = ".$iduser." AND c.ch_id = ".$campeonato."" 
                . ")";
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
        /*
                   SELECT DISTINCT p.pn_id FROM penca p
               INNER JOIN championship c ON p.pn_idchampionship = c.ch_id
                INNER JOIN user_penca up ON up.up_idpenca = p.pn_id 
                LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id 
                WHERE up.up_iduser =
                                "
                                    . "*/
     }     
        
     public function getPencasDisponiveis($iduser) {
                 $db = $this->db;
        
        $sql = "SELECT * FROM penca p"
                . " INNER JOIN championship c ON p.pn_idchampionship = c.ch_id"
                . " LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id "
                . " WHERE p.pn_ativo = 1 AND (p.pn_justfriends = 0 OR p.pn_justfriends is null) AND p.pn_id NOT IN ("
                . " SELECT DISTINCT p.pn_id FROM penca p"
                . " INNER JOIN championship c ON p.pn_idchampionship = c.ch_id"
                . " INNER JOIN user_penca up ON up.up_idpenca = p.pn_id "
                . " LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id "
                . " WHERE up.up_iduser = ".$iduser.""
                . ")";
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
        /*
                   SELECT DISTINCT p.pn_id FROM penca p
               INNER JOIN championship c ON p.pn_idchampionship = c.ch_id
                INNER JOIN user_penca up ON up.up_idpenca = p.pn_id 
                LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id 
                WHERE up.up_iduser =
                                "
                                    . "*/
     }
     
     public function getBoloes($userid) {
        $db = $this->db;
        
        $sql = "SELECT DISTINCT p.*, c.*, pa.* FROM penca p"
                . " INNER JOIN championship c ON p.pn_idchampionship = c.ch_id"
                . " INNER JOIN user_penca up ON up.up_idpenca = p.pn_id "
                . " LEFT JOIN participantespenca pa ON pa.up_idpenca = p.pn_id "
                . " WHERE up.up_iduser = ".$userid;
        
        //print_r($sql);
        
//        $query = $db->select()->from("penca")
//                ->joinInner("championship", "championship.ch_id = penca.pn_idchampionship")
//                ->joinInner("user_penca", "user_penca.up_idpenca = penca.pn_id", array("count"))
//                ->where("pn_iduser = ?", $userid);
        
        $result = $db->query($sql)->fetchAll();
        
        return $result;
    }
	
	public function update_pagseguro($email, $id_notificationcode, $id_transactionstatus, $plano, $code, $pg_foipago) {
		$db = $this->db;

	//	$query = $db->select()->from("pagseguro")
		//	->where("pg_email = ?", $email)
			//->where("pg_transactionstatus <> 3")
			//->order("pg_datahora DESC");
			
		$sql = "SELECT * FROM pagseguro WHERE pg_email = '".$email."' 
			AND pg_transactionstatus <> '3' ORDER BY pg_datahora DESC ";
			
			
		$result = $db->query($sql)->fetch();
		
		//$pago = $result[0];
		
		//print_r($result);
		//die("-");
		
		if (!empty($result)) {
			$db->update("pagseguro",array("pg_notificactioncode" => $id_notificationcode, 
				"pg_transactionstatus" => $id_transactionstatus,
				"pg_datahora" => date("Y-m-d H:i:s"),
				"pg_code" => $code,
				"pg_foipago" => $pg_foipago), "pg_id = '".$result['pg_id']."'");
		}
	}
        
        public function getPalpitesUsuario($user) {
            $db = $this->db;
            
            $sql = "SELECT (SELECT sum(rs_points) FROM result where rs_iduser = ".$user.") as pontos,
            (SELECT count(*) FROM result inner join wi490700_penca.match m ON rs_idmatch = m.mt_id where rs_iduser = ".$user." and mt_played = 1) as palpitados,
            (SELECT count(*) FROM result  where rs_iduser = ".$user." and rs_points <> 0) as acertos,
            (SELECT COUNT( * ) 
            FROM result
            INNER JOIN wi490700_penca.match m ON rs_idmatch = m.mt_id
            WHERE rs_iduser = ".$user."
            AND mt_played =1
            AND rs_points =0) as erros";
            
            $result = $db->query($sql)->fetch();
            
            return $result;
        }
    
        /**
         * Obtiene todos los usuarios que ya palpitaron un partido determinado
         * @param idmatch
         */
        public function getUsuariosPalpitaronJogo($idmatch) {
            $db = $this->db;
            
            $sql = $db->select()->from("match")
                    ->joinInner("result", "result.rs_idmatch = match.mt_id")
                    ->joinInner("user", "user.us_id = result.rs_iduser")
                    ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('tm1_id' => 't1.tm_id', 'tm1_logo' => 't1.tm_logo', 't1nome' => 't1.tm_name'))
                    ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('tm2_id' => 't2.tm_id', 'tm2_logo' => 't2.tm_logo', 't2nome' => 't2.tm_name'))
                    ->joinLeft("penca", "penca.pn_id = result.rs_idpenca")
                    ->where("match.mt_id = ?", $idmatch)
                    ->query()->fetchAll();
            
            return $sql;
        }
        
        public function usersPalpitaronJogo($idmatch) {
            $db = $this->db;
            
            $sql = $db->select()->from("match")
                    ->joinInner("result", "result.rs_idmatch = match.mt_id")
                    ->joinInner("user", "user.us_id = result.rs_iduser")
                    ->joinInner(array('t1' => 'team'), 't1.tm_id = match.mt_idteam1', array('t1nome' => 't1.tm_name'))
                    ->joinInner(array('t2' => 'team'), 't2.tm_id = match.mt_idteam2', array('t2nome' => 't2.tm_name'))                    
                    ->joinLeft("penca", "penca.pn_id = result.rs_idpenca")
                    ->where("match.mt_id = ?", $idmatch)
                    ->where("result.rs_result is null")
                    ->query()->fetchAll();
            
            return $sql;
        }

    public function email_donosboloes() {
        $sql = $this->db;
        
        $return = $sql->select()->from("penca")
                ->joinInner("user", "user.us_id = penca.pn_iduser")
                ->query()->fetchAll();
        
        return $return;
    }    
    
    public function email_quemtemsaldo() {
        $sql = $this->db;
        
        $return = $sql->select()->from("user")
                ->where("us_cash <> 0")
                ->query()->fetchAll();
        
        return $return;        
    }

    /**
     * Guarda la informacion del usuario correspondiente al recibimiento de emails
     * @param params tiene @param res_pal, @param res_rod_pal, @param info_rod
     * @param iduser
     */
    public function emailConfiguracion($params) {
        $db = $this->db;
        $db->update("user", 
            array(
                "res_pal" =>$params['res_pal'],
                "res_rod_pal" => $params['res_rod_pal'],
                "info_rod" => $params['info_rod']
            )
            , "us_id = ".$params['iduser']);
    }

    /**
     * Retorna los emails de todos los usuarios con configuracion para recibir emails
     * de informacion de una rodada
     */
    public function emailsUsuariosInformacionRodada() {
        return $this->db->select()
            ->from("user", array('us_email'))
            ->where("info_rod = 1")
            ->where("us_email <> 'null'")
            ->query()
            ->fetchAll();
    }

    /**
     * Verifica que o usuario tiene dinero suficiente
     * @param usuario es el id del usuario
     * @param rodada es el id de la rodada
     * @return true si tiene o @return false si no tiene
     */
    public function suficienteDinero($usuario, $rodada) {
        return $this->db->query("SELECT CASE WHEN 
            (u.us_cash > 
                (SELECT r.rd_custo FROM round r WHERE r.rd_id = ".$rodada.") 
            ) 
            THEN true ELSE false END as hasMoney FROM user u WHERE u.us_id = ".$usuario)
            ->fetch();
    }

    /**
     * Marca como comprada la rodada y actualiza el dinero del usuario
     * y actualiza el dinero acumulado del campeonato
     * @param usuario
     * @param rodada
     */
    public function comprarRodada($usuario, $rodada) {
        $dados = array("ru_idrodada" => $rodada, "ru_iduser" => $usuario, "ru_pago" => 1, "ru_datahora" =>  date("Y-m-d H:i:s"));
        $this->db->insert("rodadausuario", $dados);
        $this->db->query("UPDATE user 
            SET us_cash = us_cash - (SELECT r.rd_custo FROM round r WHERE r.rd_id = ".$rodada.") 
            WHERE us_id =".$usuario);

        //$dados = $this->db->select()->from("round", array("rd_idchampionship", "rd_custo"))
        //    ->where("rd_id = ?", $rodada)->query()->fetch();

        //$this->db->query("UPDATE championship SET ch_acumulado = ch_acumulado + ".$dados['rd_custo']
        //    ." WHERE ch_id = ".$dados['rd_idchampionship']);
    }

    public function guardarfoto($dir, $usuario) {
        $this->db->update("user",array("us_foto" => $dir), "us_id = ".$usuario);
    }
    

    /**
     * Devuelve toda la informacion del usuario
     * @param usuario
     */
    public function getUsuario($usuario) {
        return $this->db->select()->from("user", 
            array("us_id", "us_cash","us_grito", "us_username", "us_teamname", "us_foto")
            )->where("us_id = ?",$usuario)->query()->fetch();
    }
    
    /**
     * Cuando una persona que no estaba registrada se loga desde facebook
     * llama a este metodo para registraro
     * @param idFacebook
     * @param email
     * @param nome
     */
    public function salvarUsuarioFacebookId($idFacebook, $email, $nome) {
        $dados = array(
            'us_idfacebook'=> $idFacebook,
            'us_email' => $email,
            'us_nome' => $nome,
            'us_origen' => "FACEBOOK",
            'us_username' => $nome,
            'us_password' => "qwertyytrewq"
        );
        $this->db->insert("user",$dados);
    }

    /**
     * Cuando un usuario se loga desde facebook pero su email ya estaba registrado
     * entonces actualiza el facebook id
     * @param idFacebook
     * @param email
     */
    public function atualizarUsuarioFacebookId($idFacebook, $email) {
        $this->db
            ->update("user", 
                array("us_idfacebook" => $idFacebook), 
                array('us_email = ?' => $email));
    }
}