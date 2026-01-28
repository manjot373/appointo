<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\Department;
use App\Entity\Staff;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName')
            ->add('customer_contact')
            ->add('status', ChoiceType::class,  [
                'choices' => ['Confirmed' => 'confirmed', 'Unconfirmed' => 'unconfirmed'],
                'placeholder' => 'Select an option'
            ])
        ;

        //customer_name	customer_contact	created_at	status	assigned_user_id	slot_id	business_id
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}
