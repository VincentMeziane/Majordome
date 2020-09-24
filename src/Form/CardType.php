<?php

namespace App\Form;

use App\Entity\Card;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Ajouter des contraintes en fonction de la méthode (PUT pour la modif et POST pour la création)
        // $isEdit = $options['method'] === 'PUT';
        // $imageFileConstraints = [];
        // if($isEdit)
        // {
        //     $imageFileConstraints = ['maxSize' => "1M"];
        // }

        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => "Ajouter une image ( JPG ou PNG )",
                'required' => false,
                'allow_delete' => true,
                'delete_label' => "Supprimer l'image",
                'download_label' => 'Télécharger',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'imagine_pattern' => "square_thumbnail_small"
                // 'constraints' => [new Image($imageFileConstraints)]
            ])
            ->add('title')
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Card::class,
        ]);
    }
}
