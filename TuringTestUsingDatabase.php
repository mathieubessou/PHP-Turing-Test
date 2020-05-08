<?php
    /*
        Exemple d'utilisation:

        ********************************
        A placer dans le fichier image.php
        ********************************
        <?php
        require_once  __DIR__ . '/../../../Framework/DbConnect.php';
        require_once  __DIR__ . '/TuringTestUsingDatabase.php';
        require_once __DIR__ . "/../../../../config.php";

        // Connexion à la base de donnée
        $db = DbConnect::newConnection(
            CONFIG['DbInfo']['ServerName'],
            CONFIG['DbInfo']['DbName'],
            CONFIG['DbInfo']['Username'],
            CONFIG['DbInfo']['Password']
        );

        if (!empty($_GET['token'])) {
            TuringTestUsingDatabase::createTestAndSaveResult($db, $_GET['token']);
        }
        ?>
    */


    require_once 'TuringTest.php';

    // Requis pour l'utilisation des méthodes saveResult() et loadResult() :
    // - Créer la table suivante: turing_test (token CHAR(128) NOT NULL, result INT(2) NOT NULL, date DATE NOT NULL)
    // - Le teste doit être affiché dans une page séparé du formulaire car le jeton créé pour le test est valide que pour 2 minutes.
    
    /*  Etapes :
        1) Créer et récupérer un nouveau jeton dans la page où se trouve le formulaire.
        2) Transmettre ce jeton au fichier TuringTestImage.php?t=XXXXXXXX.
        3) Génération du fichier images contenant le test et sauvegarde du jeton ainsi que du résultat dans la base de données.
        4) Faire en sorte que le jeton est transmis dans la page suivant la validation du formulaire.
        5) Récupérer le résultat enregistré dans la base de données.
        5) Vérifier le résultat entré par l'utilisateur.
    */
    class TuringTestUsingDatabase extends TuringTest
    {
        const DbDateTimeFormat = 'Y-m-d H:i:s';
        protected static $token; // utilisé que pour l'utilisation de la base de données

        // Sauvegarde les données lié au test dans la base de données et retourne le jeton utilisé.
        // $oldToken permet de supprimer l'ancien jeton utilisé et ainsi libérer l'espace.
        protected static function saveResult($db, $token)
        {
            self::$token = $token;
            $dtn = new DateTime('now');
            $dateTimeNowForSql = $dtn->format(self::DbDateTimeFormat);
            $interval = new DateInterval('PT2M');
            $limitedDay = $dtn->sub($interval);
            $limitedDayForSql = $limitedDay->format(self::DbDateTimeFormat);

            // Effacer les données trop anciennes.
            $req = $db->prepare("DELETE FROM turing_test WHERE dateTime < :ldt OR token = :token");
            $req->bindValue('ldt', $limitedDayForSql, PDO::PARAM_STR);
            $req->bindValue('token', $token, PDO::PARAM_STR);
            $req->execute();

            // Enregistrement du resultat
            $req = $db->prepare("INSERT INTO turing_test (token, result, dateTime) VALUES(:token, :result, :dateTime)");
            $req->bindValue('token', self::$token, PDO::PARAM_STR);
            $req->bindValue('result', self::$result, PDO::PARAM_INT);
            $req->bindValue('dateTime', $dateTimeNowForSql, PDO::PARAM_STR);
            $req->execute();
            return self::$token;
        }
        
        // Charge le résultat depuis la base de données.
        // Si le résulté est présent dans la base de données et est encore valide (2 min) : return le résultat.
        // Sinon returne NULL.
        protected static function loadResult($db, $token)
        {
            $dtn = new DateTime('now');
            $interval = new DateInterval('PT2M');
            $limitedDay = $dtn->sub($interval);
            $limitedDayForSql = $limitedDay->format(self::DbDateTimeFormat);

            $req = $db->prepare("SELECT result FROM turing_test WHERE token = :token AND dateTime > :dateTime");
            $req->bindValue('token', $token, PDO::PARAM_STR);
            $req->bindValue('dateTime', $limitedDayForSql, PDO::PARAM_STR);
            $req->execute();
            
            $result = $req->fetch()['result'];

            if (!empty($result)) {
                return self::$result = $result;
            }
            else {
                return null;
            }
        }

        // Créer et retourne un nouveau jeton
        public static function newToken()
        {
            return bin2hex(random_bytes(16));
        }

        public static function createTestAndSaveResult($db, $token)
        {
            self::createTuringTestImage();
            self::saveResult($db, $token);
        }

        public static function checkResult($answer, $db, $token)
        {
            self::loadResult($db, $token);
            return self::isValid($answer);
        }

    }
        