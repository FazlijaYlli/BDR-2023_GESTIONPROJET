
-- CREATION DU SCHÉMA

DROP SCHEMA IF EXISTS public CASCADE;
CREATE SCHEMA public;
SET SCHEMA 'public';

-- CREATION DES TABLES

DROP TABLE IF EXISTS CatégorieDeCompétence CASCADE;
CREATE TABLE CatégorieDeCompétence
(
    nom VARCHAR(64),
    CONSTRAINT PK_CatégorieDeCompétence PRIMARY KEY (nom)
);

DROP TABLE IF EXISTS Compétence CASCADE;
CREATE TABLE Compétence
(
    nom                   VARCHAR(64),
    description           VARCHAR(256),
    catégorieDeCompétence VARCHAR(64) NOT NULL,
    CONSTRAINT PK_Compétence PRIMARY KEY (nom),
    CONSTRAINT FK_CatégorieDeCompétence_nom
        FOREIGN KEY (catégorieDeCompétence)
            REFERENCES CatégorieDeCompétence (nom)
            ON UPDATE CASCADE
            ON DELETE NO ACTION
);

DROP TABLE IF EXISTS Projet CASCADE;
CREATE TABLE Projet
(
    nom         VARCHAR(64),
    description VARCHAR(1024),
    CONSTRAINT PK_Projet PRIMARY KEY (nom)
);

DROP TABLE IF EXISTS ProjetRelease CASCADE;
CREATE TABLE ProjetRelease
(
    nomProjet       VARCHAR(64),
    nom             VARCHAR(64),
    sortiePrévue    DATE NOT NULL,
    sortieEffective DATE,
    CONSTRAINT PK_ProjetRelease PRIMARY KEY (nomProjet, nom),
    CONSTRAINT FK_Projet_nom
        FOREIGN KEY (nomProjet)
            REFERENCES Projet (nom)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);

DROP TABLE IF EXISTS GroupeDeTâche CASCADE;
CREATE TABLE GroupeDeTâche
(
    nom         VARCHAR(64),
    description VARCHAR(256),
    CONSTRAINT PK_GroupeDeTâche PRIMARY KEY (nom)
);

DROP TABLE IF EXISTS Utilisateur CASCADE;
CREATE TABLE Utilisateur
(
    id       SERIAL,
    nom      VARCHAR(32),
    prénom   VARCHAR(32),
    -- Espace reservé pour SHA-512
    hashMdp  CHAR(128),
    -- Utilisation du mot "fonction" pour différencier du mot-clé "role"
    fonction VARCHAR(16),
    CONSTRAINT PK_Utilisateur PRIMARY KEY (id),
    CONSTRAINT CK_Utilisateur_fonction CHECK (fonction = 'Employé' OR fonction = 'Directeur')
);

DROP TABLE IF EXISTS Tâche CASCADE;
CREATE TABLE Tâche
(
    id               SERIAL,
    titre            VARCHAR(64)   NOT NULL,
    description      VARCHAR(1024) NOT NULL,
    delai            DATE,
    statut           VARCHAR(16)   NOT NULL,
    dureeEstimée     TIME          NOT NULL,
    dureeRéelle      TIME,
    nomProjetRelease VARCHAR(64)   NOT NULL,
    nomProjet        VARCHAR(64)   NOT NULL,
    nomGroupeDeTâche VARCHAR(64),
    idUtilisateur    INT,
    CONSTRAINT PK_Tâche PRIMARY KEY (id),

    CONSTRAINT FK_ProjetRelease_nomProjet_nom
        FOREIGN KEY (nomProjet, nomProjetRelease)
            REFERENCES ProjetRelease (nomProjet, nom)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT FK_GroupeDeTâche_nom
        FOREIGN KEY (nomGroupeDeTâche)
            REFERENCES GroupeDeTâche (nom)
            ON UPDATE CASCADE
            ON DELETE NO ACTION,

    CONSTRAINT FK_Utilisateur_id
        FOREIGN KEY (idUtilisateur)
            REFERENCES Utilisateur (id)
            ON UPDATE CASCADE
            ON DELETE NO ACTION,

    CONSTRAINT CK_Tâche_Statut CHECK (statut = 'Planifié' OR statut = 'Terminé' OR statut = 'En cours')
);

