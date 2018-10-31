<?php

namespace App\Entity;

use App\Controller\UploadImageAction;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @Vich\Uploadable()
 * @ApiResource(
 *    collectionOperations={
 *        "get",
 *        "post"={
 *            "method"="POST",
 *            "path"="/images",
 *            "controller"=UploadImageAction::class,
 *            "defaults"={"_api_receive"=false}
 *        }
 *     }
 * )
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="url")
     * @Assert\NotBlank()
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"get-blog-post-with-image"})
     */
    private $url;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file): void
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return '/images/' . $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}