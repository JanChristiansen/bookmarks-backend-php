<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Interfaces\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var User $user */
        $user = $options['user'];

        $builder->add('name', TextType::class);
        $builder->add(
            'parent',
            EntityType::class,
            [
                'class' => Category::class,
                'query_builder' => function (CategoryRepository $repository) use ($user) {
                    return $repository->getCategoriesForUserQueryBuilder($user);
                },
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
            ]
        );
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['user']);
        $resolver->setAllowedTypes('user', User::class);
        $resolver->setDefaults(array('data_class' => 'AppBundle\Entity\Category'));
    }
}
