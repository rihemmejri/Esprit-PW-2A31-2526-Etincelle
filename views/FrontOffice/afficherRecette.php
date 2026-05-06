<?php
session_start();
include '../../controleurs/RecetteController.php';
require_once __DIR__ . '/../../models/recette.php';

$recetteController = new RecetteController();

// ========== SYSTÈME DE LIKES ==========
if (!isset($_SESSION['recette_likes'])) {
    $_SESSION['recette_likes'] = [];
}
if (!isset($_SESSION['compteur_likes'])) {
    $_SESSION['compteur_likes'] = [];
}

// ========== SYSTÈME DE RÉACTIONS ÉMOJI ==========
if (!isset($_SESSION['recette_reactions'])) {
    $_SESSION['recette_reactions'] = [];
}
if (!isset($_SESSION['compteur_reactions'])) {
    $_SESSION['compteur_reactions'] = [];
}

// Les réactions disponibles avec leurs emojis
$reactionsDisponibles = [
    'love' => '❤️',
    'like' => '👍',
    'wow' => '😮',
    'sad' => '😢',
    'angry' => '😠',
    'laugh' => '😂',
    'cool' => '😎',
    'yummy' => '😋'
];

// Gestion des likes AJAX
if (isset($_POST['action']) && $_POST['action'] == 'like') {
    header('Content-Type: application/json');
    $idRecette = $_POST['id_recette'] ?? null;
    
    if ($idRecette) {
        $dejaLike = isset($_SESSION['recette_likes'][$idRecette]) && $_SESSION['recette_likes'][$idRecette] === true;
        
        if ($dejaLike) {
            unset($_SESSION['recette_likes'][$idRecette]);
            $_SESSION['compteur_likes'][$idRecette]--;
            $liked = false;
        } else {
            $_SESSION['recette_likes'][$idRecette] = true;
            $_SESSION['compteur_likes'][$idRecette] = isset($_SESSION['compteur_likes'][$idRecette]) ? $_SESSION['compteur_likes'][$idRecette] + 1 : 1;
            $liked = true;
        }
        
        echo json_encode(['success' => true, 'liked' => $liked, 'likes_count' => $_SESSION['compteur_likes'][$idRecette]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID manquant']);
    }
    exit;
}

// Gestion des réactions émoji AJAX
if (isset($_POST['action']) && $_POST['action'] == 'add_reaction') {
    header('Content-Type: application/json');
    $idRecette = $_POST['id_recette'] ?? null;
    $reactionType = $_POST['reaction_type'] ?? null;
    
    if ($idRecette && $reactionType) {
        $ancienneReaction = isset($_SESSION['recette_reactions'][$idRecette]) ? $_SESSION['recette_reactions'][$idRecette] : null;
        
        if ($ancienneReaction && $ancienneReaction != $reactionType) {
            if (isset($_SESSION['compteur_reactions'][$idRecette][$ancienneReaction])) {
                $_SESSION['compteur_reactions'][$idRecette][$ancienneReaction]--;
                if ($_SESSION['compteur_reactions'][$idRecette][$ancienneReaction] <= 0) {
                    unset($_SESSION['compteur_reactions'][$idRecette][$ancienneReaction]);
                }
            }
        }
        
        if ($ancienneReaction == $reactionType) {
            unset($_SESSION['recette_reactions'][$idRecette]);
            if (isset($_SESSION['compteur_reactions'][$idRecette][$reactionType])) {
                $_SESSION['compteur_reactions'][$idRecette][$reactionType]--;
                if ($_SESSION['compteur_reactions'][$idRecette][$reactionType] <= 0) {
                    unset($_SESSION['compteur_reactions'][$idRecette][$reactionType]);
                }
            }
            $reactionAdded = false;
        } else {
            $_SESSION['recette_reactions'][$idRecette] = $reactionType;
            if (!isset($_SESSION['compteur_reactions'][$idRecette][$reactionType])) {
                $_SESSION['compteur_reactions'][$idRecette][$reactionType] = 0;
            }
            $_SESSION['compteur_reactions'][$idRecette][$reactionType]++;
            $reactionAdded = true;
        }
        
        echo json_encode([
            'success' => true, 
            'reaction_added' => $reactionAdded,
            'reaction_type' => $reactionType,
            'compteurs' => isset($_SESSION['compteur_reactions'][$idRecette]) ? $_SESSION['compteur_reactions'][$idRecette] : [],
            'user_reaction' => isset($_SESSION['recette_reactions'][$idRecette]) ? $_SESSION['recette_reactions'][$idRecette] : null
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Paramètres manquants']);
    }
    exit;
}

// ========== FONCTION VIDÉO YOUTUBE ==========
function getVideoEmbedUrl($nom, $idRecette) {
    $nomLower = strtolower($nom);
    
    if (strpos($nomLower, 'pizza') !== false) {
        return 'https://www.youtube-nocookie.com/embed/sv3TXMSv6Lw';
    }
    if (strpos($nomLower, 'pasta') !== false || strpos($nomLower, 'spaghetti') !== false) {
        return 'https://www.youtube-nocookie.com/embed/3AAdKl1UYZs';
    }
    if (strpos($nomLower, 'omelette') !== false) {
        return 'https://www.youtube-nocookie.com/embed/ixpYIgHlU60';
    }
    if (strpos($nomLower, 'tajine') !== false) {
        return 'https://www.youtube-nocookie.com/embed/FD5HeQTwdAg';
    }
    if (strpos($nomLower, 'salade') !== false) {
        return 'https://www.youtube-nocookie.com/embed/k8psC0RfYqg';
    }
    if (strpos($nomLower, 'gateau') !== false) {
        return 'https://www.youtube-nocookie.com/embed/H-PxDQf-_Zg';
    }
    if (strpos($nomLower, 'riz') !== false) {
        return 'https://www.youtube-nocookie.com/embed/PL6fQYO-AP8';
    }
    if (strpos($nomLower, 'glace') !== false) {
        return 'https://www.youtube-nocookie.com/embed/oJXvZR6D9Yo';
    }
    if (strpos($nomLower, 'poisson') !== false) {
        return 'https://www.youtube-nocookie.com/embed/8X1NTutTsFg';
    }
    if (strpos($nomLower, 'poulet') !== false) {
        return 'https://www.youtube-nocookie.com/embed/njJ8FQXZmcE';
    }
    if (strpos($nomLower, 'burger') !== false) {
        return 'https://www.youtube-nocookie.com/embed/3KzrBUGLI0w';
    }
    if (strpos($nomLower, 'crepe') !== false) {
        return 'https://www.youtube-nocookie.com/embed/UzzB0wlF-Dc';
    }
    if (strpos($nomLower, 'tiramisu') !== false) {
        return 'https://www.youtube-nocookie.com/embed/MeQuGbEyh5s';
    }
    
    return 'https://www.youtube-nocookie.com/embed/wdQ7EUoLNfU';
}

// ========== FONCTION GIF SPÉCIFIQUE POUR CHAQUE RECETTE ==========
function getRecipeGif($nom) {
    $nomLower = strtolower($nom);
    
    if (strpos($nomLower, 'pizza') !== false) {
        return 'https://usagif.com/wp-content/uploads/gifs/pizza-116.gif';
    }
     // GIF spécifique pour la glace
    if (strpos($nomLower, 'glace') !== false || strpos($nomLower, 'ice cream') !== false) {
        return 'https://img1.picmix.com/output/pic/normal/3/2/0/0/2990023_07367.gif';
    }
    if (strpos($nomLower, 'pasta') !== false || strpos($nomLower, 'spaghetti') !== false) {
        return 'https://media1.tenor.com/images/5dbb7ef8a9cd259cb55e8e04c035fac5/tenor.gif?itemid=13601285';
    }
    if (strpos($nomLower, 'tajine') !== false) {
        return 'https://upload.wikimedia.org/wikipedia/commons/4/45/Tajine_marocain.jpg';
    }
    if (strpos($nomLower, 'omelette') !== false || strpos($nomLower, 'oeuf') !== false) {
        return 'https://media.tenor.com/xAcZVVqwgaoAAAAM/frittata.gif';
    }
    if (strpos($nomLower, 'gateau') !== false || strpos($nomLower, 'cake') !== false) {
        return 'https://usagif.com/wp-content/uploads/tort-52.gif';
    }
    if (strpos($nomLower, 'burger') !== false) {
        return 'https://c.tenor.com/DEmFvNuZJV8AAAAC/burger.gif';
    }
    if (strpos($nomLower, 'poulet') !== false) {
        return 'https://media.tenor.com/n2rwbSLNmwQAAAAM/chicken-dance.gif';
    }
    if (strpos($nomLower, 'poisson') !== false) {
        return 'https://th.bing.com/th/id/R.441df77744f6d26376235d1e83632cfa?rik=XtgfRDkMoPqIAw&pid=ImgRaw&r=0';
    }
    if (strpos($nomLower, 'salade') !== false) {
        return 'https://i.gifer.com/origin/b9/b9fb275c7145fc20d4ef6abcaca68c05_w200.gif';
    }
    if (strpos($nomLower, 'crepe') !== false) {
        return 'https://th.bing.com/th/id/R.884e6280e0125d14737d18260f5c079a?rik=8z24XJcZ%2bFhgMg&pid=ImgRaw&r=0';
    }
    if (strpos($nomLower, 'riz') !== false) {
        return 'https://c.tenor.com/EAma8EsrrNIAAAAM/rice-chef-chris-cho.gif';
    }
    if (strpos($nomLower, 'tiramisu') !== false) {
        return 'https://media.tenor.com/OmuIaSuPXGcAAAAM/tiramisu-dessert.gif';
    }
    
    return 'https://media4.giphy.com/media/3o7abB06u9bNzA8LC8/giphy.gif';
}

// ========== FONCTION IMAGE ==========
function getImageUrl($nom) {
    if (strpos($nom, 'pizza') !== false) {
        return 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'tajine') !== false) {
        return 'https://images.unsplash.com/photo-1601050690597-df0568f70950?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'omelette') !== false || strpos($nom, 'oeuf') !== false) {
        return 'https://images.unsplash.com/photo-1626978378685-1fd1c2f63d9b?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'tiramisu') !== false) {
        return 'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'poisson') !== false || strpos($nom, 'fish') !== false) {
        return 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'salade') !== false) {
        return 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'gateau') !== false || strpos($nom, 'cake') !== false) {
        return 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'pasta') !== false || strpos($nom, 'spaghetti') !== false) {
        return 'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'riz') !== false) {
        return 'https://images.unsplash.com/photo-1536304993881-ff6e9eefa2a6?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'poulet') !== false) {
        return 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'glace') !== false || strpos($nom, 'ice cream') !== false) {
        return 'https://images.unsplash.com/photo-1576506295286-5cda18df43e7?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'soupe') !== false) {
        return 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'burger') !== false) {
        return 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=800&h=500&fit=crop';
    } elseif (strpos($nom, 'crepe') !== false || strpos($nom, 'crêpe') !== false) {
        // هاد هي الصورة ديال الكريب - تقدر تبدلها بأي صورة من فوق
        return 'https://cdn.beyondthebayoublog.com/wp-content/uploads/2024/05/Crepes-Recipe-500x375.png';
    } elseif (strpos($nom, 'couscous') !== false) {
        return 'https://images.unsplash.com/photo-1617098900591-3f4c9b9f5a1a?w=800&h=500&fit=crop';
    } else {
        return 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=800&h=500&fit=crop';
    }
}

