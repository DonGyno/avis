<?php


namespace App\Controller;


use App\Entity\Avis;
use App\Entity\BaseConvert;
use App\Form\AvisType;
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

    public function relanceEmail()
    {

    }

    /**
     * @Route("/enquete/{token_security}", name="app_repondre_enquete")
     * @param $token_security
     * @param AvisRepository $avisRepository
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function repondreEnquete($token_security, AvisRepository $avisRepository, Request $request, ValidatorInterface $validator)
    {
        $avis_client = $avisRepository->findBy(['token_security'=>$token_security,'statut_avis'=>'En attente de réponse']);
        if(!$avis_client)
        {
            throw new AccessDeniedException();
        }
        else
        {
            return $this->render('avis/formulaire_enquete.html.twig');
        }
    }
}