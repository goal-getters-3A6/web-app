<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Form\ProfileType;
use App\Form\PasswordProfileType;
use App\Form\UserAdminEdit;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Entity\PhoneNumber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="profile", methods={"GET"})
     */
    public function profile(UserRepository $userRepository): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $userRepository->findOneBy(['id' => $this->getUser()->getUserIdentifier()])
        ]);
    }

    /**
     * @Route("/profile/delete", name="delete_profile", methods={"GET", "POST"})
     */
    public function delete_profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$this->isCsrfTokenValid('delete' . $user->getUserIdentifier(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @Route("/profile/editPassword", name="edit_profile_password", methods={"GET", "POST"})
     */
    public function edituserpassword(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getUserIdentifier());
        $form = $this->createForm(PasswordProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setMdp(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('mdp')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/profile_edit_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/edit", name="edit_profile", methods={"GET" , "POST" })
     */
    public function edit_profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/profile_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/", name="app_users_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $users = $userRepository->findAll();
        $users = $paginator->paginate(
            // Doctrine Query, not results
            $users,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );

        return $this->render(
            'user/index.html.twig',
            array('users' => $users)
        );
    }

    /**
     * @Route("/DisabledAccount", name="DisabledAccount")
     */
    public function DisabledAccount(): Response
    {
        return $this->render('user/disabledAccount.html.twig');
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserAdminEdit::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->createQueryBuilder('u')
                ->update()
                ->set('u.nom', ':nom')
                ->set('u.prenom', ':prenom')
                ->set('u.mail', ':mail')
                ->set('u.dateNaissance', ':dateNaissance')
                ->set('u.tel', ':tel')
                ->set('u.poids', ':poids')
                ->set('u.taille', ':taille')
                ->set('u.sexe', ':sexe')
                ->where('u.id = :id')
                ->setParameter('id', $user->getId())
                ->setParameter('nom', $user->getNom())
                ->setParameter('prenom', $user->getPrenom())
                ->setParameter('mail', $user->getMail())
                ->setParameter('dateNaissance', $user->getDateNaissance())
                ->setParameter('tel', $user->getTel())
                ->setParameter('poids', $user->getPoids())
                ->setParameter('taille', $user->getTaille())
                ->setParameter('sexe', $user->getSexe())
                ->getQuery()
                ->execute();
            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     *  @IsGranted("ROLE_ADMIN")
     * @Route("/delete/{id}", name="app_user_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->createQueryBuilder('u')
                ->delete()
                ->where('u.id = :id')
                ->setParameter('id', $user->getId())
                ->getQuery()
                ->execute();
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/disable_user/{id}", name="disable_user", methods={"GET", "POST"})
     */
    public function disable_user(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->createQuery('UPDATE App\Entity\User u SET u.disable_token = :disableToken WHERE u.id = :id')
            ->setParameter('disableToken', bin2hex(random_bytes(32)))
            ->setParameter('id', $user->getId())
            ->execute();

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/enable_user/{id}", name="enable_user", methods={"GET", "POST"})
     */
    public function enable_user(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $user->setDisableToken(null);
        $entityManager->persist($user);
        $entityManager->flush();
        //$link = $request->headers->get("referer");
        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/add", name="admin_user_add", methods={"GET", "POST"})
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setMdp(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setDateInscription(new \DateTime());
            $user->setIsVerified(true);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/add.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
