<?php
class recette {
    private $id_recette;
    private $nom;
    private $description;
    private $temps_preparation;
    private $difficulte;
    private $type_repas;
    private $origine;
    private $nb_personne;

    public function __construct($nom = null, $description = null, $temps_preparation = null, 
    $difficulte = null, $type_repas = null, $origine = null, $nb_personne = null) {
        $this->nom = $nom;
        $this->description = $description;
        $this->temps_preparation = $temps_preparation;
        $this->difficulte = $difficulte;
        $this->type_repas = $type_repas;
        $this->origine = $origine;
        $this->nb_personne = $nb_personne;
    }

    // Getters
    public function getIdRecette() {
        return $this->id_recette;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getTempsPreparation() {
        return $this->temps_preparation;
    }

    public function getDifficulte() {
        return $this->difficulte;
    }

    public function getTypeRepas() {
        return $this->type_repas;
    }

    public function getOrigine() {
        return $this->origine;
    }

    public function getNbPersonne() {
        return $this->nb_personne;
    }

    // Setters
    public function setIdRecette($id_recette) {
        $this->id_recette = $id_recette;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setTempsPreparation($temps_preparation) {
        $this->temps_preparation = $temps_preparation;
    }

    public function setDifficulte($difficulte) {
        $this->difficulte = $difficulte;
    }

    public function setTypeRepas($type_repas) {
        $this->type_repas = $type_repas;
    }

    public function setOrigine($origine) {
        $this->origine = $origine;
    }

    public function setNbPersonne($nb_personne) {
        $this->nb_personne = $nb_personne;
    }
}
?>