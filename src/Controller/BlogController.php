<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
        ]);
    }
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('blog/home.html.twig',[
            'slogan' => " la démo d'un blog",
            'age'=> 28
        ]);
    }
    #[Route('/blog/show/{id}', name: 'blog_show')]

    public function show($id, ArticleRepository $repo)
    {
        $article = $repo->find($id);
        return $this->render('blog/show.html.twig', [
            'item'=> $article
        ]);
    }
    #[Route('/blog/new', name: 'blog_create')]
    #[Route('/blog/edit/{id}', name: 'blog_edit')]

    public function form(Request $globals, EntityManagerInterface $manager, Article $article = null)
    {// la classe Request contient les données véhiculées par les uperglobales ($_Post, $_Get, $_server)
        if($article == null)
        {
            $article = new Article; // je crée un objet  de la classe Article vide prêt à être rempli
            $article->setCreatedAt(new \DateTime); // ajout de la date de création  seulement à l'insertion d'un article
        }
        
        
        $form= $this->createForm(ArticleType::class, $article);// createForm() permet de récupérer un formulaire

        $form->handleRequest($globals);

        dump($article);
            if ($form->isSubmitted() && $form->isValid())
            {

               
                $manager->persist($article); // prepare l'insertion de l'article en bdd
                $manager->flush(); //exécute la requête d'insertion
                return $this->redirectToRoute('blog_show', [
                    'id'=> $article->getId()// cette méthode nous permet de nous rediriger vers la page de notre article nouvellement créé
                ]);
            }

        return $this->renderForm("blog/form.html.twig",[
            'formArticle' => $form,
            'editMode' => $article->getId() !== null
            ]);
    }
    #[Route('/blog/delete/{id}', name: 'blog_delete')]
    public function delete($id, EntityManagerInterface $manager, ArticleRepository  $repo)
    {
        $article = $repo->find($id);
        $manager->remove($article);
        $manager->flush();

        return $this->redirectToRoute('app_blog');
    }
}
