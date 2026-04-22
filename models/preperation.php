<?php
// models/preperation.php
class Preperation {
    private $id_etape;
    private $ordre;
    private $instruction;
    private $duree;
    private $temperature;
    private $type_action;
    private $outil_utilise;
    private $quantite_ingredient;
    private $astuce;
    private $id_recette;
    private $recette_nom; // Pour la jointure

    public function __construct($ordre = null, $instruction = null, $duree = null, 
                                $temperature = null, $type_action = null, $outil_utilise = null, 
                                $quantite_ingredient = null, $astuce = null, $id_recette = null) {
        $this->ordre = $ordre;
        $this->instruction = $instruction;
        $this->duree = $duree;
        $this->temperature = $temperature;
        $this->type_action = $type_action;
        $this->outil_utilise = $outil_utilise;
        $this->quantite_ingredient = $quantite_ingredient;
        $this->astuce = $astuce;
        $this->id_recette = $id_recette;
    }

    // Getters
    public function getIdEtape() { return $this->id_etape; }
    public function getOrdre() { return $this->ordre; }
    public function getInstruction() { return $this->instruction; }
    public function getDuree() { return $this->duree; }
    public function getTemperature() { return $this->temperature; }
    public function getTypeAction() { return $this->type_action; }
    public function getOutilUtilise() { return $this->outil_utilise; }
    public function getQuantiteIngredient() { return $this->quantite_ingredient; }
    public function getAstuce() { return $this->astuce; }
    public function getIdRecette() { return $this->id_recette; }
    public function getRecetteNom() { return $this->recette_nom; }

    // Setters
    public function setIdEtape($id_etape) { $this->id_etape = $id_etape; }
    public function setOrdre($ordre) { $this->ordre = $ordre; }
    public function setInstruction($instruction) { $this->instruction = $instruction; }
    public function setDuree($duree) { $this->duree = $duree; }
    public function setTemperature($temperature) { $this->temperature = $temperature; }
    public function setTypeAction($type_action) { $this->type_action = $type_action; }
    public function setOutilUtilise($outil_utilise) { $this->outil_utilise = $outil_utilise; }
    public function setQuantiteIngredient($quantite_ingredient) { $this->quantite_ingredient = $quantite_ingredient; }
    public function setAstuce($astuce) { $this->astuce = $astuce; }
    public function setIdRecette($id_recette) { $this->id_recette = $id_recette; }
    public function setRecetteNom($recette_nom) { $this->recette_nom = $recette_nom; }
}
?>