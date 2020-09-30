<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UserController
 * @package App\Controller
 * @Route ("/user")
 */
class UserController extends AbstractController
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    /**
     * @Route("/", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET", "POST"})
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return RedirectResponse|Response
     */
    public function new(Request $request, ValidatorInterface $validator){
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $errors=$validator->validate($user);
            if (count($errors)>0)return $this->render("user/new.html.twig", ['user'=>$user, 'form'=>$form, 'errors'=>$errors]);
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('user/new.html.twig', ['user' => $user, 'form' => $form->createView()]);

    }


    /**
     * @Route("/{id}", name="user_show", methods={"GET", "POST"})
     * @ParamConverter("user",class="App\Entity\User")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function show(Request $request, User $user){
        $password_form=$this->createForm(UserPasswordType::class);
        $password_form->handleRequest($request);
        if ($password_form->isSubmitted() && $password_form->isValid())
        {
            if ($this->encoder->isPasswordValid($user, $password_form->getData()['old_password'])){
                $user->setPassword($this->encoder->encodePassword($user, $password_form->getData()['new_password']));
                $entityManager=$this->getDoctrine()->getManager();
                $entityManager->flush();
            }
        }
        return $this->render('user/show.html.twig', ['user'=>$user, 'password_form'=>$password_form->createView()]);
    }
}
