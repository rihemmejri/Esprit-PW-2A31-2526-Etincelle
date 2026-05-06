<?php
include '../../controleurs/EventController.php';
require_once __DIR__ . '/../../models/event.php';

$eventController = new EventController();
$id = $_GET['id'] ?? null;

if ($id) {
    // confirmation
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $eventController->deleteEvent($id);
        header('Location: eventList.php');
        exit;
    }
} else {
    header('Location: eventList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation suppression - Event</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="../assets/css/nut-style.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f7fa;
        }

        .container-delete {
            max-width: 500px;
            width: 100%;
        }

        .confirmation-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .warning-icon {
            font-size: 50px;
            color: #ff4d4d;
            margin-bottom: 15px;
        }

        .warning-text {
            background: #fff3f3;
            padding: 10px;
            border-radius: 10px;
            margin: 15px 0;
            font-size: 14px;
            color: #b30000;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 15px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .btn-danger {
            background: #ff4d4d;
            color: white;
        }

        .btn-secondary {
            background: #ccc;
            color: black;
        }
    </style>
</head>

<body>

<div class="container-delete">
    <div class="confirmation-card">

        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h2>⚠️ Confirmation suppression</h2>

        <p>Are you sure you want to delete this event?</p>

        <div class="warning-text">
            <i class="fas fa-info-circle"></i>
            This action is irreversible. The event will be permanently deleted.
        </div>

        <div class="actions">
            <a href="deleteEvent.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger">
                <i class="fas fa-trash"></i> Oui, supprimer
            </a>

            <a href="eventList.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
            </a>
        </div>

    </div>
</div>

</body>
</html>