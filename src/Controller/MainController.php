<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\ModifProfilType;
use App\Form\RegistrationFormType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\VilleRepository;
use App\Security\AppAuthentificationAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\String\Slugger\SluggerInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/accueil", name="main_accueil")
     */
    public function accueil(): Response
    {
        return $this->render('main/accueil.html.twig');
    }

    /**
     * @Route("/delete/profil{id}",  name="main_suprimmer")
     * @ParamConverter("participant", class="App\Entity\Participant")
     */
    public function suprimmerProfil(Participant $participant, EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $entityManager->remove($participant);
        $entityManager->flush();

        $this->addFlash('danger', 'Votre compte à bien été supprimé');
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername,'error' => $error] );


    }

    /**
     * @Route("/accueil/profil{id}", name="main_profil")
     */
    public function modifierProfile(int $id,
                                    ParticipantRepository $participantRepository,
                                    Request $request,
                                    SluggerInterface $slugger,
                                    EntityManagerInterface  $entityManager,
                                    AppAuthentificationAuthenticator $authenticator,
                                    GuardAuthenticatorHandler $guardHandler,
                                    UserPasswordEncoderInterface $passwordEncoder
                                   ): Response
    {
        $participant = new Participant();
        $participant = $participantRepository->find($id);

        $modificationForm = $this->createForm(ModifProfilType::class, $participant);
        $modificationForm->handleRequest($request);

        if ( $modificationForm->isSubmitted() && $modificationForm->isValid() )
        {
            $photoFile = $modificationForm->get('photo')->getData();
            if ($photoFile)
            {
                $originalImageName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName =$slugger->slug($originalImageName);
                $imageName = $safeImageName.'-'.uniqid().'.'.$photoFile->guessExtension();
                try {
                    $photoFile->move(
                        $this->getParameter('brochures_directory'),
                        $imageName);
                }catch (FileException$e)
                {
                    return $e->getTrace();
                }
                $participant->setPhoto($imageName);

            }
            $participant->setPassword(
                $passwordEncoder->encodePassword(
                    $participant,
                    $modificationForm->get('password')->getData()
                )
            );

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success','Votre profil à bien été mofifié');
            return $guardHandler->authenticateUserAndHandleSuccess(
                $participant,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );

        }

        return $this->render('main/profil.html.twig', [
            'modifForm' => $modificationForm->createView(),
        ]);

    }

    /**
     * @Route("/accueil/sortie" , name="main_sortie")
     */
    public function creerSortie(Request $request, EntityManagerInterface $entityManager,  EtatRepository $etatRepository, UserInterface  $user)
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);



        if($form->isSubmitted() && $form->isValid())
        {
            $etat = new Etat();
            $safe= $request->request->get('safe');
            $publier = $request->request->get('publier');

            if ($safe === true)
            {
                $sortie->setEtat($etat);
                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie sauvegardée');
                return $this->redirectToRoute('main_accueil');
            }
           else{

                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'Sortie publiée');
                return $this->redirectToRoute('main_accueil');
            }
        }


        return $this->render('main/sortie.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/ville", name="main_adminVille")
     */
    public function adminVille(VilleRepository  $villeRepository, Request $request, EntityManagerInterface $entityManager)
    {

        $recherche = $request->request->get('ville');
        if ($recherche != null )
        {
            dump($recherche);
            $villes = $villeRepository->villeTrieeParMot($recherche);

            return $this->render('main/ville.html.twig' , [
                'villes' => $villes,
            ]);
        }



        $ville = new Ville();
        $nom = $request->request->get('nom');
        $codePOstal = $request->request->get('codePostal');

        if($nom != null && $codePOstal != null)
        {
            $ville->setNom(strtoupper($nom));
            $ville->setCodePostal($codePOstal);
            $entityManager->persist($ville);
            $entityManager->flush();

            $villes = $villeRepository->findAll();

            $this->addFlash('success','Votre ville a été ajoutée');
            return $this->render('main/ville.html.twig' , [
                'villes' => $villes,
            ]);

        }


        $villes = $villeRepository->findAll();

        return $this->render('main/ville.html.twig' , [
            'villes' => $villes,
        ]);
    }


}
