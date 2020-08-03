<?php


namespace App\Form;


use App\Entity\ConfigurationWebSite;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationWebSiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valueParametre',TextType::class, ['required'=>true,'label'=>'Valeur du paramètre'])
            ->add('submit', SubmitType::class, ['attr'=>['class'=>'btn monpro btn-lg btn-primary waves-effect waves-light']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConfigurationWebSite::class,
        ]);
    }
}