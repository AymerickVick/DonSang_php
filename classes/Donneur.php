<?php
require_once '../config/Database.php';

class Donneur {
    private $id;
    private $dateRemplissage;
    private $dateNaissance;
    private $niveauEtude;
    private $genre;
    private $taille;
    private $poids;
    private $situationMatrimoniale;
    private $profession;
    private $arrondissementResidence;
    private $quartierResidence;
    private $age;
    private $db;
    private $nom;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getDateRemplissage() { return $this->dateRemplissage; }
    public function getDateNaissance() { return $this->dateNaissance; }
    public function getNiveauEtude() { return $this->niveauEtude; }
    public function getGenre() { return $this->genre; }
    public function getTaille() { return $this->taille; }
    public function getPoids() { return $this->poids; }
    public function getSituationMatrimoniale() { return $this->situationMatrimoniale; }
    public function getProfession() { return $this->profession; }
    public function getArrondissementResidence() { return $this->arrondissementResidence; }
    public function getQuartierResidence() { return $this->quartierResidence; }
    public function getAge() { return $this->age; }
    public function getNom() { return $this->nom; }

    // Setters
    public function setDateRemplissage($date) { $this->dateRemplissage = $date; }
    public function setDateNaissance($date) { 
        $this->dateNaissance = $date;
        $this->age = $this->calculerAge($date);
    }
    public function setNiveauEtude($niveau) { $this->niveauEtude = $niveau; }
    public function setGenre($genre) { $this->genre = $genre; }
    public function setTaille($taille) { $this->taille = $taille; }
    public function setPoids($poids) { $this->poids = $poids; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setSituationMatrimoniale($situation) { $this->situationMatrimoniale = $situation; }
    public function setProfession($profession) { $this->profession = $profession; }
    public function setArrondissementResidence($arrondissement) { $this->arrondissementResidence = $arrondissement; }
    public function setQuartierResidence($quartier) { $this->quartierResidence = $quartier; }

    // Calculer l'âge à partir de la date de naissance
    private function calculerAge($dateNaissance) {
        $naissance = new DateTime($dateNaissance);
        $aujourdhui = new DateTime('now');
        return $naissance->diff($aujourdhui)->y;
    }

    // Ajouter un donneur
    public function ajouter() {
        $query = "INSERT INTO Donneurs (Date_Remplissage, Date_Naissance, Niveau_Etude, Genre, Taille, Poids, Situation_Matrimoniale, Profession, Arrondissement_Residence, Quartier_Residence, Age, nom) 
                  VALUES (:dateRemplissage, :dateNaissance, :niveauEtude, :genre, :taille, :poids, :situationMatrimoniale, :profession, :arrondissement, :quartier, :age, :nom)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':dateRemplissage', $this->dateRemplissage);
        $stmt->bindParam(':dateNaissance', $this->dateNaissance);
        $stmt->bindParam(':niveauEtude', $this->niveauEtude);
        $stmt->bindParam(':genre', $this->genre);
        $stmt->bindParam(':taille', $this->taille);
        $stmt->bindParam(':poids', $this->poids);
        $stmt->bindParam(':situationMatrimoniale', $this->situationMatrimoniale);
        $stmt->bindParam(':profession', $this->profession);
        $stmt->bindParam(':arrondissement', $this->arrondissementResidence);
        $stmt->bindParam(':quartier', $this->quartierResidence);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':nom', $this->nom);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    // Lire un donneur spécifique avec option info
    public function lire($id, $info = false) {
        $query = "SELECT * FROM Donneurs WHERE ID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $donneur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($donneur && $info) {
            require_once '../views/donneurs/info.php';
            return true;
        }
        return $donneur;
    }

    // Lire tous les donneurs
    public function lireTous() {
        $query = "SELECT * FROM Donneurs";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un donneur
    public function mettreAJour($id) {
        $query = "UPDATE Donneurs SET Date_Remplissage = :dateRemplissage, Date_Naissance = :dateNaissance, Niveau_Etude = :niveauEtude, Genre = :genre, Taille = :taille, Poids = :poids, Situation_Matrimoniale = :situationMatrimoniale, Profession = :profession, Arrondissement_Residence = :arrondissement, Quartier_Residence = :quartier, Age = :age, nom = :nom WHERE ID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':dateRemplissage', $this->dateRemplissage);
        $stmt->bindParam(':dateNaissance', $this->dateNaissance);
        $stmt->bindParam(':niveauEtude', $this->niveauEtude);
        $stmt->bindParam(':genre', $this->genre);
        $stmt->bindParam(':taille', $this->taille);
        $stmt->bindParam(':poids', $this->poids);
        $stmt->bindParam(':situationMatrimoniale', $this->situationMatrimoniale);
        $stmt->bindParam(':profession', $this->profession);
        $stmt->bindParam(':arrondissement', $this->arrondissementResidence);
        $stmt->bindParam(':quartier', $this->quartierResidence);
        $stmt->bindParam(':age', $this->age);
        $stmt->bindParam(':nom', $this->nom);
        return $stmt->execute();
    }
    

    // Supprimer un donneur
    public function supprimer($id) {
        try {
            $this->db->beginTransaction();

            // Supprimer les enregistrements dans conditions_sante
            $queryConditions = "DELETE FROM conditions_sante WHERE ID_Donneur = :id";
            $stmtConditions = $this->db->prepare($queryConditions);
            $stmtConditions->bindParam(':id', $id);
            $stmtConditions->execute();

            // Supprimer le donneur
            $queryDonneur = "DELETE FROM Donneurs WHERE ID = :id";
            $stmtDonneur = $this->db->prepare($queryDonneur);
            $stmtDonneur->bindParam(':id', $id);
            $result = $stmtDonneur->execute();

            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    // Dans classes/Donneur.php, ajoutez cette méthode à la fin de la classe
public function lireDonneesGeographiques() {
    $query = "SELECT Arrondissement_Residence, Quartier_Residence, COUNT(*) as nombre_donneurs 
              FROM Donneurs 
              GROUP BY Arrondissement_Residence, Quartier_Residence";
    $stmt = $this->db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>