<?php


namespace App\Form;


use App\Entity\Avis;
use App\Entity\Entreprise;
use App\Repository\EntrepriseRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomDestinataire', TextType::class, ['required'=>true,'label'=>'Nom du destinataire'])
            ->add('prenomDestinataire', TextType::class, ['required'=>true,'label'=>'Prénom du destinataire'])
            ->add('emailDestinataire', EmailType::class, ['required'=>true,'label'=>'Email du destinataire'])
            ->add('entrepriseConcernee', EntityType::class,
                [
                    'required'=>true,
                    'label'=>'Entreprise concernée',
                    'class'=>Entreprise::class,
                    'choice_label'=> function (Entreprise $entreprise){
                        return $entreprise->getNom().' - '.$entreprise->getRaisonSociale();
                    }
                ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn monpro btn-lg btn-primary waves-effect waves-light'
                ],
                'label' => 'Envoyer enquête'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>Avis::class]);
    }
}