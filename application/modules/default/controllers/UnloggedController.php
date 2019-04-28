<?php

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
include APPLICATION_PATH.'/helpers/globo.php';
include APPLICATION_PATH.'/helpers/partidos.php';
include APPLICATION_PATH.'/helpers/parsers/Parsers.php';
include APPLICATION_PATH.'/helpers/parsers/Eliminatorias.php';
include APPLICATION_PATH.'/helpers/parsers/ApiLiga.php';
include APPLICATION_PATH.'/helpers/parsers/Liga.php';
include APPLICATION_PATH.'/exceptions/VerificaPartidosParserException.php';
include APPLICATION_PATH.'/modules/default/controllers/BolaoController.php';

error_reporting(0);

class UnloggedController extends Zend_Controller_Action {

    function indexAction() {
        print_r("xxxxxxxxxxxxx");
        die("..");
    }

    /**
     * Verifica los partidos que ya fueron jugados y actualiza los resultados
     */
    function updateAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        try {
            $g = new Helpers_Globo();

            $m = new Application_Model_Matchs();
            
            $hoy = date('Y-m-d');
            $this->info("[VERIFICAR PARTIDOS] Busca todos los partidos de hoy: ".$hoy);

            //Carga los partidos que no han sido jugados hasta el dia de hoy
            //tambien carga los datos del campeonato y los datos de la rodada que 
            //sirven para saber si esa rodada del partido es la rodada actual del campeonato
            //sin hacer un nuevo select
            $timestamp = strtotime($partido['hora']) - 180*60;
            $time = date('H:i', $timestamp);

            $partidos = $m->loadPartidosNoJugados($hoy." ".$time); 
            $this->info("[VERIFICAR PARTIDOS] Partidos no actualizados hasta ahora: ".json_encode($partidos));       

            $p = new Helpers_Partidos();

            $retorno = array();
            for ($i = 0; $i < count($partidos); $i = $i + 1) {                
                $partido = $partidos[$i];
                $rodada = $partido['rd_round'];
                $campeonato = $partido['mt_idchampionship'];

                $this->info("[VERIFICAR PARTIDOS] Verificando resultado del partido con ID: ".$partido['mt_id']);
                $this->info("[VERIFICAR PARTIDOS] Rodada: ".$rodada);
                $this->info("[VERIFICAR PARTIDOS] Campeonato: ".$campeonato);

                if (empty($pGlobo[$rodada][$campeonato])) {

                    $urlcampeonatos = $m->getGlobo($partido['mt_idchampionship']);

                    $this->info("[VERIFICAR PARTIDOS] Conectando con la Globo en: ".$urlcampeonatos['dr_url']);
                    $this->info("[VERIFICAR PARTIDOS] Reemplazando los ### con: ".$partido['rd_cambio']);
                    
                    $urlcampeonatos['dr_url'] = str_replace("###",$partido['rd_cambio'], $urlcampeonatos['dr_url']);

                    $server_output = $g->get($urlcampeonatos['dr_url']);                

                    $res =  $this->getPartidosAlgoritmo($server_output, $urlcampeonatos['dr_algoritmo']) ;

                    $pGlobo[$rodada][$campeonato] = $res;
                    $this->info("[VERIFICAR PARTIDOS] Resultado de la GLOBO: ".json_encode($pGlobo[$rodada][$campeonato]));
                }

                $retorno[$i] = $pGlobo[$rodada][$campeonato];

                for ($j = 0; $j < count($pGlobo[$rodada][$campeonato]); $j = $j + 1) {
                    $partidoGlobo = $pGlobo[$rodada][$campeonato][$j];            

                    if (strcmp($partidoGlobo['equipo1']['nome'], $partido['tm1_sigla']) == 0
                        && strcmp($partidoGlobo['equipo2']['nome'], $partido['tm2_sigla']) == 0) {
                            $this->info("[VERIFICAR PARTIDOS] El siguiente partido sera actualizado: ".json_encode($partidoGlobo));

                            if ($partido['rd_id'] > $partido['ch_atualround'] ) {
                                $this->info("[VERIFICAR PARTIDOS] Hay que actualizar la rodada del campeonato para: ".$partido['rd_round']);
                                $ch = new Application_Model_Championships();
                                $ch->setRondaAtual($campeonato, $partido['rd_id']);
                            }

                            $partido['mt_goal1'] = $partidoGlobo['equipo1']['resultado'];
                            $partido['mt_goal2'] = $partidoGlobo['equipo2']['resultado'];

                            $this->info("[VERIFICAR PARTIDO] Partido: ".$partido['mt_id']." actualizando resultados para ".$partido['mt_goal1']." x ".$partido['mt_goal2']);

                            $p->save($partido, $partidoGlobo['played']);
                            $this->info("[VERIFICAR PARTIDOS] Ha finalizado la actualizacion del partido: ".json_encode($partidoGlobo));
                        }
                }

                $pGlobo[$rodada][$campeonato] = "";
            }

            $this->_helper->json($retorno);
        }
        catch (Exception $e) {
            $this->error("[VERIFICAR PARTIDOS] ".$e->getMessage());
            $this->_helper->json($e->getMessage());
        }
    }

    /**
     * Devuelve la cantidad de partidos no jugados enviadas por parametro
     * del campeonato
     */
    public function gamesAction() {
        try {
            $m = new Application_Model_Matchs();
            $matchs = $m->games();
            
            $result['body'] = $matchs;
            
            $this->_helper->json($result); 
        } catch (Exception $e) {
            $this->_helper->json($e->getMessage()); 
        }
    }

        /**
     * Retorna los ultimos 12 partidos jugados
     */
    public function ultimosjugadosAction() {
        try {
            $c = new Application_Model_Matchs();

            $partidos = $c->ultimosjugados();
            $result = array();

            $campeonatos = array();

            //Agrupa los partidos por campeonato y agrega el id del campeonato en
            //un array
            for ($i = 0; $i < count($partidos); $i = $i + 1) {
                $idCampeonato = $partidos[$i]['ch_nome'];
                $cantidad = count($result[$idCampeonato]);

                $result[$idCampeonato][$cantidad] = $partidos[$i]; 

                if (!in_array($idCampeonato, $campeonatos)) {
                    $campeonatos[]=$idCampeonato;
                }
            }

            //Saca el id del campeonato y deja un array con los array 
            //de los partidos
            $retorno = array();
            for ($i = 0; $i < count($campeonatos); $i = $i + 1) {
                $retorno[$i] = $result[$campeonatos[$i]];
              //  $retorno[$i]['nome'] = $campeonatos[$i];
            }

            $res['body'] = $retorno;

            $this->_helper->json($res);
        }
        catch (Exception $e) {
            $this->_helper->json($e->getMessage());
        }    
    }
}