<?php


namespace App\Controller;

use App\Entity\ConfigurationWebSite;
use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use App\Repository\AvisRepository;
use App\Repository\ConfigurationWebSiteRepository;
use App\Repository\EntrepriseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntrepriseController extends AbstractController
{
    /**
     * @Route("/admin/entreprise/ajouter", name="app_admin_entreprise_ajouter")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SluggerInterface $slugger
     * @return RedirectResponse|Response
     */
    public function nouvelleEntreprise(Request $request, ValidatorInterface $validator, SluggerInterface $slugger)
    {
        $entreprise = new Entreprise();
        $form = $this->createForm(EntrepriseType::class,$entreprise);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entreprise = $form->getData();
            $logo = $form->get('logo')->getData();
            if($logo)
            {
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();
                try {
                    $logo->move(
                        $this->getParameter('logo_entreprise_directory'),
                        $newFilename
                    );
                } catch (FileException $e)
                {
                    dump($e->getMessage());
                }
                $entreprise->setLogo($newFilename);
            }
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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function listeEntreprises(EntrepriseRepository $entrepriseRepository, PaginatorInterface $paginator, Request $request)
    {
        $entreprises = $entrepriseRepository->findAll();
        if(!$entreprises)
        {
            $this->addFlash('error','Aucune entreprise pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            $liste_entreprises = $paginator->paginate($entreprises,$request->query->getInt('page',1),10);
            return $this->render('admin/entreprise/liste-entreprises.html.twig', ['entreprises'=>$liste_entreprises]);
        }
    }

    /**
     * @Route("/admin/entreprise/editer/{id}", name="app_admin_editer_entreprise")
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function editerEntreprise($id, Request $request, ValidatorInterface $validator, SluggerInterface $slugger)
    {
        $em = $this->getDoctrine()->getManager();
        $entreprise = $em->getRepository(Entreprise::class)->find($id);
        $form = $this->createForm(EntrepriseType::class,$entreprise);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entreprise = $form->getData();
            $logo = $form->get('logo')->getData();
            if($logo)
            {
                $originalFilename = pathinfo($logo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logo->guessExtension();
                try {
                    $logo->move(
                        $this->getParameter('logo_entreprise_directory'),
                        $newFilename
                    );
                } catch (FileException $e)
                {
                    dump($e->getMessage());
                }
                $entreprise->setLogo($newFilename);
            }
            $em->persist($entreprise);
            $em->flush();
            $errors = $validator->validate($entreprise);
            if(count($errors) > 0){
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_editer_entreprise', ["id"=>$entreprise->getId()] );
            }
            else {
                $this->addFlash('success','Modification de l\'entreprise : '.$entreprise->getNom().' réussie avec succès');
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
            $nom_entreprise = $entreprise->getNom();
            $logo = $entreprise->getLogo();
            if($logo)
            {
                try {
                    $pathfile = $this->getParameter('logo_entreprise_directory');
                    unlink($pathfile.'/'.$logo);
                } catch (FileException $e)
                {
                    dump($e->getMessage());
                }
            }
            $entityManager->remove($entreprise);
            $entityManager->flush();
            $this->addFlash('success','Entreprise : '.$nom_entreprise.' supprimée avec succès !');
            return $this->redirectToRoute('app_admin_liste_entreprises');
        }
    }

    /**
     * @Route("/avis-client-{id}-{nom}", name="app_view_entreprise")
     * @param $id
     * @param AvisRepository $avisRepository
     * @param ConfigurationWebSiteRepository $configurationWebSiteRepository
     * @return Response
     */
    public function viewEntreprise($id, AvisRepository $avisRepository, ConfigurationWebSiteRepository $configurationWebSiteRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $entreprise = $em->getRepository(Entreprise::class)->find($id);
        $avis = $avisRepository->findBy(['entreprise_concernee'=>$entreprise->getId(),'statut_avis'=>'Réponse enregistrée']);
        $titre_header = $configurationWebSiteRepository->findOneBy(['nomParametre'=>'header_title']);
        $titre_footer = $configurationWebSiteRepository->findOneBy(['nomParametre'=>'footer_title']);
        if(!$entreprise)
        {
            return $this->render('@Twig/Exception/error404.html.twig');
        }
        else
        {
            return $this->render('entreprise/view_entreprise.html.twig',['entreprise'=>$entreprise,'avis'=>$avis,'titre_header'=>$titre_header,'titre_footer'=>$titre_footer]);
        }
    }
}