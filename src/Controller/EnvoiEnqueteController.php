<?php


namespace App\Controller;


use App\Entity\Avis;

use App\Entity\BaseConvert;
use App\Form\AvisEditType;
use App\Form\AvisType;
use App\Form\EnqueteType;
use App\Repository\AvisRepository;
use Knp\Component\Pager\PaginatorInterface;
use League\Csv\CannotInsertRecord;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EnvoiEnqueteController extends AbstractController
{

    /**
     * @Route("/envoi-enquete", name="app_envoi_enquete")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     */
    public function formulaireEnvoiEnquete(Request $request,ValidatorInterface $validator, MailerInterface $mailer)
    {
        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            // Récupère les données du formulaire
            $avis = $form->getData();
            // Définitions des autres données essentielles
            $avis->setDateEnvoiEnquete(new \DateTime());
            $avis->setStatutAvis("En attente de réponse");
            $avis->setNbRelance(0);
            $hash = hash('sha256', time() . mt_rand());
            $token_security = BaseConvert::convertBase($hash, BaseConvert::BASE16, BaseConvert::BASE54);
            $avis->setTokenSecurity($token_security);

            $errors = $validator->validate($avis);
            if(count($errors)>0)
            {
                $this->addFlash('error','Une erreur est survenue !');
            }
            else
            {
                $em = $this->getDoctrine()->getManager();
                $em->persist($avis);
                $em->flush();
                // Succès - Envoi du mail au client
                $this->addFlash('success','Enquête envoyée avec succès !');
                $email = (new TemplatedEmail())
                    ->from('avis@monprocertifie.fr')
                    ->to($avis->getEmailDestinataire())
                    ->subject('Enquête de satisfaction - MonProCertifié')
                    ->htmlTemplate('emails/enquete.html.twig')
                    ->context([
                        'nomDestinaire'=>$avis->getNomDestinataire(),
                        'prenomDestinaire'=>$avis->getPrenomDestinataire(),
                        'nomEntreprise'=>$avis->getEntrepriseConcernee()->getNom(),
                        'pathToken'=>$this->generateUrl('app_repondre_enquete',['token_security'=>$avis->getTokenSecurity()],UrlGeneratorInterface::ABSOLUTE_URL)
                    ])
                    ;
                $mailer->send($email);
                return $this->redirectToRoute('app_index_index');
            }
        }
        return $this->render('avis/add_nouvelle_enquete.html.twig',['formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/liste-avis", name="app_liste_avis")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function listeAvis(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $avis = $em->getRepository(Avis::class)->findAll();
        if(!$avis)
        {
            $this->addFlash('error','Aucun avis pour le moment');
            return $this->render('base.html.twig');
        }
        else
        {
            $liste_avis = $paginator->paginate($avis,$request->query->getInt('page',1),10);
            return $this->render('avis/liste_avis.html.twig', ['avis'=>$liste_avis]);
        }
    }

    /**
     * @Route("/enquete/{token_security}", name="app_repondre_enquete")
     * @param $token_security
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param MailerInterface $mailer
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function repondreEnquete($token_security, Request $request, ValidatorInterface $validator, MailerInterface $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avis_client = $entityManager->getRepository(Avis::class)->findOneBy(['token_security'=>$token_security,'statut_avis'=>'En attente de réponse']);
        if(!$avis_client)
        {
            return $this->render('@Twig/Exception/exceptionenquete.html.twig');
        }
        else
        {
            $form = $this->createForm(EnqueteType::class,$avis_client);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $avis_client = $form->getData();
                $avis_client->setDateReponseEnquete(new \DateTime());
                $avis_client->setStatutAvis('Réponse enregistrée');
                $avis_client->setIpDestinataire($request->getClientIp());

                $entityManager->persist($avis_client);
                $entityManager->flush();

                $errors = $validator->validate($avis_client);
                if(count($errors)>0)
                {
                    $this->addFlash('error','Une erreur est survenue !');
                }
                else
                {
                    $this->addFlash('success','Merci d\'avoir répondu à notre enquête ! Vous pouvez désormais quitter la page');
                    $email_admin = (new TemplatedEmail())
                        ->from('avis@monprocertifie.fr')
                        ->to('aleksandra@monprocertifie.fr','anthony@monprocertifie.fr')
                        ->subject('Réponse d\'une enquête de satisfaction')
                        ->htmlTemplate('emails/admin.html.twig')
                        ->context([
                            'nomDestinaire'=>$avis_client->getNomDestinataire(),
                            'prenomDestinaire'=>$avis_client->getPrenomDestinataire(),
                            'emailDestinataire'=>$avis_client->getEmailDestinataire(),
                            'nomEntreprise'=>$avis_client->getEntrepriseConcernee()->getNom(),
                            'date_reponse'=>$avis_client->getDateReponseEnquete()
                        ])
                    ;
                    $mailer->send($email_admin);

                    $email_client = (new TemplatedEmail())
                        ->from('avis@monprocertifie.fr')
                        ->to($avis_client->getEmailDestinataire())
                        ->subject('Enquête de satisfaction - Merci !')
                        ->htmlTemplate('emails/merci.html.twig')
                        ->context([
                            'nomDestinaire'=>$avis_client->getNomDestinataire(),
                            'prenomDestinaire'=>$avis_client->getPrenomDestinataire(),
                            'emailDestinataire'=>$avis_client->getEmailDestinataire(),
                            'nomEntreprise'=>$avis_client->getEntrepriseConcernee()->getNom(),
                            'date_reponse'=>$avis_client->getDateReponseEnquete()
                        ])
                    ;
                    $mailer->send($email_client);

                }
            }
            return $this->render('avis/formulaire_enquete.html.twig', ['avis'=>$avis_client,'formulaire'=>$form->createView()]);
        }
    }

    /**
     * @Route("/liste-avis/avis/{id}", name="app_voir_avis")
     * @param $id
     * @return Response
     */
    public function viewAvis($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avis_client = $entityManager->getRepository(Avis::class)->find($id);
        if(!$avis_client)
        {
            $this->addFlash('error','Aucun avis trouvé avec cet identifiant !');
            $this->redirectToRoute('app_liste_avis');
        }
        else
        {
            return $this->render('avis/view_avis.html.twig', ['avis'=>$avis_client]);
        }
    }

    /**
     * @Route("/liste-avis/avis/editer/{id}", name="app_edition_avis")
     * @param $id
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function editionAvis($id, Request $request, ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avis_client = $entityManager->getRepository(Avis::class)->find($id);
        if(!$avis_client)
        {
            $this->addFlash('error','Aucun avis trouvé avec cet identifiant !');
            $this->redirectToRoute('app_liste_avis');
        }
        else
        {
            $form = $this->createForm(AvisEditType::class,$avis_client);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $avis_client = $form->getData();
                $entityManager->persist($avis_client);
                $entityManager->flush();

                $errors = $validator->validate($avis_client);
                if(count($errors)>0)
                {
                    $this->addFlash('error','Une erreur est survenue !');
                }
                else
                {
                    $this->addFlash('success','L\'avis a bien été modifié !');
                    return $this->redirectToRoute('app_liste_avis');
                }
            }
            return $this->render('avis/edit_avis.html.twig', ['avis'=>$avis_client,'formulaire'=>$form->createView()]);
        }
    }

    /**
     * @Route("/liste-avis/relance-avis/{id}", name="app_relance_avis")
     * @param $id
     * @param MailerInterface $mailer
     * @return RedirectResponse
     * @throws TransportExceptionInterface
     */
    public function relanceEmail($id, MailerInterface $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avis_client = $entityManager->getRepository(Avis::class)->find($id);
        if(!$avis_client)
        {
            $this->addFlash('error','Une erreur est survenue !');
        }
        else
        {
            $email = (new TemplatedEmail())
                ->from('avis@monprocertifie.fr')
                ->to($avis_client->getEmailDestinataire())
                ->subject('Enquête de satisfaction - MonProCertifié')
                ->htmlTemplate('emails/relance.html.twig')
                ->context([
                    'nomDestinaire'=>$avis_client->getNomDestinataire(),
                    'prenomDestinaire'=>$avis_client->getPrenomDestinataire(),
                    'nomEntreprise'=>$avis_client->getEntrepriseConcernee()->getNom(),
                    'pathToken'=>$this->generateUrl('app_repondre_enquete',['token_security'=>$avis_client->getTokenSecurity()],UrlGeneratorInterface::ABSOLUTE_URL)
                ])
            ;
            $mailer->send($email);

            $avis_client->setDateDerniereRelance(new \DateTime());
            $nb_relance = $avis_client->getNbRelance();
            $nb_relance++;
            $avis_client->setNbRelance($nb_relance);
            $entityManager->persist($avis_client);
            $entityManager->flush();

            $this->addFlash('success','Relance envoyée avec succès !');
            return $this->redirectToRoute('app_liste_avis');
        }
    }

    /**
     * @Route("/liste-avis/export", name="app_export_avis")
     * @param AvisRepository $avisRepository
     * @param Request $request
     * @return Response
     * @throws CannotInsertRecord
     */
    public function exportAvis(AvisRepository $avisRepository, Request $request, SerializerInterface $serializer)
    {
        if($request->isXmlHttpRequest())
        {
            $array_id = $request->request->get('array_id');
            $avis_export = $avisRepository->exportAvis($array_id);
            $jsonContent = $serializer->serialize($avis_export,'json',['circular_reference_handler'=>function($object){
                return $object->getId();
            }]);
            return new JsonResponse(['avis'=>$jsonContent]);
        }
        return $this->render('@Twig/Exception/error403.html.twig');
    }
}