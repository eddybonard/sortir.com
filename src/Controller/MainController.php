<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/accueil/profil{id}", name="main_profil")
     */
    public function modifierProfile(int $id,
                                    ParticipantRepository $participantRepository,
                                    Request $request,
                                    SluggerInterface $slugger,
                                    EntityManagerInterface  $entityManager): Response
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

            $entityManager->persist($participant);
            $entityManager->flush();

            $this->addFlash('success','Votre profil à bien été mofifié');
            return $this->redirectToRoute('main_accueil' );

        }

        return $this->render('main/profil.html.twig', [
            'modifForm' => $modificationForm->createView(),
        ]);

    }
}
