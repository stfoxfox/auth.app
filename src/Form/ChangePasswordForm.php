<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * класс формы изменения пароля
 */
class ChangePasswordForm extends AbstractType
{
    /**
     * @var TranslatorInterface
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
        $builder->setAction('/change_password')->setMethod('POST');

        $builder->add('password', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => 'New password',
            'attr' => [
                'class' => "form-control",
                'autocomplete' => "false",
                'id' => "UserPassword",
            ]]);

        $builder->add('confirm_password', PasswordType::class, ['mapped' => false, 'required' => true, 'label' => 'Confirm password',
            'attr' => [
                'class' => "form-control",
                'autocomplete' => "false",
                'id' => "UserConfirmPassword",
            ]]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Save',
            'attr' => [
                'class' => "btn btn-default save-button disabled",
                'disabled' => true,
            ],
        ]);
    }

}
