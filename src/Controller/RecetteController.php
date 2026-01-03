<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteForm;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/recette')]
final class RecetteController extends AbstractController
{
    // 📌 LISTE + FILTRE PAR NOM
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_VISITEUR')"))]
    #[Route('', name: 'app_recette_index', methods: ['GET'])]
    public function index(
        Request $request,
        RecetteRepository $recetteRepository
    ): Response {
        $nom = $request->query->get('nom');

        $recettes = $recetteRepository->findByName($nom);

        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
            'nom' => $nom,
        ]);
    }

    // 📌 CRÉATION + UPLOAD IMAGE
    #[IsGranted('ROLE_VISITEUR')]
    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $recette = new Recette();
        $form = $this->createForm(RecetteForm::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('avatar2')->getData();

            if ($imageFile) {
               $fileName = pathinfo($imageFile->getClientOriginalName(),
                PATHINFO_FILENAME);
                $secureFileName =$slugger->slug($fileName);
                $newFileName = $secureFileName.uniqid().'.'.$imageFile->guessExtension();

               try {

                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw new Exception('erreur lors du téléversement de l\'image');
                }
                $recette->setAvatar2($newFileName);
            }

            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('app_recette_index');
        }

        return $this->render('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
        ]);
    }

    // 📌 AFFICHER
    #[IsGranted('ROLE_VISITEUR')]
    #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }

    // 📌 MODIFIER
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Recette $recette,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(RecetteForm::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_recette_index');
        }

        return $this->render('recette/edit.html.twig', [
            'form' => $form->createView(),
            'recette' => $recette,   
        ]);
    }

    // 📌 SUPPRIMER (ADMIN SEUL)
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/{id}', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Recette $recette,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $recette->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recette_index');
    }
}

