<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             $imageFile = $form->get('avatar1')->getData();
            if($imageFile){
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
                $user->setAvatar1($newFileName);
            }
            
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

             return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

