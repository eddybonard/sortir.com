<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/ville", name="admin_ville")
     */
    public function adminVille(VilleRepository  $villeRepository, Request $request, EntityManagerInterface $entityManager)
    {

        $recherche = $request->request->get('ville');
        if ($recherche != null )
        {

            $villes = $villeRepository->villeTrieeParMot($recherche);

            return $this->render('admin/ville.html.twig' , [
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
            return $this->render('admin/ville.html.twig' , [
                'villes' => $villes,
            ]);

        }




        return $this->render('admin/ville.html.twig' , [
            'villes' => $villeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/campus", name="admin_campus")
     */
    public function adminCampus(CampusRepository  $campusRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $recherche = $request->request->get('campus');
        if ($recherche != null )
        {
            dump($recherche);
            $campus = $campusRepository->campusTrieeParMot($recherche);

            return $this->render('admin/campus.html.twig' , [
                'campus' => $campus,
            ]);
        }

        $campus = new Campus();
        $nom = $request->request->get('nom');


        if($nom != null )
        {
            $campus->setNom(strtoupper($nom));
            $entityManager->persist($campus);
            $entityManager->flush();

            $campus = $campusRepository->findAll();

            $this->addFlash('success','Votre ville a été ajoutée');
            return $this->render('admin/campus.html.twig' , [
                'campus' => $campus,
            ]);

        }


        $campus = $campusRepository->findAll();

        return $this->render('admin/campus.html.twig' , [
            'campus' => $campus,
        ]);
    }

    /**
     * @Route("/accueil/desactiverParticipant{id}", name="admin_desactiverParticipant")
     */
    public function desactiverParticipant(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        $participant = $participantRepository->find($id);
        $participant->setActif(0);
        $entityManager->persist($participant);
        $entityManager->flush();


        $this->addFlash('danger', 'Participant désactivé');
        return $this->redirectToRoute('main_accueil');
    }

    /**
     * @Route("/accueil/activerParticipant{id}", name="admin_activerParticipant")
     */
    public function activerParticipant(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        $participant = $participantRepository->find($id);
        $participant->setActif(1);
        $entityManager->persist($participant);
        $entityManager->flush();


        $this->addFlash('success', 'Participant activé');
        return $this->redirectToRoute('main_accueil');
    }

    /**
     * @Route("/admin/suprimmerParticipant{id}", name="admin_suprimmerParticipant")
     */
    public function suprimmerParticipant(int $id, ParticipantRepository $participantRepository, EntityManagerInterface $entityManager)
    {
        $participant = $participantRepository->find($id);
        $entityManager->remove($participant);
        $entityManager->flush();

        $this->addFlash('danger', 'Participant suprimmé');
        return $this->redirectToRoute('main_accueil');
    }

    /**
     * @Route("/admin/suprimmerVille{id}", name="admin_suprimmerVille")
     */
    public function suprimmerVille(int $id, VilleRepository $villeRepository, EntityManagerInterface $entityManager)
    {
        $ville = $villeRepository->find($id);
        $entityManager->remove($ville);
        $entityManager->flush();

        $this->addFlash('danger', 'Ville suprimmée');
        return $this->redirectToRoute('admin_ville');
    }

    /**
     * @Route("/admin/suprimmerCampus{id}", name="admin_suprimmerCampus")
     */
    public function suprimmerCampus(int $id, CampusRepository $campusRepository, EntityManagerInterface $entityManager)
    {
        $campus = $campusRepository->find($id);
        $entityManager->remove($campus);
        $entityManager->flush();

        $this->addFlash('danger', 'Campus suprimmée');
        return $this->redirectToRoute('admin_campus');
    }

}
