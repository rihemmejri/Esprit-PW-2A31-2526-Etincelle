<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Participation.php');

// ── PHPMailer ──
require_once __DIR__ . '/../vendor/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/src/SMTP.php';
require_once __DIR__ . '/../vendor/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ParticipationController
{
    private function rowToParticipation($row)
    {
        $p = new Participation(
            $row['id_evenement'],
            $row['id_user'],
            $row['nom'],
            $row['email'],
            $row['telephone'],
            $row['statut'],
            $row['date_inscription'],
            $row['feedback']            ?? null,
            $row['note']                ?? null,
            $row['nb_places_reservees'] ?? 1,
            $row['statut_paiement']     ?? 'GRATUIT',
            $row['reference_paiement']  ?? null,
            $row['montant_paye']        ?? 0.00
        );
        $p->setIdParticipation($row['id_participation']);
        if (isset($row['evenement_titre'])) {
            $p->setEvenementTitre($row['evenement_titre']);
        }
        return $p;
    }

    // ===== AJOUTER participation =====
    public function addParticipation(Participation $participation, $evenement = null)
    {
        $sql = "INSERT INTO participation 
                    (id_evenement, id_user, nom, email, telephone, statut,
                     statut_paiement, reference_paiement, montant_paye,
                     feedback, note, nb_places_reservees)
                VALUES 
                    (:id_evenement, :id_user, :nom, :email, :telephone, :statut,
                     :statut_paiement, :reference_paiement, :montant_paye,
                     :feedback, :note, :nb_places_reservees)";
        $db = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute([
                'id_evenement'        => $participation->getIdEvenement(),
                'id_user'             => $participation->getIdUser(),
                'nom'                 => $participation->getNom(),
                'email'               => $participation->getEmail(),
                'telephone'           => $participation->getTelephone(),
                'statut'              => $participation->getStatut(),
                'statut_paiement'     => $participation->getStatutPaiement(),
                'reference_paiement'  => $participation->getReferencePaiement(),
                'montant_paye'        => $participation->getMontantPaye(),
                'feedback'            => $participation->getFeedback(),
                'note'                => $participation->getNote(),
                'nb_places_reservees' => $participation->getNbPlacesReservees() ?? 1,
            ]);
            $newId = $db->lastInsertId();
            $participation->setIdParticipation($newId);

            // Générer QR Code avec API externe
            $this->genererQRCode($participation);

            // Envoyer email de confirmation
            if ($evenement) {
                $this->envoyerEmailConfirmation($participation, $evenement);
            }

            return $newId;
        } catch (\PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return false;
        }
    }

    // ===== GÉNÉRER QR CODE avec API externe (pas besoin de GD) =====
    public function genererQRCode(Participation $p)
    {
        $dir = __DIR__ . '/../assets/qrcodes/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        // Données à encoder dans le QR code
        $qrData = json_encode([
            'type' => 'participation',
            'id' => $p->getIdParticipation(),
            'nom' => $p->getNom(),
            'email' => $p->getEmail(),
            'telephone' => $p->getTelephone(),
            'evenement_id' => $p->getIdEvenement(),
            'places' => $p->getNbPlacesReservees(),
            'paiement' => $p->getStatutPaiement(),
            'reference' => $p->getReferencePaiement(),
            'date' => date('Y-m-d H:i:s')
        ]);

        // Utiliser l'API gratuite de QR Server
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&margin=10&data=" . urlencode($qrData);
        
        // Télécharger et sauvegarder l'image
        $fichier = $dir . 'participation_' . $p->getIdParticipation() . '.png';
        
        try {
            $ch = curl_init($qrCodeUrl);
            $fp = fopen($fichier, 'wb');
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            fclose($fp);
            
            if ($httpCode == 200 && file_exists($fichier) && filesize($fichier) > 0) {
                return $fichier;
            } else {
                // Fallback: créer un QR code texte simple si l'API échoue
                return $this->genererQRCodeFallback($p, $fichier);
            }
        } catch (\Exception $e) {
            error_log('QR Code API error: ' . $e->getMessage());
            return $this->genererQRCodeFallback($p, $fichier);
        }
    }

    // Fallback: créer un fichier texte avec les informations
    private function genererQRCodeFallback(Participation $p, $fichier)
    {
        $content = "=== NUTRILOOP ===\n";
        $content .= "Participation #" . $p->getIdParticipation() . "\n";
        $content .= "Nom: " . $p->getNom() . "\n";
        $content .= "Email: " . $p->getEmail() . "\n";
        $content .= "Tél: " . ($p->getTelephone() ?? '-') . "\n";
        $content .= "Événement ID: " . $p->getIdEvenement() . "\n";
        $content .= "Places: " . $p->getNbPlacesReservees() . "\n";
        $content .= "Paiement: " . $p->getStatutPaiement() . "\n";
        $content .= "Réf: " . ($p->getReferencePaiement() ?? '-') . "\n";
        $content .= "Date: " . date('Y-m-d H:i:s') . "\n";
        
        // Sauvegarder en fichier texte
        $txtFile = str_replace('.png', '.txt', $fichier);
        file_put_contents($txtFile, $content);
        
        // Créer une image simple avec du texte si GD est disponible
        if (extension_loaded('gd')) {
            return $this->genererQRCodeLocal($p, $fichier);
        }
        
        return $txtFile;
    }

    // Génération locale si GD est disponible
    private function genererQRCodeLocal(Participation $p, $fichier)
    {
        $data = "=== NutriLoop ===\n"
              . "Participation #" . $p->getIdParticipation() . "\n"
              . "Nom: " . $p->getNom() . "\n"
              . "Email: " . $p->getEmail();
        
        // Créer une image simple
        $size = 300;
        $img = imagecreate($size, $size);
        $bg = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        // Dessiner des carrés pour simuler un QR code simplifié
        for ($i = 0; $i < min(100, strlen($data)); $i++) {
            $x = ($i % 20) * 15;
            $y = floor($i / 20) * 15;
            imagefilledrectangle($img, $x, $y, $x + 12, $y + 12, $black);
        }
        
        // Ajouter du texte
        $text = "ID: " . $p->getIdParticipation();
        imagestring($img, 5, 10, $size - 30, $text, $black);
        
        imagepng($img, $fichier);
        imagedestroy($img);
        
        return $fichier;
    }

    public function getQRCodeUrl($id_participation)
    {
        $rel = 'assets/qrcodes/participation_' . $id_participation . '.png';
        $fullPath = __DIR__ . '/../' . $rel;
        
        if (file_exists($fullPath)) {
            return $rel;
        }
        
        // Vérifier si le fichier texte existe
        $txtRel = 'assets/qrcodes/participation_' . $id_participation . '.txt';
        if (file_exists(__DIR__ . '/../' . $txtRel)) {
            return $txtRel;
        }
        
        return null;
    }

    // ===== ENVOYER EMAIL AVEC PHPMAILER =====
    public function envoyerEmailConfirmation(Participation $p, $evenement)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'chaima123melki@gmail.com';
            $mail->Password   = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('chaima123melki@gmail.com', 'NutriLoop');
            $mail->addAddress($p->getEmail(), $p->getNom());
            $mail->Subject = 'Confirmation — ' . $evenement->getTitre();

            $prix = $evenement->isPayant()
                ? number_format($evenement->getPrix(), 2) . ' TND'
                : 'Gratuit';

            $paiement = match($p->getStatutPaiement()) {
                'PAYE'       => 'Payé - Ref : ' . $p->getReferencePaiement(),
                'GRATUIT'    => 'Gratuit',
                'EN_ATTENTE' => 'En attente de paiement',
                default      => $p->getStatutPaiement(),
            };

            $qrCodeUrl = $this->getQRCodeUrl($p->getIdParticipation());
            $qrCodeTag = '';
            if ($qrCodeUrl) {
                $fullUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/NutriLoop/' . $qrCodeUrl;
                $qrCodeTag = '<div class="qrs">
                                <img src="' . $fullUrl . '" width="150" height="150" alt="QR Code">
                                <p>Scannez ce QR Code pour voir vos informations de participation</p>
                              </div>';
            } else {
                $qrCodeTag = '<div class="qrs">
                                <p>📱 Votre code QR sera disponible dans votre espace personnel</p>
                              </div>';
            }

            $html = '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><style>
