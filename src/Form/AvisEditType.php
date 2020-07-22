<?php


namespace App\Form;


use App\Entity\Avis;
use App\Entity\Entreprise;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('telephoneDestinataire', TelType::class, ['label'=>'Numéro de la personne à joindre pour le témoignage','required'=>false])
            ->add('notePrestationRealisee',ChoiceType::class, [
                'required'=>true,
                'label'=>'Note de la prestation réalisée :',
                'choices'=>[
                    '0.5 / 5'=>0.5,
                    '1 / 5'=>1,
                    '1.5 / 5'=>1.5,
                    '2 / 5'=>2,
                    '2.5 / 5'=>2.5,
                    '3 / 5'=>3,
                    '3.5 / 5'=>3.5,
                    '4 / 5'=>4,
                    '4.5 / 5'=>4.5,
                    '5 / 5'=>5
                ],
                'expanded'=>true,
                'multiple'=>false,
                'attr'=>['class'=>'with-gap','name'=>'notePrestationRealisee'],
            ])
            ->add('noteProfessionnalismeEntreprise',ChoiceType::class, [
                'required'=>true,
                'label'=>'Note du professionnalisme de l\'entreprise :',
                'choices'=>[
                    '0.5 / 5'=>0.5,
                    '1 / 5'=>1,
                    '1.5 / 5'=>1.5,
                    '2 / 5'=>2,
                    '2.5 / 5'=>2.5,
                    '3 / 5'=>3,
                    '3.5 / 5'=>3.5,
                    '4 / 5'=>4,
                    '4.5 / 5'=>4.5,
                    '5 / 5'=>5
                ],
                'expanded'=>true,
                'multiple'=>false,
                'attr'=>['class'=>'with-gap','name'=>'noteProfessionnalismeEntreprise'],
            ])
            ->add('noteSatisfactionGlobale',ChoiceType::class, [
                'required'=>true,
                'label'=>'Note de la satisfaction globale :',
                'choices'=>[
                    '0.5 / 5'=>0.5,
                    '1 / 5'=>1,
                    '1.5 / 5'=>1.5,
                    '2 / 5'=>2,
                    '2.5 / 5'=>2.5,
                    '3 / 5'=>3,
                    '3.5 / 5'=>3.5,
                    '4 / 5'=>4,
                    '4.5 / 5'=>4.5,
                    '5 / 5'=>5
                ],
                'expanded'=>true,
                'multiple'=>false,
                'attr'=>['class'=>'with-gap','name'=>'noteSatisfactionGlobale'],
            ])
            ->add('recommanderCommentaireAEntreprise', TextareaType::class, ['label'=>'Recommandation / Commentaire sur la prestation de l\'entreprise','required'=>false])
            ->add('temoignageVideo',ChoiceType::class, [
                'required'=>true,
                'label'=>'Consentement témoignage vidéo :',
                'choices'=>[
                    'Non'=>'Non',
                    'Oui'=>'Oui',
                ],
                'expanded'=>true,
                'multiple'=>false,
                'attr'=>['class'=>'with-gap','name'=>'temoignageVideo'],
            ])
            ->add('telephoneDestinataire', TelType::class, ['label'=>'Numéro de la personne à joindre pour le témoignage :','required'=>false])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn monpro btn-lg btn-primary waves-effect waves-light'
                ],
                'label' => 'Modifer l\'avis'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>Avis::class]);
    }
}