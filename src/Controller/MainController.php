<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Form\ModifProfilType;
use App\Form\RegistrationFormType;
use App\Form\SortieType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
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
    public function accueil(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();

        return $this->render('main/accueil.html.twig', [
            'sorties'=>$sorties
        ]);
    }

    /**
     * @Route("/delete/{id}",  name="main_suprimmer")
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
     * @Route("/accueil/sortie/{id}" , name="main_sortie")
     * @ParamConverter("participant", options={"mapping": {"id"   : "id"}})
     */
    public function creerSortie( Participant $participant,
                                 Request $request,
                                 EntityManagerInterface $entityManager,
                                 EtatRepository $etatRepository,
                                 UserInterface  $user,
                                SluggerInterface $slugger,
                                SortieRepository $sortieRepository)
    {
        $sortie = new Sortie();
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formLieu->handleRequest($request);
        $formSortie->handleRequest($request);



        if($formSortie->isSubmitted() && $formSortie->isValid())
        {
            if ($formSortie->get('photo')->getData() == null)
            {
                $sortie->setPhoto('eni.png');
            }
            $imageFile = $formSortie->get('photo')->getData();
            if ($imageFile)
            {

                $originalImageName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName =$slugger->slug($originalImageName);
                $imageName = $safeImageName.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('brochures_directory'),
                        $imageName);
                }catch (FileException$e)
                {
                    return $e->getTrace();
                }
                $sortie->setPhoto($imageName);
            }


                $sortie->setOrganisateur($participant);
                $entityManager->persist($sortie);
                $entityManager->flush();

                $sorties = $sortieRepository->findAll();
                $this->addFlash('success', 'Votre sortie à bien été créée');
                return $this->redirectToRoute('main_accueil', [
                    'sorties' => $sorties
                ]);
        }

        if($formLieu->isSubmitted() && $formLieu->isValid())
        {
                $entityManager->persist($lieu);
                $entityManager->flush();

                $this->addFlash('success', 'Lieu créer');
                return $this->redirectToRoute('main_sortie', [
                    'id'=>$participant->getId()
                ]);

        }


        return $this->render('main/sortie.html.twig', [
            'formSortie' => $formSortie->createView(),
            'formLieu' =>$formLieu->createView(),
        ]);
    }




}