body{font-family:Segoe UI,Arial,sans-serif;background:#f0f2f5;margin:0;padding:20px;}
.card{background:white;border-radius:16px;max-width:560px;margin:0 auto;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1);}
.hdr{background:linear-gradient(135deg,#2196F3,#4CAF50);padding:28px;text-align:center;}
.hdr h1{color:white;margin:0;font-size:20px;}
.hdr p{color:rgba(255,255,255,.8);margin:6px 0 0;font-size:13px;}
.bdy{padding:24px;}
.kpis{display:flex;gap:10px;margin:16px 0;}
.kpi{flex:1;background:#f5f5f5;border-radius:10px;padding:12px;text-align:center;border-left:3px solid #2196F3;}
.kpi strong{display:block;font-size:17px;color:#1a1a2e;}
.kpi small{font-size:10px;color:#888;text-transform:uppercase;}
.row{display:flex;gap:8px;padding:9px 0;border-bottom:1px solid #f0f0f0;}
.lbl{color:#888;font-size:12px;min-width:110px;}
.val{font-size:13px;color:#1a1a2e;font-weight:600;}
.qrs{text-align:center;margin:20px 0;padding:16px;background:#f8f9fa;border-radius:10px;}
.qrs p{color:#555;font-size:12px;margin:10px 0 0;}
.ftr{background:#1a1a2e;padding:14px;text-align:center;}
.ftr p{color:rgba(255,255,255,.4);font-size:11px;margin:0;}
</style></head><body>
<div class="card">
  <div class="hdr"><h1>Inscription confirmée</h1><p>Votre participation à NutriLoop a bien été enregistrée</p></div>
  <div class="bdy">
    <div class="kpis">
      <div class="kpi"><strong>#' . $p->getIdParticipation() . '</strong><small>Référence</small></div>
      <div class="kpi"><strong>' . $p->getNbPlacesReservees() . '</strong><small>Place(s)</small></div>
      <div class="kpi" style="border-color:#4CAF50"><strong>' . $prix . '</strong><small>Tarif</small></div>
    </div>
    <div class="row"><span class="lbl">Événement</span><span class="val">' . htmlspecialchars($evenement->getTitre()) . '</span></div>
    <div class="row"><span class="lbl">Date</span><span class="val">' . date('d/m/Y', strtotime($evenement->getDateEvenement())) . '</span></div>
    <div class="row"><span class="lbl">Lieu</span><span class="val">' . htmlspecialchars($evenement->getLieu()) . '</span></div>
    <div class="row"><span class="lbl">Participant</span><span class="val">' . htmlspecialchars($p->getNom()) . '</span></div>
    <div class="row"><span class="lbl">Paiement</span><span class="val">' . $paiement . '</span></div>
    ' . $qrCodeTag . '
  </div>
  <div class="ftr"><p>NutriLoop - Plateforme intelligente pour une alimentation durable</p></div>
</div></body></html>';

            $mail->isHTML(true);
            $mail->Body    = $html;
            $mail->AltBody = "Inscription confirmée — " . $evenement->getTitre() . " — " . $p->getNom();

            $mail->send();
            return true;
        } catch (\Exception $e) {
            error_log('Erreur email: ' . $e->getMessage());
            return false;
        }
    }

    // ===== LISTER =====
    public function listParticipations()
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre FROM participation pa LEFT JOIN event e ON pa.id_evenement = e.id_evenement ORDER BY pa.date_inscription DESC";
        $db = Config::getConnexion();
        try { $q = $db->prepare($sql); $q->execute(); $rows = $q->fetchAll(); $result = []; foreach ($rows as $row) $result[] = $this->rowToParticipation($row); return $result; }
        catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return []; }
    }

    public function getParticipationById($id)
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre FROM participation pa LEFT JOIN event e ON pa.id_evenement = e.id_evenement WHERE pa.id_participation = :id";
        $db = Config::getConnexion();
        try { $q = $db->prepare($sql); $q->execute(['id' => $id]); $row = $q->fetch(); return $row ? $this->rowToParticipation($row) : null; }
        catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return null; }
    }

    public function getParticipationsByEvenement($id_evenement)
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre FROM participation pa LEFT JOIN event e ON pa.id_evenement = e.id_evenement WHERE pa.id_evenement = :id_evenement ORDER BY pa.date_inscription DESC";
        $db = Config::getConnexion();
        try { $q = $db->prepare($sql); $q->execute(['id_evenement' => $id_evenement]); $rows = $q->fetchAll(); $result = []; foreach ($rows as $row) $result[] = $this->rowToParticipation($row); return $result; }
        catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return []; }
    }

    public function getParticipationsByUser($id_user)
    {
        $sql = "SELECT pa.*, e.titre AS evenement_titre FROM participation pa LEFT JOIN event e ON pa.id_evenement = e.id_evenement WHERE pa.id_user = :id_user ORDER BY pa.date_inscription DESC";
        $db = Config::getConnexion();
        try { $q = $db->prepare($sql); $q->execute(['id_user' => $id_user]); $rows = $q->fetchAll(); $result = []; foreach ($rows as $row) $result[] = $this->rowToParticipation($row); return $result; }
        catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return []; }
    }

    public function updateParticipation(Participation $p)
    {
        $sql = "UPDATE participation SET nom=:nom, email=:email, telephone=:telephone, statut=:statut, statut_paiement=:statut_paiement, reference_paiement=:reference_paiement, montant_paye=:montant_paye, feedback=:feedback, note=:note, nb_places_reservees=:nb_places_reservees WHERE id_participation=:id";
        $db = Config::getConnexion();
        try {
            $q = $db->prepare($sql);
            $q->execute([
                'id'                  => $p->getIdParticipation(),
                'nom'                 => $p->getNom(),
                'email'               => $p->getEmail(),
                'telephone'           => $p->getTelephone(),
                'statut'              => $p->getStatut(),
                'statut_paiement'     => $p->getStatutPaiement(),
                'reference_paiement'  => $p->getReferencePaiement(),
                'montant_paye'        => $p->getMontantPaye(),
                'feedback'            => $p->getFeedback(),
                'note'                => $p->getNote(),
                'nb_places_reservees' => $p->getNbPlacesReservees() ?? 1,
            ]);
            return true;
        } catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return false; }
    }

    public function deleteParticipation($id)
    {
        $qrPath = __DIR__ . '/../assets/qrcodes/participation_' . $id . '.png';
        if (file_exists($qrPath)) unlink($qrPath);
        $txtPath = __DIR__ . '/../assets/qrcodes/participation_' . $id . '.txt';
        if (file_exists($txtPath)) unlink($txtPath);
        
        $sql = "DELETE FROM participation WHERE id_participation = :id";
        $db = Config::getConnexion();
        try { $q = $db->prepare($sql); $q->execute(['id' => $id]); return true; }
        catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return false; }
    }

    public function getAllEvenements()
    {
        $sql = "SELECT id_evenement, titre, prix FROM event ORDER BY titre ASC";
        $db = Config::getConnexion();
        try { $q = $db->prepare($sql); $q->execute(); return $q->fetchAll(); }
        catch (\Exception $e) { error_log('Error: ' . $e->getMessage()); return []; }
    }
}
?>