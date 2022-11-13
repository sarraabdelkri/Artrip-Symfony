<?php

namespace App\Form;

use App\Entity\Reservationeven;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationevenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nompartici')
            ->add('type' ,ChoiceType::class,['choices' =>$this->getChoises()
            ] )

            ->add('evenement')
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
            ->add('idparticipon')
            ->add('nomevenement')
            ->add('date')


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservationeven::class,
        ]);
    }
    private function getChoises()
    {$choices=Reservationeven::TYPE;
        $output=[];
        foreach ($choices as $k => $v){
            $output[$v]=$k;
        }
        return $output;



    }
}
