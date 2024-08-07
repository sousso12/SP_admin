<?php

namespace App\Form;

use App\Entity\Portefeuille;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;

class PortefeuilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Nom complet',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(),
                    new Email([
                        'message' => 'L\'email "{{ value }}" n\'est pas valide.',
                    ]),
                    new Regex([
                        'pattern' => '/@gmail\.com$/',
                        'message' => 'L\'email doit se terminer par @gmail.com.',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'constraints' => [
                    new NotBlank(),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit avoir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Numéro de téléphone',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'Le numéro de téléphone ne doit contenir que des chiffres.',
                        'normalizer' => function ($value) {
                            return preg_replace('/[^\d]/', '', $value); // Normalizer pour retirer les caractères non numériques
                        },
                    ]),
                    new Assert\Length([
                        'min' => 8,
                        'max' => 15,
                        'minMessage' => 'Le numéro de téléphone doit avoir au moins {{ limit }} chiffres.',
                        'maxMessage' => 'Le numéro de téléphone ne doit pas dépasser {{ limit }} chiffres.',
                    ]),
                ],
            ])
            ->add('downloadURL', FileType::class, [
                'label' => 'Photo d\'identité',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control-file'],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG).',
                    ]),
                ],
            ])
            ->add('userType', TextType::class, [
                'label' => 'Type d\'utilisateur',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('solde', NumberType::class, [
                'label' => 'Solde actuel',
                'constraints' => [
                    new PositiveOrZero(),
                ],
            ]);

        // Écouteur d'événement pour la soumission du formulaire
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $portefeuille = $event->getData();
            $form = $event->getForm();

            // Validation manuelle pour les champs requis
            if (empty($portefeuille->getFullName()) || empty($portefeuille->getEmail()) || empty($portefeuille->getPassword()) || empty($portefeuille->getPhoneNumber())) {
                $form->addError(new FormError('Veuillez remplir tous les champs obligatoires.'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Portefeuille::class,
        ]);
    }
}
