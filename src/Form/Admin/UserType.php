<?php

namespace App\Form\Admin;

use App\Entity\Admin\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $rolesReference = $this->getRolesReference($options);
        $builder
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'choices' => array_keys($rolesReference),
                'choice_label' => fn($choice, $key, $value) => $value,
                'choice_attr' => fn($choice, $key, $value) => $this->getChoiceAttributes($rolesReference, $value),
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $staff = $event->getData();
            $form = $event->getForm();
            if (empty($staff->getId())) {
                $form->add('username');
                $form->add('plainPassword', RepeatedType::class, array(
                    'constraints' => array(new NotBlank(), new Length(array('min' => '6'))),
                    'mapped' => false,
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'New Password'),
                    'second_options' => array('label' => 'Confirm Password'),
                ));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'security_roles' => [],
        ]);
    }

    private function getChoiceAttributes(array $rolesReference, string $value): array
    {
        return [
            'data-controller' => 'dom-element',
            'data-action' => 'dom-element#bind',
            'data-dom-element-bind-specifications-param' => json_encode([
                [
                    'destinationsTarget' => "[data-role-parent*={$value}]",
                    'action' => 'setPropertyValue',
                    'descriptor' => ['property' => 'checked', 'value' => 'false'],
                    'condition' => '$element.checked',
                ],
                [
                    'destinationsTarget' => "[data-role-parent*={$value}]",
                    'action' => 'setPropertyValue',
                    'descriptor' => ['property' => 'disabled', 'value' => '$element.checked'],
                ],
            ]),
            'data-role-parent' => implode(' ', $rolesReference[$value]),
            'style' => 'margin-right: 8px; margin-left: ' . (24 * count($rolesReference[$value])) . 'px',
        ];
    }

    private function getRolesReference(array $options): array
    {
        $rolesByParent = [];
        $rolesByParents = [];
        foreach ($options['security_roles'] as $role => $roleChildren) {
            if (!isset($rolesByParent[$role])) {
                $rolesByParent[$role] = null;
            }
            foreach ($roleChildren as $roleChild) {
                if (!isset($rolesByParent[$roleChild])) {
                    $rolesByParent[$roleChild] = $role;
                }
            }
        }
        foreach ($rolesByParent as $role => $roleParent) {
            $rolesByParents[$role] = $this->getParentRoles($rolesByParent, $role);
        }
        return $rolesByParents;
    }

    private function getParentRoles(array $roles, ?string $name): array
    {
        $parentRoles = [];
        $currentName = $name;
        while (!empty($roles[$currentName])) {
            $parentRoles[] = $roles[$currentName];
            $currentName = $roles[$currentName];
        }
        return $parentRoles;
    }
}
