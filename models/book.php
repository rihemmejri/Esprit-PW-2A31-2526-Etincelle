<?php
class book {
    private $id;
    private $titre;
    private $auteur;
    private $publication_date;
    private $language;
    private $status;
    private $number_of_copies;
    private $category_id;

    public function __construct($titre = null, $auteur = null, $publication_date = null, $language = null, 
    $status = null, $number_of_copies = null, $category_id = null) {
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->publication_date = $publication_date;
        $this->language=$language;
        $this->status=$status;
        $this->number_of_copies=$number_of_copies;
        $this->category_id=$category_id;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function setTitre($titre) {
        $this->titre = $titre;
    }

   
    public function getAuteur() {
        return $this->auteur;
    }

    public function setAuteur($auteur) {
        $this->auteur = $auteur;
    }

    
    public function getPublicationDate() {
        return $this->publication_date;
    }

    public function setPublicationDate($publication_date) {
        $this->publication_date = $publication_date;
    }

  
    public function getLanguage() {
        return $this->language;
    }

    public function setLanguage($language) {
        $this->language = $language;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getNumberOfCopies() {
        return $this->number_of_copies;
    }

    public function setNumberOfCopies($number_of_copies) {
        $this->number_of_copies = $number_of_copies;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function setCategoryId($category_id) {
        $this->category_id = $category_id;
    }
}
?>