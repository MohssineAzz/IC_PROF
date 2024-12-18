<?php
namespace Test;

use Cours;
use PHPUnit\Framework\TestCase;
use Prof;

class ProfCoursTest extends TestCase
{

    //   #######    DO NOT CHANGE THIS     #########
    const FAKE_DBNAME = "##DB_NAME##";
    const SQL_FILE = "db.sql";

    //  #######    CHANGE THIS TO HAVE CREDENTIAL OF YOUR DATABASE       ##########
    const DB_USER = "user01";
    const DB_PASS = "user01";
    const DB_NAME = "user01_test_php";
    const DB_HOST = "192.168.250.3";

    public static $conn = null;
    // Prof
    private $prenom ="REVERGIE";
    private $nom ="TATSUM";
    private $date ="22/07/1984";
    private $lieu ="Toulouse, France";

    // cours
    private $intitule="Intégration continue";
    private $duree="3h";

    private static $prof_a = [];
    private static $cours_a = [];

    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        echo __METHOD__."\n";
        if(self::$conn===null){
            try {
                if (file_exists(self::SQL_FILE)) {
                    self::$conn = new \PDO('mysql:host=' . self::DB_HOST . ';charset=utf8', self::DB_USER, self::DB_PASS);
                    self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    self::$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
                    $sql_db = file_get_contents(self::SQL_FILE);
                    $sql_db = str_replace(self::FAKE_DBNAME, self::DB_NAME, $sql_db);
                    $sql_stmt = self::$conn->prepare($sql_db);
                    if($sql_stmt->execute()) {
                        print "Creation à la base de données ". self::DB_NAME." REUSSIE \n";
                        $sql_stmt->closeCursor();
                        self::$conn->query("USE ".self::DB_NAME.";")->closeCursor();
                        print "Connexion à la base de donnée \n";
                    } else {
                        echo 'Creation de la base de données '. self::DB_NAME .' ECHOUEE';
                    }
                } else {
                    self::$conn = null;
                    die("LE FICHIER ".self::SQL_FILE."EST INNEXISTANT.\n");
                }
            } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }

        print "Création des variables. \n";
        self::$prof_a = [


            /**
             *
             * Question 6 : Insérer les enregistrements suivantes dans la table prof
             *
             */

            new Prof("Nom_Prof1", "Prenom_Prof1", "10/01/1982", "lieu_prof1"),
            new Prof("Nom_Prof2", "Prenom_Prof2", "10/02/1982", "lieu_prof2"),
            new Prof("Nom_Prof3", "Prenom_Prof3", "10/03/1982", "lieu_prof3"),
            new Prof("Nom_prof4", "Prenom_prof4", "10/04/1982", "lieu_prof4"),      // idprof = 4
            new Prof("Nom_prof5", "Prenom_prof5", "10/05/1982", "lieu_prof5"),      // idprof = 5
            new Prof("Nom_prof6", "Prenom_prof6", "10/06/1982", "lieu_prof6"),      // idprof = 6
            new Prof("Nom_prof7", "Prenom_prof7", "10/07/1982", "lieu_prof7"),      // idprof = 7
            new Prof("Nom_prof8", "Prenom_prof8", "10/08/1982", "lieu_prof8"),      // idprof = 8       ** A SUPPRIMER **
            new Prof("Nom_prof9", "Prenom_prof9", "10/09/1982", "lieu_prof9"),      // idprof = 9
            new Prof("Nom_prof10", "Prenom_prof10", "10/10/1982", "lieu_prof10")    // idprof = 10      ** A MODIFIER **
        ];

