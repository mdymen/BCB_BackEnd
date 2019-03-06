<?php

class Helpers_Parsers_ApiLiga extends Helpers_Parsers_Parsers {

    public function htmlToArray($html) {

        $array = array();

        for ($i = 0; $i < count($html); $i = $i + 1 ) {
            $data = explode("T", $html[$i]['data_realizacao']);
            $array[$i]['data'] =  $data[0]. " ";
            $array[$i]['hora'] = $html[$i]['hora_realizacao'];
            $array[$i]['equipo1']['nome'] = $html[$i]['equipes']['mandante']['sigla'];
            $array[$i]['equipo1']['resultado'] = $html[$i]['placar_oficial_mandante'];
            $array[$i]['equipo2']['nome'] = $html[$i]['equipes']['visitante']['sigla'];
            $array[$i]['equipo2']['resultado'] = $html[$i]['placar_oficial_visitante'];
            $array[$i] = $this->isPlayed($array[$i]);
        }

        $return['body'] = $array;
        return $return;

    }

    public function jugado($partido){}

    public function noJugado($partido){}
}


