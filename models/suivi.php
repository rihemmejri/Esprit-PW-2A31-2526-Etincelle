<?php
class suivi {
    private $id;
    private $user_id;
    private $id_objectif;
    private $date;
    private $poids;
    private $calories_consommees;
    private $calories_objectif;
    private $calories_restant;
    private $eau_bue;
    private $eau_objectif;
<<<<<<< HEAD
    private $userName;
    private $poidsCible;
=======
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647

    public function __construct($user_id = null, $id_objectif = null, $date = null, $poids = null, 
    $calories_consommees = null, $calories_objectif = null, $calories_restant = null, 
    $eau_bue = null, $eau_objectif = null) {
        $this->user_id = $user_id;
        $this->id_objectif = $id_objectif;
        $this->date = $date;
        $this->poids = $poids;
        $this->calories_consommees = $calories_consommees;
        $this->calories_objectif = $calories_objectif;
        $this->calories_restant = $calories_restant;
        $this->eau_bue = $eau_bue;
        $this->eau_objectif = $eau_objectif;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getIdObjectif() { return $this->id_objectif; }
    public function getDate() { return $this->date; }
    public function getPoids() { return $this->poids; }
    public function getCaloriesConsommees() { return $this->calories_consommees; }
    public function getCaloriesObjectif() { return $this->calories_objectif; }
    public function getCaloriesRestant() { return $this->calories_restant; }
    public function getEauBue() { return $this->eau_bue; }
    public function getEauObjectif() { return $this->eau_objectif; }
<<<<<<< HEAD
    public function getUserName() { return $this->userName; }
    public function getPoidsCible() { return $this->poidsCible; }
=======
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setIdObjectif($id_objectif) { $this->id_objectif = $id_objectif; }
    public function setDate($date) { $this->date = $date; }
    public function setPoids($poids) { $this->poids = $poids; }
    public function setCaloriesConsommees($calories_consommees) { $this->calories_consommees = $calories_consommees; }
    public function setCaloriesObjectif($calories_objectif) { $this->calories_objectif = $calories_objectif; }
    public function setCaloriesRestant($calories_restant) { $this->calories_restant = $calories_restant; }
    public function setEauBue($eau_bue) { $this->eau_bue = $eau_bue; }
    public function setEauObjectif($eau_objectif) { $this->eau_objectif = $eau_objectif; }
<<<<<<< HEAD
    public function setUserName($userName) { $this->userName = $userName; }
    public function setPoidsCible($poidsCible) { $this->poidsCible = $poidsCible; }
=======
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
}
?>
