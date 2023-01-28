SET SCHEMA 'public';

INSERT INTO CatégorieDeCompétence (nom)
VALUES ('Développement'),
       ('Base de données'),
       ('Langues'),
       ('Gestion');

INSERT INTO Compétence (nom, description, catégorieDeCompétence)
VALUES ('Java', 'Langage de programmation orienté objet', 'Développement'),
       ('SQL', 'Langage de gestion de base de données', 'Base de données'),
       ('Anglais', 'Langue étrangère', 'Langues'),
       ('Gestion de projet', 'Capacité à planifier et gérer des projets', 'Gestion');

INSERT INTO Projet (nom, description)
VALUES ('Projet A', 'Projet de développement d une application mobile'),
       ('Projet B', 'Projet de mise en place d un système de gestion de base de données'),
       ('Projet C', 'Projet vide en cours de création');

INSERT INTO ProjetRelease (nomProjet, nom, sortiePrévue, sortieEffective)
VALUES ('Projet A', 'Release 1', '2022-01-01', '2022-03-01'),
       ('Projet A', 'Release 2', '2022-06-01', '2022-08-01'),
       ('Projet B', 'Release 1', '2022-01-01', '2022-03-01');

INSERT INTO GroupeDeTâche (nom, description)
VALUES ('Base de données', 'Tâches liées à la mise en place de la base de données'),
       ('UX Design', 'Tâches liées au développement de l interface utilisateur');

INSERT INTO Utilisateur (nom, prénom, hashMdp, fonction)
VALUES ('Smith', 'John', 'd404559f602eab6fd602ac7680dacbfaadd13630335e951f097af3900e9de176b6db28512f2e000b9d04fba5133e8b1c6e8df59db3a8ab9d60be4b97cc9e81db', 'Employé'),
       ('Doe', 'Jane', 'd404559f602eab6fd602ac7680dacbfaadd13630335e951f097af3900e9de176b6db28512f2e000b9d04fba5133e8b1c6e8df59db3a8ab9d60be4b97cc9e81db', 'Directeur'),
       ('Employé', 'Nouveau', 'd404559f602eab6fd602ac7680dacbfaadd13630335e951f097af3900e9de176b6db28512f2e000b9d04fba5133e8b1c6e8df59db3a8ab9d60be4b97cc9e81db', 'Employé');

INSERT INTO Tâche (titre, description, delai, statut, dureeEstimée, dureeRéelle, nomProjetRelease, nomProjet,
                   nomGroupeDeTâche, idUtilisateur)
VALUES ('Mise en place de la base de données', 'Créer les tables et les relations nécessaires', '2022-02-01', 'Terminé', '05:00:00', '04:00:00', 'Release 1', 'Projet A', 'Base de données', 1),
       ('Développement de l interface utilisateur', 'Créer les écrans et les fonctionnalités nécessaires', '2022-04-01', 'En cours', '15:00:00', NULL, 'Release 1', 'Projet A', 'UX Design', 2),
       ('Effectuer les Tests sur l interface utilisateur', 'Effectuer les différents tests demandés', '2022-06-01', 'Planifié', '3:00:00', NULL, 'Release 1', 'Projet A', 'UX Design', NULL),
       ('Corrections des bugs', 'Corriger les bugs mis en evidence durant la phase de test', '2022-08-01', 'Planifié', '15:00:00', NULL, 'Release 1', 'Projet A', 'UX Design', NULL),

       ('Mise en place de la base de données', 'Créer les tables et les relations nécessaires', '2022-02-01',
        'En cours', '03:00:00', NULL, 'Release 1', 'Projet B', 'Base de données', 1),
       ('Développement de l interface utilisateur', 'Créer les écrans et les fonctionnalités nécessaires', '2022-04-01',
        'Terminé', '10:00:00', NULL, 'Release 1', 'Projet B', 'UX Design', 2);

INSERT INTO Tâche_Tâche (requise, debloquée)
VALUES (2, 3);

INSERT INTO Tâche_Compétence (idTâche, nomCompétence, niveauRequis)
VALUES (1, 'SQL', 1),
       (1, 'Gestion de projet', 1),
       (2, 'Java', 1),
       (2, 'Anglais', 1),
       (2, 'Gestion de projet', 1);

INSERT INTO Utilisateur_Compétence (idUtilisateur, nomCompétence, niveaupossédé)
VALUES (1, 'SQL', 1),
       (1, 'Gestion de projet', 1),
       (2, 'Java', 1),
       (2, 'Anglais', 1),
       (2, 'Gestion de projet', 1),
       (2, 'SQL', 1);

INSERT INTO Commentaire (idTâche, idUtilisateur, contenu, datecréation)
VALUES (1, 1, 'Bonne avancée sur la mise en place de la base de données', '2022-02-15 13:58:10'),
       (1, 2, 'Il y a des incohérences dans les relations des tables', '2022-02-20 15:32:12'),
       (2, 2, 'L interface utilisateur est presque terminée', '2022-03-30 17:45:23'),
       (2, 1, 'Il manque des fonctionnalités pour l interface utilisateur', '2022-04-01 10:18:59');

INSERT INTO Utilisateur_Tâche (idUtilisateur, idTâche)
VALUES (1, 1),
       (2, 1),
       (2, 2);

INSERT INTO Congé (idUtilisateur, debut, fin)
VALUES (1, '2022-05-01', '2022-05-05'),
       (1, '2023-01-20', '2023-04-20'),
       (1, '2023-12-11', '2023-12-17'),
       (1, '2025-06-20', '2025-07-02'),
       (1, '2021-02-03', '2021-02-04'),
       (2, '2022-06-01', '2022-06-15');

INSERT INTO Utilisateur_Projet (idUtilisateur, nomProjet, responsabilité)
VALUES (1, 'Projet A', 'Employé'),
       (2, 'Projet A', 'Responsable'),
       (1, 'Projet B', 'Responsable'),
       (2, 'Projet B', 'Employé'),
       (1, 'Projet C', 'Responsable');