<?php

namespace AppBundle\Entity\Mooc;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 *
 * @Algolia\Index(autoIndex=false)
 */
class Quizz extends BaseMoocElement
{
    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Url
     */
    private $typeformUrl;

    public function __construct(string $title = null, string $content = null, string $typeformUrl = null)
    {
        parent::__construct($title, $content);
        $this->typeformUrl = $typeformUrl;
    }

    public function getType(): string
    {
        return parent::ELEMENT_TYPE_QUIZ;
    }

    public function getTypeformUrl(): ?string
    {
        return $this->typeformUrl;
    }

    public function setTypeformUrl(string $typeformUrl): void
    {
        $this->typeformUrl = $typeformUrl;
    }
}
