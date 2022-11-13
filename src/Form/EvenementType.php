<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {    $builder
        ->add('titre')
        ->add('lieu', ChoiceType::class, [
            'choices' => [
                'Mahdia' => 'Mahdia',
                'Sousse' => 'Sousse',
                'Monastir' => 'Monastir',
                'Tunis' => 'Tunis',
                'Ariana' => 'Ariana',
                'Ben Arous' => 'Ben Arous',
                'Mannouba' => 'Mannouba',
                'Bizerte' => 'Bizerte',
                'Nabeul' => 'Nabeul',
                'Béja' => 'Béja',
                'Jendouba' => 'Jendouba',
                'Zaghouan' => 'Zaghouan',
                'Siliana' => 'Siliana',
                'Kef' => 'Kef',
                'Kasserine' => 'Kasserine',
                'Sidi Bouzid' => 'Sidi Bouzid',
                'Kairouan' => 'Kairouan',
                'Gafsa' => 'Gafsa',
                'Sfax' => 'Sfax',
                'Gabès' => 'Gabès',
                'Médenine' => 'Médenine',
                'Tozeur' => 'Tozeur',
                'Kebili' => 'Kebili',
                'Tataouine' => 'Tataouine',


            ],
            'label' => 'Pick your city :'
        ])
        ->add('description')
        ->add('image', FileType::class ,array('data_class'=> null,'label' => 'Choose an Image for your Event'))
        ->add('type' ,ChoiceType::class,['choices' =>$this->getChoises()
        ] )
        ->add('date')
        ->add('dateend')
        ->add('prix')

        ->add('nbrmaxpart')
    ;
}

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Evenement::class,
    ]);
}
private function getChoises()
{$choices=Evenement::TYPE;
    $output=[];
    foreach ($choices as $k => $v){
        $output[$v]=$k;
    }
    return $output;



}
}
