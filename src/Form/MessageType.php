<?php


namespace App\Form;

use App\Entity\MessagePersonnalise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, ['required'=>true,'label'=>'Titre du message (n\'apparaîtra pas à l\'affichage) :'])
            ->add('contenu', TextareaType::class, ['required'=>true,'label'=>'Contenu du message :'])
            ->add('submit', SubmitType::class, ['attr'=>['class'=>'btn monpro btn-lg btn-primary waves-effect waves-light'],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MessagePersonnalise::class,
        ]);
    }
}