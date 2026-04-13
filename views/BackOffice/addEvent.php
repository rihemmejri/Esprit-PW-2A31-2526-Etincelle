<?php
include '../../controleurs/EventController.php';
require_once __DIR__ . '/../../models/event.php';

$error = "";
$success = "";
$eventController = new EventController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST["titre"]) &&
        isset($_POST["description"]) &&
        isset($_POST["type_evenement"]) &&
        isset($_POST["date_evenement"]) &&
        isset($_POST["lieu"]) &&
        isset($_POST["nb_places_max"])
    ) {

        if (
            !empty($_POST["titre"]) &&
            !empty($_POST["description"]) &&
            !empty($_POST["type_evenement"]) &&
            !empty($_POST["date_evenement"]) &&
            !empty($_POST["lieu"]) &&
            !empty($_POST["nb_places_max"])
        ) {

            $event = new Event(
                htmlspecialchars($_POST['titre']),
                htmlspecialchars($_POST['description']),
                htmlspecialchars($_POST['type_evenement']),
                $_POST['date_evenement'],
                htmlspecialchars($_POST['lieu']),
                intval($_POST['nb_places_max']),
                "ACTIF"
            );

            $eventController->addEvent($event);

            header('Location: eventList.php');
            exit;

        } else {
            $error = "Tous les champs obligatoires doivent être remplis.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter Event - NutriLoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/nut-style.css">

</head>

<script src="js/event.js"></script>

<body>

<div class="container-form">
    <h1><i class="fas fa-calendar-plus"></i> Ajouter un événement</h1>

    <?php if ($error): ?>
        <p style="color:red;"> <?= $error ?> </p>
    <?php endif; ?>

<form method="POST" action="addEvent.php" id="eventForm">

        <label>Titre *</label>
        <input type="text" name="titre" required>

        <label>Description *</label>
        <textarea name="description" required></textarea>

        <label>Type *</label>
        <select name="type_evenement" required>
            <option value="SPORT">SPORT</option>
            <option value="NUTRITION">NUTRITION</option>
            <option value="WORKSHOP">WORKSHOP</option>
            <option value="AUTRE">AUTRE</option>
        </select>

        <label>Date *</label>
        <input type="date" name="date_evenement" required>

        <label>Lieu *</label>
        <input type="text" name="lieu" required>

        <label>Nombre de places *</label>
        <input type="number" name="nb_places_max" min="1" required>

        <button type="submit">
            <i class="fas fa-save"></i> Ajouter
        </button>

        <a href="eventList.php">Annuler</a>

    </form>
</div>

</body>
</html>