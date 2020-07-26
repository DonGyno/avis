<?php


namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\AvisRepository;
use App\Repository\EntrepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/admin/entreprise/ajouter", name="app_admin_entreprise_ajouter")
     * @param Request $request
     * @param ValidatorInterface $validator
     */
    public function nouvelleEntreprise(Request $request, ValidatorInterface $validator)
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class,$entreprise);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entreprise = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entreprise);
            $entityManager->flush();

            $errors = $validator->validate($entreprise);
            if(count($errors)>0)
            {
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_entreprise_ajouter');
            }
            else{
                $this->addFlash('success','Entreprise '.$form->get('nom')->getData().' ajoutée avec succès !');
                return $this->redirectToRoute('app_admin_entreprise_ajouter');
            }
        }
        return $this->render('admin/entreprise/ajout.html.twig', ['formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/admin/liste-entreprises", name="app_admin_liste_entreprises")
     * @param EntrepriseRepository $entrepriseRepository
     * @return Response
     */
    public function listeEntreprises(EntrepriseRepository $entrepriseRepository)
    {
        $entreprises = $entrepriseRepository->findAll();
        if(!$entreprises)
        {
            $this->addFlash('error','Aucune entreprise pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            return $this->render('admin/entreprise/liste-entreprises.html.twig', ['entreprises'=>$entreprises]);
        }
    }

    /**
     * @Route("/admin/entreprise/editer/{id}", name="app_admin_editer_entreprise")
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function editerEntreprise($id, Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        $entreprise = $em->getRepository(Entreprise::class)->find($id);
        $form = $this->createForm(EntrepriseType::class,$entreprise);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entreprise->setNom($form->get('nom')->getData());
            $entreprise->setRaisonSociale($form->get('raisonSociale')->getData());
            $entreprise->setAdresseRue($form->get('adresseRue')->getData());
            $entreprise->setAdresseVille($form->get('adresseVille')->getData());
            $entreprise->setAdresseCodePostal($form->get('adresseCodePostal')->getData());
            $entreprise->setTelephoneFixe($form->get('telephoneFixe')->getData());
            $entreprise->setTelephonePortable($form->get('telephonePortable')->getData());
            $entreprise->setEmailContact($form->get('emailContact')->getData());
            $entreprise->setHoraires($form->get('horaires')->getData());
            $entreprise->setApe($form->get('ape')->getData());
            $entreprise->setSirenSiret($form->get('sirenSiret')->getData());
            $em->persist($entreprise);
            $em->flush();
            $errors = $validator->validate($entreprise);
            if(count($errors) > 0){
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_editer_entreprise', ["id"=>$entreprise->getId()] );
            }
            else {
                $this->addFlash('success','Modification réussie avec succès');
                return $this->redirectToRoute('app_admin_liste_entreprises');
            }
        }
        return $this->render('admin/entreprise/edit.html.twig',['entreprise'=>$entreprise,'formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/admin/entreprise/supprimer/{id}", name="app_admin_supprimer_entreprise")
     * @return Response
     */
    public function supprimerEntreprise($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entreprise = $entityManager->getRepository(Entreprise::class)->find($id);
        if(!$entreprise)
        {
            $this->addFlash('error','Aucune entreprise ayant cet identifiant');
            return $this->redirectToRoute('app_admin_liste_entreprises');
        }
        else
        {
            $entityManager->remove($entreprise);
            $entityManager->flush();
            $this->addFlash('success','Entreprise supprimée avec succès !');
            return $this->redirectToRoute('app_admin_liste_entreprises');
        }
    }

    /**
     * @Route("/avis-client-{id}-{nom}", name="app_view_entreprise")
     * @return Response
     */
    public function viewEntreprise($id, AvisRepository $avisRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $entreprise = $em->getRepository(Entreprise::class)->find($id);
        $avis = $avisRepository->findBy(['entreprise_concernee'=>$entreprise->getId()]);
        if(!$entreprise)
        {
            return $this->render('@Twig/Exception/error404.html.twig');
        }
        else
        {
            return $this->render('entreprise/view_entreprise.html.twig',['entreprise'=>$entreprise,'avis'=>$avis]);
        }
    }
}