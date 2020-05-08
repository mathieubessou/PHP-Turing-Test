<?php
    /*
        Exemple d'utilisation:

        ********************************
        A placer dans le fichier image.php
        ********************************
        <?php
        session_start();
        require_once  __DIR__ . '/TuringTestUsingSession.php';
        TuringTestUsingSession::createTestAndSaveResult();
        ?>

    */



    require_once 'TuringTest.php';

    class TuringTestUsingSession extends TuringTest
    {
        // Sauvegarde le résultat dans la session
        protected static function saveResult()
        {
            $_SESSION['result'] = self::$result;
        }

        // Récupère le résultat depuis la session
        protected static function loadResult()
        {
            if (isset($_SESSION['result'])) {
                return self::$result = $_SESSION['result'];
            }
            else {
                return null;
            }
        }

        public static function createTestAndSaveResult()
        {
            self::createTuringTestImage();
            self::saveResult();
        }

        public static function checkResult($answer)
        {
            self::loadResult();
            return self::isValid($answer);
        }
    }
        