$recettes = $recetteController->listRecettes();
$reactionsDisponiblesJson = json_encode($reactionsDisponibles);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Recettes - NutriLoop</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        
        .header { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 40px; max-width: 1200px; margin: 0 auto; }
        .logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .logo-img { width: 40px; height: 40px; border-radius: 10px; }
        .logo-text { font-size: 1.5rem; font-weight: 700; color: #2e7d32; }
        .nav-menu { display: flex; list-style: none; gap: 30px; }
        .nav-menu li a { text-decoration: none; color: #1a1a2e; font-weight: 500; transition: 0.3s; }
        .nav-menu li a:hover, .nav-menu li a.active { color: #4CAF50; }
        .btn-dashboard { background: #1a1a2e; color: white !important; padding: 8px 20px; border-radius: 25px; }
        .btn-dashboard:hover { background: #388e3c; }
        .hamburger { display: none; flex-direction: column; cursor: pointer; }
        .hamburger span { width: 25px; height: 3px; background: #333; margin: 3px 0; transition: 0.3s; }
        
        .hero-section { background: linear-gradient(135deg, #e8f5e9, #c8e6c9); padding: 60px 20px; text-align: center; margin: 20px; border-radius: 30px; }
        .hero-section h1 { font-size: 2rem; color: #2e7d32; }
        
        .filters-section { background: white; padding: 20px; margin: 0 20px 30px; border-radius: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .filters-grid { display: flex; gap: 20px; flex-wrap: wrap; }
        .filter-group { flex: 1; min-width: 200px; }
        .filter-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; }
        .filter-group input, .filter-group select { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 10px; font-family: 'Poppins', sans-serif; }
        
        .recipes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; padding: 0 20px 60px; }
        
        .recipe-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.08); transition: all 0.3s ease; cursor: pointer; }
        .recipe-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        
        .recipe-image { height: 200px; overflow: hidden; position: relative; }
        .recipe-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .recipe-card:hover .recipe-img { transform: scale(1.05); }
        .recipe-badge { position: absolute; top: 15px; right: 15px; background: #4CAF50; color: white; padding: 5px 12px; border-radius: 20px; font-size: 12px; }
        
        .recipe-content { padding: 20px; }
        .recipe-title { font-size: 1.3rem; color: #2e7d32; margin-bottom: 10px; }
        .recipe-meta { display: flex; gap: 15px; margin-bottom: 15px; font-size: 0.8rem; color: #666; }
        .recipe-meta i { margin-right: 5px; color: #4CAF50; }
        .recipe-description { color: #555; line-height: 1.6; margin-bottom: 15px; font-size: 0.9rem; }
        
        .card-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f0f0f0;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .btn-like-card, .btn-gif-card, .btn-reactions {
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 30px;
            transition: 0.3s;
            font-size: 0.85rem;
            background: #f5f5f5;
        }
        
        .btn-like-card i { font-size: 1rem; transition: 0.2s; }
        .btn-like-card.liked i { color: #e53935; }
        .btn-like-card.liked { background: #fff0f0; }
        
        .btn-gif-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .btn-gif-card:hover {
            transform: scale(1.05);
            background: linear-gradient(135deg, #5a67d8, #6b46a0);
        }
        
        .reactions-container {
            position: relative;
            display: inline-block;
        }
        
        .btn-reactions:hover { background: #e8e8e8; }
        
        .reactions-picker {
            position: absolute;
            bottom: 45px;
            left: 0;
            background: white;
            border-radius: 60px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            display: flex;
            gap: 8px;
            padding: 10px 15px;
            z-index: 100;
            animation: fadeInUp 0.2s;
            border: 1px solid #eee;
        }
        
        .reaction-option {
            font-size: 1.6rem;
            cursor: pointer;
            padding: 5px 8px;
            border-radius: 50px;
            transition: 0.2s;
        }
        
        .reaction-option:hover {
            transform: scale(1.3);
            background: #f0f0f0;
        }
        
        .reactions-summary {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
            font-size: 0.7rem;
            color: #666;
        }
        
        .reaction-badge {
            background: #f5f5f5;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .difficulte-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 500; }
        .difficulte-FACILE { background: #e8f5e9; color: #2e7d32; }
        .difficulte-MOYEN { background: #fff3e0; color: #e65100; }
        .difficulte-DIFFICILE { background: #ffebee; color: #c62828; }
        
        .btn-details { background: #4CAF50; color: white; border: none; padding: 8px 16px; border-radius: 25px; cursor: pointer; font-family: 'Poppins', sans-serif; font-weight: 500; transition: all 0.3s; margin-top: 10px; width: 100%; }
        .btn-details:hover { background: #388e3c; transform: scale(1.02); }
        
        /* Modal GIF */
        .gif-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 3000;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        
        .gif-modal-content {
            max-width: 500px;
            width: 90%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            animation: zoomIn 0.3s ease;
            cursor: default;
        }
        
        @keyframes zoomIn {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        
        .gif-modal-header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .gif-modal-header h4 { margin: 0; font-size: 1rem; }
        .gif-modal-close { background: none; border: none; color: white; font-size: 24px; cursor: pointer; }
        .gif-modal-body { padding: 20px; text-align: center; }
        .gif-modal-body img { max-width: 100%; border-radius: 12px; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 2000; justify-content: center; align-items: center; backdrop-filter: blur(8px); }
        .modal-content { background: white; max-width: 1200px; width: 95%; max-height: 90vh; border-radius: 24px; overflow: hidden; animation: slideIn 0.3s ease; display: flex; flex-direction: column; }
        @keyframes slideIn { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { background: linear-gradient(135deg, #4CAF50 0%, #2e7d32 100%); color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; }
        .modal-header h3 { margin: 0; font-size: 1.2rem; }
        .modal-close { background: none; border: none; color: white; font-size: 28px; cursor: pointer; }
        .modal-body-container { display: flex; flex-direction: row; overflow: hidden; max-height: calc(90vh - 60px); }
        .modal-left { flex: 1.2; background: #000; display: flex; flex-direction: column; overflow-y: auto; }
        .modal-image { width: 100%; height: 280px; overflow: hidden; }
        .modal-image img { width: 100%; height: 100%; object-fit: cover; }
        .video-section { padding: 15px; background: #f5f5f5; }
        .video-title { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; color: #2e7d32; font-weight: 600; }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; background: #000; border-radius: 12px; overflow: hidden; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: none; }
        .modal-right { flex: 1; padding: 25px; overflow-y: auto; background: white; }
        .modal-detail { margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; display: flex; flex-wrap: wrap; }
        .modal-detail strong { width: 130px; color: #2e7d32; font-size: 0.9rem; }
        .modal-detail span { flex: 1; color: #444; font-size: 0.95rem; }
        .like-container { display: flex; align-items: center; gap: 15px; margin: 20px 0 15px; flex-wrap: wrap; }
        .btn-like { background: #f5f5f5; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 30px; }
        .btn-like i { font-size: 1.3rem; }
        .btn-like.liked i { color: #e53935; }
        .modal-reactions { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-modal-reaction { background: #f5f5f5; border: none; padding: 8px 15px; border-radius: 30px; cursor: pointer; font-size: 1.1rem; }
        .btn-modal-reaction.active { background: #e8f5e9; border: 2px solid #4CAF50; }
        
        .share-container { display: flex; gap: 12px; justify-content: center; margin: 20px 0; flex-wrap: wrap; }
        .btn-share { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 30px; font-weight: 600; font-size: 0.85rem; cursor: pointer; border: none; }
        .btn-share-instagram { background: linear-gradient(135deg, #feda77, #d62976, #962fbf, #4f5bd5); color: white; }
        .btn-copy-link { background: #4CAF50; color: white; }
        .btn-follow-ig { background: #262626; color: white; }
        
        .toast-message { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: #4CAF50; color: white; padding: 12px 24px; border-radius: 30px; z-index: 9999; animation: fadeInUp 0.3s; }
        @keyframes fadeInUp { from { transform: translateX(-50%) translateY(20px); opacity: 0; } to { transform: translateX(-50%) translateY(0); opacity: 1; } }
        
        .btn-etapes { background: linear-gradient(135deg, #4CAF50 0%, #2e7d32 100%); color: white; padding: 12px 24px; border-radius: 30px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-top: 10px; }
        .no-results { text-align: center; padding: 60px; background: white; border-radius: 20px; grid-column: 1 / -1; }
        
        .footer { background: #1a1a2e; color: white; padding: 60px 40px 20px; margin-top: 40px; }
        .footer-content { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; max-width: 1200px; margin: 0 auto; }
        .footer-section h3, .footer-section h4 { margin-bottom: 20px; color: #4CAF50; }
        .footer-section ul { list-style: none; }
        .footer-section ul li { margin-bottom: 10px; }
        .footer-section ul li a { color: #ccc; text-decoration: none; }
        .footer-section ul li a:hover { color: #4CAF50; }
        .social-links { display: flex; gap: 15px; margin-top: 20px; }
        .social-links a { color: white; background: rgba(255,255,255,0.1); width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .footer-bottom { text-align: center; padding-top: 30px; margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); }
        
        @media (max-width: 768px) {
            .recipes-grid { grid-template-columns: 1fr; }
            .filters-grid { flex-direction: column; }
            .nav-menu { position: fixed; left: -100%; top: 70px; flex-direction: column; background: white; width: 100%; text-align: center; transition: 0.3s; padding: 20px 0; gap: 15px; }
            .nav-menu.active { left: 0; }
            .hamburger { display: flex; }
            .modal-body-container { flex-direction: column; }
            .card-actions { justify-content: center; }
        }
    </style>
</head>
<body>

<header class="header">
    <nav class="navbar">
        <div class="logo">
            <img src="image/logo.PNG" alt="NutriLoop Logo" class="logo-img" onerror="this.src='https://via.placeholder.com/45x45?text=🌱'">
            <span class="logo-text">NutriLoop</span>
        </div>
        <ul class="nav-menu">
            <li><a href="index.html">Accueil</a></li>
            <li><a href="index.html#features">Fonctionnalités</a></li>
            <li><a href="index.html#modules">Modules</a></li>
            <li><a href="about.html">À propos</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="../backoffice/index.html" class="btn-dashboard">Dashboard</a></li>
        </ul>
        <div class="hamburger"><span></span><span></span><span></span></div>
    </nav>
</header>

<div class="hero-section">
    <h1><i class="fas fa-utensils"></i> Nos Recettes Anti-Gaspi</h1>
    <p>Découvrez des recettes saines, équilibrées et délicieuses pour tous les goûts</p>
</div>

<div class="filters-section">
    <div class="filters-title"><i class="fas fa-filter"></i> Filtrer les recettes</div>
    <div class="filters-grid">
        <div class="filter-group"><label><i class="fas fa-search"></i> Rechercher</label><input type="text" id="searchRecette" placeholder="Nom de la recette..." onkeyup="filterRecettes()"></div>
        <div class="filter-group"><label><i class="fas fa-chart-line"></i> Difficulté</label><select id="filterDifficulte" onchange="filterRecettes()"><option value="">Toutes</option><option value="FACILE">Facile</option><option value="MOYEN">Moyen</option><option value="DIFFICILE">Difficile</option></select></div>
        <div class="filter-group"><label><i class="fas fa-mug-hot"></i> Type de repas</label><select id="filterTypeRepas" onchange="filterRecettes()"><option value="">Tous</option><option value="PETIT_DEJEUNER">Petit déjeuner</option><option value="DEJEUNER">Déjeuner</option><option value="DINER">Dîner</option><option value="DESSERT">Dessert</option></select></div>
    </div>
</div>

<div class="recipes-grid" id="recipesGrid">
    <?php if (count($recettes) > 0): ?>
        <?php foreach ($recettes as $recette): 
            $nom = strtolower($recette->getNom());
            $imageUrl = getImageUrl($nom);
            $videoUrl = getVideoEmbedUrl($nom, $recette->getIdRecette());
            $gifUrl = getRecipeGif($nom);
            $likesCount = isset($_SESSION['compteur_likes'][$recette->getIdRecette()]) ? $_SESSION['compteur_likes'][$recette->getIdRecette()] : 0;
            $isLiked = isset($_SESSION['recette_likes'][$recette->getIdRecette()]) && $_SESSION['recette_likes'][$recette->getIdRecette()] === true;
            $userReaction = isset($_SESSION['recette_reactions'][$recette->getIdRecette()]) ? $_SESSION['recette_reactions'][$recette->getIdRecette()] : null;
            $reactionCompteurs = isset($_SESSION['compteur_reactions'][$recette->getIdRecette()]) ? $_SESSION['compteur_reactions'][$recette->getIdRecette()] : [];
        ?>
            <div class="recipe-card" 
                 data-id="<?= $recette->getIdRecette() ?>"
                 data-titre="<?= strtolower(htmlspecialchars($recette->getNom())) ?>"
                 data-difficulte="<?= $recette->getDifficulte() ?>"
                 data-type="<?= $recette->getTypeRepas() ?>"
                 data-nom="<?= htmlspecialchars($recette->getNom()) ?>"
                 data-description="<?= htmlspecialchars($recette->getDescription()) ?>"
                 data-temps="<?= $recette->getTempsPreparation() ?>"
                 data-personnes="<?= $recette->getNbPersonne() ?>"
                 data-origine="<?= htmlspecialchars($recette->getOrigine()) ?>"
                 data-image="<?= $imageUrl ?>"
                 data-video="<?= $videoUrl ?>"
                 data-gif="<?= $gifUrl ?>"
                 data-likes="<?= $likesCount ?>"
                 data-liked="<?= $isLiked ? 'true' : 'false' ?>"
                 data-user-reaction="<?= $userReaction ?>">
                
                <div class="recipe-image">
                    <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($recette->getNom()) ?>" class="recipe-img">
                    <div class="recipe-badge">⭐ NutriLoop</div>
                </div>
                
                <div class="recipe-content">
                    <h3 class="recipe-title"><?= htmlspecialchars($recette->getNom()) ?></h3>
                    <div class="recipe-meta">
                        <span><i class="fas fa-clock"></i> <?= $recette->getTempsPreparation() ?> min</span>
                        <span><i class="fas fa-users"></i> <?= $recette->getNbPersonne() ?> pers</span>
                    </div>
                    <p class="recipe-description"><?= htmlspecialchars(substr($recette->getDescription(), 0, 100)) . (strlen($recette->getDescription()) > 100 ? '...' : '') ?></p>
                    
                    <div class="card-actions">
                        <button class="btn-like-card <?= $isLiked ? 'liked' : '' ?>" onclick="event.stopPropagation(); handleLikeCard(this, <?= $recette->getIdRecette() ?>)">
                            <i class="<?= $isLiked ? 'fas' : 'far' ?> fa-heart"></i>
                            <span class="like-count"><?= $likesCount ?></span>
                        </button>
                        
                        <button class="btn-gif-card" onclick="event.stopPropagation(); showGifModal('<?= addslashes($recette->getNom()) ?>', '<?= $gifUrl ?>')">
                            <i class="fas fa-play-circle"></i> 🎬 GIF
                        </button>
                        
                        <div class="reactions-container">
                            <button class="btn-reactions" onclick="event.stopPropagation(); toggleReactionPicker(this)">
                                <i class="far fa-smile-wink"></i> 
                                <span class="reaction-display">
                                    <?php if ($userReaction && isset($reactionsDisponibles[$userReaction])): ?>
                                        <?= $reactionsDisponibles[$userReaction] ?>
                                    <?php else: ?>
                                        Réagir
                                    <?php endif; ?>
                                </span>
                            </button>
                            <div class="reactions-picker" style="display: none;">
                                <?php foreach ($reactionsDisponibles as $key => $emoji): ?>
                                    <span class="reaction-option" data-reaction="<?= $key ?>" data-emoji="<?= $emoji ?>" onclick="event.stopPropagation(); addReaction(<?= $recette->getIdRecette() ?>, '<?= $key ?>', this)"><?= $emoji ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($reactionCompteurs)): ?>
                    <div class="reactions-summary">
                        <?php foreach ($reactionCompteurs as $reactionType => $count): ?>
                            <?php if ($count > 0 && isset($reactionsDisponibles[$reactionType])): ?>
                                <span class="reaction-badge"><?= $reactionsDisponibles[$reactionType] ?> <?= $count ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <button class="btn-details" onclick='showDetails(this)'>Voir détails <i class="fas fa-arrow-right"></i></button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-results"><i class="fas fa-empty-folder"></i><h3>Aucune recette disponible</h3></div>
    <?php endif; ?>
</div>

<div id="noResults" class="no-results" style="display: none;"><i class="fas fa-search"></i><h3>Aucune recette trouvée</h3></div>

<!-- Modal GIF -->
<div id="gifModal" class="gif-modal" onclick="closeGifModal()">
    <div class="gif-modal-content" onclick="event.stopPropagation()">
        <div class="gif-modal-header">
            <h4><i class="fas fa-play-circle"></i> <span id="gifModalTitle">GIF</span></h4>
            <button class="gif-modal-close" onclick="closeGifModal()">&times;</button>
        </div>
        <div class="gif-modal-body">
            <img id="gifModalImage" src="" alt="GIF">
        </div>
    </div>
</div>

<!-- Modal Détails -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Détails de la recette</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body-container">
            <div class="modal-left">
                <div class="modal-image" id="modalImage"></div>
                <div class="video-section" id="videoSection" style="display: none;">
                    <div class="video-title"><i class="fab fa-youtube" style="color: #FF0000;"></i><span>📺 Tutoriel vidéo</span></div>
                    <div class="video-container" id="videoContainer"></div>
                </div>
            </div>
            <div class="modal-right" id="modalBody"></div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-section"><h3>NutriLoop</h3><p>L'intelligence artificielle au service de votre assiette.</p><div class="social-links"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-twitter"></i></a><a href="#"><i class="fab fa-instagram"></i></a></div></div>
        <div class="footer-section"><h4>Liens rapides</h4><ul><li><a href="index.html">Accueil</a></li><li><a href="about.html">À propos</a></li><li><a href="contact.html">Contact</a></li></ul></div>
        <div class="footer-section"><h4>Modules</h4><ul><li><a href="#">Utilisateurs</a></li><li><a href="#">Nutrition</a></li><li><a href="afficherRecette.php">Recettes</a></li></ul></div>
        <div class="footer-section"><h4>Contact</h4><ul><li><i class="fas fa-map-marker-alt"></i> Tunis, Tunisie</li><li><i class="fas fa-envelope"></i> contact@nutriloop.ai</li></ul></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 NutriLoop - Tous droits réservés | Projet étudiant - Ryhem Mejri</p></div>
</footer>

<script>
const reactionsDisponibles = <?= $reactionsDisponiblesJson ?>;
let currentIframe = null;
let activePicker = null;

function filterRecettes() {
    const searchTerm = document.getElementById('searchRecette').value.toLowerCase();
    const difficulte = document.getElementById('filterDifficulte').value;
    const typeRepas = document.getElementById('filterTypeRepas').value;
    const cards = document.querySelectorAll('.recipe-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        const titre = card.getAttribute('data-titre') || '';
        const cardDifficulte = card.getAttribute('data-difficulte');
        const cardType = card.getAttribute('data-type');
        let match = true;
        if (searchTerm && !titre.includes(searchTerm)) match = false;
        if (difficulte && cardDifficulte !== difficulte) match = false;
        if (typeRepas && cardType !== typeRepas) match = false;
        card.style.display = match ? '' : 'none';
        if(match) visibleCount++;
    });
    document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
}

function showGifModal(recipeName, gifUrl) {
    document.getElementById('gifModalTitle').innerHTML = '🎬 ' + recipeName + ' en action !';
    document.getElementById('gifModalImage').src = gifUrl;
    document.getElementById('gifModal').style.display = 'flex';
}

function closeGifModal() {
    document.getElementById('gifModal').style.display = 'none';
}

function handleLikeCard(btnElement, recetteId) {
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=like&id_recette=' + recetteId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const likeSpan = btnElement.querySelector('.like-count');
            likeSpan.textContent = data.likes_count;
            if(data.liked) {
                btnElement.classList.add('liked');
                btnElement.querySelector('i').classList.remove('far');
                btnElement.querySelector('i').classList.add('fas');
            } else {
                btnElement.classList.remove('liked');
                btnElement.querySelector('i').classList.remove('fas');
                btnElement.querySelector('i').classList.add('far');
            }
            const card = btnElement.closest('.recipe-card');
            if(card) {
                card.setAttribute('data-likes', data.likes_count);
                card.setAttribute('data-liked', data.liked);
            }
        }
    });
}

function toggleReactionPicker(btnElement) {
    event.stopPropagation();
    const container = btnElement.closest('.reactions-container');
    const picker = container.querySelector('.reactions-picker');
    if(activePicker && activePicker !== picker) {
        activePicker.style.display = 'none';
    }
    picker.style.display = picker.style.display === 'none' ? 'flex' : 'none';
    activePicker = picker.style.display === 'flex' ? picker : null;
}

function addReaction(recetteId, reactionType, element) {
    const picker = element.closest('.reactions-picker');
    const container = picker.closest('.reactions-container');
    const btnReaction = container.querySelector('.btn-reactions');
    const reactionDisplay = btnReaction.querySelector('.reaction-display');
    
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add_reaction&id_recette=' + recetteId + '&reaction_type=' + reactionType
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(data.reaction_added) {
                reactionDisplay.innerHTML = reactionsDisponibles[reactionType];
            } else {
                reactionDisplay.innerHTML = 'Réagir';
            }
            const card = container.closest('.recipe-card');
            const reactionsSummary = card.querySelector('.reactions-summary');
            if(data.compteurs && Object.keys(data.compteurs).length > 0) {
                let summaryHtml = '';
                for(const [rType, count] of Object.entries(data.compteurs)) {
                    if(count > 0 && reactionsDisponibles[rType]) {
                        summaryHtml += `<span class="reaction-badge">${reactionsDisponibles[rType]} ${count}</span>`;
                    }
                }
                if(reactionsSummary) {
                    reactionsSummary.innerHTML = summaryHtml;
                    reactionsSummary.style.display = 'flex';
                } else if(summaryHtml) {
                    const newSummary = document.createElement('div');
                    newSummary.className = 'reactions-summary';
                    newSummary.innerHTML = summaryHtml;
                    card.querySelector('.card-actions').after(newSummary);
                }
            } else {
                if(reactionsSummary) reactionsSummary.style.display = 'none';
            }
            card.setAttribute('data-user-reaction', data.user_reaction || '');
            picker.style.display = 'none';
            activePicker = null;
            showToast(data.reaction_added ? 'Réaction ajoutée !' : 'Réaction retirée');
        }
    });
}

document.addEventListener('click', function() {
    if(activePicker) {
        activePicker.style.display = 'none';
        activePicker = null;
    }
});

function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
}

function showToast(message) {
    const oldToast = document.querySelector('.toast-message');
    if(oldToast) oldToast.remove();
    const toast = document.createElement('div');
    toast.className = 'toast-message';
    toast.innerHTML = ' ' + message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

async function shareToInstagram(recetteId, recetteNom, recetteDesc, recetteTemps, recettePersonnes, recetteDiff, recetteImage) {
    const caption = `🍽️ ${recetteNom}\n\n${recetteDesc.substring(0, 120)}...\n\n⏱️ ${recetteTemps} min | 👥 ${recettePersonnes} pers | 📊 ${recetteDiff}\n\n#NutriLoop #RecetteHealthy #AntiGaspi #CuisineFacile\n\n👉 @nutri_loop1`;
    showToast("Préparation du partage...");
    try {
        const response = await fetch(recetteImage);
        const blob = await response.blob();
        const file = new File([blob], `NutriLoop_${recetteNom}.jpg`, { type: 'image/jpeg' });
        if (navigator.share && navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({ title: recetteNom, text: caption, files: [file] });
            showToast("");
        } else {
            const link = document.createElement('a');
            link.download = `NutriLoop_${recetteNom}.jpg`;
            link.href = recetteImage;
            link.click();
            copyToClipboard(caption);
            window.open('https://www.instagram.com/', '_blank');
            showToast("Image téléchargée + Légende copiée !");
        }
    } catch (error) {
        const link = document.createElement('a');
        link.download = `NutriLoop_${recetteNom}.jpg`;
        link.href = recetteImage;
        link.click();
        copyToClipboard(caption);
        window.open('https://www.instagram.com/', '_blank');
        showToast("Image téléchargée + Légende copiée !");
    }
}

function copyRecipeLink(recetteId, recetteNom) {
    const url = window.location.href.split('?')[0] + '?id=' + recetteId;
    copyToClipboard(url);
    showToast(`Lien "${recetteNom}" copié !`);
}

function followOnInstagram() {
    window.open('https://www.instagram.com/nutri_loop1/', '_blank');
}

function showDetails(button) {
    const card = button.closest('.recipe-card');
    const modal = document.getElementById('detailsModal');
    const modalImage = document.getElementById('modalImage');
    const modalBody = document.getElementById('modalBody');
    const videoSection = document.getElementById('videoSection');
    const videoContainer = document.getElementById('videoContainer');
    
    const id = card.getAttribute('data-id');
    const nom = card.getAttribute('data-nom');
    const description = card.getAttribute('data-description');
    const temps = card.getAttribute('data-temps');
    const personnes = card.getAttribute('data-personnes');
    const difficulte = card.getAttribute('data-difficulte');
    const typeRepas = card.getAttribute('data-type');
    const origine = card.getAttribute('data-origine');
    const imageUrl = card.getAttribute('data-image');
    const videoUrl = card.getAttribute('data-video');
    let likesCount = card.getAttribute('data-likes') || 0;
    const isLiked = card.getAttribute('data-liked') === 'true';
    const userReaction = card.getAttribute('data-user-reaction');
    
    const typeRepasText = { 'PETIT_DEJEUNER': 'Petit déjeuner', 'DEJEUNER': 'Déjeuner', 'DINER': 'Dîner', 'DESSERT': 'Dessert' };
    const difficulteText = { 'FACILE': 'Facile', 'MOYEN': 'Moyen', 'DIFFICILE': 'Difficile' };
    
    if(currentIframe) { currentIframe.remove(); currentIframe = null; }
    videoContainer.innerHTML = '';
    modalImage.innerHTML = `<img src="${imageUrl}" alt="${nom}">`;
    
    if(videoUrl && videoUrl !== '') {
        videoSection.style.display = 'block';
        const iframe = document.createElement('iframe');
        iframe.src = videoUrl + '?rel=0';
        iframe.setAttribute('allowfullscreen', 'true');
        iframe.style.width = '100%';
        iframe.style.height = '100%';
        iframe.style.position = 'absolute';
        iframe.style.top = '0';
        iframe.style.left = '0';
        videoContainer.appendChild(iframe);
        currentIframe = iframe;
    } else {
        videoSection.style.display = 'none';
    }
    
    let reactionsButtonsHtml = '';
    for(const [rKey, rEmoji] of Object.entries(reactionsDisponibles)) {
        const activeClass = (userReaction === rKey) ? 'active' : '';
        reactionsButtonsHtml += `<button class="btn-modal-reaction ${activeClass}" onclick="addModalReaction(${id}, '${rKey}', this)">${rEmoji}</button>`;
    }
    
    modalBody.innerHTML = `
        <div class="modal-detail"><strong><i class="fas fa-utensils"></i> Nom :</strong> <span>${nom}</span></div>
        <div class="modal-detail"><strong><i class="fas fa-align-left"></i> Description :</strong> <span>${description}</span></div>
        <div class="modal-detail"><strong><i class="fas fa-clock"></i> Temps :</strong> <span>${temps} minutes</span></div>
        <div class="modal-detail"><strong><i class="fas fa-users"></i> Personnes :</strong> <span>${personnes}</span></div>
        <div class="modal-detail"><strong><i class="fas fa-chart-line"></i> Difficulté :</strong> <span>${difficulteText[difficulte] || difficulte}</span></div>
        <div class="modal-detail"><strong><i class="fas fa-mug-hot"></i> Type :</strong> <span>${typeRepasText[typeRepas] || typeRepas}</span></div>
        ${origine && origine !== 'null' && origine !== '' ? `<div class="modal-detail"><strong><i class="fas fa-globe"></i> Origine :</strong> <span>${origine}</span></div>` : '<div class="modal-detail"><strong><i class="fas fa-globe"></i> Origine :</strong> <span>Non spécifiée</span></div>'}
        
        <div class="like-container">
            <button class="btn-like ${isLiked ? 'liked' : ''}" onclick="handleLikeModal(${id}, this)">
                <i class="${isLiked ? 'fas' : 'far'} fa-heart"></i>
                <span class="like-count">${likesCount}</span>
            </button>
            <div class="modal-reactions">${reactionsButtonsHtml}</div>
        </div>
        
        <div class="share-container">
            <button class="btn-share btn-share-instagram" onclick="shareToInstagram(${id}, '${nom.replace(/'/g, "\\'")}', '${description.replace(/'/g, "\\'")}', ${temps}, ${personnes}, '${difficulteText[difficulte] || difficulte}', '${imageUrl}')">
                <i class="fab fa-instagram"></i> 📤 Partager
            </button>
            <button class="btn-share btn-copy-link" onclick="copyRecipeLink(${id}, '${nom.replace(/'/g, "\\'")}')">
                <i class="fas fa-link"></i> 🔗 Copier le lien
            </button>
            <button class="btn-share btn-follow-ig" onclick="followOnInstagram()">
                <i class="fab fa-instagram"></i> 👥 Suivre @nutri_loop1
            </button>
        </div>
        
        <div style="text-align: center;">
            <a href="afficherPreperation.php?id=${id}" class="btn-etapes">
                <i class="fas fa-list-ol"></i> Voir les étapes de préparation
            </a>
        </div>
    `;
    
    modal.style.display = 'flex';
}

function handleLikeModal(recetteId, btnElement) {
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=like&id_recette=' + recetteId
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const likeSpan = btnElement.querySelector('.like-count');
            likeSpan.textContent = data.likes_count;
            if(data.liked) {
                btnElement.classList.add('liked');
                btnElement.querySelector('i').classList.remove('far');
                btnElement.querySelector('i').classList.add('fas');
            } else {
                btnElement.classList.remove('liked');
                btnElement.querySelector('i').classList.remove('fas');
                btnElement.querySelector('i').classList.add('far');
            }
            const card = document.querySelector(`.recipe-card[data-id="${recetteId}"]`);
            if(card) {
                const cardLikeBtn = card.querySelector('.btn-like-card');
                if(cardLikeBtn) {
                    cardLikeBtn.querySelector('.like-count').textContent = data.likes_count;
                    if(data.liked) {
                        cardLikeBtn.classList.add('liked');
                        cardLikeBtn.querySelector('i').classList.remove('far');
                        cardLikeBtn.querySelector('i').classList.add('fas');
                    } else {
                        cardLikeBtn.classList.remove('liked');
                        cardLikeBtn.querySelector('i').classList.remove('fas');
                        cardLikeBtn.querySelector('i').classList.add('far');
                    }
                }
                card.setAttribute('data-likes', data.likes_count);
                card.setAttribute('data-liked', data.liked);
            }
        }
    });
}

function addModalReaction(recetteId, reactionType, btnElement) {
    fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=add_reaction&id_recette=' + recetteId + '&reaction_type=' + reactionType
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            document.querySelectorAll('.btn-modal-reaction').forEach(btn => btn.classList.remove('active'));
            if(data.reaction_added) btnElement.classList.add('active');
            const card = document.querySelector(`.recipe-card[data-id="${recetteId}"]`);
            if(card) {
                const reactionDisplay = card.querySelector('.reaction-display');
                const reactionsSummary = card.querySelector('.reactions-summary');
                if(data.reaction_added) {
                    reactionDisplay.innerHTML = reactionsDisponibles[reactionType];
                } else {
                    reactionDisplay.innerHTML = 'Réagir';
                }
                if(data.compteurs && Object.keys(data.compteurs).length > 0) {
                    let summaryHtml = '';
                    for(const [rType, count] of Object.entries(data.compteurs)) {
                        if(count > 0 && reactionsDisponibles[rType]) {
                            summaryHtml += `<span class="reaction-badge">${reactionsDisponibles[rType]} ${count}</span>`;
                        }
                    }
                    if(reactionsSummary) {
                        reactionsSummary.innerHTML = summaryHtml;
                        reactionsSummary.style.display = 'flex';
                    }
                } else if(reactionsSummary) {
                    reactionsSummary.style.display = 'none';
                }
                card.setAttribute('data-user-reaction', data.user_reaction || '');
            }
            showToast(data.reaction_added ? 'Réaction ajoutée !' : 'Réaction retirée');
        }
    });
}

function closeModal() { 
    const modal = document.getElementById('detailsModal');
    const videoContainer = document.getElementById('videoContainer');
    if(currentIframe) { currentIframe.remove(); currentIframe = null; }
    videoContainer.innerHTML = '';
    modal.style.display = 'none'; 
}

window.onclick = function(event) { 
    if (event.target === document.getElementById('detailsModal')) closeModal(); 
}

document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }
});
</script>
</body>
</html>