        self::$cours_a = [
            new Cours("IoT",10,1),
            new Cours("IA",12,3),
            new Cours("EDL",5,6),
            new Cours("Cours1", "2", 1),       // idcours = 1
            new Cours("Cours2", "2.5", 3),     // idcours = 2
            new Cours("Cours3", "3", 5),       // idcours = 3
            new Cours("Cours4", "2", 3),       // idcours = 4
            new Cours("Cours5", "3", 3),       // idcours = 5
            new Cours("Cours6", "2", 4),       // idcours = 6


            /**
             *
             * Question 7 : Insérer les enregistrements suivantes dans la table cours
             *
             */

        ];

    }


    public static function tearDownAfterClass(): void{
        parent::tearDownAfterClass();
        print __METHOD__."\n";
        if(self::$conn===null){
            print "Connexion NULL \n";
            self::$conn = (new ProfCoursTest)->getConnection();
        }
        self::$conn->exec('DROP DATABASE IF EXISTS '.self::DB_NAME);
        print "SUPPRESSION DE LA BASE DONNEE ". self::DB_NAME ." REUSSIE \n";
        self::$conn = null;

        print "SUPPRESSION DES VARIABLES. \n";
        self::$prof_a = [];
        self::$cours_a = [];
    }


    /**
     * Returns the test database connection.
     *
     * @return \PDO
     */
    protected function getConnection()
    {
        if(self::$conn === null){
            self::$conn = new \PDO('mysql:host=localhost;dbname='.self::DB_NAME.';charset=utf8', 'root', '');
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        }
        return self::$conn;
    }


    protected function setUp():void
    {
        parent::setUp();
        print __METHOD__."\n";
    }


    protected function tearDown():void
    {
        parent::tearDown();
        print __METHOD__."\n\n";
    }


    /**
     * Respect this order
     * 1. Add Prof
     * 2. Add Cours
     * @order 1
     */
    public function testAdd()
{
    print __METHOD__."\n";
    $conn = $this->getConnection();

    // Prof
    print "ADD prof \n";
    foreach (self::$prof_a as $prof) {
        $prof->add($conn);
    }
    $expected = count(self::$prof_a);
    $num_records = Prof::count($conn);
    $this->assertEquals($expected, $num_records, "Enregistrement des profs ...\n");
    $this->assertCount($num_records, self::$prof_a, "Enregistrement des profs ...\n");

    // Cours
    print "ADD cours \n";
    foreach (self::$cours_a as $cours) {
        $cours->add($conn);
    }
    $expected_cours = count(self::$cours_a);
    $num_records_cours = Cours::count($conn);
    $this->assertEquals($expected_cours, $num_records_cours, "Enregistrement des cours ...\n");
    $this->assertCount($num_records_cours, self::$cours_a, "Enregistrement des cours ...\n");

    print "Tous les tests d'ajout sont passés.\n";
}



    /**
     * Nous compterons le nobre d'enregistrement que nous comparerons au nombre d'élément du tableau.
     * REQUIRE: Assurez vous qu'aucune suppression n'a été faite.
     * @order 2
     */
    public function testPrintAll()
{
    print __METHOD__."\n";
    $conn = $this->getConnection();

    // Prof
    $record_prof_a = Prof::printAll($conn);
    print "########## - LISTE DES PROFS - AVANT TOUT ########## \n";
    foreach ($record_prof_a as $record_prof) {
        print $record_prof . "\n";
    }
    print "################################################################\n\n";
    $this->assertCount(count(self::$prof_a), $record_prof_a, "Nombre d'enregistrement égale pour Prof\n");

    // Cours
    $record_cours_a = Cours::printAll($conn);
    print "########## - LISTE DES COURS - AVANT TOUT ########## \n";
    foreach ($record_cours_a as $record_cours) {
        print $record_cours . "\n";
    }
    print "################################################################\n\n";
    $this->assertCount(count(self::$cours_a), $record_cours_a, "Nombre d'enregistrement égale pour Cours\n");
}



    /**
     * Liste des cours et leur Prof
     * => Cours...
     *      =W prof ...
     * @order 3
     * @doesNotPerformAssertions
     */
    public function testGetMyProf() {
        print __METHOD__."\n";
        $conn = $this->getConnection();
        $cours_a = self::$cours_a;
        print "+++++++++++++++++++++ - LISTE DES COURS ET LEUR PROF - ++++++++++++++++++++\n";
        foreach ($cours_a as $cours){
            $prof = $cours->getMyProf($conn);
            print $cours ."\t". $prof."\n";
        }
        print "++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++\n\n";
    }


    /**
     * Affichage d'un enregistrement
     * @order 4
     */
    public function testPrintOne()
{
    print __METHOD__."\n";
    $conn = $this->getConnection();

    // Prof
    $prof = Prof::printOne($conn);
    $prof_str = $prof->__toString();
    print "########## - 1er PROF EN BASE - ########## \n";
    print $prof_str . "\n";
    print "################################################################\n\n";
    $expected = self::$prof_a[0]->__toString();
    $this->assertEquals($expected, $prof_str, "Prof \n");

    // Cours
    $cours = Cours::printOne($conn);
    $cours_str = $cours->__toString();
    print "########## - 1er COURS EN BASE - ########## \n";
    print $cours_str . "\n";
    print "################################################################\n\n";
    $expected = self::$cours_a[0]->__toString();
    $this->assertEquals($expected, $cours_str, "Cours \n");

    // Avec des IDs spécifiques
    $idProf = 10;
    $idCours = 9;

    // Prof
    $prof = Prof::printOne($conn, $idProf);
    $prof_str = $prof->__toString();
    print "########## - ${idProf}e PROF EN BASE - ########## \n";
    print $prof_str . "\n";
    print "################################################################\n\n";
    $expected = self::$prof_a[$idProf - 1]->__toString();
    $this->assertEquals($expected, $prof_str, "Prof \n");

    // Cours
    $cours = Cours::printOne($conn, $idCours);
    $cours_str = $cours->__toString();
    print "@@@@@@@@@@@@@ - ${idCours}e COURS EN BASE - @@@@@@@@@@@@@ \n";
    print $cours_str . "\n";
    print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
    $expected = self::$cours_a[$idCours - 1]->__toString();
    $this->assertEquals($expected, $cours_str, "Cours \n");
}



    /**
     * Mise à jour des enregistrements
     * 1. UPDATE prof num 10
     * 2. UPDATE cours num 9
     * @order 5
     */
