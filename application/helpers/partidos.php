<?php

class Helpers_Partidos {

     /**
     * PARTIDO
     * -------
     * @param mt_id 
     * @param mt_goal1
     * @param mt_goal2
     * @param mt_idteam1
     * @param mt_idteam2
     * @param mt_idchampionship
     * @param mt_played
     * @param mt_date
     * @param mt_idround
     * 
     * @param played si el partido terminó, es un atributo que depende de verificar en la globo
     */
    public function save($resultado, $played) {        

        $partido = array(
            "mt_id" => $resultado['mt_id'],
            "mt_goal1" => $resultado['mt_goal1'],
            "mt_goal2" => $resultado['mt_goal2'],
            "mt_idteam1" => $resultado['mt_idteam1'],
            "mt_idteam2" => $resultado['mt_idteam2'],
            "mt_idchampionship" => $resultado['mt_idchampionship'],
            "mt_played" => $resultado['mt_played'],
            "mt_date" => $resultado['mt_date'],
            "mt_idround" => $resultado['mt_idround']
        );

        $m = new Application_Model_Matchs();        
        $r = new Application_Model_Result();  

        $idMatch = $m->save($partido);

        //Si el partido está marcado como NO JUGADO pero se están pasando los goles
        //quiere decir que se tiene que procesar como NUEVO PARTIDO JUGADO
        if (strcmp($partido['mt_played'], "0") == 0 
            && strcmp($played, "1") == 0 
            && !is_null($partido['mt_goal1']) 
            && !is_null($partido['mt_goal2'])) {                       

            //verifica el resultado del partido y setea los puntos
            //y setea al partido como jugado
            $r->verificarGanadores($partido['mt_idteam1'], $partido['mt_idteam2'], $partido['mt_goal1'], $partido['mt_goal2'], $idMatch);
            
            //verifica los usuarios ganadores y setea los puntos
            $this->usuariosGanadores($partido['mt_goal1'], $partido['mt_goal2'], $idMatch);

        }
    }

        /**
     * Busca los ganadores del partido y luego les coloca 5 puntos.
     * @param res1
     * @param res2
     * @param idmatch
     */
    private function usuariosGanadores($res1, $res2, $idmatch) {
        $result = new Application_Model_Result();
        $ganadores = $result->obtenerUsuariosGanadores($res1, $res2, $idmatch);
        for ($i = 0; $i < count($ganadores); $i = $i + 1) {
            $result->puntosAlGanador($ganadores[$i]['rs_iduser'], $idmatch);
        }
    }

    /**
     * Dado algunos datos retorna el array armado de un partido.
     * @param partidoGlobo es el partido en formato de la globo
     *  partidoGlobo['equipo1']['nome']
     *  partidoGlobo['equipo2']['nome']
     *  partidoGlobo['equipo1']['resultado']
     *  partidoGlobo['equipo2']['resultado']
     *  partidoGlobo['data']
     *  partidoGlobo['hora']
     *  partidoGlobo['played']
     * @param idCampeonato
     * @param idRodada
     */
    public function getPartido($partidoGlobo, $idCampeonato, $idRodada) {
        $e = new Application_Model_Equipo();
        $m = new Application_Model_Matchs();   
        $equipo1 = $e->getBySigla($partidoGlobo['equipo1']['nome']);
        $equipo2 = $e->getBySigla($partidoGlobo['equipo2']['nome']);

        $partido = array();

        for ($i = 0; $i < count($equipo1); $i = $i + 1) {
            $equipo = $equipo1[$i];
            $partido['equipo1'][$i]['mt_idteam1'] = $equipo1[$i]['eq_id'];
            $partido['equipo1'][$i]['t1nome'] = $equipo1[$i]['eq_nome'];
            $partido['equipo1'][$i]['tm1_logo'] = $equipo1[$i]['eq_logo'];
            $partido['equipo1'][$i]['tm1_sigla'] = $equipo1[$i]['eq_sigla'];
        }


        for ($i = 0; $i < count($equipo2); $i = $i + 1) {
            $equipo = $equipo2[$i];
            $partido['equipo2'][$i]['mt_idteam2'] = $equipo2[$i]['eq_id'];
            $partido['equipo2'][$i]['t2nome'] = $equipo2[$i]['eq_nome'];
            $partido['equipo2'][$i]['tm2_logo'] = $equipo2[$i]['eq_logo'];
            $partido['equipo2'][$i]['tm2_sigla'] = $equipo2[$i]['eq_sigla'];
        }

        $match = $m->loadBySiglaAndCampeoantoAndRodada($partidoGlobo['equipo1']['nome'], $partidoGlobo['equipo2']['nome'], $idCampeonato, $idRodada);

        $partido['mt_date'] = $partidoGlobo['data'].$partidoGlobo['hora'];
        $partido['mt_goal1'] = $partidoGlobo['equipo1']['resultado'];
        $partido['mt_goal2'] = $partidoGlobo['equipo2']['resultado'];;
        $partido['mt_idchampionship'] = $idCampeonato;
        $partido['mt_played'] = 0;
        $partido['mt_acumulado'] = 0;
        $partido['mt_idround'] = $idRodada;
        $partido['mt_goal2'] = $partidoGlobo['equipo2']['resultado'];;
        $partido['ch_id'] = $idCampeonato;
        $partido['played'] = $partidoGlobo['played'];
        $partido['mt_id'] = $match['mt_id'];

        return $partido;
    }
}