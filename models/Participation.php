<?php
class Participation
{
    private $id_participation;
    private $id_evenement;
    private $id_user;
    private $nom;
    private $email;
    private $telephone;
    private $statut;
    private $statut_paiement;     // NOUVEAU
    private $reference_paiement;  // NOUVEAU
    private $montant_paye;        // NOUVEAU
    private $date_inscription;
    private $feedback;
    private $note;
    private $nb_places_reservees;
    private $evenement_titre;

    public function __construct(
        $id_evenement,
        $id_user,
        $nom,
        $email,
        $telephone,
        $statut,
        $date_inscription,
        $feedback             = null,
        $note                 = null,
        $nb_places_reservees  = 1,
        $statut_paiement      = 'GRATUIT',
        $reference_paiement   = null,
        $montant_paye         = 0.00
    ) {
        $this->id_evenement        = $id_evenement;
        $this->id_user             = $id_user;
        $this->nom                 = $nom;
        $this->email               = $email;
        $this->telephone           = $telephone;
        $this->statut              = $statut;
        $this->date_inscription    = $date_inscription;
        $this->feedback            = $feedback;
        $this->note                = $note;
        $this->nb_places_reservees = $nb_places_reservees;
        $this->statut_paiement     = $statut_paiement ?? 'GRATUIT';
        $this->reference_paiement  = $reference_paiement;
        $this->montant_paye        = $montant_paye ?? 0.00;
    }

    // ── Getters ──
    public function getIdParticipation()   { return $this->id_participation; }
    public function getIdEvenement()       { return $this->id_evenement; }
    public function getIdUser()            { return $this->id_user; }
    public function getNom()               { return $this->nom; }
    public function getEmail()             { return $this->email; }
    public function getTelephone()         { return $this->telephone; }
    public function getStatut()            { return $this->statut; }
    public function getStatutPaiement()    { return $this->statut_paiement ?? 'GRATUIT'; }
    public function getReferencePaiement() { return $this->reference_paiement; }
    public function getMontantPaye()       { return $this->montant_paye ?? 0.00; }
    public function getDateInscription()   { return $this->date_inscription; }
    public function getFeedback()          { return $this->feedback; }
    public function getNote()              { return $this->note; }
    public function getNbPlacesReservees() { return $this->nb_places_reservees; }
    public function getEvenementTitre()    { return $this->evenement_titre; }

    // ── Setters ──
    public function setIdParticipation($v)   { $this->id_participation   = $v; }
    public function setIdEvenement($v)       { $this->id_evenement        = $v; }
    public function setIdUser($v)            { $this->id_user             = $v; }
    public function setNom($v)               { $this->nom                 = $v; }
    public function setEmail($v)             { $this->email               = $v; }
    public function setTelephone($v)         { $this->telephone           = $v; }
    public function setStatut($v)            { $this->statut              = $v; }
    public function setStatutPaiement($v)    { $this->statut_paiement     = $v; }
    public function setReferencePaiement($v) { $this->reference_paiement  = $v; }
    public function setMontantPaye($v)       { $this->montant_paye        = $v; }
    public function setDateInscription($v)   { $this->date_inscription    = $v; }
    public function setFeedback($v)          { $this->feedback            = $v; }
    public function setNote($v)              { $this->note                = $v; }
    public function setNbPlacesReservees($v) { $this->nb_places_reservees = $v; }
    public function setEvenementTitre($v)    { $this->evenement_titre     = $v; }
}
?>