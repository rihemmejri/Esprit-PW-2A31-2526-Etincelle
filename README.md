# NutriLoop AI 🥗🤖

### *L'intelligence artificielle au service de votre assiette pour une meilleure santé et un monde plus durable.*

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![AI](https://img.shields.io/badge/Groq_AI-f3d03e?style=for-the-badge&logo=openai&logoColor=white)
![Gemini](https://img.shields.io/badge/Google_Gemini-4285F4?style=for-the-badge&logo=google&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap_5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

---

## 📌 Table des Matières
- [📖 Aperçu du Projet](#-aperçu-du-projet)
- [🚀 Fonctionnalités Clés](#-fonctionnalités-clés)
- [💡 Métiers Avancés (AI Core)](#-métiers-avancés-ai-core)
- [📸 Captures d'Écran](#-captures-décran)
- [🏗️ Architecture MVC](#️-architecture-mvc)
- [🗄️ Base de Données](#️-base-de-données)
- [⚙️ Installation](#️-installation)
- [🛠️ Stack Technique](#️-stack-technique)
- [🤖 Intelligence Artificielle](#-intelligence-artificielle)
- [🔒 Sécurité](#-sécurité)
- [🗺️ Roadmap](#️-roadmap)
- [👥 Équipe](#-équipe)

---

## 📖 Aperçu du Projet
**NutriLoop AI** est une plateforme SaaS révolutionnaire qui fusionne la nutrition personnalisée et l'éco-responsabilité. 

### 🎯 Le Problème
Aujourd'hui, il est difficile de concilier une alimentation saine pour le corps et respectueuse de la planète. Les consommateurs manquent souvent de données précises sur l'impact carbone de leurs achats et d'un accompagnement personnalisé pour leurs objectifs de santé.

### ✨ Notre Vision
Créer un écosystème où l'IA guide chaque utilisateur vers une alimentation optimisée, tout en favorisant le commerce local et durable grâce à un indicateur unique : l'**Eco-Score**.

---

## 🚀 Fonctionnalités Clés

### 👤 Gestion Utilisateur & Profil
*   **Authentification Robuste** : Système sécurisé avec gestion des sessions.
*   **Profil Intelligent** : Centralisation des données de santé, commandes et préférences.

### 📈 Suivi & Objectifs (Health Track)
*   **Objectifs Personnalisés** : Paramétrage de cibles de poids, calories et hydratation.
- **Tracking Dynamique** : Journalisation quotidienne simplifiée des apports nutritionnels.
- **Dashboards Interactifs** : Visualisation claire de l'évolution de vos indicateurs de santé.

### 🛍️ Produits & Catégories (Eco-Marketplace)
*   **Marketplace Transparente** : Catalogue complet classé par catégories avec affichage de l'impact écologique.
*   **Filtrage Avancé** : Recherche optimisée par prix, popularité et durabilité.

### 📖 Recettes & Préparations
*   **Bibliothèque Culinaire** : Recettes saines avec calcul automatique des macros.
*   **Guides Immersifs** : Instructions pas-à-pas pour une exécution parfaite.

### 📅 Gestion d'Événements
*   **Ateliers & Conférences** : Inscription aux événements communautaires.
*   **Billetterie QR Code** : Accès simplifié grâce à la génération automatique de billets digitaux.

### 🧠 Nutrition Smart (AI Core)
*   **NutriBot AI** : Conseils nutritionnels intelligents et assistant personnel.
*   **Plans IA** : Génération de programmes alimentaires personnalisés via LLM.
*   **Analyse & Eco-Score** : Prédiction santé et optimisation de l'empreinte carbone.

---

## 💡 Métiers Avancés (AI Core)

NutriLoop AI intègre des algorithmes métier sophistiqués pour offrir une valeur ajoutée unique :

1.  **Algorithme de Score Journalier** : Calcul pondéré en temps réel de votre performance santé (0-100) en croisant vos apports réels et vos objectifs.
2.  **Analyse Prédictive AI** : Utilisation de modèles de langage (LLM) pour analyser vos tendances sur 7 jours et prédire vos chances d'atteindre vos objectifs.
3.  **Calculateur d'Eco-Score Multi-facteurs** : Évaluation complexe basée sur la distance de transport, le cycle de vie de l'emballage et le degré de transformation.
4.  **Optimisation de Panier Intelligente** : L'IA suggère des produits alternatifs ayant un meilleur Eco-Score pour réduire votre empreinte carbone globale.
5.  **NutriBot - Assistant d'Achat** : Parsing en langage naturel pour filtrer instantanément la marketplace (ex: *"Je veux des légumes locaux à moins de 3 DT"*).
6.  **Système d'Alertes Prédictives** : Envoi automatisé d'emails contextuels basés sur l'analyse comportementale de l'utilisateur.

---

## 📸 Captures d'Écran

### 🖥️ Dashboard Utilisateur
*(Placeholder: Image du dashboard moderne avec graphes de santé)*

### 🛒 Marketplace & Eco-Score
*(Placeholder: Interface de la boutique avec les badges Eco-Score)*

### 🤖 NutriBot AI en Action
*(Placeholder: Capture de la fenêtre de chat IA)*

### ⚙️ Admin Panel (BackOffice)
*(Placeholder: Interface de gestion centralisée)*

---

## 🏗️ Architecture MVC

Le projet suit une structure **Model-View-Controller** stricte pour une modularité maximale :

```text
NutriLoop-AI/
├── controleurs/   # Logique métier et orchestration
├── models/        # Gestion des données et calculs IA
├── views/         # Interfaces Front-End & Back-End
├── config.php     # Configuration de la base de données
└── assets/        # Ressources statiques (CSS, JS, Images)
```

**Flux de données :**
`Utilisateur` ➡️ `View` ➡️ `Controller` ➡️ `Model` ➡️ `Base de Données` ➡️ `IA Analytics` ➡️ `View`

---

## 🗄️ Base de Données

Notre schéma SQL est conçu pour l'intégrité et la performance :
- **Relations Fortes** : Clés étrangères sur les commandes, participations et suivis.
- **Intégrité Référentielle** : Cascade d'actions pour maintenir la cohérence des données.
- **Optimisation** : Indexation des champs de recherche fréquents (produits, dates).

---

## ⚙️ Installation

### 1️⃣ Pré-requis
- Serveur local (XAMPP, WAMP ou MAMP)
- PHP 8.1+
- Clés API (Groq, Gemini)

### 2️⃣ Clonage & Setup
```bash
# Cloner le projet
git clone https://github.com/rihemmejri/Esprit-PW-2A31-2526-Etincelle.git

# Importer la base de données
# Utilisez phpMyAdmin pour importer le fichier nutriloop.sql
```

### 3️⃣ Configuration
Éditez le fichier `config.php` ou votre `.env` :
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'nutriloop');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4️⃣ Lancement
Ouvrez votre navigateur sur : `http://localhost/Esprit-PW-2A31-2526-Etincelle/`

---

## 🛠️ Stack Technique

| Technologie | Utilisation |
| :--- | :--- |
| **PHP 8** | Moteur backend et architecture MVC |
| **MySQL** | Stockage persistant et gestion des relations |
| **Bootstrap 5** | Design moderne, premium et responsive |
| **JavaScript ES6** | Interactions dynamiques et appels AJAX |
| **Groq / Llama 3.1** | Traitement du langage naturel (Chatbot) |
| **Google Gemini** | Analyse prédictive et insights santé |
| **PHPMailer** | Service de notifications transactionnelles |
| **QR API** | Génération de billets pour événements |

---

## 🤖 Intelligence Artificielle

NutriLoop AI n'est pas qu'une simple application CRUD. Elle intègre :
- **Llama 3.1 (via Groq)** : Pour un chatbot ultra-rapide capable de comprendre des requêtes complexes.
- **Gemini Pro** : Pour la génération de programmes alimentaires personnalisés et l'analyse des données de santé.
- **IA Comportementale** : Analyse des habitudes pour déclencher des conseils de nutrition "juste-à-temps".

---

## 🔒 Sécurité
- **Protection SQL Injection** : Utilisation systématique de requêtes préparées (PDO).
- **Validation XSS** : Nettoyage de toutes les entrées utilisateur.
- **Session Secure** : Gestion stricte des cookies de session.
- **Architecture Masquée** : Séparation des fichiers sensibles du répertoire public.

---

## 🗺️ Roadmap
- [ ] 📱 Application Mobile Native (React Native)
- [ ] 📸 OCR Nutritionnel : Scannez vos étiquettes via la caméra
- [ ] 🤖 Reconnaissance d'aliments par image
- [ ] 🏆 Gamification : Système de points pour les achats éco-responsables
- [ ] ⌚ Intégration montres connectées (Apple Health / Google Fit)

---

## 👥 Équipe "Étincelle"
Projet réalisé avec passion par l'équipe **Étincelle** dans le cadre du cursus académique à l'**Esprit**.

---

### 🌟 Footer
**NutriLoop AI : Redéfinir la nutrition, une boucle à la fois.**
*Built with ❤️ for a better planet.*
