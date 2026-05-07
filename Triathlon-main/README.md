🏊🚴🏃 Application de Gestion des Triathlons – FFTRI

Application Web MVC en PHP/MySQL – Projet Agile

📌 Présentation du projet

Ce projet consiste à développer une application Web sécurisée destinée à la Fédération Française de Triathlon (FFTRI) afin de gérer :
  - Les triathlons organisés par la fédération
  - Les clubs, catégories, types de triathlon
  - Les inscriptions et résultats des compétitions
  - Les licenciés des clubs

Le système doit être accessible via un navigateur, hébergé en ligne, et conforme aux normes de développement fournies.

🎯 Objectifs du projet : 
  - Fournir à la FFTRI un outil complet de gestion de triathlons.
  - Garantir une application sécurisée, ergonomique et conforme aux normes PHP.
  - Utiliser une démarche Agile (SCRUM ou Kanban) pour piloter le projet.
  - Mettre en place une base de données conforme aux méthodes Merise.
  - Assurer la qualité via des tests unitaires, fonctionnels et métiers.

🧱 Architecture et technologies

🔹Architecture
Architecture MVC (Model – View – Controller)

🔹 Technologies
PHP (sous Laragon en développement)
MySQL
HTML/CSS/JavaScript
Outil de versioning : Git/GitHub

🔹 Méthodologie
Méthode Agile : SCRUM / Kanban
Suivi des tâches via Trello

🔐 Sécurité et accessibilité
  - Authentification obligatoire
  - Accès réservé :
    - Aux gestionnaires FFTRI
    - Aux responsables de clubs
    - La partie publique n’existe pas : tout accès nécessite une connexion.

🗂️ Cahier des charges – Synthèse

🧑‍🤝‍🧑 Les triathlètes:
  - Identifiés par un numéro de licence permanent, même en cas de changement de club/statut
  - Deux types de licence :
      - Club
      - Individuelle
  - Appartiennent à une catégorie d’âge (benjamin → vétéran)

🏢 Les clubs
  - Identifiés par un numéro
  - Possèdent nom, adresse, téléphone
  - Rattachés à un comité départemental

🗺️ Les comités départementaux
  - Regroupent les clubs de leur département

🏆 Les triathlons
  - Ouverts à toutes les catégories
  - Identifiés par : numéro, nom, type, lieu, date
  - Type défini par des distances (ex : TROP = triathlon olympique)

📝 Inscription et déroulement
  - Ouvertes jusqu'à 15 jours avant la compétition
  - À l'inscription :
      - Attribution d'un numéro de dossard
      - Sauvegarde de la date d’inscription
  - Le jour J : statut présent / forfait
  - Après course :
      - Classement par catégorie
      - Temps par épreuve (natation, cyclisme, course)

🧩 Fonctionnalités attendues
👨‍💼 Pour la FFTRI
  - Gestion des clubs
  - Gestion des catégories
  - Gestion des types de triathlon
  - Organisation complète d’un triathlon :
      - Inscription des triathlètes
      - Saisie des résultats

🏫 Pour les responsables de clubs
  - Gestion de leurs licenciés
