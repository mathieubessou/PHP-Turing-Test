<?php
    $contactEmail = CONFIG['ContactForm']['ContactEmail'];
    $formMentions = CONFIG['ContactForm']['FormMentions'];
    $sentNotify = 'Le message à été envoyé.';
    $errorNotify = '<!> ERREUR <!> Le message n\'a pas pu être envoyé.';

    require_once  ToolsPath . '/PHP-Turing-Test/TuringTestUsingDatabase.php';
    
    // Etape 4 : Le message, la réponse et le jeton existe.
    if (!empty($_POST['turingTest_answer']) && !empty($_POST['token'])) {
        // Si la réponse à la question est correcte
        if (TuringTestUsingDatabase::checkResult($_POST['turingTest_answer'], DbConnect::getInstance(), $_POST['token'])) {
            // Envoyer le message.
            if (!empty($_POST['email']) && !empty($_POST['object']) && !empty($_POST['message']))
            {
                $email = $_POST['email'];
                $mailResult = mail(
                    $contactEmail,
                    $_POST['object'],
                    "Email du contact: $email\n\n" . $_POST['message'],
                    array(
                        'From' => $email,
                        'Reply-To' => $email,
                        'X-Mailer' => 'PHP/' . phpversion(),
                        'Content-Type' => 'text/plain; charset=utf-8',
                        'Content-Transfer-Encoding' => '8bit',
                    ));
                    header('location:?sent=' . $mailResult ? 'true' : 'false');
            }
        }
        else {
            unset($_POST['turingTest_answer']); // Effacer la réponse pour de nouveau afficher le Turing test
        }
    }


    // Si le formulaire n'a pas été envoyé
    if (empty($_POST['message'])) {
        // Affichage de la notification
        if (!empty($_GET['sent'])) {
            if ($_GET['sent'] == 'true') {
                echo "<script>alert(\"$sentNotify\");</script>";
            }
            else {
                echo "<script>alert(\"$errorNotify\");</script>";
            }
        }
        // Afficher le formulaire
        echo '
            <h1>Contact</h1>
            <form action="" method="POST">
                <label for="email">Adresse e-mail *</label>
                <input type="email" name="email" id="email" required>
                <br/>
                <label for="object">Objet du message *</label>
                <input type="text" name="object" id="object" required>
                <br/>
                <label for="message">Message *</label>
                <textarea name="message" id="message" rows="10" required></textarea>
                <br/>
                <p class="textAttention">(*) Le remplissage de tous les champs est obligatoire.</p>
                <br/>
            <input type="submit" value="Envoyer !">
            </form>';
            if (!empty($formMentions)) {
                echo '<br/><br/><font size="3">'.$formMentions.'</font>';
            }
    }
    else { // Vérifier si ce n'est pas un robot
        // Si la réponse au test n'a pas été envoyé
        $infoMessageHtml = '<h3>Avant d\'envoyer votre message, nous devons nous assurer que vous n\'êtes pas un robot.</h3>';
        $formAction = '';
        $imageSRC = '/template/images/TuringTestImage.php'; // Indiquer l'emplacement du fichier image.php générant le test
        
        require_once  ToolsPath . '/PHP-Turing-Test/TuringTestView.php';
        TuringTestView::prepare($formAction, $infoMessageHtml, $imageSRC, true);
        TuringTestView::showView();
    }
?>