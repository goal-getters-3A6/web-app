<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginType;
use App\Form\ResetPassType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Monolog\Formatter\GoogleCloudLoggingFormatter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $this->isGranted('IS_AUTHENTICATED_FULLY');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('user/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/denied_access", name="denied_access")
     */
    public function index(): Response
    {
        return $this->render('user/login_denied.html.twig');
    }
    /**
     * @Route("/forgotten-password", name="app_forgotten_password")
     */
    public function forgottenPass(Request $request, UserRepository $userRepo, TokenGeneratorInterface $tokenGenerator,   MailerInterface $mailer): Response
    {
        // create form to retrieve email
        $form = $this->createForm(ResetPassType::class);

        //trait form
        $form->handleRequest($request);


        //if form is valid
        if ($form->isSubmitted() && $form->isValid()) {
            //extract data of the email to reset the password of
            $data = $form->getData();
            //search if the user exist with that email
            $user = $userRepo->findOneBy([
                'mail' => $data['email']
            ]);
            //if user doesn't exist
            if (!$user) {
                // we send a flash message
                $this->addFlash('danger', 'this email doesnt exist');
                return $this->redirectToRoute('app_login');
            }

            // if user exist we generate a token
            $token = $tokenGenerator->generateToken();
            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'an error occured : ' . $e->getMessage());
                return $this->redirectToRoute('app_login');
            }

            // generate url or resetting the password
            $url = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $email = (new TemplatedEmail())
                ->from('appgzone@gmail.com')
                ->to($user->getMail())
                ->subject('PASSWORD RESET')
                ->htmlTemplate('security/reset_email.html.twig')
                ->context([
                    'fullname' => $user->getNom(),
                    'url' => $url,
                ]);

            $mailer->send($email);


            //flash message of email sent confirmation
            $this->addFlash('message', 'The reset Password email has been sent');
            return $this->redirectToRoute('app_login');
        }
        // send to email request page
        return $this->render('user/forgotten_password.html.twig', ['emailForm' => $form->createView()]);
    }

    /**
     * @Route("/resetPassword/{token}", name="app_reset_password")
     */
    public function verifyUserEmail($token, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // search for user with the token
        $user = new User();
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'token not existing');
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $Password = $request->request->get('password');
            $Password2 = $request->request->get('password2');
            $error = "PASSWORD DOESNT MATCH ";
            if ($Password != $Password2) {
                return $this->render('security/reset_password.html.twig', [
                    'token' => $token,
                    'error' => $error
                ]);
            }

            //delete token of the user
            $user->setResetToken(null);
            //encode password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $request->request->get('password')
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'password has been updated successfully');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('user/reset_password.html.twig', [
                'token' => $token,
                'error' => ""
            ]);
        }
    }


    /**
     * @Route("/choice", name="choice")
     */
    public function choice(AuthenticationUtils $authenticationUtils)
    {

        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('user/choice.html.twig', [
            'full_name' => $lastUsername,
        ]);
    }

    /**
     * @Route("/tfa/setup", name="setup_tfa")
     */

    public function setupTfa(EntityManagerInterface $entityManager, GoogleAuthenticatorInterface $googleAuthenticator)
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getUserIdentifier());
        if (!$user->isGoogleAuthenticatorEnabled()) {
            $user->setGoogleAuthenticatorSecret($googleAuthenticator->generateSecret());
            $entityManager->flush();
        }
        return $this->render('user/enable2fa.html.twig');
    }

    /**
     * @Route("/tfa/qr-code", name="app_qr_code")
     */
    public function displayGoogleAuthenticatorQrCode(GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager)
    {
        $qrCodeContent = $googleAuthenticator->getQRContent(($entityManager->getRepository(User::class)->find($this->getUser()->getUserIdentifier())));
        $result = Builder::create()
            ->data($qrCodeContent)
            ->build();
        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }

    /**
     * @Route("/verify/tfa", name="app_verify_tfa")
     */
    public function verifyTfa(Request $request): Response
    {
        // Check if there's an error parameter in the query string
        $error = $request->query->get('error');

        // Render the template with the error message, if any
        return $this->render('user/2fa_form.html.twig', [
            'error' => $error,
        ]);
    }

    /**
     * @Route("/tfa/check", name="app_check_tfa")
     */
    public function checkTfa(Request $request, GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->find($this->getUser()->getUserIdentifier());
        $code = $request->request->get('_auth_code');

        if ($googleAuthenticator->checkCode($user, $code)) {
            $hasAccess = in_array('ROLE_ADMIN', $this->getUser()->getRoles());
            if ($hasAccess) {
                return $this->redirectToRoute('choice');
            }
            return $this->redirectToRoute('profile');
        } else {
            return $this->redirectToRoute('app_verify_tfa', ['error' => 'InvalidTFA']);
        }
    }
}
