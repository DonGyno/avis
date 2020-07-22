<?php


namespace App\Controller;


use App\Entity\FicheProspection;
use App\Form\FicheProspectionType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}