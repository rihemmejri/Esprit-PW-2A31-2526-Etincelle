<?php
class repas {
    private $id_repas;
    private $nom;
    private $type;
    private $calories;
    private $proteines;
    private $glucides;
    private $lipides;

    public function __construct($nom = null, $type = null, $calories = null, 
    $proteines = null, $glucides = null, $lipides = null) {
        $this->nom = $nom;
        $this->type = $type;
        $this->calories = $calories;
        $this->proteines = $proteines;
        $this->glucides = $glucides;
        $this->lipides = $lipides;
    }

    // Getters
    public function getIdRepas() {
        return $this->id_repas;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getType() {
        return $this->type;
    }

    public function getCalories() {
        return $this->calories;
    }

    public function getProteines() {
        return $this->proteines;
    }

    public function getGlucides() {
        return $this->glucides;
    }

    public function getLipides() {
        return $this->lipides;
    }

    // Setters
    public function setIdRepas($id_repas) {
        $this->id_repas = $id_repas;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setCalories($calories) {
        $this->calories = $calories;
    }

    public function setProteines($proteines) {
        $this->proteines = $proteines;
    }

    public function setGlucides($glucides) {
        $this->glucides = $glucides;
    }

    public function setLipides($lipides) {
        $this->lipides = $lipides;
    }
}
?>