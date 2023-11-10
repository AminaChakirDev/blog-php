<?php

namespace App\Controller;

use App\Model\ArticleManager;
use App\Model\CategoryManager;

class ArticleController extends AbstractController
{
    /**
     * List articles
     */
    public function index(): string
    {
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAllWithCategory();

        return $this->twig->render('Article/index.html.twig', ['articles' => $articles]);
    }

    public function adminIndex(): string
    {
        $articleManager = new ArticleManager();
        $articles = $articleManager->selectAllWithCategory();

        return $this->twig->render('Admin/Article/index.html.twig', ['articles' => $articles]);
    }

    /**
     * Show informations for a specific article
     */
    public function show(int $id): string
    {
        $articleManager = new ArticleManager();
        $article = $articleManager->selectOneByIdWithCategory($id);

        return $this->twig->render('Article/show.html.twig', ['article' => $article]);
    }

    /**
     * Edit a specific article
     */

    public function edit(int $id): ?string
    {
        $articleManager = new ArticleManager();
        $article = $articleManager->selectOneByIdWithCategory($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // clean $_POST data
            $article = array_map('trim', $_POST);

            // TODO validations (length, format...)

            // if validation is ok, update and redirection
            $articleManager->update($article);

            header('Location: /admin/articles');

            // we are redirecting so we don't want any content rendered
            return null;
        }

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();

        return $this->twig->render('Admin/Article/edit.html.twig', [
            'article' => $article,
            'categories' => $categories,
        ]);
    }

    /**
     * Add a new article
     */
    public function add(): ?string
    {
        $errors = [];

        $categoryManager = new CategoryManager();
        $categories = $categoryManager->selectAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          // clean $_POST data
            $article = array_map('trim', $_POST);

          // Securité en php
          // chemin vers un dossier sur le serveur qui va recevoir les fichiers uploadés (attention ce dossier doit être accessible en écriture)
            $uploadDir = '/Users/aminachakir/www/2309_PHP/97_projects/projects2/prepa-fil-rouge-blog/public/uploads/';
          // le nom de fichier sur le serveur est ici généré à partir du nom de fichier sur le poste du client (mais d'autre stratégies de nommage sont possibles)
            $uploadFile = $uploadDir . uniqid() . '_' . basename($_FILES['avatar']['name']);

          // Je récupère l'extension du fichier
            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);

          // Les extensions autorisées
            $authorizedExtensions = ['jpg','jpeg','png'];
          // Le poids max géré par PHP par défaut est de 2M
            $maxFileSize = 2000000;

          // Je sécurise et effectue mes tests

          /****** Si l'extension est autorisée *************/
            if ((!in_array($extension, $authorizedExtensions))) {
                $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
            }

          /****** On vérifie si l'image existe et si le poids est autorisé en octets *************/
            if (file_exists($_FILES['avatar']['tmp_name']) && filesize($_FILES['avatar']['tmp_name']) > $maxFileSize) {
                $errors[] = "Votre fichier doit faire moins de 2M !";
            }

            if ($errors) {
                return $this->twig->render('Admin/Article/add.html.twig', ['categories' => $categories, 'errors' => $errors,]);
            }


            if (empty($errors)) {
            // on déplace le fichier temporaire vers le nouvel emplacement sur le serveur. Ça y est, le fichier est uploadé
                move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile);
            }

          // TODO validations (length, format...)

          // if validation is ok, insert and redirection
            $articleManager = new ArticleManager();
            $id = $articleManager->insert($article);

            header('Location:/admin/articles');
            return null;
        }



        return $this->twig->render('Admin/Article/add.html.twig', ['categories' => $categories, 'errors' => $errors,]);
    }

    /**
     * Delete a specific article
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id']);
            $articleManager = new ArticleManager();
            $articleManager->delete((int)$id);

            header('Location:/admin/articles');
        }
    }
}
