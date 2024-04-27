<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, VerifyEmailHelperInterface $verifyEmailHelper, AuthenticationUtils $authenticationUtils): Response
    {

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $date = new \DateTime('now');
        $user->setDateInscription($date);
        $user->setRole("CLIENT");

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            //upload to dropbox
            $app = new DropboxApp("29jc4g04ebdszbm", "7el38mr9szx12fu");
            $dropbox = new Dropbox($app);
            $authHelper = $dropbox->getOAuth2Client();

            $accessToken = $authHelper->getAccessToken(
                '-utECBOm-pIAAAAAAAAAAb6kjimQJxPDKglTUw3JOE1h-6OatTG4VUdfdLR23omt',
                null,
                'refresh_token'
            );

            $dropbox->setAccessToken($accessToken['access_token']);
            $mode = DropboxFile::MODE_READ;
            $dropBoxFile = DropboxFile::createByPath($image->getPathname(), $mode);
            $dropbox->upload($dropBoxFile, '/' . $image->getClientOriginalName(), ['autorename' => true]);
            $imageURL = $dropbox->getTemporaryLink('/' . $image->getClientOriginalName())->getLink();
            $user->setImage($imageURL);
            // encode the plain password
            $user->setMdp(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getMail(),
                ['id' => $user->getId()]
            );


            $transport = Transport::fromDsn('smtp://gofitpro8@gmail.com:czrr%20mudh%20itak%20iwhy@smtp.gmail.com:587');

            // Create a Mailer object
            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from(new Address(
                    'gofitpro8@gmail.com',
                    'Go Fit Pro'
                ))
                ->to($user->getMail())
                ->subject('Please Confirm your Email')
                ->html('<p>Thank you for registering on our site. Please confirm your email by clicking on the link below:</p><a href="' . $signatureComponents->getSignedUrl() . '">Confirm my email</a>');

            $mailer->send($email);

            $error = $authenticationUtils->getLastAuthenticationError();

            return $this->render(
                'user/login.html.twig',
                ['last_username' => $user->getMail(), 'error' => $error]
            );
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
