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
}