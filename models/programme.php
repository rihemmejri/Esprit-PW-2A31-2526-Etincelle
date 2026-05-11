<?php
class programme {
    private $id_programme;
    private $id_user;
    private $objectif;
    private $date_debut;
    private $date_fin;
    private $repas; // Tableau des repas associés (jointure)

    public function __construct($id_user = null, $objectif = null, $date_debut = null, $date_fin = null) {
        $this->id_user = $id_user;
        $this->objectif = $objectif;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->repas = []; // Initialiser le tableau des repas
    }

    // ========== GETTERS ==========
    public function getIdProgramme() {
        return $this->id_programme;
    }

    public function getIdUser() {
        return $this->id_user;
    }

    public function getObjectif() {
        return $this->objectif;
    }

    public function getDateDebut() {
        return $this->date_debut;
    }

    public function getDateFin() {
        return $this->date_fin;
    }

    public function getRepas() {
        return $this->repas;
    }

    // ========== SETTERS ==========
    public function setIdProgramme($id_programme) {
        $this->id_programme = $id_programme;
    }

    public function setIdUser($id_user) {
        $this->id_user = $id_user;
    }

    public function setObjectif($objectif) {
        $this->objectif = $objectif;
    }

    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
    }

    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
    }

    public function setRepas($repas) {
        $this->repas = $repas;
    }

    // ========== MÉTHODES POUR GÉRER LES REPAS ==========
    public function addRepas($repasItem) {
        $this->repas[] = $repasItem;
    }

    public function removeRepas($index) {
        if (isset($this->repas[$index])) {
            unset($this->repas[$index]);
            $this->repas = array_values($this->repas);
        }
    }

    public function clearRepas() {
        $this->repas = [];
    }

    // ========== MÉTHODES MÉTIER ==========
    public function getNombreRepas() {
        return count($this->repas);
    }

    public function getCaloriesTotales() {
        $total = 0;
        foreach ($this->repas as $item) {
            if (isset($item['calories'])) {
                $total += $item['calories'];
            }
        }
        return $total;
    }

    public function getDureeProgramme() {
        $debut = new DateTime($this->date_debut);
        $fin = new DateTime($this->date_fin);
        $interval = $debut->diff($fin);
        return $interval->days + 1; // +1 pour inclure le premier jour
    }
}
?>