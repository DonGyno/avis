<?php


namespace App\Controller;

use App\Entity\ConfigurationWebSite;
use App\Form\ConfigurationWebSiteType;
use App\Repository\ConfigurationWebSiteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConfigurationWebSiteController extends AbstractController
{
    /**
     * @Route("/admin/parametres/edition/parametre-{id}", name="app_admin_edition_parametre")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return RedirectResponse|Response
     */
    public function modifierParametres(Request $request, ValidatorInterface $validator, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $parameter = $em->getRepository(ConfigurationWebSite::class)->find($id);
        $form = $this->createForm(ConfigurationWebSiteType::class,$parameter);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $parameter = $form->getData();
            $em->persist($parameter);
            $em->flush();
            $errors = $validator->validate($parameter);
            if(count($errors) > 0)
            {
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_edition_parametre', ["id"=>$parameter->getId()] );
            }
            else
            {
                $this->addFlash('success','Modification du paramètre : '.$parameter->getNomParametre().' réussie avec succès');
                return $this->redirectToRoute('app_admin_liste_parametres');
            }
        }
        return $this->render('admin/configuration/edit_parameter.html.twig',['parameter'=>$parameter,'formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/admin/parametres/", name="app_admin_liste_parametres")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @param ConfigurationWebSiteRepository $configurationWebSiteRepository
     * @return RedirectResponse|Response
     */
    public function listeParametres(Request $request, PaginatorInterface $paginator, ConfigurationWebSiteRepository $configurationWebSiteRepository)
    {
        $parameters = $configurationWebSiteRepository->findAll();
        if(!$parameters)
        {
            $this->addFlash('error','Aucun paramètre pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            $liste_parameters = $paginator->paginate($parameters, $request->query->get('page',1),10);
            return $this->render('admin/configuration/configuration.html.twig', ['parameters'=>$liste_parameters]);
        }
    }
}