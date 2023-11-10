<?php

namespace App\Controller;

use App\Model\UserManager;

class AuthController extends AbstractController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire de connexion
            $email = $_POST['email'];
            $password = $_POST['password'];

            $authManager = new UserManager();
            $user = $authManager->validateUser($email, $password);

            // Valider les données (ex. vérifier si l'utilisateur existe en base de données)
            if ($user) {
                // L'utilisateur est authentifié, rediriger vers la page d'accueil ou une autre page
                header('Location: /');
                exit();
            } else {
                // Authentification échouée, afficher un message d'erreur
                $errorMessage = "Nom d'utilisateur ou mot de passe incorrect.";
                return $this->twig->render('Auth/login.html.twig', ['error' => $errorMessage]);
            }
        } else {
            // Afficher le formulaire de connexion
            return $this->twig->render('Auth/login.html.twig');
        }
    }
}
