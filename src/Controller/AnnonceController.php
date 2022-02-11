<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AutomobileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * @Route("/annonce", name="annonce")
     */
    // public function index(): Response
    // {
    //     return $this->json([
    //         'message' => 'Welcome to your new controller!',
    //         'path' => 'src/Controller/AnnonceController.php',
    //     ]);
    // }

    /**
     * @Route("/home", name="annonce-home")
     */
    public function home()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("/creation", name="annonce-creation", methods={"GET"})
     */
    public function creation()
    {
        $annonce = new Annonce;
        $form = $this->createForm(AnnonceType::class, $annonce);

        return $this->render('creationHome.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/creation", name="annonce-creation-validation", methods={"POST"})
     */
    public function creationValidation(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonce;
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();
            $message = "Creation reussie de votre annonce " . $annonce->getTitre() . " !";
            return $this->json($message);
        } else {
            $message = "Le formulaire est mal rempli. Merci de recommencer.";
            return $this->json($message);
        }
    }

    /**
     * 
     * @Route("/recherche", name="annonce-recherche", methods={"GET"})
     */
    public function recherche()
    {
        return $this->render('rechercheAnnonce.html.twig');
    }

    /**
     * 
     * @Route("/recherche", name="annonce-recherche-post", methods={"POST"})
     */
    public function recherchePost(Request $request, AutomobileRepository $automobileRepository): Response
    {
        $dataForm = $request->request->all();
        $rechercheUtilisateur = $dataForm['recherche-auto'];
        $modeleVoiture = $automobileRepository->findOneBy(['modele' => $rechercheUtilisateur]);

        $trouve = false;
        $goodSearch = '';

        if ($modeleVoiture) //On trouve le modele
        {
            $trouve = true;
            $goodSearch = $rechercheUtilisateur;
            //return $this->json("Voila la marque et le modele de la voiture: " . $modeleVoiture->getMarque() . " " . $modeleVoiture->getModele());
        } elseif (strpos($rechercheUtilisateur, " ") !== false) { //Si le modèle est composé de plusieurs mots
            $tableauDeMots = explode(" ", $rechercheUtilisateur);

            //Tester recherche sur chaque mot unitairement
            foreach ($tableauDeMots as $valeur) {
                $modeleVoiture = $automobileRepository->findOneBy(['modele' => $valeur]);

                if ($modeleVoiture) {
                    $trouve = true;
                    $goodSearch = $valeur;
                    //return $this->json("Voila la marque et le modele de la voiture: " . $modeleVoiture->getMarque() . " " . $modeleVoiture->getModele());
                }
            }

            //Tester recherche sur chaque mot en étant insensible à la casse
            // => Pas la peine de la faire, MySQL est insensible à la casse
            // ==> IDEM pour les accents

            //Verifier s'il n'y a pas un espace oublié avant le chiffre dans chaque mot
            foreach ($tableauDeMots as $valeur) {
                $isMatch = preg_match('/[a-zA-Z]+[0-9]+/', $valeur);

                if ($isMatch) {
                    $nouvelleValeur = preg_replace('/(?<=\D)[0-9]/', ' $0', $valeur);
                    $modeleVoiture = $automobileRepository->findOneBy(['modele' => $nouvelleValeur]);

                    if ($modeleVoiture) //On a trouvé le modele
                    {
                        $trouve = true;
                        $goodSearch = $nouvelleValeur;
                        //return $this->json("Voila la marque et le modele de la voiture: " . $modeleVoiture->getMarque() . " " . $modeleVoiture->getModele());
                    }
                }
            }

            //Voir si on n'a pas un chiffre précédé par un espace qui ne devrait pas y être
            $isMatch = preg_match('/(?<=\s)[0-9]/', $rechercheUtilisateur);

            if ($isMatch) {
                $nouvelleRecherche = preg_replace('/\s(?=[0-9])/', '', $rechercheUtilisateur);

                if (strpos($nouvelleRecherche, " ") !== false) //Si la nouvelle recherche est encore composé de plusieurs mots
                {
                    $tableauDeMots = explode(" ", $nouvelleRecherche);

                    //Tester recherche sur chaque mot unitairement
                    foreach ($tableauDeMots as $valeur) {
                        $modeleVoiture = $automobileRepository->findOneBy(['modele' => $valeur]);

                        if ($modeleVoiture) //On a trouvé le modele
                        {
                            $trouve = true;
                            $goodSearch = $valeur;
                            //return $this->json("Voila la marque et le modele de la voiture: " . $modeleVoiture->getMarque() . " " . $modeleVoiture->getModele());
                        }
                    }
                } else //Sinon on n'a plus qu'un seul mot
                {
                    $modeleVoiture = $automobileRepository->findOneBy(['modele' => $nouvelleRecherche]);

                    if ($modeleVoiture) //On a trouvé le modele
                    {
                        $trouve = true;
                        $goodSearch = $nouvelleRecherche;
                        //return $this->json("Voila la marque et le modele de la voiture: " . $modeleVoiture->getMarque() . " " . $modeleVoiture->getModele());
                    }
                }
            }
        }

        if ($trouve) {
            //Faire une requet croisée sur la table annonce
            //LAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
        } else {
            return $this->json("Le modele de vehicule est introuvable...");
        }
    }
}
