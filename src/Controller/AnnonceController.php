<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Repository\AutomobileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Node\RenderBlockNode;
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
     * @Route("/creation", name="annonce-creation-post", methods={"POST"})
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
    public function recherchePost(Request $request, AutomobileRepository $automobileRepository, AnnonceRepository $annonceRepository): Response
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
        } elseif (strpos($rechercheUtilisateur, " ") == false) //Un seul mot dans la recherche utilisateur
        {
            //Verifier s'il n'y a pas un espace oublié avant le chiffre
            $isMatch = preg_match('/[a-zA-Z]+[0-9]+/', $rechercheUtilisateur);

            if ($isMatch) {
                $nouvelleValeur = preg_replace('/(?<=\D)[0-9]/', ' $0', $rechercheUtilisateur);
                $modeleVoiture = $automobileRepository->findOneBy(['modele' => $nouvelleValeur]);

                if ($modeleVoiture) //On a trouvé le modele
                {
                    $trouve = true;
                    $goodSearch = $nouvelleValeur;
                }
            }
        } elseif (strpos($rechercheUtilisateur, " ") !== false) { //Plusieurs mots dans la recherche utilisateur
            $tableauDeMots = explode(" ", $rechercheUtilisateur);

            //Tester recherche sur chaque mot unitairement
            foreach ($tableauDeMots as $valeur) {
                $modeleVoiture = $automobileRepository->findOneBy(['modele' => $valeur]);

                if ($modeleVoiture) {
                    $trouve = true;
                    $goodSearch = $valeur;
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
                        }
                    }
                } else //Sinon on n'a plus qu'un seul mot
                {
                    $modeleVoiture = $automobileRepository->findOneBy(['modele' => $nouvelleRecherche]);

                    if ($modeleVoiture) //On a trouvé le modele
                    {
                        $trouve = true;
                        $goodSearch = $nouvelleRecherche;
                    }
                }
            }
        }

        if ($trouve) {
            $annoncesArray = $annonceRepository->trouverParModele($goodSearch);

            if ($annoncesArray) {
                $automobile = $automobileRepository->findOneBy(['modele' => $goodSearch]);
                $messageSucces = "Vous recherchez des annonces pour une " . $automobile->getMarque() . " " . $automobile->getModele() . ". Voici le(s) annonce(s) correspondante(s) : ";

                foreach ($annoncesArray as $annonce) {
                    $messageSucces .= "[" . $annonce->getTitre() . " : " . $annonce->getContenu() . "]";
                }

                return $this->json($messageSucces);
            } else {
                $automobile = $automobileRepository->findOneBy(['modele' => $goodSearch]);
                $messageEchec = "Vous recherchez des annonces pour une " . $automobile->getMarque() . " " . $automobile->getModele() . ", mais aucune annonce n'existe pour ce modèle. Désolé.";
                return $this->json($messageEchec);
            }
        } else {
            return $this->json("Le modele de vehicule est introuvable...");
        }
    }

    /**
     * 
     * @Route("/modifier/accueil", name="annonce-modifier-accueil", methods={"GET"})
     */
    public function modifierAcceuil()
    {
        return $this->render('modifierAnnonceHome.html.twig');
    }

    /**
     * 
     * @Route("/modifier", name="annonce-modifier", methods={"POST"})
     */
    public function modifier(AnnonceRepository $annonceRepository, Request $request)
    {
        $dataForm = $request->request->all();
        $id = $dataForm['id-annonce'];

        $annonce = $annonceRepository->findOneBy(array('id' => $id));

        $form = $this->createForm(AnnonceType::class, $annonce, [
            'action' => $this->generateUrl('annonce-validation-modification', array('id' => $id))
        ]);

        if ($annonce) {
            return $this->render('modifierAnnonce.html.twig', [
                "formView" => $form->createView()
            ]);
        } else {
            return $this->json("Pas d'annonce pour cet identifiant...");
        }
    }

    /**
     * 
     * @Route("/validation/modification/{id}", name="annonce-validation-modification", methods={"POST"})
     */
    public function validationModification($id, Request $request, EntityManagerInterface $em, AnnonceRepository $annonceRepository)
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Annonce */
            $annonceDuFormulaire = $form->getData();

            $annonceEnBase = $annonceRepository->findOneBy(array('id' => $id));
            $annonceEnBase->setTitre($annonceDuFormulaire->getTitre());
            $annonceEnBase->setContenu($annonceDuFormulaire->getContenu());

            $em->persist($annonceEnBase);
            $em->flush();
            return $this->json("Annonce mise a jour !");
        } else {
            //$erreurs = $form->getErrors();
            //dd($erreurs);
            return $this->json("Formulaire non valide...");
        }
    }

    ////////////// EN CHANTIER DESSOUS ///////////////////

    /**
     * 
     * @Route("/modifier2", name="annonce-modifier-2", methods={"POST"})
     */
    public function modifier2(AnnonceRepository $annonceRepository, Request $request): Response
    {
        $dataForm = $request->request->all();
        $id = $dataForm['id-annonce'];

        $annonce = $annonceRepository->findOneBy(array('id' => $id));
        $id = $annonce->getId();
        $titre = $annonce->getTitre();
        $contenu = $annonce->getContenu();

        if ($annonce) {
            return $this->render('modifierAnnoncePut.html.twig', [
                "id" => $id,
                'titre' => $titre,
                'contenu' => $contenu
            ]);
        } else {
            return $this->json("Pas d'annonce pour cet identifiant...");
        }
    }

    /**
     * @Route("/validation/modification/put/{id}", name="annonce-validation-modification-put", methods={"PUT"})
     */
    public function modificationPut($id, Request $request): Response
    {
        $request->request->all();
        dd($request);
        return $this->json("Annonce mise a jour en PUT");
    }
}
