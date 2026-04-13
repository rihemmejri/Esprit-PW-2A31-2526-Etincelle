<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/produit.php');

class ProduitController
{
    // Ajouter un produit
    public function addProduit(produit $produit)
    {
        $sql = "INSERT INTO produit (nom, image, id_categorie, origine, distance_transport, type_transport, emballage, transformation, saison) 
                VALUES (:nom, :image, :id_categorie, :origine, :distance_transport, :type_transport, :emballage, :transformation, :saison)";
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
                'saison' => $produit->getSaison()
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
                    $row['saison']
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

    // Récupérer un produit par ID (retourne un objet produit)
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
                    $row['saison']
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
        $sql = "UPDATE produit 
                SET nom = :nom, 
                    image = :image, 
                    id_categorie = :id_categorie,
                    origine = :origine,
                    distance_transport = :distance_transport,
                    type_transport = :type_transport,
                    emballage = :emballage,
                    transformation = :transformation,
                    saison = :saison
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
                'saison' => $produit->getSaison()
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
}
?>
