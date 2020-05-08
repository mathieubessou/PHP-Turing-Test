<?php
    /* utilisation:
        TuringTestView::prepare($formAction, $infoMessageHtml, $imageSRC, $useToken);
        TuringTestView::showView();
    */

    require_once 'TuringTestUsingDatabase.php';

    class TuringTestView
    {
        public static function prepare(
            string $formAction, string $infoMessageHtml, string $imageSRC, bool $useToken) {

            self::$formAction = $formAction;
            self::$infoMessageHtml = $infoMessageHtml;
            self::$imageSRC = $imageSRC;
            self::$useToken = $useToken;
        }

        protected static $formAction;
        protected static $infoMessageHtml;
        protected static $imageSRC;
        protected static $useToken;

        public static function showView()
        {
            $imageTag = '';
            $tokenInput = '';
            if (self::$useToken) {
                $token = TuringTestUsingDatabase::newToken();
                $imageTag = '<img src="'.self::$imageSRC.'?token='.$token.'" style="width: 130px;">';
                $tokenInput = '<input type="hidden" name="token" value="'.$token.'" />';
            }
            
            // Préparation du formulaire
            if (empty($_POST['turingTest_answer'])) {
                $content = '';
                $content .= self::$infoMessageHtml . '
                    <form action="' . self::$formAction . '" method="post">
                    <br/>
                    <label for="turingTest_answer">Veuillez résoudre le problème ci-dessous</label>
                    ' . $imageTag . '
                    <br/>
                    <input type="text" name="turingTest_answer" id="turingTest_answer" size="10" required/>
                    <br/>
                    ' . $tokenInput;
                // Renvoyer les données du formulaire précédent
                foreach ($_POST as $key => $value) {
                    if ($key == 'token' || $key == 'turingTest_answer') continue;
                    $content .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
                }
                $content .= '<input type="submit" value="Vérifier">
                    </form>';
                // Affichage du formulaire permettant de passer le test
                echo $content;
            }
            else {
                // La réponse au test doit être capturé plus haut.
            }
        }
    }