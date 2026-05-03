<?php
class objectif {
    private $id;
    private $user_id;
    private $poids_cible;
    private $calories_objectif;
    private $eau_objectif;
    private $date_debut;
    private $date_fin;
    private $userName;

    public function __construct($user_id = null, $poids_cible = null, $calories_objectif = null, 
    $eau_objectif = null, $date_debut = null, $date_fin = null) {
        $this->user_id = $user_id;
        $this->poids_cible = $poids_cible;
        $this->calories_objectif = $calories_objectif;
        $this->eau_objectif = $eau_objectif;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getPoidsCible() {
        return $this->poids_cible;
    }

    public function getCaloriesObjectif() {
        return $this->calories_objectif;
    }

    public function getEauObjectif() {
        return $this->eau_objectif;
    }

    public function getDateDebut() {
        return $this->date_debut;
    }

    public function getDateFin() {
        return $this->date_fin;
    }

    public function getUserName() {
        return $this->userName;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function setPoidsCible($poids_cible) {
        $this->poids_cible = $poids_cible;
    }

    public function setCaloriesObjectif($calories_objectif) {
        $this->calories_objectif = $calories_objectif;
    }

    public function setEauObjectif($eau_objectif) {
        $this->eau_objectif = $eau_objectif;
    }

    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
    }

    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }
}
?>
