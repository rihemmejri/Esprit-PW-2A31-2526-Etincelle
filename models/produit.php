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
<<<<<<< HEAD
    private $prix;
    private $stock;
    private $eco_score;

    public function __construct($nom = null, $image = null, $id_categorie = null, 
    $origine = null, $distance_transport = null, $type_transport = null, 
    $emballage = null, $transformation = null, $saison = null, 
    $prix = 0, $stock = 0, $eco_score = 0) {
=======

    public function __construct($nom = null, $image = null, $id_categorie = null, 
    $origine = null, $distance_transport = null, $type_transport = null, 
    $emballage = null, $transformation = null, $saison = null) {
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
        $this->nom = $nom;
        $this->image = $image;
        $this->id_categorie = $id_categorie;
        $this->origine = $origine;
        $this->distance_transport = $distance_transport;
        $this->type_transport = $type_transport;
        $this->emballage = $emballage;
        $this->transformation = $transformation;
        $this->saison = $saison;
<<<<<<< HEAD
        $this->prix = $prix;
        $this->stock = $stock;
        $this->eco_score = $eco_score;
    }

    // Getters
    public function getIdProduit() { return $this->id_produit; }
    public function getNom() { return $this->nom; }
    public function getImage() { return $this->image; }
    public function getIdCategorie() { return $this->id_categorie; }
    public function getOrigine() { return $this->origine; }
    public function getDistanceTransport() { return $this->distance_transport; }
    public function getTypeTransport() { return $this->type_transport; }
    public function getEmballage() { return $this->emballage; }
    public function getTransformation() { return $this->transformation; }
    public function getSaison() { return $this->saison; }
    public function getPrix() { return $this->prix; }
    public function getStock() { return $this->stock; }
    public function getEcoScore() { return $this->eco_score; }

    // Setters
    public function setIdProduit($id_produit) { $this->id_produit = $id_produit; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setImage($image) { $this->image = $image; }
    public function setIdCategorie($id_categorie) { $this->id_categorie = $id_categorie; }
    public function setOrigine($origine) { $this->origine = $origine; }
    public function setDistanceTransport($distance_transport) { $this->distance_transport = $distance_transport; }
    public function setTypeTransport($type_transport) { $this->type_transport = $type_transport; }
    public function setEmballage($emballage) { $this->emballage = $emballage; }
    public function setTransformation($transformation) { $this->transformation = $transformation; }
    public function setSaison($saison) { $this->saison = $saison; }
    public function setPrix($prix) { $this->prix = $prix; }
    public function setStock($stock) { $this->stock = $stock; }
    public function setEcoScore($eco_score) { $this->eco_score = $eco_score; }

    /**
     * Converts object to associative array for easier eco_score calculation
     */
    public function toArray() {
        return [
            'origine' => $this->origine,
            'distance_transport' => $this->distance_transport,
            'emballage' => $this->emballage,
            'transformation' => $this->transformation
        ];
=======
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
>>>>>>> e9e6ef124486afecac8b8e53d48909ce23fc6647
    }
}
?>
