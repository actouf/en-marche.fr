<?php

namespace AppBundle\Admin;

use AppBundle\Entity\TurnkeyProject;
use AppBundle\Form\PurifiedTextareaType;
use AppBundle\Repository\TurnkeyProjectRepository;
use League\Flysystem\Filesystem;
use League\Glide\Server;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TurnkeyProjectAdmin extends AbstractAdmin
{
    /**
     * @var Filesystem
     */
    private $storage;

    /**
     * @var Server
     */
    private $glide;

    /**
     * @var TurnkeyProjectRepository
     */
    private $turnkeyProjectRepository;

    public function __construct(string $code, string $class, string $baseControllerName, TurnkeyProjectRepository $turnkeyProjectRepository)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->turnkeyProjectRepository = $turnkeyProjectRepository;
    }

    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 32,
        '_sort_order' => 'DESC',
        '_sort_by' => 'position',
    ];

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Projet clé en main', ['class' => 'col-md-7'])
                ->add('name', null, [
                    'label' => 'Nom',
                ])
                ->add('subtitle', null, [
                    'label' => 'Sous-titre',
                ])
                ->add('category', null, [
                    'label' => 'Catégorie',
                ])
                ->add('problemDescription', null, [
                    'label' => 'Description du problème',
                ])
                ->add('proposedSolution', PurifiedTextareaType::class, [
                    'label' => 'Solution du problème',
                ])
                ->add('requiredMeans', null, [
                    'label' => 'Feuille de route',
                ])
                ->add('isPinned', null, [
                    'label' => 'Epinglé',
                ])
                ->add('isPutForward', null, [
                    'label' => 'Mis en avant',
                ])
                ->add('position', null, [
                    'label' => 'Position',
                ])
            ->end()
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Projet clé en main', ['class' => 'col-md-7'])
                ->add('name', null, [
                    'label' => 'Nom',
                    'format_title_case' => true,
                ])
                ->add('subtitle', null, [
                    'label' => 'Sous-titre',
                    'format_title_case' => true,
                ])
                ->add('category', null, [
                    'required' => true,
                    'label' => 'Catégorie',
                ])
                ->add('problemDescription', null, [
                    'required' => true,
                    'label' => 'Description du problème',
                ])
                ->add('proposedSolution', PurifiedTextareaType::class, [
                    'label' => 'Solution du problème',
                    'filter_emojis' => true,
                    'purifier_type' => 'enrich_content',
                    'attr' => ['class' => 'ck-editor'],
                ])
                ->add('requiredMeans', null, [
                    'required' => true,
                    'label' => 'Feuille de route',
                    'filter_emojis' => true,
                ])
                ->add('image', FileType::class, [
                    'label' => 'Ajoutez une image d\'illustration',
                    'required' => false,
                ])
                ->add('isPinned', null, [
                    'label' => 'Epingler ce projet sur la page d\'accueil des Projets citoyens',
                ])
                ->add('isPutForward', null, [
                    'label' => 'Projet clé en main à mettre en avant',
                ])
                ->add('position', null, [
                    'label' => 'Position',
                ])
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, [
                'label' => 'ID',
            ])
            ->add('name', null, [
                'label' => 'Nom',
                'show_filter' => true,
            ])
            ->add('category', null, [
                'label' => 'Catégorie',
            ])
            ->add('isPinned', null, [
                'label' => 'Epinglé',
            ])
            ->add('isPutForward', null, [
                'label' => 'Mis en avant',
            ])
            ->add('position', null, [
                'label' => 'Position',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'template' => 'admin/turnkey_project/list_name.html.twig',
            ])
            ->add('category', null, [
                'label' => 'Catégorie',
            ])
            ->add('_image', null, [
                'label' => 'Miniature d\'image',
                'virtual_field' => true,
                'template' => 'admin/list/list_image_miniature.html.twig',
            ])
            ->add('isPinned', null, [
                'label' => 'Epinglé',
            ])
            ->add('isPutForward', null, [
                'label' => 'Mis en avant',
            ])
            ->add('position', null, [
                'label' => 'Position',
            ])
            ->add('_action', null, [
                'virtual_field' => true,
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $count = $this->turnkeyProjectRepository->countProjects();
        $instance->setPosition(++$count);

        return $instance;
    }

    /**
     * @param TurnkeyProject $trunkeyProject
     */
    public function preRemove($trunkeyProject)
    {
        parent::preRemove($trunkeyProject);

        try {
            if (null !== $trunkeyProject->getImageName()) {
                $path = $trunkeyProject->getImagePath();

                // Deletes the file
                $this->storage->delete($path);

                // Clears the cache file
                $this->glide->deleteCache($path);

                $trunkeyProject->setImageName(null);
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * @param TurnkeyProject $trunkeyProject
     */
    public function prePersist($trunkeyProject)
    {
        parent::prePersist($trunkeyProject);

        $this->saveImage($trunkeyProject);
    }

    /**
     * @param TurnkeyProject $trunkeyProject
     */
    public function preUpdate($trunkeyProject)
    {
        parent::preUpdate($trunkeyProject);

        if ($trunkeyProject->getImage()) {
            $this->saveImage($trunkeyProject);
        }
    }

//    public function postPersist($object)
//    {
//        foreach ($collection as $item) {
//            if (!$item instanceof SummaryItemPositionableInterface) {
//                throw new \InvalidArgumentException(sprintf('Expected instance of "%s", got "%s".', SummaryItemPositionableInterface::class, is_object($item) ? get_class($item) : gettype($item)));
//            }
//            if ($newPosition <= ($order = $item->getDisplayOrder())) {
//                $item->setDisplayOrder(++$order);
//            }
//        }
//    }
//
//    public function postUpdate($object)
//    {
//    }
//
//    public function postRemove($object)
//    {
//    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if (null === $object->getId()) {
            $count = $this->turnkeyProjectRepository->countProjects();

            $errorElement
                ->with('position')
                    ->assertGreaterThan(['value' => $count])
                ->end()
            ;
        }
    }

    public function setStorage(Filesystem $storage)
    {
        $this->storage = $storage;
    }

    public function setGlide(Server $glide)
    {
        $this->glide = $glide;
    }

    private function saveImage(TurnkeyProject $turnkeyProject): void
    {
        if (!$turnkeyProject->getImage() instanceof UploadedFile) {
            throw new \RuntimeException(sprintf('The image must be an instance of %s', UploadedFile::class));
        }

        // Clears the old image if needed
        if (null !== $turnkeyProject->getImageName() && $oldImagePath = $turnkeyProject->getImagePath()) {
            $this->storage->delete($oldImagePath);
        }

        $turnkeyProject->setImageName($turnkeyProject->getImage());
        $path = $turnkeyProject->getImagePath();

        // Uploads the file : creates or updates if exists
        $this->storage->put($path, file_get_contents($turnkeyProject->getImage()->getPathname()));

        // Clears the cache file
        $this->glide->deleteCache($path);
    }
}
