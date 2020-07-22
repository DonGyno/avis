<?php


namespace App\Form;

use App\Entity\FicheProspection;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FicheProspectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomEntreprise',TextType::class, ['required'=>true,'label'=>'Nom de l\'entreprise'])
            ->add('raisonSocialeEntreprise',ChoiceType::class, ['required'=>true,'label'=>'Raison Sociale',
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
            ->add('codeApe',TextType::class, ['label'=>'Code APE','required'=>true])
            ->add('siretSiren',TextType::class, ['label'=>'SIREN/SIRET','required'=>true])
            ->add('rueEntreprise',TextType::class, ['label'=>'Rue','required'=>true])
            ->add('villeEntreprise',TextType::class, ['label'=>'Ville','required'=>true])
            ->add('codePostalEntreprise',TextType::class, ['label'=>'Code Postal','required'=>true])
            ->add('telephoneFixeEntreprise',TelType::class, ['label'=>'Téléphone Fixe','help'=>'Le format du téléphone fixe est la suivante : 03 XX XX XX XX ou 03.XX.XX.XX.XX','required'=>true])
            ->add('telephonePortableEntreprise',TelType::class, ['label'=>'Téléphone Portable','help'=>'Le format du téléphone portable est la suivante : 06 XX XX XX XX ou 06.XX.XX.XX.XX','required'=>true])
            ->add('emailEntreprise',EmailType::class, ['label'=>'Email de l\'entreprise','required'=>true])
            ->add('responsableFicheProspection', EntityType::class,
                [
                    'required'=>true,
                    'label'=>'Responsable de la fiche de prospection',
                    'class'=>User::class,
                    'choice_label'=>function(User $user){
                        return $user->getPrenom() . ' ' . $user->getNom();
                    }
                ])
            ->add('submit', SubmitType::class, ['attr'=>['class'=>'btn monpro btn-lg btn-primary waves-effect waves-light'],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FicheProspection::class,
        ]);
    }
}