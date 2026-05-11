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
    FOREIGN KEY (id_user) REFERENCES user(id_user)
        ON DELETE CASCADE
);

CREATE TABLE programme_repas (
    id_programme INT,
    id_repas INT,
    jour_semaine VARCHAR(20),
    type_repas VARCHAR(50),
    PRIMARY KEY (id_programme, id_repas, jour_semaine, type_repas),
    FOREIGN KEY (id_programme) REFERENCES programme(id_programme) ON DELETE CASCADE,
    FOREIGN KEY (id_repas) REFERENCES repas(id_repas) ON DELETE CASCADE
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
-- =========================================
-- AJOUTS ET AMELIORATIONS NUTRILOOP
-- =========================================

USE Nutriloop;

-- =========================================
-- TABLE CONNEXION LOGS
-- =========================================
CREATE TABLE connexion_logs (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    success BOOLEAN DEFAULT FALSE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =========================================
-- TABLE CONNEXION STATS
-- =========================================
CREATE TABLE connexion_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    connexion_date DATE NOT NULL,
    connexion_count INT DEFAULT 1,
    last_connexion DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

-- =========================================
-- TABLE NOTIFICATIONS
-- =========================================
CREATE TABLE notifications (
    id_notification INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-bell',
    color VARCHAR(20) DEFAULT '#4CAF50',
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    lien VARCHAR(255),
    FOREIGN KEY (id_user) REFERENCES user(id_user)
        ON DELETE CASCADE
);

-- =========================================
-- TABLE FACE DATA
-- =========================================
CREATE TABLE face_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    face_descriptor LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id_user)
        ON DELETE CASCADE
);

-- =========================================
-- AMELIORATION TABLE EVENT
-- =========================================
ALTER TABLE event
MODIFY type_evenement 
ENUM('SPORT','NUTRITION','WORKSHOP','AUTRE');

ALTER TABLE event
ADD prix DECIMAL(8,2) DEFAULT 0.00;

-- =========================================
-- AMELIORATION TABLE PARTICIPATION
-- =========================================
ALTER TABLE participation
MODIFY statut 
ENUM('EN_ATTENTE','CONFIRMEE','ANNULEE','PRESENTE')
DEFAULT 'EN_ATTENTE';

ALTER TABLE participation
ADD nom VARCHAR(100),
ADD email VARCHAR(255),
ADD telephone VARCHAR(20),
ADD statut_paiement 
ENUM('GRATUIT','EN_ATTENTE','PAYE','ANNULE')
DEFAULT 'GRATUIT',
ADD reference_paiement VARCHAR(50),
ADD montant_paye DECIMAL(8,2) DEFAULT 0.00,
ADD nb_places_reservees INT UNSIGNED DEFAULT 1;

-- =========================================
-- AMELIORATION TABLE USER
-- =========================================
ALTER TABLE user
ADD is_banned BOOLEAN DEFAULT FALSE,
ADD is_locked BOOLEAN DEFAULT FALSE,
ADD failed_attempts INT DEFAULT 0,
ADD last_login DATETIME,
ADD reset_token VARCHAR(10) DEFAULT NULL,
ADD reset_expires DATETIME DEFAULT NULL,
ADD locked_at DATETIME DEFAULT NULL,
ADD banned_at DATETIME DEFAULT NULL,
ADD banned_reason TEXT DEFAULT NULL,
ADD banned_by VARCHAR(100) DEFAULT NULL;

-- =========================================
-- INDEXES POUR PERFORMANCE
-- =========================================
CREATE INDEX idx_user_email ON user(email);

CREATE INDEX idx_commande_user ON commande(user_id);

CREATE INDEX idx_panier_user ON panier(user_id);

CREATE INDEX idx_event_date ON event(date_evenement);

-- =========================================
-- FOREIGN KEY MANQUANTE COMMANDE
-- =========================================
ALTER TABLE commande
ADD FOREIGN KEY (user_id)
REFERENCES user(id_user)
ON DELETE CASCADE;

-- =========================================
-- FOREIGN KEY MANQUANTE PANIER
-- =========================================
ALTER TABLE panier
ADD FOREIGN KEY (user_id)
REFERENCES user(id_user)
ON DELETE CASCADE;

-- =========================================
-- AMELIORATION PRODUIT
-- =========================================
ALTER TABLE produit
MODIFY saison ENUM('hiver','printemps','ete','automne');

-- =========================================
-- AJOUT CREATED_AT
-- =========================================
ALTER TABLE recette
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE repas
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE objectif
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE suivi
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE participation
ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- =========================================
-- TABLES AI & SANTE (Nouveaux modules)
-- =========================================

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