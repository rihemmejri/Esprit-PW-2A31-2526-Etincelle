<?php
class Evenement
{
    private $id_evenement;
    private $titre;
    private $description;
    private $type_evenement;
    private $date_evenement;
    private $lieu;
    private $nb_places_max;
    private $prix;        // NOUVEAU
    private $statut;

    public function __construct(
        $titre,
        $description,
        $type_evenement,
        $date_evenement,
        $lieu,
        $nb_places_max,
        $statut,
        $prix = 0.00      // NOUVEAU — 0 = gratuit
    ) {
        $this->titre          = $titre;
        $this->description    = $description;
        $this->type_evenement = $type_evenement;
        $this->date_evenement = $date_evenement;
        $this->lieu           = $lieu;
        $this->nb_places_max  = $nb_places_max;
        $this->statut         = $statut;
        $this->prix           = $prix ?? 0.00;
    }

    // ── Getters ──
    public function getIdEvenement()   { return $this->id_evenement; }
    public function getTitre()         { return $this->titre; }
    public function getDescription()   { return $this->description; }
    public function getTypeEvenement() { return $this->type_evenement; }
    public function getDateEvenement() { return $this->date_evenement; }
    public function getLieu()          { return $this->lieu; }
    public function getNbPlacesMax()   { return $this->nb_places_max; }
    public function getStatut()        { return $this->statut; }
    public function getPrix()          { return $this->prix ?? 0.00; }
    public function isPayant()         { return ($this->prix ?? 0) > 0; }

    // ── Setters ──
    public function setIdEvenement($v)   { $this->id_evenement   = $v; }
    public function setTitre($v)         { $this->titre           = $v; }
    public function setDescription($v)   { $this->description     = $v; }
    public function setTypeEvenement($v) { $this->type_evenement  = $v; }
    public function setDateEvenement($v) { $this->date_evenement  = $v; }
    public function setLieu($v)          { $this->lieu             = $v; }
    public function setNbPlacesMax($v)   { $this->nb_places_max   = $v; }
    public function setStatut($v)        { $this->statut           = $v; }
    public function setPrix($v)          { $this->prix             = $v; }
}
?>