SET SCHEMA 'public';

CREATE OR REPLACE FUNCTION Verification_Action_Sur_Tache()
    RETURNS TRIGGER AS
$$
BEGIN
    IF NEW.statut = 'Terminée' THEN
        IF NOT EXISTS(SELECT 1
                      FROM Utilisateur_Projet AS UP
                               INNER JOIN Tâche T on UP.idUtilisateur = T.idUtilisateur
                               INNER JOIN Utilisateur U on U.id = T.idUtilisateur
                      WHERE UP.nomprojet = NEW.nomProjet
                        AND UP.idutilisateur = NEW.idUtilisateur)
        THEN
            RAISE EXCEPTION 'Utilisateur est absent du projet ou n est pas le propriétaire de la tâche';
        ELSE
            RETURN NEW;
        END IF;
    ELSE
        IF NEW.statut = 'En cours' THEN
            IF NOT EXISTS(SELECT 1
                          FROM Utilisateur_Projet AS UP
                                   INNER JOIN Tâche T on UP.idUtilisateur = T.idUtilisateur
                                   INNER JOIN Utilisateur U on U.id = T.idUtilisateur
                          WHERE UP.nomprojet = NEW.nomProjet
                            AND OLD.idutilisateur IS NULL
                            AND NEW.idutilisateur IS NOT NULL)
            THEN
                RAISE EXCEPTION 'Utilisateur est absent du projet ou n est pas le propriétaire de la tâche';
            ELSE
                RETURN NEW;
            END IF;
        END IF;
    END IF;
END
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
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;

CREATE OR REPLACE TRIGGER Check_Ajout_Commentaire
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
    ELSE
        RETURN NEW;
    END IF;
END
$$ LANGUAGE PLPGSQL;

CREATE OR REPLACE TRIGGER Check_Ajout_Tâche
    BEFORE INSERT
    ON Tâche
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

DROP FUNCTION IF EXISTS tâches_requises cascade;

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
            SELECT *
            FROM tâches_requises(NEW.requise)
            WHERE tâches_requises.requise = NEW.requise
        )
    THEN
        RAISE EXCEPTION 'Ajout de prérequis de tâche cyclique impossible';
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
