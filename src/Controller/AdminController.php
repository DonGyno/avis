<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\AdminEditUserType;
use App\Repository\UserRepository;
use App\Form\AdminInscriptionType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/admin/inscription", name="app_admin_inscription")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return RedirectResponse|Response
     */
    public function adminInscription(Request $request, ValidatorInterface $validator)
    {
        $user = new User();
        $form = $this->createForm(AdminInscriptionType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $user->setDateCreation(new \DateTime());
            $user->setRoles(array('ROLE_USER'));
            $user->setPassword($this->passwordEncoder->encodePassword($user,$form['password']->getData()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $errors = $validator->validate($user);
            if(count($errors) > 0){
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_inscription');
            }
            else {
                $this->addFlash('success','Inscription réalisée avec succès !');
                return $this->redirectToRoute('app_admin_inscription');
            }
        }
        return $this->render('admin/utilisateur/inscription.html.twig', ['formulaire' => $form->createView()]);
    }

    /**
     * @Route("/admin/liste-utilisateurs", name="app_admin_liste_users")
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function listeUtilisateur(UserRepository $userRepository, PaginatorInterface $paginator, Request $request)
    {
        $users = $userRepository->findAllByUserRole('"ROLE_USER"');
        if(!$users)
        {
            $this->addFlash('error','Aucun utilisateur pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            $liste_users = $paginator->paginate($users,$request->query->getInt('page',1),10);
            return $this->render('admin/utilisateur/liste-utilisateurs.html.twig', ['utilisateurs'=>$liste_users]);
        }
    }

    /**
     * @Route("/admin/utilisateur/editer/{id}", name="app_admin_editer_utilisateur")
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function editerUtilisateur($id, Request $request, ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(AdminEditUserType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $entityManager->persist($user);
            $entityManager->flush();
            $errors = $validator->validate($user);
            if(count($errors) > 0){
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_editer_utilisateur', ["id"=>$user->getId()] );
            }
            else {
                $this->addFlash('success','Modification réussie avec succès');
                return $this->redirectToRoute('app_admin_liste_users');
            }
        }
        return $this->render('admin/utilisateur/edit.html.twig', ['utilisateur'=>$user,'formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/admin/utilisateur/supprimer/{id}", name="app_admin_supprimer_utilisateur")
     * @return Response
     */
    public function supprimerUtilisateur($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if(!$user)
        {
            $this->addFlash('error','Aucun utilisateur ayant cet identifiant');
            return $this->redirectToRoute('app_admin_liste_users');
        }
        else
        {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success','Utilisateur supprimé avec succès !');
            return $this->redirectToRoute('app_admin_liste_users');
        }
    }

    /**
     * @Route("/admin/utilisateurs", name="app_admin_get_users")
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function getAllUser(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $users = $userRepository->findAll();
        $jsonContent = $serializer->serialize($users,'json',['circular_reference_handler'=>function($object){
            return $object->getId();
        }]);
        return new JsonResponse(array('users'=>$jsonContent));
    }
}