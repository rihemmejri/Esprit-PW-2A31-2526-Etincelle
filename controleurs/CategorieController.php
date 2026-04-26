<?php
include_once(__DIR__ . '/../config.php');
include_once(__DIR__ . '/../models/categorie.php');

class CategorieController {
    // Ajouter une catégorie
    public function addCategorie(Categorie $categorie) {
        $sql = "INSERT INTO categorie (nom_categorie, description, image_categorie, type_categorie) 
                VALUES (:nom, :description, :image, :type)";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom' => $categorie->getNomCategorie(),
                'description' => $categorie->getDescription(),
                'image' => $categorie->getImageCategorie(),
                'type' => $categorie->getTypeCategorie()
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    // Lister toutes les catégories
    public function listCategories() {
        $sql = "SELECT * FROM categorie ORDER BY nom_categorie ASC";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $data = $query->fetchAll();
            $categories = [];
            foreach ($data as $row) {
                $c = new Categorie($row['nom_categorie']);
                $c->setIdCategorie($row['id_categorie']);
                $c->setDescription($row['description']);
                $c->setImageCategorie($row['image_categorie']);
                $c->setTypeCategorie($row['type_categorie']);
                $c->setDateCreation($row['date_creation']);
                $categories[] = $c;
            }
            return $categories;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Récupérer une catégorie par ID
    public function getCategorieById($id) {
        $sql = "SELECT * FROM categorie WHERE id_categorie = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();
            if ($row) {
                $c = new Categorie($row['nom_categorie']);
                $c->setIdCategorie($row['id_categorie']);
                $c->setDescription($row['description']);
                $c->setImageCategorie($row['image_categorie']);
                $c->setTypeCategorie($row['type_categorie']);
                $c->setDateCreation($row['date_creation']);
                return $c;
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return null;
    }

    // Mettre à jour une catégorie
    public function updateCategorie(Categorie $categorie) {
        $sql = "UPDATE categorie 
                SET nom_categorie = :nom, 
                    description = :description, 
                    image_categorie = :image, 
                    type_categorie = :type 
                WHERE id_categorie = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $categorie->getIdCategorie(),
                'nom' => $categorie->getNomCategorie(),
                'description' => $categorie->getDescription(),
                'image' => $categorie->getImageCategorie(),
                'type' => $categorie->getTypeCategorie()
            ]);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }

    // Supprimer une catégorie
    public function deleteCategorie($id) {
        $sql = "DELETE FROM categorie WHERE id_categorie = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    }
}
?>
