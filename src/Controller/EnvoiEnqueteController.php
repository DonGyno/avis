<?php


namespace App\Controller;


use App\Entity\Avis;
use App\Entity\BaseConvert;
use App\Form\AvisType;
use App\Form\EnqueteType;
use App\Repository\AvisRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class EnvoiEnqueteController extends AbstractController
{

    /**
     * @Route("/envoi-enquete", name="app_envoi_enquete")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param MailerInterface $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
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
                        'token_security'=>$token_security
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
     */
    public function listeAvis()
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
            return $this->render('avis/liste_avis.html.twig', ['avis'=>$avis]);
        }
    }

    /**
     * @Route("/enquete/{token_security}", name="app_repondre_enquete")
     * @param $token_security
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function repondreEnquete($token_security, Request $request, ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avis_client = $entityManager->getRepository(Avis::class)->findOneBy(['token_security'=>$token_security,'statut_avis'=>'En attente de réponse']);
        if(!$avis_client)
        {
            return $this->render('@Twig/Exception/exceptionenquete.html.twig');
        }
        else
        {
            $form = $this->createForm(EnqueteType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid())
            {
                $avis_client->setDateReponseEnquete(new \DateTime());
                $avis_client->setStatutAvis('Réponse enregistrée');
                $avis_client->setIpDestinataire($request->getClientIp());
                $avis_client->setNotePrestationRealisee($form->get('notePrestationRealisee')->getData());
                $avis_client->setNoteProfessionnalismeEntreprise($form->get('noteProfessionnalismeEntreprise')->getData());
                $avis_client->setNoteSatisfactionGlobale($form->get('noteSatisfactionGlobale')->getData());
                $avis_client->setRecommanderCommentaireAEntreprise($form->get('recommanderCommentaireAEntreprise')->getData());
                $avis_client->setTemoignageVideo($form->get('temoignageVideo')->getData());
                $avis_client->setTelephoneDestinataire($form->get('telephoneDestinataire')->getData());

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
     * @Route("/liste-avis/relance-avis/{id}", name="app_relance_avis")
     * @param $id
     * @param MailerInterface $mailer
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
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
                    'token_security'=>$avis_client->getTokenSecurity()
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
}