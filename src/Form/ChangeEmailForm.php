<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Translation\TranslatorInterface;


/**
 * класс формы изменения email 
 */
class ChangeEmailForm extends AbstractType
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
        $builder->setAction('/send_email_change_email')->setMethod('POST');

        $builder->add('email', EmailType::class, ['mapped' => false, 'required' => true, 'label' => 'New email',
            'attr' => [
                'class' => "form-control",
                'autocomplete' => "false",
                'id' => "UserEmail"
            ]]);

        $builder->add('submit', SubmitType::class, [
            'label' => 'Save',
            'attr' => [
                'class' => "btn btn-default save-button",
            ]
        ]);
    }

}


