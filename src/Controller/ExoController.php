<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExoController extends AbstractController
{
    #[Route('/exo', name: 'app_exo')]
    public function index(): Response
    {
        return $this->render('exo/index.html.twig', [
            'controller_name' => 'ExoController',
        ]);
    }


    #[Route('/exo/voiture', name: 'voiture')]
    public function voiture(): Response
    {
        return $this->render('exo/voiture.html.twig',[
            'voiture' => "R5",
            'description'=> " Voiture sport rouge cher",
            'prix'=> 2500,
        
        
        ]);
    }
    #[Route('/voiture/liste', name:'voiture_liste')]
    public function liste (VoitureRepository $repo)
    {
        $voiture = $repo->findAll();
        return $this->render('exo/liste.html.twig', [
            'voitures' => $voiture,
        ]);
    }
    #[Route('/voiture/new', name: 'voiture_new')]
    #[Route('/voiture/edit/{id}', name: 'voiture_edit')]

    public function form(Request $globals, EntityManagerInterface $manager, Voiture $voiture = null)
    {// la classe Request contient les données véhiculées par les uperglobales ($_Post, $_Get, $_server)
        if($voiture == null)
        {
            $voiture = new Voiture; // je crée un objet  de la classe Article vide prêt à être rempli
            
        }
        
        
        $form= $this->createForm(VoitureType::class, $voiture);// createForm() permet de récupérer un formulaire

        $form->handleRequest($globals);

       // dump($voiture);
            if ($form->isSubmitted() && $form->isValid())
            {

               
                $manager->persist($voiture); // prepare l'insertion de l'article en bdd
                $manager->flush(); //exécute la requête d'insertion
                return $this->redirectToRoute('voiture_liste', [
                    'id'=> $voiture->getId()// cette méthode nous permet de nous rediriger vers la page de notre article nouvellement créé
                ]);
            }

        return $this->renderForm("exo/form.html.twig",[
            'formVoiture' => $form,
            'editMode' => $voiture->getId() !== null
            ]);
    }
    #[Route('/voiture/delete/{id}', name: 'voiture_delete')]

    public function delete(Voiture $voiture, EntityManagerInterface $manager)
    {
        $manager->remove($voiture);
        $manager->flush();

        return $this->redirectToRoute('voiture_liste');
    }
}
    
    


