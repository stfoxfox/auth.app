<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * класс формы восстановления пароля
 */
class RecoverPasswordForm extends AbstractType
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
        $builder->setAction('/send-email-change-password')->setMethod('POST');

        $builder->add('email', EmailType::class, ['mapped' => false, 'required' => true,
            'attr' => [
                'class' => "form-control",
                'autocomplete' => "false",
                'id' => "UserEmail",
                'placeholder' => 'Email',
                'maxlength' => "50",
            ],
        ]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Send',
            'attr' => [
                'class' => "btn btn-default pull-right",
                'id' => 'passwdReset',
            ],
        ]);

    }

}
