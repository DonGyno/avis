<?php


namespace App\Controller;


use App\Entity\FicheProspection;
use App\Form\FicheProspectionType;
use App\Form\FicheProspectionUserType;
use App\Repository\FicheProspectionRepository;
use App\Repository\UserRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class FicheProspectionController extends AbstractController
{
    /**
     * @Route("admin/fiche-prospection/ajouter", name="app_add_admin_fiche_prospection")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     */
    public function addFicheProspectionAdmin(Request $request, ValidatorInterface $validator, MailerInterface $mailer, UserRepository $userRepository)
    {
        $fiche_prospection = new FicheProspection();
        $form = $this->createForm(FicheProspectionType::class,$fiche_prospection);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $fiche_prospection = $form->getData();
            $fiche_prospection->setDateCreation(new DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($fiche_prospection);
            $em->flush();

            $errors = $validator->validate($fiche_prospection);
            if(count($errors)>0)
            {
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_add_admin_fiche_prospection');
            }
            else
            {
                $this->addFlash('success','Fiche de prospection : '.$form->get('nomEntreprise')->getData().' ajoutée avec succès !');
                $responsable = $userRepository->find($fiche_prospection->getResponsableFicheProspection());
                $email = (new TemplatedEmail())
                    ->from('avis@monprocertifie.fr')
                    ->to($responsable->getEmail())
                    ->subject('Attribution d\'une nouvelle fiche de prospection - MonProCertifié')
                    ->htmlTemplate('emails/admin_add_prospection.html.twig')
                    ->context([
                        "nomResponsable"=>$responsable->getNom(),
                        "prenomResponsable"=>$responsable->getPrenom(),
                        "nomFicheEntreprise"=>$fiche_prospection->getNomEntreprise(),
                        "dateCreation"=>$fiche_prospection->getDateCreation()
                    ])
                ;
                $mailer->send($email);
                return $this->redirectToRoute('app_add_admin_fiche_prospection');
            }
        }
        return $this->render('admin/prospection/add_fiche_prospection.html.twig',['formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/fiche-prospection/ajouter", name="app_add_user_fiche_prospection")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param MailerInterface $mailer
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     */
    public function addFicheProspectionUser(Request $request, ValidatorInterface $validator, MailerInterface $mailer, UserRepository $userRepository)
    {
        $fiche_prospection_user = new FicheProspection();
        $form = $this->createForm(FicheProspectionUserType::class,$fiche_prospection_user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $fiche_prospection_user = $form->getData();
            $fiche_prospection_user->setDateCreation(new DateTime());
            $responsable_courant = $userRepository->findOneBy(['email'=>$this->getUser()->getUsername()]);
            $fiche_prospection_user->setResponsableFicheProspection($responsable_courant->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($fiche_prospection_user);
            $em->flush();

            $errors = $validator->validate($fiche_prospection_user);
            if(count($errors)>0)
            {
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_add_user_fiche_prospection');
            }
            else
            {
                $this->addFlash('success','Fiche de prospection : '.$form->get('nomEntreprise')->getData().' ajoutée avec succès !');
                $email = (new TemplatedEmail())
                    ->from('avis@monprocertifie.fr')
                    ->to('aleksandra@monprocertifie.fr')
                    ->subject('Création d\'une nouvelle fiche de prospection - MonProCertifié')
                    ->htmlTemplate('emails/admin_add_prospection_user.html.twig')
                    ->context([
                        "nomResponsable"=>$responsable_courant->getNom(),
                        "prenomResponsable"=>$responsable_courant->getPrenom(),
                        "nomFicheEntreprise"=>$fiche_prospection_user->getNomEntreprise(),
                        "dateCreation"=>$fiche_prospection_user->getDateCreation()
                    ])
                ;
                $mailer->send($email);
                return $this->redirectToRoute('app_add_user_fiche_prospection');
            }
        }
        return $this->render('ficheprospection/add_fiche_prospection_user.html.twig',['formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/admin/fiches-prospection/", name="app_admin_liste_fiche_prospection")
     * @param Request $request
     * @param FicheProspectionRepository $ficheProspectionRepository
     * @return Response
     */
    public function listeFichesProspectionAdmin(FicheProspectionRepository $ficheProspectionRepository)
    {
        $fiches_prospections = $ficheProspectionRepository->findAll();
        if(!$fiches_prospections)
        {
            $this->addFlash('error','Aucune fiche de prospection pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            return $this->render('admin/prospection/liste_fiches_prospection.html.twig',['fiches'=>$fiches_prospections]);
        }
    }

    /**
     * @Route("/fiches-prospection/", name="app_user_liste_fiche_prospection")
     * @param FicheProspectionRepository $ficheProspectionRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listeFichesProspectionUser(FicheProspectionRepository $ficheProspectionRepository, UserRepository $userRepository)
    {
        $current_user = $this->getUser()->getUsername();
        $responsable = $userRepository->findOneBy(['email'=>$current_user]);
        $fiches_prospections = $ficheProspectionRepository->findBy(['responsable_fiche_prospection'=>$responsable->getId()]);
        if(!$fiches_prospections)
        {
            $this->addFlash('error','Vous n\'avez aucune fiche de prospection pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            return $this->render('ficheprospection/liste_fiches_prospection_user.html.twig',['fiches'=>$fiches_prospections]);
        }
    }

    /**
     * @Route("/admin/modification-responsable", name="app_admin_modification_responsable")
     * @param Request $request
     * @param FicheProspectionRepository $ficheProspectionRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function modificationResponsableAjax(Request $request, FicheProspectionRepository $ficheProspectionRepository, UserRepository $userRepository)
    {
        if($request->isXmlHttpRequest())
        {
            $id_fiche = $request->request->get('id_fiche');
            $id_responsable = $request->request->get('id_responsable');

            $fiche_concernee = $ficheProspectionRepository->find($id_fiche);
            $responsable = $userRepository->find($id_responsable);

            if($id_responsable === 'NULL')
            {
                $fiche_concernee->setResponsableFicheProspection(null);
            }
            else
            {
                $fiche_concernee->setResponsableFicheProspection($responsable);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($fiche_concernee);
            $em->flush();

            return new JsonResponse(array(
                'status'=>'Modification réussie !'
            ));
        }
    }

    /**
     * @Route("/admin/fiche-prospection/supprimer/{id}", name="app_admin_supprimer_fiche_prospection")
     * @return Response
     */
    public function suppressionFicheProspectionAdmin($id)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository(FicheProspection::class)->find($id);
        if(!$fiche)
        {
            $this->addFlash('error','Aucune fiche de prospection ayant cet identifiant !');
            return $this->redirectToRoute('app_admin_liste_fiche_prospection');
        }
        else
        {
            $em->remove($fiche);
            $em->flush();
            $this->addFlash('success','Fiche de prospection supprimé avec succès !');
            return $this->redirectToRoute('app_admin_liste_fiche_prospection');
        }
    }

    /**
     * @Route("/admin/fiche-prospection/modifier/{id}", name="app_admin_modifier_fiche_prospection")
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function modificationFicheProspectionAdmin($id, Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository(FicheProspection::class)->find($id);
        $form = $this->createForm(FicheProspectionType::class,$fiche);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $fiche = $form->getData();
            $em->persist($fiche);
            $em->flush();
            $errors = $validator->validate($fiche);
            if(count($errors) > 0){
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_modifier_fiche_prospection', ["id"=>$fiche->getId()] );
            }
            else {
                $this->addFlash('success','Modification réussie avec succès');
                return $this->redirectToRoute('app_admin_liste_fiche_prospection');
            }
        }
        return $this->render('admin/prospection/edit_fiche_prospection.html.twig',['fiche'=>$fiche,'formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/fiche-prospection/modifier/{id}", name="app_user_modifier_fiche_prospection")
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function modificationFicheProspectionUser($id, Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        $fiche = $em->getRepository(FicheProspection::class)->find($id);
        $form = $this->createForm(FicheProspectionUserType::class,$fiche);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $fiche = $form->getData();
            $em->persist($fiche);
            $em->flush();
            $errors = $validator->validate($fiche);
            if(count($errors) > 0){
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_user_modifier_fiche_prospection', ["id"=>$fiche->getId()] );
            }
            else {
                $this->addFlash('success','Modification réussie avec succès');
                return $this->redirectToRoute('app_user_liste_fiche_prospection');
            }
        }
        return $this->render('ficheprospection/edit_fiche_prospection_user.html.twig',['fiche'=>$fiche,'formulaire'=>$form->createView()]);
    }
}