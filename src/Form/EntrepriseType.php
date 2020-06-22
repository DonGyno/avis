<?php


namespace App\Form;



use App\Entity\Entreprise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntrepriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class, ['required'=>true,'label'=>'Nom de l\'entreprise'])
            ->add('raisonSociale',ChoiceType::class, ['label'=>'Raison Sociale',
                'choices'=>[
                    "Société à Responsabilité Limitée (SARL)"=>"SARL",
                    "Société par Actions Simplifiées Unipersonnelle (SASU)"=>"SASU",
                    "Société par Actions Simplifiée (SAS)"=>"SAS",
                    "Société Anonyme (SA)"=>"SA",
                    "Entreprise Individuelle (EI)"=>"EI",
                    "Entreprise Unipersonnelle A Responsabilité Limitée (EURL)"=>"EURL",
                    "Entreprise Individuelle A Responsabilité Limitée (EIRL)"=>"EIRL",
                    "Auto-Entreprise (AE)"=>"AE"
                    ]])
            ->add('adresseRue',TextType::class, ['label'=>'Rue','required'=>false])
            ->add('adresseVille',TextType::class, ['label'=>'Ville','required'=>false])
            ->add('adresseCodePostal',TextType::class, ['label'=>'Code Postal','required'=>false])
            ->add('telephoneFixe',TelType::class, ['label'=>'Téléphone Fixe','help'=>'Le format du téléphone fixe est la suivante : 03 XX XX XX XX ou 03.XX.XX.XX.XX','required'=>false])
            ->add('telephonePortable',TelType::class, ['label'=>'Téléphone Portable','help'=>'Le format du téléphone portable est la suivante : 06 XX XX XX XX ou 06.XX.XX.XX.XX','required'=>false])
            ->add('emailContact',EmailType::class, ['label'=>'Email de l\'entreprise','required'=>false])
            ->add('horaires', TextareaType::class, ['label'=>'Horaires de l\'entreprise','required'=>false])
            ->add('submit', SubmitType::class, ['attr'=>['class'=>'btn monpro btn-lg btn-primary waves-effect waves-light'],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entreprise::class,
        ]);
    }
}