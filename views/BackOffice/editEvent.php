<?php
include '../../controleurs/EventController.php';
require_once __DIR__ . '/../../models/event.php';

$eventController = new EventController();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: eventList.php');
    exit;
}

// récupérer event
$event = $eventController->getEventById($id);

if (!$event) {
    header('Location: eventList.php');
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $type_evenement = $_POST['type_evenement'];
    $date_evenement = $_POST['date_evenement'];
    $lieu = $_POST['lieu'];
    $nb_places_max = $_POST['nb_places_max'];
    $statut = $_POST['statut'];

    if (!empty($titre) && !empty($description) && !empty($type_evenement) && !empty($date_evenement) && !empty($lieu)) {

        $event->setTitre($titre);
        $event->setDescription($description);
        $event->setTypeEvenement($type_evenement);
        $event->setDateEvenement($date_evenement);
        $event->setLieu($lieu);
        $event->setNbPlacesMax($nb_places_max);
        $event->setStatut($statut);

        $eventController->updateEvent($event);

        header('Location: eventList.php');
        exit;

    } else {
        $error = "Tous les champs obligatoires doivent être remplis.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Event</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nut-style.css">
</head>

<body>

<div class="container-edit">

    <div class="form-card">

        <div class="header">
            <h1><i class="fas fa-edit"></i> Modifier Event</h1>
            <p>Event ID #<?= $id ?></p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <!-- TITRE -->
            <label>Titre *</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($event->getTitre()) ?>" required>

            <!-- DESCRIPTION -->
            <label>Description *</label>
            <textarea name="description" required><?= htmlspecialchars($event->getDescription()) ?></textarea>

            <!-- TYPE -->
            <label>Type *</label>
            <select name="type_evenement" required>
                <option value="SPORT" <?= $event->getTypeEvenement()=='SPORT'?'selected':'' ?>>SPORT</option>
                <option value="NUTRITION" <?= $event->getTypeEvenement()=='NUTRITION'?'selected':'' ?>>NUTRITION</option>
                <option value="WORKSHOP" <?= $event->getTypeEvenement()=='WORKSHOP'?'selected':'' ?>>WORKSHOP</option>
                <option value="AUTRE" <?= $event->getTypeEvenement()=='AUTRE'?'selected':'' ?>>AUTRE</option>
            </select>

            <!-- DATE -->
            <label>Date *</label>
            <input type="date" name="date_evenement" value="<?= $event->getDateEvenement() ?>" required>

            <!-- LIEU -->
            <label>Lieu *</label>
            <input type="text" name="lieu" value="<?= htmlspecialchars($event->getLieu()) ?>" required>

            <!-- PLACES -->
            <label>Nombre de places</label>
            <input type="number" name="nb_places_max" value="<?= $event->getNbPlacesMax() ?>">

            <!-- STATUT -->
            <label>Statut</label>
            <select name="statut">
                <option value="ACTIF" <?= $event->getStatut()=='ACTIF'?'selected':'' ?>>ACTIF</option>
                <option value="CANCELLED" <?= $event->getStatut()=='CANCELLED'?'selected':'' ?>>CANCELLED</option>
                <option value="COMPLETED" <?= $event->getStatut()=='COMPLETED'?'selected':'' ?>>COMPLETED</option>
            </select>

            <br><br>

            <button type="submit">
                <i class="fas fa-save"></i> Save
            </button>

            <a href="eventList.php">Annuler</a>

        </form>

    </div>

</div>

</body>
</html>