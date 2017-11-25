    <?php

    namespace AppBundle\Form;

    use AppBundle\Entity\Genus;
    use AppBundle\Repository\SubFamilyRepository;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use \Symfony\Component\Form\Extension\Core\Type\ChoiceType;

    /**
     * Created by PhpStorm.
     * User: constantin.andreescu
     * Date: 7/23/2017
     * Time: 9:10 AM
     */
    class GenusFormType extends \Symfony\Component\Form\AbstractType
    {
        public function buildForm(\Symfony\Component\Form\FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('name')
                ->add('speciesCount')
                ->add('funFact')
                ->add('subFamily', null, [
                    'placeholder' => 'Choose a subfamily',
                    'query_builder' => function(SubFamilyRepository $repo) {
                        return $repo->findAllAlphabeticalReversed();
                    }
                ])
                ->add('isPublished', ChoiceType::class, [
                    'choices' => [
                    'Yes' => true,
                    'No' => false]
                ])
                ->add('firstDiscoveredAt', null, [
                    'widget' => 'single_text'
                ]);
        }
        public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
        {
            $resolver->setDefaults([
                'data_class' => Genus::class
            ]);
        }
    }