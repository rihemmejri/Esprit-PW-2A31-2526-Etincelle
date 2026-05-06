<?php
class Alert {
    private $id;
    private $user_id;
    private $type;
    private $categorie;
    private $message;
    private $date;
    private $lu;

    public function __construct($user_id = null, $type = null, $categorie = null, $message = null, $date = null, $lu = false) {
        $this->user_id = $user_id;
        $this->type = $type;
        $this->categorie = $categorie;
        $this->message = $message;
        $this->date = $date;
        $this->lu = $lu;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->user_id; }
    public function getType() { return $this->type; }
    public function getCategorie() { return $this->categorie; }
    public function getMessage() { return $this->message; }
    public function getDate() { return $this->date; }
    public function getLu() { return $this->lu; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setType($type) { $this->type = $type; }
    public function setCategorie($categorie) { $this->categorie = $categorie; }
    public function setMessage($message) { $this->message = $message; }
    public function setDate($date) { $this->date = $date; }
    public function setLu($lu) { $this->lu = $lu; }
}
?>