public function testUpdateOne()
{
    print __METHOD__ . "\n";
    $conn = $this->getConnection();

    // Avec Id en dur.
    $idProf = 10;
    $idCours = 9;

    // ============================
    // Test de la modification du Prof
    // ============================
    $prof = new Prof($this->nom, $this->prenom, $this->date, $this->lieu);
    $val = $prof->updateOne($conn, $idProf);
    $expected_prof_str = $prof->__toString();
    $record_prof = Prof::printOne($conn, $idProf);

    $this->assertEquals($expected_prof_str, $record_prof->__toString(), "Update du prof $idProf ...\n");
    $this->assertTrue($val, "Update du prof num $idProf ...\n");

    // ============================
    // Test de la modification du Cours
    // ============================
    $cours = new Cours($this->titre, $this->description, $this->dateDebut, $this->dateFin);
    $val_cours = $cours->updateOne($conn, $idCours);
    $expected_cours_str = $cours->__toString();
    $record_cours = Cours::printOne($conn, $idCours);

    $this->assertEquals($expected_cours_str, $record_cours->__toString(), "Update du cours $idCours ...\n");
    $this->assertTrue($val_cours, "Update du cours num $idCours ...\n");

    // ============================
    // Affichage des Profs après Update
    // ============================
    print "########## - LISTE DES PROFS - APRES UPDATE DU PROF NUM $idProf ########## \n";
    foreach ($record_prof_a = Prof::printAll($conn) as $record_prof) {
        print $record_prof;
    }
    print "################################################################\n\n";

    // ============================
    // Affichage des Cours après Update
    // ============================
    print "@@@@@@@@@@@@@ - LISTE DES COURS - APRES UPDATE DU COURS NUM $idCours @@@@@@@@@@@@@ \n";
    foreach ($record_cours_a = Cours::printAll($conn) as $record_cours) {
        print $record_cours;
    }
    print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
}



    /**
     *
     */
    public function testUpdateOne_2()
    {
        print __METHOD__."\n";
        $conn = $this->getConnection();
        // ############################### - ####################################"
        // Sans Id. - Le premier enregistrement sera traiter à chaque fois (ce que retourne la méthode getOneId)
        // Prof
        $prof = new Prof($this->nom, $this->prenom, $this->date, $this->lieu);
        $val = $prof->updateOne($conn);
        $expected_prof_str = $prof->__toString();
        $record_prof = Prof::printOne($conn);
        $this->assertEquals($expected_prof_str, $record_prof->__toString(), "Update du 1e prof ...\n");
        $this->assertTrue($val, "Update du 1e prof ...\n");

        // Cours
        $cours = new Cours($this->intitule, $this->duree, 10);
        $val = $cours->updateOne($conn);
        $expected_cours_str= $cours->__toString();
        $record_cours = Cours::printOne($conn);
        $this->assertEquals($expected_cours_str, $record_cours->__toString(), "Update du 1e cours  ...\n");
        $this->assertTrue($val, "Update du 1e cours ...\n");

        print "########## - LISTE DES PROFS - APRES UPDATE DU 1e PROF  ########## \n";
        foreach ( $record_prof_a = Prof::printAll($conn) as $record_prof ) {
            print $record_prof;
        }
        print "################################################################\n\n";
        print "@@@@@@@@@@@@@ - LISTE DES COURS - APRES UPDATE DU 1e COURS @@@@@@@@@@@@@ \n";
        foreach( $record_cours_a = Cours::printAll($conn) as $record_cours ) {
            print $record_cours;
        }
        print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
    }

    /**
     * Suppression d'un enregistrement.
     * @order 6
     */
    public function testDeleteOne()
{
    print __METHOD__ . "\n";
    $conn = $this->getConnection();

    // Suppression avec id à supprimer.
    $idProf = 8; // ID du professeur à supprimer
    $idCours = 7; // ID du cours à supprimer

    // Suppression du professeur
    $val = Prof::deleteOne($conn, $idProf);
    $this->assertTrue($val, "Prof num $idProf supprimé avec succès\n");

    // Vérification après suppression
    $record_prof_a = Prof::printAll($conn);
    print "########## - LISTE DES PROFS APRES SUPPRESSION - Vérifiez le prof num $idProf ########## \n";
    foreach ($record_prof_a as $record_prof) {
        print $record_prof;
    }
    print "################################################################\n\n";

    // Suppression du cours
    $val = Cours::deleteOne($conn, $idCours);
    $this->assertTrue($val, "Cours num $idCours supprimé avec succès\n");

    // Vérification après suppression
    $record_cours_a = Cours::printAll($conn);
    print "@@@@@@@@@@@@@ - LISTE DES COURS APRES SUPPRESSION - Vérifiez le cours num $idCours @@@@@@@@@@@@@ \n";
    foreach ($record_cours_a as $record_cours) {
        print $record_cours;
    }
    print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
}



