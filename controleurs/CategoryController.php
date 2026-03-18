<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/Category.php');

class CategoryController
{
    // Ajouter une catégorie
    public function addCategory(Category $category)
    {
        $sql = "INSERT INTO category (title, description) VALUES (:title, :description)";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'title' => $category->getTitle(),
                'description' => $category->getDescription()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Lister toutes les catégories
    public function listCategories()
    {
        $sql = "SELECT * FROM category";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $categoriesData = $query->fetchAll();

            $categories = [];
            foreach ($categoriesData as $row) {
                $category = new Category($row['title'], $row['description']);
                $category->setId($row['id']);
                $categories[] = $category;
            }
            return $categories;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // ✅ AJOUTEZ CETTE MÉTHODE - Récupérer une catégorie par ID
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM category WHERE id = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $row = $query->fetch();
            
            if ($row) {
                $category = new Category($row['title'], $row['description']);
                $category->setId($row['id']);
                return $category;
            }
            return null;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // ✅ AJOUTEZ AUSSI CES MÉTHODES UTILES
    // Mettre à jour une catégorie
    public function updateCategory(Category $category)
    {
        $sql = "UPDATE category SET title = :title, description = :description WHERE id = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $category->getId(),
                'title' => $category->getTitle(),
                'description' => $category->getDescription()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Supprimer une catégorie
    public function deleteCategory($id)
    {
        $sql = "DELETE FROM category WHERE id = :id";
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