<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../models/produit.php');
include_once(__DIR__ . '/../models/ecommerce_functions.php');

class ProduitController
{
    // Ajouter un produit
    public function addProduit(produit $produit)
    {
        // Calculate eco_score before saving
        $eco_score = calculateEcoScore($produit->toArray());
        $produit->setEcoScore($eco_score);

        $sql = "INSERT INTO produit (nom, image, id_categorie, origine, distance_transport, type_transport, emballage, transformation, saison, prix, stock, eco_score) 
                VALUES (:nom, :image, :id_categorie, :origine, :distance_transport, :type_transport, :emballage, :transformation, :saison, :prix, :stock, :eco_score)";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $produit->getNom(),
                'image' => $produit->getImage(),
                'id_categorie' => $produit->getIdCategorie(),
                'origine' => $produit->getOrigine(),
                'distance_transport' => $produit->getDistanceTransport(),
                'type_transport' => $produit->getTypeTransport(),
                'emballage' => $produit->getEmballage(),
                'transformation' => $produit->getTransformation(),
                'saison' => $produit->getSaison(),
                'prix' => $produit->getPrix(),
                'stock' => $produit->getStock(),
                'eco_score' => $produit->getEcoScore()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Lister tous les produits
    public function listProduits()
    {
        $sql = "SELECT * FROM produit ORDER BY nom ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $produitsData = $query->fetchAll();

            $produits = [];
            foreach ($produitsData as $row) {
                $produit = new produit(
                    $row['nom'], 
                    $row['image'], 
                    $row['id_categorie'],
                    $row['origine'],
                    $row['distance_transport'],
                    $row['type_transport'],
                    $row['emballage'],
                    $row['transformation'],
                    $row['saison'],
                    $row['prix'],
                    $row['stock'],
                    $row['eco_score']
                );
                $produit->setIdProduit($row['id_produit']);
                $produits[] = $produit;
            }
            return $produits;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer un produit par ID
    public function getProduitById($id)
    {
        $sql = "SELECT * FROM produit WHERE id_produit = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();

            if ($row) {
                $produit = new produit(
                    $row['nom'],
                    $row['image'],
                    $row['id_categorie'],
                    $row['origine'],
                    $row['distance_transport'],
                    $row['type_transport'],
                    $row['emballage'],
                    $row['transformation'],
                    $row['saison'],
                    $row['prix'],
                    $row['stock'],
                    $row['eco_score']
                );
                $produit->setIdProduit($row['id_produit']);
                return $produit;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour un produit
    public function updateProduit(produit $produit)
    {
        // Re-calculate eco_score
        $eco_score = calculateEcoScore($produit->toArray());
        $produit->setEcoScore($eco_score);

        $sql = "UPDATE produit 
                SET nom = :nom, 
                    image = :image, 
                    id_categorie = :id_categorie,
                    origine = :origine,
                    distance_transport = :distance_transport,
                    type_transport = :type_transport,
                    emballage = :emballage,
                    transformation = :transformation,
                    saison = :saison,
                    prix = :prix,
                    stock = :stock,
                    eco_score = :eco_score
                WHERE id_produit = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $produit->getIdProduit(),
                'nom' => $produit->getNom(),
                'image' => $produit->getImage(),
                'id_categorie' => $produit->getIdCategorie(),
                'origine' => $produit->getOrigine(),
                'distance_transport' => $produit->getDistanceTransport(),
                'type_transport' => $produit->getTypeTransport(),
                'emballage' => $produit->getEmballage(),
                'transformation' => $produit->getTransformation(),
                'saison' => $produit->getSaison(),
                'prix' => $produit->getPrix(),
                'stock' => $produit->getStock(),
                'eco_score' => $produit->getEcoScore()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Supprimer un produit
    public function deleteProduit($id)
    {
        $sql = "DELETE FROM produit WHERE id_produit = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Advanced search, filter, sort
    public function advancedSearch($searchTerm = '', $sortBy = 'nom', $sortOrder = 'ASC', $idCategorie = '', $origine = '')
    {
        $sql = "SELECT p.*, c.nom_categorie 
                FROM produit p 
                LEFT JOIN categorie c ON p.id_categorie = c.id_categorie 
                WHERE 1=1";
        $params = [];

        if (!empty($searchTerm)) {
            $sql .= " AND (p.nom LIKE :search OR p.origine LIKE :search OR p.saison LIKE :search)";
            $params['search'] = "%$searchTerm%";
        }

        if (!empty($idCategorie)) {
            $sql .= " AND p.id_categorie = :idCategorie";
            $params['idCategorie'] = $idCategorie;
        }

        if (!empty($origine)) {
            $sql .= " AND p.origine = :origine";
            $params['origine'] = $origine;
        }

        $allowedSortColumns = ['nom', 'distance_transport', 'saison', 'nom_categorie', 'prix', 'stock', 'eco_score'];
        $sortBy = in_array($sortBy, $allowedSortColumns) ? $sortBy : 'nom';
        
        if ($sortBy === 'nom_categorie') {
            $sortBy = 'c.nom_categorie';
        } else {
            $sortBy = 'p.' . $sortBy;
        }

        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY $sortBy $sortOrder";
        $db = Config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            $produitsData = $query->fetchAll();

            $produits = [];
            foreach ($produitsData as $row) {
                $produit = new produit(
                    $row['nom'], 
                    $row['image'], 
                    $row['id_categorie'],
                    $row['origine'],
                    $row['distance_transport'],
                    $row['type_transport'],
                    $row['emballage'],
                    $row['transformation'],
                    $row['saison'],
                    $row['prix'],
                    $row['stock'],
                    $row['eco_score']
                );
                $produit->setIdProduit($row['id_produit']);
                $produits[] = $produit;
            }
            return $produits;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Obtenir les statistiques
    public function getStats($produits = null)
    {
        if ($produits === null) {
            $produits = $this->listProduits();
        }

        $total = count($produits);
        $totalDistance = 0;
        $totalPrix = 0;
        $totalStock = 0;
        $avgEcoScore = 0;
        $origineDistribution = ['local' => 0, 'importe' => 0];

        foreach ($produits as $p) {
            $totalDistance += $p->getDistanceTransport();
            $totalPrix += $p->getPrix();
            $totalStock += $p->getStock();
            $avgEcoScore += $p->getEcoScore();
            
            $orig = strtolower($p->getOrigine());
            if (isset($origineDistribution[$orig])) {
                $origineDistribution[$orig]++;
            }
        }

        $avgDistance = $total > 0 ? round($totalDistance / $total, 2) : 0;
        $avgEcoScore = $total > 0 ? round($avgEcoScore / $total, 2) : 0;

        return [
            'total' => $total,
            'avgDistance' => $avgDistance,
            'avgEcoScore' => $avgEcoScore,
            'totalStock' => $totalStock,
            'origineDistribution' => $origineDistribution
        ];
    }

    public function getCategories()
    {
        $sql = "SELECT id_categorie, nom_categorie FROM categorie ORDER BY nom_categorie ASC";
        $db = Config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
