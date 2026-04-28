<?php
// model/User.php
class User {
    private ?int $id_user;
    private ?string $nom;
    private ?string $prenom;
    private ?string $email;
    private ?string $mot_de_passe;
    private ?string $date_inscription;
    private ?string $role;
    private ?string $statut;

    public function __construct(
        ?int $id_user = null, 
        ?string $nom = null, 
        ?string $prenom = null, 
        ?string $email = null, 
        ?string $mot_de_passe = null, 
        ?string $date_inscription = null, 
        ?string $role = null, 
        ?string $statut = null
    ) {
        $this->id_user = $id_user;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->date_inscription = $date_inscription;
        $this->role = $role;
        $this->statut = $statut;
    }

    // Getters
    public function getIdUser(): ?int { return $this->id_user; }
    public function getNom(): ?string { return $this->nom; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function getEmail(): ?string { return $this->email; }
    public function getMotDePasse(): ?string { return $this->mot_de_passe; }
    public function getDateInscription(): ?string { return $this->date_inscription; }
    public function getRole(): ?string { return $this->role; }
    public function getStatut(): ?string { return $this->statut; }

    // Setters
    public function setIdUser(?int $id_user): void { $this->id_user = $id_user; }
    public function setNom(?string $nom): void { $this->nom = $nom; }
    public function setPrenom(?string $prenom): void { $this->prenom = $prenom; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function setMotDePasse(?string $mot_de_passe): void { $this->mot_de_passe = $mot_de_passe; }
    public function setDateInscription(?string $date_inscription): void { $this->date_inscription = $date_inscription; }
    public function setRole(?string $role): void { $this->role = $role; }
    public function setStatut(?string $statut): void { $this->statut = $statut; }
}
?>