<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChampionshipController
 *
 * @author Martin Dymenstein
 */
include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/models/pencas.php';
//include APPLICATION_PATH.'/models/bd_adapter.php';
include APPLICATION_PATH.'/helpers/html.php';
include APPLICATION_PATH.'/helpers/box.php';
include APPLICATION_PATH."/helpers/data.php";
include APPLICATION_PATH.'/helpers/translate.php';
include APPLICATION_PATH.'/helpers/paginacao.php';
include APPLICATION_PATH.'/helpers/posicoes.php';
include APPLICATION_PATH.'/helpers/ranking.php';
class CampeonatosController extends Zend_Controller_Action
{
    public function indexAction() {
        $params = $this->_request->getParams();
                    
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load();
        
        $p_obj = new Application_Model_Penca();
        
        if (!empty($params['champ'])) {  
        
            $this->view->teamuserid = $this->getTimeUserId();
            $this->view->teamusername = $this->getTimeUserName();
            
            $champ_id = $params['champ'];

            $this->view->champ = $champ_id;
            $this->view->championship = $champ->getChamp($champ_id);
            
            if (empty($params['rodada'])) {
                $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
            } else {            
                $rodada_id = $params['rodada'];
            }
            
            $this->view->rodada = $rodada_id;

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            $p_obj = new Application_Model_Penca();
            
            if (empty($params['team'])) {
                $rodadas = $matchs_obj->load_rodada_com_palpites($champ_id, $rodada_id, $data['us_id']);
                $this->view->porteam = true;
                $this->view->porrodada = false;
            } else {
                $this->view->porteam = false;
                $this->view->porrodada = true;
                $team_id = $params['team'];
                $rodadas = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            
            
            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 
            
            $ranking = new Application_Model_Result();
            $rankings = $ranking->ranking_round($rodada_id, $champ_id);
            $rankings_champ = $ranking->ranking_champ($champ_id);
            
            $this->view->teams = $teams;
            $this->view->rodadas = $rodadas;
            $this->view->n_rodada = $rodada_id;
            $this->view->rondas = $rondas;   
            $this->view->rankings = $rankings;
            $this->view->ranking_champ = $rankings_champ;
        }
    }
    
    public function encerradosAction() {
        $params = $this->_request->getParams();
                    
        $champ = new Application_Model_Championships();
        $this->view->championships = $champ->load_encerrados();
        
        $p_obj = new Application_Model_Penca();
        
        if (!empty($params['champ'])) {  
        
            $this->view->teamuserid = $this->getTimeUserId();
            $this->view->teamusername = $this->getTimeUserName();
            
            $champ_id = $params['champ'];

            $this->view->champ = $champ_id;
            $this->view->championship = $champ->getChamp($champ_id);
            
            if (empty($params['rodada'])) {
                $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
            } else {            
                $rodada_id = $params['rodada'];
            }
            
            $this->view->rodada = $rodada_id;

            $storage = new Zend_Auth_Storage_Session();
            $data = (get_object_vars($storage->read()));

            $matchs_obj = new Application_Model_Matchs();
            $rondas = $matchs_obj->getrondas($champ_id);

            $p_obj = new Application_Model_Penca();
            
            if (empty($params['team'])) {
                $rodadas = $matchs_obj->load_matchs($champ_id, $rodada_id, $data['us_id']);
                $this->view->porteam = true;
                $this->view->porrodada = false;
            } else {
                $this->view->porteam = false;
                $this->view->porrodada = true;
                $team_id = $params['team'];
                $rodadas = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $data['us_id']);
            }

            
            
            $teams_obj = new Application_Model_Teams();
            $teams = $teams_obj->load_teams_championship($champ_id); 
            
            $ranking = new Application_Model_Result();
            $rankings = $ranking->ranking_round($rodada_id, $champ_id);
            $rankings_champ = $ranking->ranking_champ($champ_id);
            
            $this->view->teams = $teams;
            $this->view->rodadas = $rodadas;
            $this->view->n_rodada = $rodada_id;
            $this->view->rondas = $rondas;   
            $this->view->rankings = $rankings;
            $this->view->ranking_champ = $rankings_champ;
        }
    }    
    
