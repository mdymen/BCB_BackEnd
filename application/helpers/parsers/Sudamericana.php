<?php

class Helpers_Parsers_Sudamericana extends Helpers_Parsers_Parsers {

    public function htmlToArray($html) {
        try {

            $object = $this->html_to_obj($html);

            $lista = $object['children'][0]['children'][0]['children'][1]['children'];

            $partidos = array();
            $return = array();
            $result = array();

            $p = 0;
            for ($i = 3; $i < count($lista); $i = $i + 1) {
                $partido = $lista[$i]['children'][0]['children'];

                $result[$p] = $this->jugado($partido[0]);

                if ($result[$p]['hora'] == null && $result[$p]['data'] == null) {
                    $partido = $lista[$i]['children'][1]['children'];
                    $result[$p] = $this->segundaFase($partido[0]);

                    if ($result[$p]['equipo1']['nome'] == null) {
                        $result[$p] = $this->segundaFaseVejaComoFoi($partido[0]);
                    }

                    $result[$p] = $this->isPlayed($result[$p]);
                    $p = $p + 1;
                
                    $result[$p] = $this->segundaFase($partido[2]);
                    if ($result[$p]['equipo1']['nome'] == null) {
                        $result[$p] = $this->segundaFaseVejaComoFoi($partido[2]);
                    }

                    $result[$p] = $this->isPlayed($result[$p]);                
                    $p = $p + 1;

                    $partido = $lista[$i]['children'][2]['children'];
                    $result[$p] = $this->segundaFase($partido[0]);
                    if ($result[$p]['equipo1']['nome'] == null) {
                        $result[$p] = $this->segundaFaseVejaComoFoi($partido[0]);
                    }

                    $result[$p] = $this->isPlayed($result[$p]);
                    $p = $p + 1;
                
                    $result[$p] = $this->segundaFase($partido[2]);
                    if ($result[$p]['equipo1']['nome'] == null) {
                        $result[$p] = $this->segundaFaseVejaComoFoi($partido[2]);
                    }

                    $result[$p] = $this->isPlayed($result[$p]);                
                    $p = $p + 1;

                } else {
                    if ($result[$p]['hora'] == null && $result[$p]['data'] != null) {
                        $result[$p] = $this->jugadoSecundario($partido[0]);
                        $result[$p] = $this->isPlayed($result[$p]);
                        $p = $p + 1;
                    
                        $result[$p] = $this->jugadoSecundario($partido[2]);
                        $result[$p] = $this->isPlayed($result[$p]);
                        $p = $p + 1;
                    } else {
                        $result[$p] = $this->isPlayed($result[$p]);

                        $p = $p + 1;
                    
                        $result[$p] = $this->jugado($partido[2]);
                        $result[$p] = $this->isPlayed($result[$p]);
                        $p = $p + 1;
                    }
                }



            }

            $this->verify($result);

            $return['body'] = $result;

            return $return;
        }
        catch (VerificaPartidosParserException $v) {
            throw $v;
        }

    }

    private function segundaFase($partido) {
        $result['data'] = $partido['children'][1]['content'];
        $result['hora'] = $partido['children'][2]['html'];

        $result['equipo1']['nome'] = $partido['children'][3]['children'][0]['children'][1]['html'];
        $result['equipo1']['resultado'] = $partido['children'][3]['children'][1]['children'][0]['html'];

        $result['equipo2']['nome'] = $partido['children'][3]['children'][2]['children'][2]['html'];
        $result['equipo2']['resultado'] = $partido['children'][3]['children'][1]['children'][2]['html'];

        return $result;

    }

    private function segundaFaseVejaComoFoi($partido) {
        $result['data'] = $partido['children'][1]['content'];
        $result['hora'] = $partido['children'][2]['children'][0]['html'];

        $result['equipo1']['nome'] = $partido['children'][2]['children'][1]['children'][0]['children'][1]['html'];
        $result['equipo1']['resultado'] = $partido['children'][2]['children'][1]['children'][1]['children'][0]['html'];

        $result['equipo2']['nome'] = $partido['children'][2]['children'][1]['children'][2]['children'][2]['html'];
        $result['equipo2']['resultado'] = $partido['children'][2]['children'][1]['children'][1]['children'][2]['html'];

        return $result;        
    }

    private function jugadoSecundario($partido) {
        $result['data'] = $partido['children'][1]['content'];
        $result['hora'] = $partido['children'][2]['children'][0]['html'];

        $result['equipo1']['nome'] = $partido['children'][2]['children'][1]['children'][0]['children'][1]['html'];
        $result['equipo1']['resultado'] = $partido['children'][2]['children'][1]['children'][1]['children'][0]['html'];

        $result['equipo2']['nome'] = $partido['children'][2]['children'][1]['children'][2]['children'][2]['html'];
        $result['equipo2']['resultado'] = $partido['children'][2]['children'][1]['children'][1]['children'][2]['html'];

        return $result;
    }

    public function noJugado($partido) {}

    public function jugado($partido) {
        $result['data'] = $partido['children'][1]['content'];
        $result['hora'] = $partido['children'][2]['html'];

        $result['equipo1']['nome'] = $partido['children'][3]['children'][0]['children'][1]['html'];
        $result['equipo1']['resultado'] = $partido['children'][3]['children'][1]['children'][0]['html'];

        $result['equipo2']['nome'] = $partido['children'][3]['children'][2]['children'][2]['html'];
        $result['equipo2']['resultado'] = $partido['children'][3]['children'][1]['children'][2]['html'];

        return $result;

    }

}