<?php

namespace App\Controller;


use App\Entity\Participant;

use App\Form\ModifProfilType;

use App\Repository\CampusRepository;

use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;

use App\Security\AppAuthentificationAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function accueil(SortieRepository $sortieRepository,
                            PaginatorInterface $paginator,
                            CampusRepository $campusRepository,
                            Request $request,
                            EtatRepository $etatRepository,
                            ParticipantRepository $participantRepository
                            ): Response
    {
        $sorties = $paginator->paginate(
            $sortieRepository->sortiePlusRecent(),
            $request->query->getInt('page',1),8
        );

        $etatEncours = $etatRepository->find(1);
        $etatFermee = $etatRepository->find(2);
        $campus = $campusRepository->findall();
        $participants = $participantRepository->listeDesParticpantsConnecte();
        $etatAnnuler = $etatRepository->find(5);
        $dateNow = new \DateTime();

        return $this->render('main/accueil.html.twig', [
            'sorties'=>$sorties,
            'campus' =>$campus,
            'etatAnnuler'=>$etatAnnuler,
            'participants'=>$participants,
           'dateNow' => $dateNow,
           'etatEncours' =>$etatEncours,
           'etatFermee' =>$etatFermee
           /* 'participantSortie' => $participantSortie,*/



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
     * @Route("/accueil/filtre", name="main_filtre")
     */
    public function filtrerLesSortie(Request $request,
                                     CampusRepository $campusRepository,
                                     SortieRepository $sortieRepository,
                                    PaginatorInterface $paginator,
                                    EtatRepository $etatRepository,
                                    ParticipantRepository $participantRepository)
    {
        $recherche = $request->request->get('campus');
        $dateDebut = $request->request->get('debut');
        $dateFin = $request->request->get('fin');

        if ($dateDebut != null)
        {
            $etatAnnuler = $etatRepository->find(5);
            $campus = $campusRepository->findAll();
            $sorties = $paginator->paginate(
                $sortieRepository->sortieTrieeParCampusetDate($recherche, $dateDebut, $dateFin),
                $request->query->getInt('page', 1),8
            );

            $participants = $participantRepository->listeDesParticpantsConnecte();

            return $this->render('main/accueil.html.twig', [
                'campus' => $campus,
                'sorties' =>$sorties,
                'etatAnnuler'=>$etatAnnuler,
                'participants' => $participants

            ]);
        }

        $etatAnnuler = $etatRepository->find(5);
        $campus = $campusRepository->findAll();
        $sorties = $paginator->paginate(
            $sortieRepository->sortieTrieeParCampus($recherche),
            $request->query->getInt('page', 1),8
        );

        $participants = $participantRepository->listeDesParticpantsConnecte();

        return $this->render('main/accueil.html.twig', [
                'campus' => $campus,
                'sorties' =>$sorties,
                'etatAnnuler'=>$etatAnnuler,
                'participants' => $participants

            ]);



    }








}
