DROP SCHEMA IF EXISTS public CASCADE;
CREATE SCHEMA public;
SET SCHEMA 'public';

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
