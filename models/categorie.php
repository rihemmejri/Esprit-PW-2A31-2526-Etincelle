<?php
class Categorie {
    private ?int $id_categorie = null;
    private ?string $nom_categorie = null;
    private ?string $description = null;
    private ?string $image_categorie = null;
    private ?string $type_categorie = null;
    private ?string $date_creation = null;

    public function __construct(?string $nom_categorie = null) {
        $this->nom_categorie = $nom_categorie;
    }

    public function getIdCategorie(): ?int { return $this->id_categorie; }
    public function setIdCategorie(?int $id): void { $this->id_categorie = $id; }

    public function getNomCategorie(): ?string { return $this->nom_categorie; }
    public function setNomCategorie(?string $nom): void { $this->nom_categorie = $nom; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }

    public function getImageCategorie(): ?string { return $this->image_categorie; }
    public function setImageCategorie(?string $image): void { $this->image_categorie = $image; }

    public function getTypeCategorie(): ?string { return $this->type_categorie; }
    public function setTypeCategorie(?string $type): void { $this->type_categorie = $type; }

    public function getDateCreation(): ?string { return $this->date_creation; }
    public function setDateCreation(?string $date): void { $this->date_creation = $date; }
}
?>