public function testDeleteOne_2()
{
    print __METHOD__ . "\n";
    $conn = $this->getConnection();

    // Suppression sans id spécifié ==> Suppression du premier enregistrement
    // Suppression du premier professeur
    $val = Prof::deleteOne($conn);
    $this->assertTrue($val, "Premier Prof supprimé avec SUCCÈS\n");

    // Vérification après suppression
    $record_prof_a = Prof::printAll($conn);
    print "########## - LISTE DES PROFS APRES SUPPRESSION - Vérifier avec celui juste avant (1er supprimé) ########## \n";
    foreach ($record_prof_a as $record_prof) {
        print $record_prof;
    }
    print "################################################################\n\n";

    // Suppression du premier cours
    $val = Cours::deleteOne($conn); // Suppression sans spécifier d'id supprime le premier
    $this->assertTrue($val, "Premier Cours supprimé avec SUCCÈS\n");

    // Vérification après suppression
    $record_cours_a = Cours::printAll($conn);
    print "@@@@@@@@@@@@@ - LISTE DES COURS APRES SUPPRESSION - Vérifier avec celui juste avant (1er supprimé) @@@@@@@@@@@@@ \n";
    foreach ($record_cours_a as $record_cours) {
        print $record_cours;
    }
    print "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@\n\n";
}


}
