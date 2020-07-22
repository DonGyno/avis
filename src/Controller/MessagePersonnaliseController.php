<?php


namespace App\Controller;

use App\Entity\MessagePersonnalise;

use App\Form\MessageType;
use App\Repository\MessagePersonnaliseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MessagePersonnaliseController extends AbstractController
{
    /**
     * @Route("/admin/message/ajouter", name="app_admin_message_ajouter")
     * @param Request $request
     * @param ValidatorInterface $validator
     */
    public function nouveauMessagePersonnalise(Request $request, ValidatorInterface $validator)
    {
        $message = new MessagePersonnalise();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $message = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $errors = $validator->validate($message);
            if(count($errors)>0)
            {
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_message_ajouter');
            }
            else
            {
                $this->addFlash('success','Le message a été ajouté avec succès !');
                return $this->redirectToRoute('app_admin_message_ajouter');
            }
        }
        return $this->render('admin/message/add_message.html.twig', ['formulaire'=>$form->createView()]);
    }

    /**
     * @Route("/admin/liste-messages", name="app_admin_liste_messages")
     * @param MessagePersonnaliseRepository $messagePersonnaliseRepository
     * @return Response
     */
    public function listeMessages(MessagePersonnaliseRepository $messagePersonnaliseRepository)
    {
        $messages = $messagePersonnaliseRepository->findAll();
        if(!$messages)
        {
            $this->addFlash('error','Aucun message pour le moment');
            return $this->render('admin/index.html.twig');
        }
        else
        {
            return $this->render('admin/message/liste-messages.html.twig', ['messages'=>$messages]);
        }
    }

    /**
     * @Route("/admin/message/editer/{id}", name="app_admin_message_editer")
     * @param Request $request
     * @param ValidatorInterface $validator
     */
    public function editerMessagePersonnalise($id, Request $request, ValidatorInterface $validator)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository(MessagePersonnalise::class)->find($id);
        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $message->setTitre($form->get('titre')->getData());
            $message->setContenu($form->get('contenu')->getData());
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();

            $errors = $validator->validate($message);
            if(count($errors)>0)
            {
                $this->addFlash('error','Une erreur est survenue !');
                return $this->redirectToRoute('app_admin_message_editer');
            }
            else
            {
                $this->addFlash('success','Le message a été modifié avec succès !');
                return $this->redirectToRoute('app_admin_liste_messages');
            }
        }
        return $this->render('admin/message/edit_message.html.twig', ['formulaire'=>$form->createView(),'message'=>$message]);
    }

    /**
     * @Route("/admin/message/supprimer/{id}", name="app_admin_supprimer_message")
     * @return Response
     */
    public function supprimerMessage($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager->getRepository(MessagePersonnalise::class)->find($id);
        if(!$message)
        {
            $this->addFlash('error','Aucun message ayant cet identifiant');
            return $this->redirectToRoute('app_admin_liste_messages');
        }
        else
        {
            $entityManager->remove($message);
            $entityManager->flush();
            $this->addFlash('success','Message supprimée avec succès !');
            return $this->redirectToRoute('app_admin_liste_messages');
        }
    }
}