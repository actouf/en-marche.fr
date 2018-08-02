<?php

namespace AppBundle\Entity;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use AppBundle\Validator\WysiwygLength as AssertWysiwygLength;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This entity represents a turnkey project.
 *
 * @ORM\Table(
 *     name="turnkey_projects",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="turnkey_project_canonical_name_unique", columns="canonical_name"),
 *         @ORM\UniqueConstraint(name="turnkey_project_slug_unique", columns="slug")
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TurnkeyProjectRepository")
 *
 * @Algolia\Index(autoIndex=false)
 */
class TurnkeyProject
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Length(min=2, max=60)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $canonicalName;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Gedmo\Slug(fields={"canonicalName"})
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column
     *
     * @Assert\NotBlank
     * @Assert\Length(min=5, max=80)
     */
    private $subtitle;

    /**
     * @var CitizenProjectCategory
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CitizenProjectCategory")
     *
     * @Assert\NotNull
     */
    private $category;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=500)
     */
    private $problemDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\NotBlank
     * @AssertWysiwygLength(max=800)
     */
    private $proposedSolution;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=800)
     */
    private $requiredMeans;

    private $urlImage = '#';

    /**
     * @var UploadedFile|null
     *
     * @Assert\Image(
     *     maxSize="5M",
     *     mimeTypes={"image/jpeg", "image/png"},
     *     minWidth="1200",
     *     minHeight="675",
     *     minRatio=1.77,
     * )
     */
    private $image;

    /**
     * @var string|null
     *
     * @ORM\Column(length=255, nullable=true)
     */
    private $imageName;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $isPinned = false;

    /**
     * @ORM\Column(type="boolean", options={"default": 0})
     */
    private $isPutForward = false;

    /**
     * @var int|null
     *
     * @ORM\Column(type="smallint", options={"default": 1})
     *
     * @Assert\NotBlank
     */
    private $position = 1;

    public function __construct(
        string $name = '',
        string $subtitle = '',
        CitizenProjectCategory $category = null,
        string $problemDescription = '',
        string $proposedSolution = '',
        string $requiredMeans = '',
        bool $isPinned = false,
        bool $isPutForward = false,
        int $position = 1,
        string $slug = null
    ) {
        $this->setName($name);
        $this->slug = $slug;
        $this->subtitle = $subtitle;
        $this->category = $category;
        $this->problemDescription = $problemDescription;
        $this->proposedSolution = $proposedSolution;
        $this->requiredMeans = $requiredMeans;
        $this->isPinned = $isPinned;
        $this->isPutForward = $isPutForward;
        $this->position = $position;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
        $this->canonicalName = static::canonicalize($name);
    }

    public static function canonicalize(string $name): string
    {
        return mb_strtolower($name);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setCategory(CitizenProjectCategory $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): ?CitizenProjectCategory
    {
        return $this->category;
    }

    public function setSubtitle(string $subtitle)
    {
        $this->subtitle = $subtitle;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setProblemDescription(?string $problemDescription): void
    {
        $this->problemDescription = $problemDescription;
    }

    public function getProblemDescription(): ?string
    {
        return $this->problemDescription;
    }

    public function setProposedSolution(?string $proposedSolution): void
    {
        $this->proposedSolution = $proposedSolution;
    }

    public function getProposedSolution(): ?string
    {
        return $this->proposedSolution;
    }

    public function setRequiredMeans(?string $requiredMeans): void
    {
        $this->requiredMeans = $requiredMeans;
    }

    public function getRequiredMeans(): ?string
    {
        return $this->requiredMeans;
    }

    public function getImagePath(): string
    {
        return sprintf('images/turnkey_projects/%s', $this->getImageName());
    }

    public function getAssetImagePath(): string
    {
        return sprintf('%s/%s', 'assets', $this->getImagePath());
    }

    public function setUrlimage(string $url): void
    {
        $this->urlImage = $url;
    }

    public function getUrlImage(): string
    {
        return $this->urlImage;
    }

    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

    public function setImage(?UploadedFile $image): void
    {
        $this->image = $image;
    }

    public function setImageName(?UploadedFile $image): void
    {
        $this->imageName = null === $image ? null :
            sprintf('%s.%s',
                md5(sprintf('%s@%s', $this->getId(), $image->getClientOriginalName())),
                $image->getClientOriginalExtension()
            )
        ;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): void
    {
        $this->isPinned = $isPinned;
    }

    public function isPutForward(): ?bool
    {
        return $this->isPutForward;
    }

    public function setIsPutForward(bool $isPutForward): void
    {
        $this->isPutForward = $isPutForward;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function update(
        string $name,
        string $subtitle,
        CitizenProjectCategory $category,
        string $problemDescription,
        string $proposedSolution,
        string $requiredMeans,
        bool $isPinned,
        bool $isPutForward,
        int $position,
        ?UploadedFile $image
    ): void {
        $this->setName($name);
        $this->setSubtitle($subtitle);
        $this->setCategory($category);
        $this->setProblemDescription($problemDescription);
        $this->setProposedSolution($proposedSolution);
        $this->setRequiredMeans($requiredMeans);
        $this->setIsPinned($isPinned);
        $this->setIsPutForward($isPutForward);
        $this->setPosition($position);

        if ($image instanceof UploadedFile) {
            $this->setImage($image);
        }
    }

    public function __toString()
    {
        return $this->name ?: '';
    }
}
