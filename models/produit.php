<?php
class produit {
    private $id_produit;
    private $nom;
    private $image;
    private $id_categorie;
    private $origine;
    private $distance_transport;
    private $type_transport;
    private $emballage;
    private $transformation;
    private $saison;

    public function __construct($nom = null, $image = null, $id_categorie = null, 
    $origine = null, $distance_transport = null, $type_transport = null, 
    $emballage = null, $transformation = null, $saison = null) {
        $this->nom = $nom;
        $this->image = $image;
        $this->id_categorie = $id_categorie;
        $this->origine = $origine;
        $this->distance_transport = $distance_transport;
        $this->type_transport = $type_transport;
        $this->emballage = $emballage;
        $this->transformation = $transformation;
        $this->saison = $saison;
    }

    // Getters
    public function getIdProduit() {
        return $this->id_produit;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getImage() {
        return $this->image;
    }

    public function getIdCategorie() {
        return $this->id_categorie;
    }

    public function getOrigine() {
        return $this->origine;
    }

    public function getDistanceTransport() {
        return $this->distance_transport;
    }

    public function getTypeTransport() {
        return $this->type_transport;
    }

    public function getEmballage() {
        return $this->emballage;
    }

    public function getTransformation() {
        return $this->transformation;
    }

    public function getSaison() {
        return $this->saison;
    }

    // Setters
    public function setIdProduit($id_produit) {
        $this->id_produit = $id_produit;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setIdCategorie($id_categorie) {
        $this->id_categorie = $id_categorie;
    }

    public function setOrigine($origine) {
        $this->origine = $origine;
    }

    public function setDistanceTransport($distance_transport) {
        $this->distance_transport = $distance_transport;
    }

    public function setTypeTransport($type_transport) {
        $this->type_transport = $type_transport;
    }

    public function setEmballage($emballage) {
        $this->emballage = $emballage;
    }

    public function setTransformation($transformation) {
        $this->transformation = $transformation;
    }

    public function setSaison($saison) {
        $this->saison = $saison;
    }
}
?>
