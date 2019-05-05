<?php

include APPLICATION_PATH.'/models/bd_adapter.php';
class AquehorajuegaController  extends Zend_Controller_Action {

    public $logger;

    public function info($msg) {
        $this->logger->log($msg, Zend_Log::INFO);
    }

    public function error($msg) {
        $this->logger->log($msg, Zend_Log::ERR);
    }

    public function logAction() {
        $params = $this->_request->getParams();

        $file1 = APPLICATION_PATH."../logs/bolaoLog_".$params["fecha"].".txt";
        $lines = file($file1);
        foreach($lines as $line_num => $line)
        {
            echo $line;
            echo "<br>";
        }
        die(".");
    }

    public function preDispatch() {
       $this->logger = new Zend_Log();
       $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH."../logs/bolaoLog_".date("Y_m_d").".txt");
       $this->logger->addWriter($writer);
    }

    /**
     * GET
     * recalcula la tabla de posiciones del campeonato especificado
     * @param idCampeonato
     */
    function recalculartablaAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $params = $this->_request->getParams();
            $idCampeonato = $params['idCampeonato'];

            $this->info("[recalculartabla] recalcular la tabla de posiciones del campeonato: '".$idCampeonato."'");

            $t = new Application_Model_Teams();

            $teams = $t->load_teams_championship($idCampeonato); 

            $this->info("[recalculartabla] cargados todos los equipos del campeonato: '".print_r($teams, true)."'");

            for ($i = 0; $i < count($teams); $i = $i + 1) {
                $equipo = $teams[$i];
                
                $idEquipo = $equipo["eq_id"];

                $this->info("[recalculartabla] verificando partidos jugados del equipo: '".$idEquipo."' - ".$equipo['eq_nome']." del campeonato: '".$idCampeonato."'");

                $partidosJugados = $t->partidosJugados($idEquipo, $idCampeonato);

                $this->info("[recalculartabla] partidos jugados: '".$partidosJugados."'");

                $partidosGanados = $t->partidosGanados($idEquipo, $idCampeonato);
                $partidosEmpatados = $t->partidosEmpatados($idEquipo, $idCampeonato);

                $puntos = ($partidosGanados * 3) + $partidosEmpatados;

                $t->


                
            }

            print_r($teams);
            die(";");

        }
        catch (Exception $e) {

        }

    }

    /**
     * GET
     * Actualiza los resultados de un campeonato que tenga algun partido 
     * que ya fue jugado y que no tiene los resultados cerrados.
     * Selecciona el campeonato activo con id mas alto.
     */
    function updatesecuencialAction() {
        try {

            $this->info("[updatesecuencialAction]");

            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $m = new Application_Model_Matchs();

            $idCampeonato = $m->idCampeonatoParaActualizar();

            $this->info("[updatesecuencialAction] idCampeonatoParaActualizar -> resultado ".$idCampeonato);

            if (!empty($idCampeonato)) {
                $this->info("[updatesecuencialAction] el campeonato '".$idCampeonato."' precisa ser actualizado");
                
                $resultado = $this->update($idCampeonato);        
                
                $this->info("[updatesecuencialAction] campeonato '".$idCampeonato."' actualizado con éxito");
                $this->info("[updatesecuencialAction] '".$resultado."'");

                $this->_helper->json($idCampeonato);

            } else {
                $this->info("[updatesecuencialAction] no existe ningun campeonato que precise ser actualizado");
            }

        } catch (Exception $e) {
            $this->error("[updatesecuencialAction] ".$e->getMessage());
            print_r($e->getMessage());
            die(";");
        }
    }

    function update($idCampeonato) {

        $m = new Application_Model_Matchs();

        if (!empty($idCampeonato)) {

            $this->info("[update] campeonato: ".$idCampeonato);

            $url = $m->getGlobo($idCampeonato);

            $this->info("[update] url de globo para el campeonatos: ".$url);

            $resultados = $this->partidos($url["dr_url"], $idCampeonato);

            $this->setRodadaActivaActual($idCampeonato);

        } else {

            $this->info("[update] todos los campeonatos");

            $urls = $m->getGloboUrls();

            $this->info("[update] urls de globo para todos los campeonatos: ".$urls);

            $resultados = array();

            for ($i = 0; $i < count($urls); $i = $i + 1) {
                $url = $urls[$i]['dr_url'];
                $idCampeonato = $urls[$i]['dr_idchampionship'];

                $resultados[$i] = $this->partidos($url, $idCampeonato);

                $this->setRodadaActivaActual($idCampeonato);
            }    
        }

        return $resultados;
    }


    /**
     * GET
     * Actualiza todos los campeonatos y sus rodadas.
     * Con los horarios, dias, partidos y resultados.
     * 
     * Si recibe los parametros entonces actualiza ese campeonato,
     * si no recibe parametros, actualiza todos los registrados.
     * @param idCampeonato [optional] id del campeonato
     */
    function updateAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $params = $this->_request->getParams();
        $idCampeonato = $params['idCampeonato'];

        $resultados = $this->update($idCampeonato);

        $this->_helper->json($resultados);
    }

    /**
     * Dado el campeonato, busca cual deveria ser la rodada activa actual
     * y la setea
     * 
     * @param idCampeonato
     */
    function setRodadaActivaActual($idCampeonato) {
        $idRodada = $this->getRodadaActivaActual($idCampeonato);

        $c = new Application_Model_Championships();

        if (empty($idRodada)) {
            $r = new Application_Model_Rodada();
            $idRodada = $r->ultimaRodadaFinalizada($idCampeonato);
            $idRodada = $idRodada["rd_id"];
        }

        $c->setRondaAtual($idCampeonato, $idRodada);
    }

    /**
     * Retorna la que deveria ser la rodada activa actual del campeonato
     * 
     * @param idCampeonato
     */
    function getRodadaActivaActual($idCampeonato) {

        try {

            $r = new Application_Model_Rodada();
            $idRound = $r->rodadaActiva($idCampeonato);

            return $idRound["rd_id"];

        }
        catch (Exception $e) {
            print_r($e->getMessage());
            die(".");
        }
    }

    function rodadaactivaAction() {
        print_r($this->getRodadaActivaActual(47));
        die(";");
    }

    function partidostestAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(TRUE);

        $resultados = $this->partidos("https://api.globoesporte.globo.com/tabela/009b5a68-dd09-46b8-95b3-293a2d494366/fase/fase-unica-serieb-2019/rodada/###/jogos/", 42);

        $this->_helper->json($resultados);
    }

    /**
     * Recibe la URL de globo donde tiene que buscar los datos del partidos y el id del campeonato
     * del sistema.
     * @param url de globo
     * @param idCampeonato id del campeonato
     */
    function partidos($url, $idCampeonato) {

        try {

            $this->info("[partidos] url: ".$url);
            $this->info("[partidos] idCampeonato: ".$idCampeonato);

            $result = true;
            $rodada = 1;
            $results = array();
            $rodadas = array();
            $i = 0;

            ini_set('max_execution_time', 300);

            while ($result) {

                $this->info("[partidos] verificando rodada: ".$rodada);

                //Si la rodada ya fue finalizada en el sistema, no precisa ir mas a buscar informacion a la globo
                if (!$this->isRodadaFinalizada($rodada, $idCampeonato)) {

                    $this->info("[partidos] rodada todavia no fue finalizada. Preparándose para actualizar los partidos...");

                    $urlGlobo = str_replace("###", $rodada, $url);
                                    
                    $ch = curl_init($urlGlobo);
            
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                            
                    $server_output = curl_exec ($ch);
            
                    curl_close ($ch);  
                    
                    $partidos = json_decode($server_output, true);
                    $result = !empty($partidos);
                    
                    if ($result) {
                        $this->info("[partidos] partidos de la rodada: ".$partidos);

                        $results[$i] = $partidos;
                        $rodadas[$i] = $rodada;
                    }

                    $i = $i + 1;
                }

                $rodada = $rodada + 1;
            }

            $partidos = array();      

            for ($i = 0; $i < count($rodadas); $i = $i + 1) {
                for ($j = 0; $j < count($results[$i]); $j = $j + 1) {
                    $partido = $results[$i][$j];
                    $partidos[$i][$j] = $this->grabarPartidos($partido, $rodadas[$i], $idCampeonato);
                }

            }

            for ($i = 0; $i < count($rodadas); $i = $i + 1) {
                $round = $this->rodada($rodadas[$i], $idCampeonato);
                $this->verificarRodadaFinalizada($round);
            }

            return $partidos;
        }
        catch (Exception $e) {
            print_r($e->getMessage());
            die(".");
        }
    }

    /**
     * @param partido Recibe la lista de partidos de una rodada en la siguiente estructura:
     *  {
     *       "id": 232418,
     *       "data_realizacao": "2019-04-27T16:00",
     *       "hora_realizacao": "16:00",
     *       "placar_oficial_visitante": 0,
     *       "placar_oficial_mandante": 2,
     *       "placar_penaltis_visitante": null,
     *       "placar_penaltis_mandante": null,
     *       "equipes": {
     *           "mandante": {
     *               "id": 276,
     *               "nome_popular": "São Paulo",
     *               "sigla": "SAO",
     *               "escudo": "https://s.glbimg.com/es/sde/f/equipes/2018/03/11/sao-paulo.svg"
     *           },
     *           "visitante": {
     *               "id": 263,
     *               "nome_popular": "Botafogo",
     *               "sigla": "BOT",
     *               "escudo": "https://s.glbimg.com/es/sde/f/equipes/2018/03/11/botafogo.svg"
     *           }
     *       },
     *       "sede": {
     *           "nome_popular": "Morumbi"
     *       },
     *       "transmissao": {
     *           "label": "veja como foi",
     *           "url": "https://globoesporte.globo.com/sp/futebol/brasileirao-serie-a/jogo/27-04-2019/sao-paulo-botafogo.ghtml"
     *       }
     *   },
     * 
     * @param rodada numero de la rodada NO EL ID
     * @param campeonato es el id del campeonato
     */
    private function grabarPartidos($partido, $rodada, $campeonato) {

        try {

            $this->info("[grabarPartidos] partido: ".$partido);
            $this->info("[grabarPartidos] rodada: ".$rodada);
            $this->info("[grabarPartidos] campeonato: ".$cameponato);

            $this->crearEquipoSiNoExiste($partido["equipes"]["mandante"], $campeonato);
            $this->crearEquipoSiNoExiste($partido["equipes"]["visitante"], $campeonato);
            
            $round = $this->rodada($rodada, $campeonato);
            $this->info("[grabarPartidos] cargando la rodada '".$rodada."' del campeonato: '".$campeonato."'");
            $this->info("[grabarPartidos] rodada cargada: ".$round);

            if ($round == null) {
                $this->info("[grabarPartidos] rodada no existe. Creando la rodada: '".$round."' del campeonato '".$campeonato."'");
                $round = $this->crearRodada($rodada, $campeonato);
            }

            $match = $this->partido($partido["id"]);
            $this->info("[grabarPartidos] cargando partido con idGlobo: ".$partido["id"]);

            if ($match == null) {
                $this->info("[grabarPartidos] partido no existe. Creando el partido: '".$partido["id"]."' de la rodada '".$round."' del campeonato '".$campeonato."'");
                $match = $this->crearPartido($partido, $round, $campeonato);
            } else {
                $this->info("[grabarPartidos] partido existe, entonces vamos a actualizar: '".$partido["id"]."' del match: '".$match."'");
                $match = $this->actualizarPartido($partido, $match);
            }
            
            $this->info("[grabarPartidos] verificar si el partido fue jugado: '".$match['jugado']."' y si esta marcado como ya jugado: '".$match['mt_played']."'");
            //si no está marcado como que el partido ya habia sido jugado
            //pero ya finalizó y el resultado final es la primera vez que es actualizado
            if ($match['jugado'] == 1 && $match['mt_played'] == 0) {
                $this->info("[grabarPartidos] partido: '".$match["mt_id"]."' aun no marcado como que fue jugado, pero ya fue jugado");
                $r = new Application_Model_Result();
                $r->verificarGanadores($match['mt_idteam1'], $match['mt_idteam2'], $match['mt_goal1'], $match['mt_goal2'], $match['mt_id'], $campeonato);
            }

            return $partido;

        }
        catch (Exception $e) {
            print_r($e->getMessage());
            print_r($partido);
            $this->error("[grabarPartidos] ".$e->getMessage());
            die(".");
        }
    }

    /**
     * Encierra una rodada que tiene todos los partidos jugados
     * 
     * @param idRodada es el numero de la rodada NO EL ID
     * @param idCampeonato id del campeonato
     */
    public function rodadacerradaAction() {
        try {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(TRUE);

            $params = $this->_request->getParams();

            $idRodada = $params["idRodada"];
            $idCampeonato = $params["idCampeonato"];

            $round = $this->rodada($idRodada, $idCampeonato);

            $this->verificarRodadaFinalizada($round);
        }
        catch (Exception $e) {
            print_r($e->getMessage());
            die(";");
        }
    }

    /**
     * verifica si la rodada ya tiene todos los partidos jugados,
     * si los tiene entonces coloca como rodada finalizada.
     * 
     * @param round que es la rodada, todos los campos de la base
     */
    public function verificarRodadaFinalizada($round) {
        $m = new Application_Model_Matchs();
        $int_noJugados = $m->existsPartidosNoJugados($round["rd_id"]);

        if ($int_noJugados["jogos"] == 0) {
            $round["rd_finalizado"] = 1;
            $r = new Application_Model_Rodada();
            $r->actualizar($round);
        }
    }

    /**
     * Verifica si la rodada está finalizada
     * 
     * @param idRodada es el numero de la rodada no el id
     * @param idCampeonato
     * 
     * @return boolean si la rodada ya fue marcada como finalizada.
     */
    public function isRodadaFinalizada($idRodada, $idCampeonato) {
        $round = $this->rodada($idRodada, $idCampeonato);

        if (!empty($round)) {
            return $round["rd_finalizado"];
        }

        return false;
    }

    public function rodadaAction() {
               
       $this->_helper->layout->disableLayout();
       $this->_helper->viewRenderer->setNoRender(TRUE);
       
       $params = $this->getRequest()->getParams();

       $this->rodada($params['rodada'], $params['campeonato']);
    }

    /**
     * @param idGlobo el id del partido de la globo
     */
    private function partido($idGlobo) {
        $m = new Application_Model_Matchs();
        $partido = $m->loadByIdGlobo($idGlobo);
        if (empty($partido)) {
            return null;
        }
        return $partido;
    }

    public function infoPlayed($partido) {

        $array = array();

        $data = explode("T", $partido['data_realizacao']);
        $array['data'] =  $data[0]. " ";
        $array['hora'] = $partido['hora_realizacao'];
        $array = $this->isPlayed($array);

        return $array;

    }

    /**
     * Dado un partido coloca 1 si la fecha del partido es menor
     * que la de hoy, o sea, el partido ya fue jugado.
     * retorna 0 si el partido no fue jugado todavia
     * @param partido
     */
    public function isPlayed($partido) {
        $timestamp = strtotime($partido['hora']) + 180*60;

        $time = date('H:i', $timestamp);

        $date = $partido['data']." ".$time;

        $partido['played'] = 0;
        if (strtotime($date) < strtotime('now')) {
            $partido['played'] = 1;
        }
        return $partido;
    }

    
    /**
     * {
     *    "id": 276,
     *    "nome_popular": "São Paulo",
     *    "sigla": "SAO",
     *    "escudo": "https://s.glbimg.com/es/sde/f/equipes/2018/03/11/sao-paulo.svg"
     * }
     * @param idCampeonato
     */
    public function crearEquipoSiNoExiste($equipoGlobo, $idCampeonato) {
        $t = new Application_Model_Equipo();
        $equipo = $t->loadByIdGlobo($equipoGlobo["id"], $idCampeonato);

        if (empty($equipo)) {
            $equipo["eq_nome"] = $equipoGlobo['nome_popular'];
            $equipo["eq_logo"] = $equipoGlobo['id'].".jpg";
            $equipo["eq_sigla"] = $equipoGlobo["sigla"];
            $equipo["eq_idglobo"] = $equipoGlobo["id"];

            $id = $t->salvar($equipo);

            $equipoCampeonato["ec_idchampionship"] = $idCampeonato;
            $equipoCampeonato["ec_idequipo"] = $id;
            $equipoCampeonato["ec_jugados"] = 0;
            $equipoCampeonato["ec_pontos"] = 0;

            $t->salvarEquipoCampeonato($equipoCampeonato);
        }
    }


    /**
     * {
     *    "id": 276,
     *    "nome_popular": "São Paulo",
     *    "sigla": "SAO",
     *    "escudo": "https://s.glbimg.com/es/sde/f/equipes/2018/03/11/sao-paulo.svg"
     * }
     * @param idChamponship
     */
    public function actualizaridequipo($equipoGlobo, $idChamponship) {
        $t = new Application_Model_Equipo();
        $equipo = $t->loadBySiglaAndCampeonato($equipoGlobo["sigla"], $idChamponship);

        if (empty($equipo["eq_idglobo"])) {
            $equipo["eq_idglobo"] = $equipoGlobo["id"];
            $t->atualizar($equipo);
        }

    }

    private function crearPartido($partido, $round, $campeonato) {

        $info = $this->infoPlayed($partido);

        $idGlobo1 = $partido['equipes']['mandante']['id'];
        $idGlobo2 = $partido['equipes']['visitante']['id'];

        $t = new Application_Model_Equipo();

        $team1 = $t->loadByIdGlobo($idGlobo1);
        $team1 = $team1['eq_id'];
        $team2 = $t->loadByIdGlobo($idGlobo2);
        $team2 = $team2['eq_id'];

        $match['mt_idteam1'] = $team1;
        $match['mt_idteam2'] = $team2;
        $match['mt_date'] = $info["data"]."".$info["hora"];
        $match['mt_goal1'] = $partido["placar_oficial_mandante"];
        $match['mt_goal2'] = $partido["placar_oficial_visitante"];
        $match['mt_idchampionship'] = $campeonato;
        $match['mt_played'] = 0; //$info['played'];
        $match['mt_idround'] = $round['rd_id'];
        $match['mt_idglobo'] = $partido['id'];

        $m = new Application_Model_Matchs();
        $match["mt_id"] = $m->save($match);

        $match["jugado"] = $info["played"];

        return $match;
    }

    private function actualizarPartido($partido, $match) {

        $info = $this->infoPlayed($partido);

        $match["mt_date"] = $info["data"]."".$info["hora"];
        $match['mt_idglobo'] = $partido['id'];
        $match['mt_goal1'] = $partido["placar_oficial_mandante"];
        $match['mt_goal2'] = $partido["placar_oficial_visitante"];

        $m = new Application_Model_Matchs();
        $m->save($match);

        $match['jugado'] = $info['played'];

        return $match;
    }

    /**
    * @param rodada es el numero de la rodada no el ID
    * @param campeonato es el id del campeonato
    */
    private function rodada($rodada, $campeonato) {
        $r = new Application_Model_Rodada();
        $rodada = $r->rodada($rodada, $campeonato);
        if (empty($rodada)) {
            return null;
        }
        return $rodada;
    }

    private function crearRodada($rodada, $campeonato) {
        $r = new Application_Model_Rodada();
        $round["rd_round"] = $rodada;
        $round["rd_idchampionship"] = $campeonato;
        $round["rd_suma"] = 1;
        $round["rd_cambio"] = $rodada;

        return $r->save($round);
    }
  
}