<?php
/**
 * Es el modelo con todas las responsabilidades
 * de la tabla realizar los posts en redes sociales
 *
 * @author Martin Dymenstein
 */
class Application_Model_Posts extends Application_Model_Bd_Adapter
{
    protected $_name = 'post';

    public function get() {
        $hora = date("H:i");

        return $this->db->select()
        ->from("post")     
        ->where("post.ps_id = ?", 7)
        ->query()
        ->fetchAll();  
    }

    public function getPost($id) {
        return $this->db->select()
            ->from("post")
            ->where("post.ps_id = ?", $id)
            ->query()
            ->fetch();
    }

    public function getByTagAndCampeonato($tag, $campeonato) {
        return $this->db->select()
            ->from("post")
            ->where("post.ps_tag = ?", $tag)
            ->where("post.ps_idchampionship = ?", $campeonato)
            ->query()
            ->fetch();
    }

}