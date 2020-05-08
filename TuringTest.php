<?php
    // Class permettant de vérifier si celui qui passe le test est un robot ou un humain.
    class TuringTest
    {
        protected static $letterKey1;
        protected static $letterKey2;
        protected static $number1;
        protected static $number2;
        protected static $symbolKey;
        protected static $result;
        
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
            self::$letterKey1 = rand(0, 25);
            
            restartCL2:
            self::$letterKey2 = rand(0, 25);
            if (self::$letterKey2 == self::$letterKey1) GOTO restartCL2;
            
            self::$symbolKey = rand(0, 3);
            
            self::$number1 = rand(0, 10);
            if (self::$symbolKey > 1) self::$number2 = rand(0, self::$number1);
            else self::$number2 = rand(0, 10);

            // Correction
            if (self::$symbolKey < 2) self::$result = self::$number1 + self::$number2;
            else self::$result = self::$number1 - self::$number2;
        }


        // Créer une image suivant les données généré par la méthode valueGenerator().
        // <!>Attention! Cette méthode doit être utilisé dans un fichier vide et non inclus via les méthodes d'inclusion de fichier (exemple: include(), require(), ...)
        /* Exemple d'utilisation:
            require '/Path/To/TuringTest.php';
            TuringTest::createTuringTestImage();
        */
        public static function createTuringTestImage()
        {
            header ("Content-type: image/png");
            self::valueGenerator();
            imagepng(self::getTuringTestImageString());
        }

        // Récupère les données de l'image généré à partir des informations stocké dans la classe.
        protected static function getTuringTestImageString()
        {
            $image = imagecreate(130,90);
            $white = imagecolorallocate($image, 255, 255, 255);
            $black = imagecolorallocate($image, 0, 0, 0);

            if (!isset(self::$letterKey1) || !isset(self::$letterKey2) ||
            !isset(self::$number1) || !isset(self::$number2) ||
            !isset(self::$symbolKey) || !isset(self::$result))
            {
                imagestring($image, 4, 10, 10, "", $black);
                imagestring($image, 4, 10, 30, 'ERREUR !', $black);
                imagestring($image, 4, 10, 50, 'Formulaire', $black);
                imagestring($image, 4, 10, 70, 'indisponible', $black);
            }
            else
            {
                $p1 = "Si " . self::$letters[self::$letterKey1] . " = " . self::$number1;
                $p2 = "et que " . self::$letters[self::$letterKey2] . " = " . self::$number2;
                $p3 = "______________";
                $p4 = self::$letters[self::$letterKey1] . " " . self::$symbols[self::$symbolKey] . " " . self::$letters[self::$letterKey2] . " = ?";

                imagestring($image, 4, 10, 10, $p1, $black);
                imagestring($image, 4, 10, 30, $p2, $black);
                imagestring($image, 4, 10, 50, $p3, $black);
                imagestring($image, 4, 10, 70, $p4, $black);
            }
            
            return $image;
        }

        // Indique si la valeur passé en paramètre correspond au résultat attendu.
        /* Exemple d'utilisation
            require '/Path/To/TuringTest.php';
            TuringTest::isValid($_POST['turingTestAnswer'])
        */
        public static function isValid($answer)
        {
            if (!isset(self::$result) || $answer == null) return false;
            return $answer == self::$result;
        }

        public static function getResult()
        {
            return self::$result;
        }
    }