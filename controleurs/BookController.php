<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../models/book.php');

class BookController
{
    // Ajouter un livre
    public function addBook(book $book)
    {
        $sql = "INSERT INTO book (titre, auteur, publication_date, language, status, number_of_copies, category_id) 
                VALUES (:titre, :auteur, :publication_date, :language, :status, :number_of_copies, :category_id)";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $book->getTitre(),
                'auteur' => $book->getAuteur(),
                'publication_date' => $book->getPublicationDate(),
                'language' => $book->getLanguage(),
                'status' => $book->getStatus(),
                'number_of_copies' => $book->getNumberOfCopies(),
                'category_id' => $book->getCategoryId()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Lister tous les livres
    public function listBooks()
    {
        $sql = "SELECT * FROM book ORDER BY titre ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $booksData = $query->fetchAll();

            $books = [];
            foreach ($booksData as $row) {
                $book = new book(
                    $row['titre'], 
                    $row['auteur'], 
                    $row['publication_date'],
                    $row['language'],
                    $row['status'],
                    $row['number_of_copies'],
                    $row['category_id']
                );
                $book->setId($row['id']);
                $books[] = $book;
            }
            return $books;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    // Récupérer un livre par ID (retourne un objet Book)
// Récupérer un livre par ID (retourne un objet Book)
public function getBookById($id)
{
    $sql = "SELECT * FROM book WHERE id = :id";
    $db = Config::getConnexion();
    
    try {
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        $row = $query->fetch();

        if ($row) {
            $book = new Book(
                $row['titre'],
                $row['auteur'],
                $row['publication_date'],
                $row['language'],
                $row['status'],
                $row['number_of_copies'],
                $row['category_id']
            );
            $book->setId($row['id']);
            return $book;  // Retourne un OBJET Book
        }
        return null;
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        return null;
    }
}

    // Récupérer un livre par ID (retourne un tableau)
    public function getBook($id)
    {
        $sql = "SELECT * FROM book WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(); // Retourne un tableau associatif
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Mettre à jour un livre
    public function updateBook(book $book)
    {
        $sql = "UPDATE book 
                SET titre = :titre, 
                    auteur = :auteur, 
                    publication_date = :publication_date,
                    language = :language,
                    status = :status,
                    number_of_copies = :number_of_copies,
                    category_id = :category_id
                WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $book->getId(),
                'titre' => $book->getTitre(),
                'auteur' => $book->getAuteur(),
                'publication_date' => $book->getPublicationDate(),
                'language' => $book->getLanguage(),
                'status' => $book->getStatus(),
                'number_of_copies' => $book->getNumberOfCopies(),
                'category_id' => $book->getCategoryId()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Supprimer un livre
    public function deleteBook($id)
    {
        $sql = "DELETE FROM book WHERE id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    // Afficher les détails d'un livre avec sa catégorie
    public function showBook($id)
    {
        $sql = "SELECT b.*, c.title as category_title 
                FROM book b 
                LEFT JOIN category c ON b.category_id = c.id 
                WHERE b.id = :id";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch(); // Retourne un tableau associatif avec les infos du livre + nom catégorie
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer les livres par catégorie
    public function getBooksByCategory($category_id)
    {
        $sql = "SELECT * FROM book WHERE category_id = :category_id ORDER BY titre ASC";
        $db = Config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute(['category_id' => $category_id]);
            $booksData = $query->fetchAll();

            $books = [];
            foreach ($booksData as $row) {
                $book = new book(
                    $row['titre'],
                    $row['auteur'],
                    $row['publication_date'],
                    $row['language'],
                    $row['status'],
                    $row['number_of_copies'],
                    $row['category_id']
                );
                $book->setId($row['id']);
                $books[] = $book;
            }
            return $books;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }
}
?>