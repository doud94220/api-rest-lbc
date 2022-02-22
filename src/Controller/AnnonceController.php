<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Form\AnnonceAutomobileType;
use App\Repository\AnnonceRepository;
use App\Repository\AutomobileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Node\RenderBlockNode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnnonceController extends AbstractController
{

    //------------------------------------------------- ACCUEIL ------------------------------------------------

    /**
     * @Route("", name="racine")
     */
    public function racine()
    {
        return $this->render('home.html.twig');
    }

    /**
     * @Route("home", name="annonce-api-home")
     */
    public function home()
    {
        return $this->render('home.html.twig');
    }

    //------------------------------------------------ CREATION ------------------------------------------------

    /**
     * @Route("/creation-automobile", name="annonce-creation-automobile", methods={"GET"})
     */
    public function creationAutomobile()
    {
        $annonce = new Annonce;
        $form = $this->createForm(AnnonceAutomobileType::class, $annonce);

        return $this->render('creationAutomobileHome.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/creation-automobile", name="annonce-creation-automobile-post", methods={"POST"})
     */
    public function creationValidation(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonce;
        $form = $this->createForm(AnnonceAutomobileType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();
            $message = "Creation reussie de votre annonce " . $annonce->getTitre() . " ! L'id de l'annonce est : " . $annonce->getId();
            return $this->json($message);
        } else {
            $message = "Le formulaire est mal rempli. Merci de recommencer.";
            return $this->json($message);
        }
    }

    /**
     * @Route("/creation", name="annonce-creation", methods={"GET"})
     */
    public function creation()
    {
        $annonce = new Annonce;
        //dd('ici');
        $form = $this->createForm(AnnonceType::class, $annonce);
        //dd('la');
        return $this->render('creationHome.html.twig', [
            'formView' => $form->createView()
        ]);
    }

    /**
     * @Route("/creation", name="annonce-creation-post", methods={"POST"})
     */
    public function creationHorsAutoValidation(Request $request, EntityManagerInterface $em): Response
    {
        $annonce = new Annonce;
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($annonce);
            $em->flush();
            $message = "Creation reussie de votre annonce " . $annonce->getTitre() . " ! L'id de l'annonce est : " . $annonce->getId();
            return $this->json($message);
        } else {
            $message = "Le formulaire est mal rempli. Merci de recommencer.";
            return $this->json($message);
        }
    }

    //------------------------------------------------ RECHERCHE ------------------------------------------------

    /**
     * 
     * @Route("/recherche/automobile", name="annonce-recherche-automobile", methods={"GET"})
     */
    public function rechercheAutomobile()
    {
        return $this->render('rechercheAnnonceAuto.html.twig');
    }

    /**
     * 
     * @Route("/recherche/automobile", name="annonce-recherche-automobile-post", methods={"POST"})
     */
    public function rechercheAutomobilePost(Request $request, AutomobileRepository $automobileRepository, AnnonceRepository $annonceRepository): Response
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
     * @Route("/recherche/emploi", name="annonce-recherche-emploi", methods={"GET"})
     */
    public function rechercheEmploi()
    {
        return $this->render('rechercheAnnonceEmploi.html.twig');
    }

    /**
     * 
     * @Route("/recherche/emploi", name="annonce-recherche-emploi-post", methods={"POST"})
     */
    public function rechercheEmploiPost(Request $request, AnnonceRepository $annonceRepository)
    {
        $dataForm = $request->request->all();
        $rechercheUtilisateur = $dataForm['recherche-emploi'];
        $emploi = $annonceRepository->findOneBy(['titre' => $rechercheUtilisateur, 'categorie' => "Emploi"]);

        $trouve = false;
        $goodSearch = '';
        $searchInformation = '';

        if ($emploi) //On trouve un ou des emplois
        {
            $trouve = true;
            $goodSearch = $rechercheUtilisateur;
        } elseif (strpos($rechercheUtilisateur, " ") == false) //Si il n'y a qu'un seul mot dans la recherche
        {
            /** @var Annonce[] */
            $annonceEmploi = $annonceRepository->trouverEmploiParMot($rechercheUtilisateur); //Recherche avec "like"

            if ($annonceEmploi) {
                $trouve = true;
                $goodSearch = $rechercheUtilisateur;
                $searchInformation = "LIKE";
            }
        } elseif (strpos($rechercheUtilisateur, " ") !== false) //Si y'a plusieurs mots dans la recherche
        {
            $tableauDeMots = explode(" ", $rechercheUtilisateur);

            //Tester recherche sur chaque mot unitairement
            foreach ($tableauDeMots as $valeur) {

                $annonceEmploi = $annonceRepository->trouverEmploiParMot($valeur); //Recherche avec "like"

                if ($annonceEmploi) {
                    $trouve = true;
                    $goodSearch = $valeur;
                    $searchInformation = "LIKE";
                }
            }
        }

        if ($trouve) {

            if ($searchInformation == '') {
                $annoncesArray = $annonceRepository->findBy(['titre' => $goodSearch]);
            } elseif ($searchInformation == 'LIKE') {
                $annoncesArray = $annonceRepository->trouverEmploiParMot($goodSearch);
            } else {
                return $this->json("Problem dans le programme. Variable searchInformation mal renseigne...");
            }

            if ($annoncesArray) {
                $messageSucces = "Voici le(s) emploi(s) correspondant a votre recherche : ";

                foreach ($annoncesArray as $annonce) {
                    $messageSucces .= "[" . $annonce->getTitre() . " : " . $annonce->getContenu() . "]";
                }

                return $this->json($messageSucces);
            } else {
                $this->json("Probleme etrange, la recherche qui avait donne quelques chose, ne retourne finalement rien...");
            }
        } else {
            return $this->json("Aucune offre d emploi ne correspond a votre rercherche...");
        }
    }

    /**
     * 
     * @Route("/recherche/immobilier", name="annonce-recherche-immobilier", methods={"GET"})
     */
    public function rechercheImmobilier()
    {
        return $this->render('rechercheAnnonceImmobilier.html.twig');
    }

    /**
     * 
     * @Route("/recherche/immobilier", name="annonce-recherche-immobilier-post", methods={"POST"})
     */
    public function rechercheImmobilierPost(Request $request, AnnonceRepository $annonceRepository)
    {
        $dataForm = $request->request->all();
        $rechercheUtilisateur = $dataForm['recherche-immobilier'];
        $emploi = $annonceRepository->findOneBy(['titre' => $rechercheUtilisateur, 'categorie' => "Immobilier"]);

        $trouve = false;
        $goodSearch = '';
        $searchInformation = '';

        if ($emploi) //On trouve un ou des offres immobilières
        {
            $trouve = true;
            $goodSearch = $rechercheUtilisateur;
        } elseif (strpos($rechercheUtilisateur, " ") == false) //Si il n'y a qu'un seul mot dans la recherche
        {
            /** @var Annonce[] */
            $annonceImmo = $annonceRepository->trouverImmoParMot($rechercheUtilisateur); //Recherche avec "like"

            if ($annonceImmo) {
                $trouve = true;
                $goodSearch = $rechercheUtilisateur;
                $searchInformation = "LIKE";
            }
        } elseif (strpos($rechercheUtilisateur, " ") !== false) //Si y'a plusieurs mots dans la recherche
        {
            $tableauDeMots = explode(" ", $rechercheUtilisateur);

            //Tester recherche sur chaque mot unitairement
            foreach ($tableauDeMots as $valeur) {

                $annonceImmo = $annonceRepository->trouverImmoParMot($valeur); //Recherche avec "like"

                if ($annonceImmo) {
                    $trouve = true;
                    $goodSearch = $valeur;
                    $searchInformation = "LIKE";
                }
            }
        }

        if ($trouve) {

            if ($searchInformation == '') {
                $annoncesArray = $annonceRepository->findBy(['titre' => $goodSearch]);
            } elseif ($searchInformation == 'LIKE') {
                $annoncesArray = $annonceRepository->trouverImmoParMot($goodSearch);
            } else {
                return $this->json("Problem dans le programme. Variable searchInformation mal renseigne...");
            }

            if ($annoncesArray) {
                $messageSucces = "Voici le(s) offre(s) immobiliere(s) correspondantes a votre recherche : ";

                foreach ($annoncesArray as $annonce) {
                    $messageSucces .= "[" . $annonce->getTitre() . " : " . $annonce->getContenu() . "]";
                }

                return $this->json($messageSucces);
            } else {
                $this->json("Probleme etrange, la recherche qui avait donne quelques chose, ne retourne finalement rien...");
            }
        } else {
            return $this->json("Aucune offre immobiliere ne correspond a votre rercherche...");
        }
    }

    //------------------------------------------------ MODIFICATION ------------------------------------------------

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
     * @Route("/modifier2", name="annonce-modifier-2", methods={"POST"})
     */
    public function modifier2(AnnonceRepository $annonceRepository, Request $request): Response
    {
        $dataForm = $request->request->all();
        $id = $dataForm['id-annonce'];

        $annonce = $annonceRepository->findOneBy(array('id' => $id));

        if ($annonce) {
            $id = $annonce->getId();
            $titre = $annonce->getTitre();
            $contenu = $annonce->getContenu();

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
     * @Route("/validation/modification/put", name="annonce_edit", methods={"PUT"})
     */
    public function modificationPut(Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $em): Response
    {
        $idAnnonce = $request->request->get('id');
        $titreModifie = $request->request->get('titre');
        $contenuModifie = $request->request->get('contenu');

        $annonceQuiDoitEtreModifie = $annonceRepository->findOneBy(array('id' => $idAnnonce));
        $annonceQuiDoitEtreModifie->setTitre($titreModifie);
        $annonceQuiDoitEtreModifie->setContenu($contenuModifie);

        $em->flush();

        return $this->json("Annonce mise a jour en base en methode PUT !");
    }

    //------------------------------------------------ SUPPRESSION ------------------------------------------------

    /**
     * @Route("/supprimer/accueil", name="annonce-supprimer-accueil", methods={"GET"})
     */
    public function supprimerAccueil()
    {
        return $this->render('supprimerAnnonceAccueil.html.twig');
    }

    /**
     * @Route("/supprimer", name="annonce_supprimer", methods={"POST"})
     */
    public function supprimer(Request $request, AnnonceRepository $annonceRepository)
    {
        $dataForm = $request->request->all();
        $id = $dataForm['id-annonce'];

        $annonce = $annonceRepository->findOneBy(array('id' => $id));

        if ($annonce) {
            $id = $annonce->getId();
            $titre = $annonce->getTitre();
            $contenu = $annonce->getContenu();

            return $this->render('supprimerAnnonce.html.twig', [
                "id" => $id,
                'titre' => $titre,
                'contenu' => $contenu
            ]);
        } else {
            return $this->json("Pas d\'annonce pour cet identifiant...");
        }
    }

    /**
     * @Route("/validation/suppression", name="annonce_validation_suppresion", methods={"DELETE"})
     */
    public function validationSupprimer(Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $em)
    {
        $idAnnonce = $request->request->get('id');
        $annonceQuiDoitEtreSupprimee = $annonceRepository->findOneBy(array('id' => $idAnnonce));
        $em->remove($annonceQuiDoitEtreSupprimee);
        $em->flush();

        return $this->json("Annonce supprimée en base en methode DELETE !");
    }
}
