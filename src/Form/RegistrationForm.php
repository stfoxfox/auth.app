<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * класс формы регистрации
 */
class RegistrationForm extends AbstractType
{
    /**
     * @var TranslationInterface
     */
    private $translator;

    /**
     * RegistrationForm constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction('/registration')
            ->setMethod('POST');

        $builder->add('name', TextType::class, ['mapped' => false, 'required' => true, 'label' => ' ',
            'attr' => [
                'placeholder' => 'First name',
                'id' => "UserName",
                'class' => "form-control",
                'autocomplete' => "false",
                'maxlength' => "1023",
            ]]);

        $builder->add('surname', TextType::class, ['mapped' => false, 'required' => true, 'label' => ' ',
            'attr' => [
                'placeholder' => 'Last name',
                'id' => "UserSurname",
                'class' => "form-control",
                'autocomplete' => "false",
                'maxlength' => "1023",
            ]]);

        $builder->add('email', EmailType::class, ['mapped' => false, 'required' => true, 'label' => ' ',
            'attr' => [
                'placeholder' => 'Email',
                'id' => "regMail",
                'class' => "form-control",
                'autocomplete' => "false",
                'maxlength' => "50",
            ]]);

        $builder->add('password', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => ' ',
            'attr' => [
                'placeholder' => 'Password',
                'id' => "regPasswd",
                'class' => "form-control",
                'autocomplete' => "false",
                'maxlength' => "50",
            ]]);

        $builder->add('submit', SubmitType::class, [
            'attr' => [
                'class' => "btn btn-primary btn-disable",
            ],
            'label' => 'Next',
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
