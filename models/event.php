<?php
class Event {
    private $id_evenement;
    private $titre;
    private $description;
    private $type_evenement;
    private $date_evenement;
    private $lieu;
    private $nb_places_max;
    private $statut;

    public function __construct(
        $titre = null,
        $description = null,
        $type_evenement = null,
        $date_evenement = null,
        $lieu = null,
        $nb_places_max = null,
        $statut = "ACTIF"
    ) {
        $this->titre = $titre;
        $this->description = $description;
        $this->type_evenement = $type_evenement;
        $this->date_evenement = $date_evenement;
        $this->lieu = $lieu;
        $this->nb_places_max = $nb_places_max;
        $this->statut = $statut;
    }

    // GETTERS
    public function getIdEvenement() { return $this->id_evenement; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getTypeEvenement() { return $this->type_evenement; }
    public function getDateEvenement() { return $this->date_evenement; }
    public function getLieu() { return $this->lieu; }
    public function getNbPlacesMax() { return $this->nb_places_max; }
    public function getStatut() { return $this->statut; }

    // SETTERS
    public function setIdEvenement($id) { $this->id_evenement = $id; }
    public function setTitre($titre) { $this->titre = $titre; }
    public function setDescription($description) { $this->description = $description; }
    public function setTypeEvenement($type) { $this->type_evenement = $type; }
    public function setDateEvenement($date) { $this->date_evenement = $date; }
    public function setLieu($lieu) { $this->lieu = $lieu; }
    public function setNbPlacesMax($nb) { $this->nb_places_max = $nb; }
    public function setStatut($statut) { $this->statut = $statut; }
}
?>