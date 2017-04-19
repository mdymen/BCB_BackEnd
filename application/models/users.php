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
    
    public function load_userbyid($user) {
        $db = $this->db;
        
        $result =  $db->select()->from("user")
                ->where("us_id = ?", $user)
                ->query()
                ->fetch();
        
        return $result;
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
        
        $db->update("user", array("us_codverificacion" => ""), "us_id = ".$data);
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
		
		print_r($query->__toString());		
		
		$result = $query->query()->fetch();
		
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
}