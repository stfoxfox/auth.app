<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * класс формы авторизации
 */
class LoginForm extends AbstractType
{
    /**
     * @var TranslationInterface
     */
    private $translator;

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
        $builder->setAction('/authenticate')->setMethod('POST');

        $builder->add('email', EmailType::class, ['mapped' => false, 'required' => true, 'label' => ' ',
            'attr' => [
                'placeholder' => 'Email',
                'class' => "form-control",
                'autocomplete' => "false",
            ]]);

        $builder->add('password', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => ' ',
            'attr' => [
                'placeholder' => 'Password',
                'class' => "form-control",
                'autocomplete' => "false",
            ]]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Enter',
            'attr' => [
                'class' => "login-submit",
            ],
        ]);
    }

}
