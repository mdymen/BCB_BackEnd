<?php


abstract class Helpers_Parsers_Parsers {

    /**
     * Dado un HTML pasa los tags para objetos con su valor
     * ejemplo $array['html'] => 2008-09-09
     * @param html
     */
    abstract public function htmlToArray($html);

    /**
     * Es el formato en array y transforma el partido 
     * en formato 
     * $array['data']
     * $array['hora']
     * $array['equipo1']['nome'] -> sigla del equipo
     * $array['equipo1']['resultado']
     * $array['equipo2']['nome'] -> sigla del equipo
     * $array['equipo2']['resultado']
     * @param partido
     */
    abstract public function jugado($partido);

    abstract public function noJugado($partido);

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
     * Verifica que todos los partidos tienen data, hora y nombre del equipo
     * @param partidos lista de partidos en formato:
     * $array['data']
     * $array['hora']
     * $array['equipo1']['nome'] -> sigla del equipo
     * $array['equipo1']['resultado']
     * $array['equipo2']['nome'] -> sigla del equipo
     * $array['equipo2']['resultado']
     */
    public function verify($partidos) {
        for ($i = 0; $i < count($partidos); $i = $i + 1) {
            $partido = $partidos[$i];

            if ($partido['data'] == null
            || $partido['hora'] == null
            || $partido['equipo1']['nome'] == null
            || $partido['equipo2']['nome'] == null) {
                throw new Exception("error al verificar el partido: Data".$partido['data']." Hora: ".$partido['hora']." Equipo1: ".$partido['equipo1']['nome']." Equipo2: ".$partido['equipo2']['nome']);
            }
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

}