    public function getTimeUserId() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        return $data['us_team'];
    }
    
    public function getTimeUserName() {
        $storage = new Zend_Auth_Storage_Session();
        $data = (get_object_vars($storage->read())); 
        
        return $data['us_teamname'];
    }


    /**
     * GET
     * Retorna todos los campeonatos activos
     */
    public function getAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $champ = new Application_Model_Championships();
        $result['body'] = $champ->load();

        $this->_helper->json($result);

    }

    /**
     * POST
     * Lista de equipos-campeonato para ser grabados
     * como asociados.
     * Adiciona los equipos al campeonato
     * @param equipos
     */
    public function saveAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);

            $equipos = $data['equipos'];

            for ($i = 0; $i < count($equipos); $i = $i + 1) {
                $equipo = $equipos[$i];
                unset($equipo['nome']);

                $c = new Application_Model_Championships();


                $c->saveEquipoCampeonato($equipo);

            }

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Retorna todos los equipos cadastrados en el campeonato
     * @param idCampeonato
     */
    public function getbycampeonatoAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $body = $this->getRequest()->getParams();
            $idCampeonato = $body['idCampeonato'];

            $c = new Application_Model_Championships();

            $result['body'] = $c->loadByCampeonato($idCampeonato);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * GET
     * Retorna todas las rodadas del campeonato
     * @param idCampeonato
     */
    public function getrodadasAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $body = $this->getRequest()->getParams();
            $idCampeonato = $body['idCampeonato'];

            $c = new Application_Model_Championships();
            $result['body'] = $c->loadRodadasByCampeonato($idCampeonato);

            $this->_helper->json($result);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }


    /**
     * GET
     * Retorna todos los partidos de la rodada especificada y del campeonato especificado
     * @param idUsuario
     * @param idCampeonato
     * @param idRodada
     */
    public function getpartidosAction() {
       
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            
            $params = $this->getRequest()->getParams();
        
            
            $champ = new Application_Model_Championships();
            $result['championships']= $champ->load();
            
            $id = $params['idUsuario'];
            
            $p_obj = new Application_Model_Penca();

            if (!empty($params['idCampeonato'])) {                      
                
                $champ_id = $params['idCampeonato'];

                $ranking = new Application_Model_Result();
                $result['rankings_champ'] = $ranking->ranking_champ($champ_id);
                
                $result['idCampeonato'] = $champ_id;
                $result['championship'] = $champ->getChamp($champ_id);
                
                if (!is_numeric($params['idRodada'])) {
                    $rodada_id = $p_obj->getIdPrimeraRodadaDisponivel($champ_id);
                } else {            
                    $rodada_id = $params['idRodada'];
                }

                $storage = new Zend_Auth_Storage_Session();

                $matchs_obj = new Application_Model_Matchs();
                $rondas = $matchs_obj->getrondas($champ_id);

                $tem_grupo = false;
                
                if (empty($params['team'])) {
                    $rodada = $matchs_obj->loadRodadaPalpitada($champ_id, $rodada_id, $id);
                    $result['porteam'] = true;
                    $result['porrodada'] = false;
                } else {
                    $result['porteam']  = false;
                    $$result['porrodada'] = true;
                    $team_id = $params['team'];
                    $rodada = $matchs_obj->load_rodada_porteam($champ_id, $team_id, $id);
                }
                
                $rodadaAtual = $matchs_obj->getRodada($rodada_id, $id);

                $result['teams'] = $teams;
                
                //los partidos de la rodada n_rodada
                $result['rodada'] = $rodada;
                
                //el numero de la rodada activa. La que siguiente inmediata que se va a jugar
                $result['n_rodada'] = $rodada_id;

                //toda la informacion de la rodada actual
                $result['rodadaAtual'] = $rodadaAtual;            

                //las rodadas del campeonato registradas en el sistema
                $result['rondas'] = $rondas;      		

                $result['status'] = 200;
                            
            }
                
            $res['body'] = $result;

            $this->_helper->json($res);
            
        }
        catch (Exception $e) {
            $result['status'] = 400;
            $result['error'] = $e->getMessage();
            $this->_helper->json($result);
        }
    }

    /**
     * Retorna todos los campeonatos abiertos
     * 
     * @return ch_id
     * @return ch_acumulado
     * @return ch_nome
     * @return ch_logocampeonato
     * @return ch_id
     */
    public function getbasicAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $champ = new Application_Model_Championships();
            $result['body']= $champ->get();

            $this->_helper->json($result);

        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    public function tojsonAction() {

        error_reporting(E_ERROR | E_PARSE);


        $html = '
        <li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="São Paulo x Vitória"><meta itemprop="startDate" content="2018-06-12"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/sp/futebol/brasileirao-serie-a/jogo/12-06-2018/sao-paulo-vitoria.ghtml"><div class="placar-jogo-informacoes">Ter 12/06/2018 <span class="placar-jogo-informacoes-local">Morumbi</span> 21:30</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="São Paulo"><span class="placar-jogo-equipes-sigla" title="São Paulo">SAO</span><span class="placar-jogo-equipes-nome">São Paulo</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/sao_paulo_60x60.png" width="30" height="30" title="São Paulo" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">3</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">0</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Vitória"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/vitoria_60x60.png" width="30" height="30" title="Vitória"><span class="placar-jogo-equipes-sigla" title="Vitória">VIT</span><span class="placar-jogo-equipes-nome">Vitória</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="América-MG x Chapecoense"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/mg/futebol/brasileirao-serie-a/jogo/13-06-2018/america-mg-chapecoense.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Independência</span> 16:00</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="América-MG"><span class="placar-jogo-equipes-sigla" title="América-MG">AME</span><span class="placar-jogo-equipes-nome">América-MG</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/organizacoes/2018/01/24/AmericaMG-65.png" width="30" height="30" title="América-MG" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">0</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">0</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Chapecoense"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2015/08/03/Escudo-Chape-165.png" width="30" height="30" title="Chapecoense"><span class="placar-jogo-equipes-sigla" title="Chapecoense">CHA</span><span class="placar-jogo-equipes-nome">Chapecoense</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Fluminense x Santos"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/rj/futebol/brasileirao-serie-a/jogo/13-06-2018/fluminense-santos.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Maracanã</span> 19:00</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Fluminense"><span class="placar-jogo-equipes-sigla" title="Fluminense">FLU</span><span class="placar-jogo-equipes-nome">Fluminense</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2015/07/21/fluminense_60x60.png" width="30" height="30" title="Fluminense" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">0</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">1</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Santos"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/santos_60x60.png" width="30" height="30" title="Santos"><span class="placar-jogo-equipes-sigla" title="Santos">SAN</span><span class="placar-jogo-equipes-nome">Santos</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Paraná x Cruzeiro"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/pr/futebol/brasileirao-serie-a/jogo/13-06-2018/parana-cruzeiro.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Durival Britto</span> 19:30</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Paraná"><span class="placar-jogo-equipes-sigla" title="Paraná">PAR</span><span class="placar-jogo-equipes-nome">Paraná</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/13/parana_60x60.png" width="30" height="30" title="Paraná" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">1</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">1</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Cruzeiro"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2015/04/29/cruzeiro_65.png" width="30" height="30" title="Cruzeiro"><span class="placar-jogo-equipes-sigla" title="Cruzeiro">CRU</span><span class="placar-jogo-equipes-nome">Cruzeiro</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Sport x Grêmio"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/pe/futebol/brasileirao-serie-a/jogo/13-06-2018/sport-gremio.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Ilha do Retiro</span> 19:30</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Sport"><span class="placar-jogo-equipes-sigla" title="Sport">SPO</span><span class="placar-jogo-equipes-nome">Sport</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2015/07/21/sport65.png" width="30" height="30" title="Sport" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">0</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">0</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Grêmio"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/gremio_60x60.png" width="30" height="30" title="Grêmio"><span class="placar-jogo-equipes-sigla" title="Grêmio">GRE</span><span class="placar-jogo-equipes-nome">Grêmio</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Botafogo x Atlético-PR"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/rj/futebol/brasileirao-serie-a/jogo/13-06-2018/botafogo-atletico-pr.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Engenhão</span> 21:00</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Botafogo"><span class="placar-jogo-equipes-sigla" title="Botafogo">BOT</span><span class="placar-jogo-equipes-nome">Botafogo</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/botafogo_60x60.png" width="30" height="30" title="Botafogo" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">2</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">0</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Atlético-PR"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2015/06/24/atletico-pr_2015_65.png" width="30" height="30" title="Atlético-PR"><span class="placar-jogo-equipes-sigla" title="Atlético-PR">CAP</span><span class="placar-jogo-equipes-nome">Atlético-PR</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Palmeiras x Flamengo"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/sp/futebol/brasileirao-serie-a/jogo/13-06-2018/palmeiras-flamengo.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Arena Palmeiras</span> 21:00</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Palmeiras"><span class="placar-jogo-equipes-sigla" title="Palmeiras">PAL</span><span class="placar-jogo-equipes-nome">Palmeiras</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/palmeiras_60x60.png" width="30" height="30" title="Palmeiras" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">1</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">1</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Flamengo"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2018/04/09/Flamengo-65.png" width="30" height="30" title="Flamengo"><span class="placar-jogo-equipes-sigla" title="Flamengo">FLA</span><span class="placar-jogo-equipes-nome">Flamengo</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Atlético-MG x Ceará"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/mg/futebol/brasileirao-serie-a/jogo/13-06-2018/atletico-mg-ceara.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Independência</span> 21:45</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Atlético-MG"><span class="placar-jogo-equipes-sigla" title="Atlético-MG">CAM</span><span class="placar-jogo-equipes-nome">Atlético-MG</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2017/11/23/Atletico-Mineiro-escudo65px.png" width="30" height="30" title="Atlético-MG" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">2</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">1</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Ceará"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2018/05/11/ceara-65x65.png" width="30" height="30" title="Ceará"><span class="placar-jogo-equipes-sigla" title="Ceará">CEA</span><span class="placar-jogo-equipes-nome">Ceará</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Bahia x Corinthians"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/ba/futebol/brasileirao-serie-a/jogo/13-06-2018/bahia-corinthians.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Fonte Nova</span> 21:45</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Bahia"><span class="placar-jogo-equipes-sigla" title="Bahia">BAH</span><span class="placar-jogo-equipes-nome">Bahia</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/bahia_60x60.png" width="30" height="30" title="Bahia" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">1</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">0</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Corinthians"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2014/04/14/corinthians_60x60.png" width="30" height="30" title="Corinthians"><span class="placar-jogo-equipes-sigla" title="Corinthians">COR</span><span class="placar-jogo-equipes-nome">Corinthians</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li><li class="lista-de-jogos-item"><div class="placar-jogo" itemscope itemtype="http://schema.org/SportsEvent"><meta itemprop="name" content="Internacional x Vasco"><meta itemprop="startDate" content="2018-06-13"><a class="placar-jogo-link placar-jogo-link-confronto-js" href="https://globoesporte.globo.com/rs/futebol/brasileirao-serie-a/jogo/13-06-2018/internacional-vasco.ghtml"><div class="placar-jogo-informacoes">Qua 13/06/2018 <span class="placar-jogo-informacoes-local">Beira-Rio</span> 21:45</div><div class="placar-jogo-equipes"><span class="placar-jogo-equipes-item placar-jogo-equipes-mandante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Internacional"><span class="placar-jogo-equipes-sigla" title="Internacional">INT</span><span class="placar-jogo-equipes-nome">Internacional</span><img class="placar-jogo-equipes-escudo-mandante" itemprop="image" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2016/05/03/inter65.png" width="30" height="30" title="Internacional" ></span><span class="placar-jogo-equipes-item placar-jogo-equipes-placar"><span class="placar-jogo-equipes-placar-mandante">3</span><span class="tabela-icone tabela-icone-versus"></span><span class="placar-jogo-equipes-placar-visitante">1</span></span><span class="placar-jogo-equipes-item placar-jogo-equipes-visitante" itemprop="performer" itemscope itemtype="http://schema.org/SportsTeam"><meta itemprop="name" content="Vasco"><img class="placar-jogo-equipes-escudo-visitante" itemprop="image" src="https://s.glbimg.com/es/sde/f/equipes/2016/07/29/Vasco-65.png" width="30" height="30" title="Vasco"><span class="placar-jogo-equipes-sigla" title="Vasco">VAS</span><span class="placar-jogo-equipes-nome">Vasco</span></span></div><p class="placar-jogo-complemento">veja como foi</p></a></div></li>

            ';
        
        $x2= $this->html_to_obj($html);
                               //   echo $doc->saveHTML();

         // class=(["'])(?:(?=(\\?))\2.)*?\1  $x1 = preg_replace("class=([\"'])(?:(?=(\\?))\2.)*?\1",$x,"");

       //  $x1 = preg_replace('class="',$x," ");

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $lista = $x2['children'][0]['children'];

            $partidos = array();
            $result = array();

            $p = array();

            for ($i = 0; $i < count($lista); $i = $i + 1) {
                $partido = $lista[$i];

                $partidos[$i] = $partido['children'][0]['children'][2]['children'][1]['children'];

                $result['equipo1']['nome'] = $partidos[$i][0]['children'][1]['html'];
                $result['equipo1']['resultado'] = $partidos[$i][1]['children'][0]['html'];
                $result['equipo2']['resultado'] = $partidos[$i][1]['children'][2]['html'];
                $result['equipo2']['nome'] = $partidos[$i][2]['children'][2]['html'];

                $p['partidos'][$i] = $result;

            }



//$res = $x2->children->children;///['children']['children'];

            $this->_helper->json($p);

    }

    public function globores($html) {

        $x2= $this->html_to_obj($html);

        $lista = $x2['children'][0]['children'];

        $partidos = array();
        $result = array();

        $p = array();

        for ($i = 0; $i < count($lista); $i = $i + 1) {
            $partido = $lista[$i];

            $partidos[$i] = $partido['children'][0]['children'][2]['children'][1]['children'];

            $result['data'] = $partido['children'][0]['children'][1]['content'];
            $result['hora'] = $partido['children'][0]['children'][2]['children']['html'];
            $result['equipo1']['nome'] = $partidos[$i][0]['children'][1]['html'];
            $result['equipo1']['resultado'] = $partidos[$i][1]['children'][0]['html'];
            $result['equipo2']['resultado'] = $partidos[$i][1]['children'][2]['html'];
            $result['equipo2']['nome'] = $partidos[$i][2]['children'][2]['html'];

            $p['body'][$i] = $result;

        }

        return $p;


    }

    public function getjsongloboAction() {
        error_reporting(E_ERROR | E_PARSE);

        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);
            
            $body = $this->getRequest()->getRawBody();
            $data = Zend_Json::decode($body);
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $data['dir']);
        
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $server_output = curl_exec ($ch);

            curl_close ($ch);        
            
            $this->getResponse()->setHeader('Content-Type', 'application/json');

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $res = $this->html_to_obj($server_output);

            $this->_helper->json($res);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }
    }

    public function html_to_obj($html) {
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        return $this->element_to_obj($dom->documentElement);
    }

    public function element_to_obj($element) {
        $obj = array( "tag" => $element->tagName );
        foreach ($element->attributes as $attribute) {
            $obj[$attribute->name] = $attribute->value;
        }
        foreach ($element->childNodes as $subElement) {
            if ($subElement->nodeType == XML_TEXT_NODE) {
                $obj["html"] = $subElement->wholeText;
            }
            else {
                $obj["children"][] = $this->element_to_obj($subElement);
            }
        }
        return $obj;
    }



    function globoAction() { 
        error_reporting(E_ERROR | E_PARSE);

        try {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);
        
        $body = $this->getRequest()->getRawBody();
        $data = Zend_Json::decode($body);
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $data['dir']);
     
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec ($ch);

        curl_close ($ch);        
        
        $this->getResponse()->setHeader('Content-Type', 'application/json');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

           $res =  $this->globores($server_output);

        $this->_helper->json($res);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }

    }

}
