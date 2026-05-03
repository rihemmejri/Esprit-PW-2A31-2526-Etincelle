CREATE DATABASE Nutriloop;
USE Nutriloop;

-- =========================
-- USER (moomen)
-- =========================
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    mot_de_passe VARCHAR(255),
    date_inscription DATE,
    role ENUM('ADMIN','USER'),
    statut ENUM('ACTIF','INACTIF')
);

-- =========================
-- CATEGORIE + -- PRODUIT (Molka)
-- =========================
CREATE TABLE categorie (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    nom_categorie VARCHAR(50),
    description TEXT,
    image_categorie VARCHAR(255),
    type_categorie ENUM('aliment','boisson','autre'),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE produit (
    id_produit INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    image VARCHAR(255),
    id_categorie INT,
    origine ENUM('local','importe'),
    distance_transport INT,
    type_transport ENUM('avion','camion','bateau'),
    emballage ENUM('plastique','carton','aucun'),
    transformation ENUM('brut','transforme','ultra_transforme'),
    saison ENUM('hiver','ete','automne'),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categorie) REFERENCES categorie(id_categorie)
        ON DELETE SET NULL
);

-- =========================
-- RECETTE + PREPARATION (Ryhem)
-- =========================
CREATE TABLE recette (
    id_recette INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    description TEXT,
    temps_preparation INT,
    difficulte ENUM('FACILE','MOYEN','DIFFICILE'),
    type_repas ENUM('PETIT_DEJEUNER','DEJEUNER','DINER','DESSERT'),
    origine VARCHAR(255),
    nb_personne INT
);

CREATE TABLE preperation (
    id_etape INT AUTO_INCREMENT PRIMARY KEY,
    ordre INT,
    instruction TEXT,
    duree INT,
    temperature INT,
    type_action ENUM('COUPER','MELANGER','CUISSON'),
    outil_utilise ENUM('FOUR','MIXEUR','CUILLERE','RAPE'),
    quantite_ingredient VARCHAR(255),
    astuce TEXT,
    id_recette INT,
    FOREIGN KEY (id_recette) REFERENCES recette(id_recette)
        ON DELETE CASCADE
);

-- =========================
-- REPAS + PROGRAMME (Douaa)
-- =========================
CREATE TABLE repas (
    id_repas INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    type VARCHAR(100),
    calories INT,
    proteines FLOAT,
    glucides FLOAT,
    lipides FLOAT
);

CREATE TABLE programme (
    id_programme INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    objectif VARCHAR(255),
    date_debut DATE,
    date_fin DATE,
    id_repas INT,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
        ON DELETE CASCADE,
    FOREIGN KEY (id_repas) REFERENCES repas(id_repas)
        ON DELETE SET NULL
);

-- =========================
-- SUIVI + OBJECTIF (Mahdy)
-- =========================
CREATE TABLE objectif (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    poids_cible FLOAT,
    calories_objectif INT,
    eau_objectif FLOAT,
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE suivi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    id_objectif INT,
    date DATE,
    poids FLOAT,
    calories_consommees INT,
    calories_objectif INT,
    calories_restant INT,
    eau_bue FLOAT,
    eau_objectif FLOAT,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE,
    FOREIGN KEY (id_objectif) REFERENCES objectif(id)
        ON DELETE SET NULL
);

CREATE TABLE score_journalier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE,
    calories_consommees INT,
    eau_bue FLOAT,
    objectif_calories INT,
    objectif_eau FLOAT,
    score FLOAT,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE alert (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('INFO','WARNING','CRITICAL','SUCCESS'),
    categorie VARCHAR(50),
    message TEXT,
    date DATETIME,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE badge (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('GOLD','SILVER','BRONZE','PERFECT_DAY','STREAK','WARNING'),
    titre VARCHAR(100),
    description TEXT,
    date_obtention DATE,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE daily_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE,
    calories INT,
    eau FLOAT,
    score FLOAT,
    status ENUM('PERFECT','NORMAL','BAD','NO_DATA'),
    message TEXT,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE ai_prediction (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    date DATE,
    input_data TEXT,
    prediction TEXT,
    risk_level ENUM('LOW','MEDIUM','HIGH'),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);
-- =========================
-- EVENTS (Chaima)
-- =========================
CREATE TABLE event (
    id_evenement INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    titre VARCHAR(255),
    description TEXT,
    type_evenement VARCHAR(100),
    date_evenement DATE,
    lieu VARCHAR(255),
    nb_places_max INT,
    nb_places_restantes INT,
    statut VARCHAR(50),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE participation (
    id_participation INT AUTO_INCREMENT PRIMARY KEY,
    id_evenement INT,
    id_user INT,
    statut VARCHAR(50),
    date_inscription DATE,
    feedback TEXT,
    note FLOAT,
    FOREIGN KEY (id_evenement) REFERENCES event(id_evenement)
        ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
        ON DELETE CASCADE
);