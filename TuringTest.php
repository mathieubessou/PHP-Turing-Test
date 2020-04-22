<?php
    class TuringTest
    {
        // Symbols
        protected static $symbols = array("+", "plus", "-", "moins");
        
        // Lettres
        protected static $letters = array(
            "A", "B", "C", "D", "E", "F", "G",
            "H", "I", "J", "K", "L", "M", "N",
            "O", "P", "Q", "R", "S", "T", "U",
            "V", "W", "X", "Y", "Z");
            
        // Génère les valeurs pour la création et la correction du test
        protected static function valueGenerator()
        {
            $letterKey1 = rand(0, 25);
            
            restartCL2:
            $letterKey2 = rand(0, 25);
            if ($letterKey2 == $letterKey1) GOTO restartCL2;
            
            $symbolKey = rand(0, 3);
            
            $number1 = rand(0, 10);
            if ($symbolKey > 1)$number2 = rand(0, $number1);
            else $number2 = rand(0, 10);

            $_SESSION['letterKey1'] = $letterKey1;
            $_SESSION['letterKey2'] = $letterKey2;
            $_SESSION['number1'] = $number1;
            $_SESSION['number2'] = $number2;
            $_SESSION['symbolKey'] = $symbolKey;

            // Mise en mémoire de la correction
            if ($symbolKey < 2)
            {
                $_SESSION['turingTestResult'] = $number1 + $number2;
            }
            else
            {
                $_SESSION['turingTestResult'] = $number1 - $number2;
            }
            // FIN -- Mise en mémoire de la correction
        }

        // Créer une image suivant les données généré par la méthode valueGenerator().
        // <!>Attention! Cette méthode doit être utilisé dans un fichier vide et non inclus via les méthodes d'inclusion de fichier (exemple: include(), require(), ...)
        /* Exemple d'utilisation:
            require '/Path/To/TuringTest.php';
            TuringTest::createTuringTestImage();
        */
        public static function createTuringTestImage()
        {
            session_start();
            header ("Content-type: image/png");
            self::valueGenerator();
            imagepng(self::getTuringTestImageString());
        }

        // Récupère les données de l'image généré à partir des informations stocké en session.
        protected static function getTuringTestImageString()
        {
            $image = imagecreate(130,90);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);

            if (!isset($_SESSION['letterKey1']) || !isset($_SESSION['letterKey2']) ||
            !isset($_SESSION['number1']) || !isset($_SESSION['number2']) ||
            !isset($_SESSION['turingTestResult']))
            {
                imagestring($image, 4, 10, 10, "", $black);
                imagestring($image, 4, 10, 30, 'ERREUR !', $black);
                imagestring($image, 4, 10, 50, 'Formulaire', $black);
                imagestring($image, 4, 10, 70, 'indisponible', $black);
            }
            else
            {
                $p1 = "Si " . self::$letters[$_SESSION['letterKey1']] . " = " . $_SESSION['number1'];
                $p2 = "et que " . self::$letters[$_SESSION['letterKey2']] . " = " . $_SESSION['number2'];
                $p3 = "______________";
                $p4 = self::$letters[$_SESSION['letterKey1']] . " " . self::$symbols[$_SESSION['symbolKey']] . " " . self::$letters[$_SESSION['letterKey2']] . " = ?";

                imagestring($image, 4, 10, 10, $p1, $black);
                imagestring($image, 4, 10, 30, $p2, $black);
                imagestring($image, 4, 10, 50, $p3, $black);
                imagestring($image, 4, 10, 70, $p4, $black);
            }
            
            return $image;
        }

        // Indique si la valeur passé en paramètre correspond au résulta attendu.
        /* Exemple d'utilisation
            require '/Path/To/TuringTest.php';
            TuringTest::isValid($_POST['turingTestAnswer'])
        */
        public static function isValid($turingTestAnswer)
        {
            if (!isset($_SESSION['turingTestResult'])) return false;
            return $turingTestAnswer == $_SESSION['turingTestResult'];
        }
    }