DROP TABLE IF EXISTS Tâche_Tâche CASCADE;
CREATE TABLE Tâche_Tâche
(
    requise   INT,
    debloquée INT,
    CONSTRAINT PK_Tâche_Tâche PRIMARY KEY (requise, debloquée),
    CONSTRAINT FK_Requise_id
        FOREIGN KEY (requise)
            REFERENCES Tâche (id)
            ON UPDATE CASCADE
            ON DELETE NO ACTION,

    CONSTRAINT FK_Debloquée_id
        FOREIGN KEY (debloquée)
            REFERENCES Tâche (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);

DROP TABLE IF EXISTS Tâche_Compétence CASCADE;
CREATE TABLE Tâche_Compétence
(
    idTâche       INT,
    nomCompétence VARCHAR(64),
    -- Champ pour stocker un enum
    niveauRequis  SMALLINT NOT NULL,
    CONSTRAINT PK_Tâche_Compétence PRIMARY KEY (idTâche, nomCompétence),
    CONSTRAINT FK_Tâche_id
        FOREIGN KEY (idTâche)
            REFERENCES Tâche (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT FK_Compétence_nom
        FOREIGN KEY (nomCompétence)
            REFERENCES Compétence (nom)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT CK_Tâche_Compétence_niveauRequis CHECK (niveauRequis < 3 AND niveauRequis >= 0)
);

DROP TABLE IF EXISTS Utilisateur_Compétence CASCADE;
CREATE TABLE Utilisateur_Compétence
(
    idUtilisateur INT,
    nomCompétence VARCHAR(64),
    -- Champ pour stocker un enum
    niveauPossédé SMALLINT NOT NULL,
    CONSTRAINT PK_Utilisateur_Compétence PRIMARY KEY (idUtilisateur, nomCompétence),
    CONSTRAINT FK_Utilisateur_id
        FOREIGN KEY (idUtilisateur)
            REFERENCES Utilisateur (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT FK_Compétence_nom
        FOREIGN KEY (nomCompétence)
            REFERENCES Compétence (nom)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT CK_Utilisateur_Compétence_niveauPossédé CHECK (niveauPossédé < 3 AND niveauPossédé >= 0)
);

DROP TABLE IF EXISTS Commentaire CASCADE;
CREATE TABLE Commentaire
(
    idUtilisateur INT,
    dateCréation  TIMESTAMP,
    contenu       VARCHAR(1024) NOT NULL,
    idTâche       INT           NOT NULL,
    CONSTRAINT PK_Commentaire PRIMARY KEY (idUtilisateur, dateCréation),

    CONSTRAINT FK_Utilisateur_id
        FOREIGN KEY (idUtilisateur)
            REFERENCES Utilisateur (id)
            ON UPDATE CASCADE
            ON DELETE NO ACTION,

    CONSTRAINT FK_Tâche_id
        FOREIGN KEY (idTâche)
            REFERENCES Tâche (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);

DROP TABLE IF EXISTS Utilisateur_Tâche CASCADE;
CREATE TABLE Utilisateur_Tâche
(
    idUtilisateur INT,
    idTâche       INT,
    CONSTRAINT PK_Utilisateur_Tâche PRIMARY KEY (idUtilisateur, idTâche),

    CONSTRAINT FK_Utilisateur_id
        FOREIGN KEY (idUtilisateur)
            REFERENCES Utilisateur (id)
            ON UPDATE CASCADE
            ON DELETE NO ACTION,

    CONSTRAINT FK_Tâche_id
        FOREIGN KEY (idTâche)
            REFERENCES Tâche (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);

DROP TABLE IF EXISTS Congé CASCADE;
CREATE TABLE Congé
(
    id            SERIAL,
    debut         DATE NOT NULL,
    fin           DATE,
    idUtilisateur INT,
    CONSTRAINT PK_Congé PRIMARY KEY (id),

    CONSTRAINT FK_Utilisateur_id
        FOREIGN KEY (idUtilisateur)
            REFERENCES Utilisateur (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE
);

DROP TABLE IF EXISTS Utilisateur_Projet CASCADE;
CREATE TABLE Utilisateur_Projet
(
    idUtilisateur  INT,
    nomProjet      VARCHAR(64),
    responsabilité VARCHAR(16) NOT NULL,

    CONSTRAINT PK_Utilisateur_Projet PRIMARY KEY (idUtilisateur, nomProjet),

    CONSTRAINT FK_Utilisateur_id
        FOREIGN KEY (idUtilisateur)
            REFERENCES Utilisateur (id)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT FK_Projet_nom
        FOREIGN KEY (nomProjet)
            REFERENCES Projet (nom)
            ON UPDATE CASCADE
            ON DELETE CASCADE,

    CONSTRAINT CK_Utilisateur_Projet_responsabilité CHECK (responsabilité = 'Responsable' OR responsabilité = 'Employé')
);

CREATE VIEW vCongé AS 
	SELECT debut, fin, idutilisateur,
		CASE WHEN CURRENT_DATE BETWEEN debut AND fin THEN 'En cours'
		WHEN fin < CURRENT_DATE THEN 'Terminée'
		ELSE 'Futur' END  AS statut
	FROM congé
	ORDER BY statut ASC, ABS(EXTRACT (DAY FROM debut::timestamp - CURRENT_DATE::timestamp)) ASC;

-- INSERTION DE DONNÉE

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
      
      
-- CREATION DES TRIGGERS

CREATE OR REPLACE FUNCTION Verification_Action_Sur_Tache()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.statut = 'Terminé' THEN
        IF NOT EXISTS(SELECT 1
                      FROM Utilisateur_Projet
                      INNER JOIN Tâche ON Utilisateur_Projet.idUtilisateur = Tâche.idUtilisateur
                      INNER JOIN Utilisateur ON Utilisateur.id = Tâche.idUtilisateur
                      WHERE Utilisateur_Projet.nomprojet = NEW.nomProjet
                      AND Utilisateur_Projet.idutilisateur = NEW.idUtilisateur
                      AND Tâche.id = NEW.id) THEN
            RAISE EXCEPTION 'Utilisateur est absent du projet ou n est pas le propriétaire de la tâche';
            RETURN NULL;
        ELSE
            RETURN NEW;
        END IF;
    ELSIF NEW.statut = 'En cours' THEN
        IF NOT EXISTS(SELECT 1
                      FROM Utilisateur_Projet
                      INNER JOIN Tâche ON Utilisateur_Projet.idUtilisateur = Tâche.idUtilisateur
                      INNER JOIN Utilisateur ON Utilisateur.id = Tâche.idUtilisateur
                      WHERE Utilisateur_Projet.nomprojet = NEW.nomProjet
                      AND Utilisateur_Projet.idutilisateur = NEW.idUtilisateur
                      AND Tâche.id = NEW.id) THEN
            RAISE EXCEPTION 'Utilisateur est absent du projet ou n est pas le propriétaire de la tâche';
            RETURN NULL;
        ELSE
            RETURN NEW;
        END IF;
    ELSE
        RETURN NEW;
    END IF;
END;
$$ LANGUAGE PLPGSQL;


CREATE OR REPLACE TRIGGER Check_Action_Sur_Tache
    BEFORE UPDATE
    ON tâche
    FOR EACH ROW
EXECUTE FUNCTION Verification_Action_Sur_Tache();


CREATE OR REPLACE FUNCTION Verification_Insertion_Commentaire()
    RETURNS TRIGGER AS
$$
BEGIN
    IF NOT EXISTS(SELECT 1
                  FROM Commentaire
                           INNER JOIN Tâche ON Commentaire.idTâche = Tâche.id
                           INNER JOIN ProjetRelease ON Tâche.nomProjet = ProjetRelease.nomProjet
                      AND Tâche.nomProjetRelease = ProjetRelease.nom
                           INNER JOIN Utilisateur_Projet on Utilisateur_Projet.idUtilisateur = Commentaire.idUtilisateur
                  WHERE Commentaire.idutilisateur = NEW.idUtilisateur)
    THEN
        RAISE EXCEPTION 'Utilisateur est absent du projet et ne peut pas commenter cette tâche';
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;

CREATE OR REPLACE TRIGGER Check_Insertion_Commentaire
    BEFORE INSERT
    ON commentaire
    FOR EACH ROW
EXECUTE FUNCTION Verification_Insertion_Commentaire();


CREATE OR REPLACE FUNCTION Verification_Insertion_Projet()
    RETURNS TRIGGER AS
$$
BEGIN
    IF NOT EXISTS(SELECT 1
                  FROM Utilisateur_Projet
                  WHERE Utilisateur_Projet.nomprojet = NEW.nom
                    AND Utilisateur_Projet.responsabilité = 'Responsable')
    THEN
        RAISE EXCEPTION 'Un projet doit avoir au moins un responsable';
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;

CREATE OR REPLACE TRIGGER Check_Ajout_Projet
    BEFORE INSERT
    ON projet
    FOR EACH ROW
EXECUTE FUNCTION Verification_Insertion_Projet();


CREATE OR REPLACE FUNCTION Verification_Insertion_Tâche()
    RETURNS TRIGGER AS
$$
BEGIN
    IF NOT EXISTS(SELECT 1
                  FROM Tâche
                           INNER JOIN Utilisateur_Projet on Tâche.idUtilisateur = Utilisateur_Projet.idUtilisateur
                  WHERE Utilisateur_Projet.nomprojet = NEW.nomProjet
                    AND Tâche.idutilisateur = NEW.idUtilisateur
                    AND responsabilité = 'Responsable')
    THEN
        RAISE EXCEPTION 'Utilisateur est pas responsable de ce projet';
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;

CREATE OR REPLACE TRIGGER Check_Ajout_Tâche
    BEFORE INSERT
    ON tâche
    FOR EACH ROW
EXECUTE FUNCTION Verification_Insertion_Tâche();


CREATE OR REPLACE FUNCTION Verification_Insertion_Congé()
    RETURNS TRIGGER AS
$$
BEGIN
    IF NOT EXISTS(SELECT 1
                  FROM Congé
                  WHERE Congé.idUtilisateur = NEW.idUtilisateur
                    AND EXISTS(SELECT 1
                               FROM Congé
                               WHERE Congé.idUtilisateur = NEW.idUtilisateur
                                   AND NEW.debut BETWEEN Congé.debut AND Congé.fin
                                  OR NEW.fin BETWEEN Congé.debut AND Congé.fin))
    THEN
        RAISE EXCEPTION 'Utilisateur est pas responsable de ce projet';
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;

CREATE OR REPLACE TRIGGER Check_Ajout_Congé
    BEFORE INSERT
    ON Congé
    FOR EACH ROW
EXECUTE FUNCTION Verification_Insertion_Congé();


CREATE OR REPLACE FUNCTION tâches_requises(idTâche INTEGER)
    RETURNS TABLE(requise INTEGER) AS
$$
WITH RECURSIVE recursive_tâches_requises(requise, debloquée) AS (
    SELECT Tâche_Tâche.requise, Tâche_Tâche.debloquée
    FROM Tâche_Tâche
    WHERE Tâche_Tâche.debloquée = idTâche
    UNION
    SELECT Tâche_Tâche.requise, recursive_tâches_requises.requise
    FROM recursive_tâches_requises
     INNER JOIN Tâche_Tâche
        ON recursive_tâches_requises.requise = Tâche_Tâche.debloquée
)
SELECT recursive_tâches_requises.requise FROM recursive_tâches_requises;
$$ LANGUAGE SQL;


CREATE OR REPLACE FUNCTION Verification_Relation_Cyclique_Tâches()
    RETURNS TRIGGER AS
$$
BEGIN
    IF EXISTS(
            SELECT requise
            FROM tâches_requises(NEW.requise)
            WHERE tâches_requises.requise = NEW.requise
        )
    THEN
        RAISE EXCEPTION 'Ajout de prérequis de tâche cyclique impossible';
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;


CREATE OR REPLACE TRIGGER Check_Relation_Cyclique
    BEFORE INSERT
    ON Tâche_Tâche
    FOR EACH ROW 
EXECUTE FUNCTION Verification_Relation_Cyclique_Tâches();



CREATE OR REPLACE FUNCTION Verification_Insertion_Tâche_Tâches()
    RETURNS TRIGGER AS
$$
BEGIN
    IF NOT EXISTS(
            SELECT requise, debloquée
            FROM Tâche_Tâche
            INNER JOIN Tâche AS TâcheRequise ON TâcheRequise.id = NEW.requise
            INNER JOIN Tâche AS TâcheDebloqué ON TâcheDebloqué.id = NEW.debloquée
            WHERE TâcheDebloqué.nomprojet = TâcheRequise.nomprojet
        )
    THEN
        RAISE EXCEPTION 'La tâche requise doit appartenir au même projet';
        RETURN NULL;
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;


CREATE OR REPLACE TRIGGER Check_Insertion_Tâche_Tâches
    BEFORE INSERT
    ON Tâche_Tâche
    FOR EACH ROW
EXECUTE FUNCTION Verification_Insertion_Tâche_Tâches();
