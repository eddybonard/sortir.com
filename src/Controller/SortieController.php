<?php

namespace App\Controller;


use App\Entity\Etat;
use App\Entity\Lieu;

use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\CampusRepository;

use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;

use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SortieController extends AbstractController
{
    /**
     * @Route("/accueil/sortie" , name="sortie_creation")
     *
     */
    public function creerSortie(
                                 Request $request,
                                 EntityManagerInterface $entityManager,
                                 SluggerInterface $slugger,
                                 SortieRepository $sortieRepository,
                                    EtatRepository $etatRepository
                           )
    {
        $sortie = new Sortie();
        $lieu = new Lieu();
        $paticipant = $this->getUser();
        $campusOrganisateur = $this->getUser()->getCampus();

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

            $sortie->setDatePublication(new \DateTime());
            $sortie->setOrganisateur($paticipant);
            $sortie->setCampusOrganisateur($campusOrganisateur);


            $publier = $request->request->get('publier');
            $sauvegarder = $request->request->get('safe');

            if($publier != null)
            {
                $sorties = $sortieRepository->findAll();
                $etatOuvert = $etatRepository->find(4);
                $sortie->setEtat($etatOuvert);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'Votre sortie à bien été publiée');
                return $this->redirectToRoute('main_accueil', [
                    'sorties' => $sorties
                ]);
            }else
            {
                $user = $this->getUser()->getId();
                $etatCreation = $etatRepository->find(3);
                $sortie->setEtat($etatCreation);
                $entityManager->persist($sortie);
                $entityManager->flush();
                $this->addFlash('success', 'Votre sortie à bien été sauvegardée');
                return $this->redirectToRoute('sortie_historique', [
                    'id' => $user
                ]);
            }

        }

        if($formLieu->isSubmitted() && $formLieu->isValid())
        {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu créer');
            return $this->redirectToRoute('sortie_creation');

        }

        return $this->render('main/sortie.html.twig', [
            'formSortie' => $formSortie->createView(),
            'formLieu' =>$formLieu->createView(),
            'campus' => $this->getUser()->getCampus(),
        ]);
    }

    /**
     * @Route("/accueil/mesSortie{id}", name="sortie_historique")
     */
    public function historiqueSortie(int $id, SortieRepository $sortieRepository,
                                     PaginatorInterface $paginator,
                                    Request $request): Response
    {

        $sorties = $paginator->paginate(
            $sortieRepository->mesSortie($id),
            $request->query->getInt('page', 1),8
        );
        return $this->render('main/sortieHistorique.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    /**
     * @Route("/sortie/modifier{id}", name="sortie_modifier")
     *
     */
    public function modifierSortie(int $id, Request $request,
                                    SluggerInterface $slugger,
                                    SortieRepository $sortieRepository,
                                   EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $sortie = new Sortie();
        $sortie = $sortieRepository->find($id);
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

            $user = $this->getUser()->getId();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $sorties = $sortieRepository->mesSortie($user);
            $this->addFlash('success', 'Votre sortie à bien été modifier');
            return $this->redirectToRoute('sortie_historique', [
                'sorties' => $sorties,
                'id'=>$user
            ]);
        }

        if($formLieu->isSubmitted() && $formLieu->isValid())
        {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Lieu créer');
            return $this->redirectToRoute('sortie_modifier');

        }

        return $this->render('main/modificationSortie.html.twig', [
            'formSortie' => $formSortie->createView(),
            'formLieu' =>$formLieu->createView(),
            'sortie' =>$sortie
        ]);


    }

    /**
     * @Route("/sortieSuprimmer{id}", name="sortie_suprimmer")
     */
    public function suprimmerSortie(Sortie $sortie, EntityManagerInterface $entityManager, SortieRepository $sortieRepository)
    {
        $entityManager->remove($sortie);
        $entityManager->flush();

        $user = $this->getUser()->getId();
        $this->addFlash('danger', 'Votre sortie a bien été suprimmée');
        return $this->redirectToRoute('sortie_historique', [
            'id' => $user
        ]);
    }

    /**
     * @Route("/sortie/filtre{id}", name="sortie_filtre")
     */
    public function filtrerLesSortie(int $id,SortieRepository $sortieRepository,Request $request,PaginatorInterface $paginator):Response
    {
        $recherche = $request->request->get('sortie');
        $sorties = $sortieRepository->mesSortie($id);
        $user = $this->getUser()->getId();

        if($recherche != null)
        {

            $sorties2 = $paginator->paginate(
                $sortieRepository->sortieTrieeParMot($recherche),
                $request->query->getInt('page', 1),8
            );
            return $this->render('main/sortieHistorique.html.twig',[
                'sorties' => $sorties2,
                'id' => $user
            ]);
        }
        return $this->render('main/sortieHistorique.html.twig',[
            'sorties' => $sorties,

        ]);

    }

    /**
     * @Route("/sortie/affichage{id}", name="sortie_affichage")
     */
    public function afficherLaSortie(int $id,
                                     SortieRepository $sortieRepository,
                                     LieuRepository $lieuRepository,
                                    VilleRepository  $villeRepository)
    {

        $sortie = $sortieRepository->find($id);
        $lieuDeLaSortie = $sortie->getLieu();
        $lieu = $lieuRepository->find($lieuDeLaSortie);
        $ville = $villeRepository->find($lieu);
        $user = $this->getUser();


        return $this->render('main/afficherLaSortie.html.twig', [
            'sortie' =>$sortie,
            'lieu' =>$lieu,
            'campus' => $this->getUser()->getCampus(),
            'ville'=>$ville,
            'participants' => $sortie->getParticipants(),
            'date'=>new \DateTime(),
            'isParticipant'=>$sortie->getParticipants()->contains($user)

        ]);
    }

    /**
     * @Route("/sortie/inscription{id}", name="sortie_inscription")
     */
    public function inscriptionSortie(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();

        $sortie->addParticipant($user);
        $entityManager->persist($sortie);
        $entityManager->flush();


        $this->addFlash('success', 'Inscription réussite');
        return $this->redirectToRoute('sortie_affichage',[
            'id'=>$sortie->getId()
        ]);
    }

    /**
     * @Route("/sortie/seDesister{id}", name="sortie_desister")
     */
    public function sortieDesister(int $id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager)
    {
        $sortie = $sortieRepository->find($id);
        $user = $this->getUser();

        $sortie->removeParticipant($user);
        $entityManager->persist($sortie);
        $entityManager->flush();

        $this->addFlash('danger', 'Votre désinscription a bien été pris en compte');
        return $this->redirectToRoute('sortie_affichage',[
            'id' =>$sortie->getId(),
        ]);
    }

    /**
     * @Route("/sortie/annulation{id}", name="sortie_annulation")
     */
    public function annulationSortie(int $id,SortieRepository $sortieRepository,
                                     Request $request,
                                        EntityManagerInterface $entityManager,
                                    EtatRepository $etatRepository)
    {
        $sortie = $sortieRepository->find($id);
        $annuler = $request->request->get('annuler');

        if ($annuler != null)
        {
          $etatAnnuler = $etatRepository->find(5);
          $sortie->setEtat($etatAnnuler);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('danger', 'Votre sortie bien été annulée');
            return $this->redirectToRoute('main_accueil');
        }



        return $this->render('main/sortieAnnulation.html.twig', [
            'sortie' =>$sortie,
             'campus' => $this->getUser()->getCampus(),
            'lieu' => $sortie->getLieu(),
            'ville' => $sortie->getLieu()->getVille()
        ]);
    }
}
