<?php

namespace AppBundle\Entity;

use AppBundle\Validator\Constraints as FeedAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(
 *     name="feed",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="feed_slug_unique", columns={"slug"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeedRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Feed
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=191)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(name="link", type="string")
     * @Assert\NotBlank()
     * @Assert\Url()
     * @FeedAssert\ConstraintRss
     */
    protected $link;

    /**
     * @ORM\Column(name="host", type="string")
     * @Assert\NotBlank()
     */
    protected $host;

    /**
     * @ORM\Column(name="logo", type="string", nullable=true)
     */
    protected $logo;

    /**
     * @ORM\Column(name="color", type="string", nullable=true)
     */
    protected $color;

    /**
     * @ORM\Column(name="parser", type="string")
     */
    protected $parser;

    /**
     * @ORM\Column(name="formatter", type="string")
     */
    protected $formatter;

    /**
     * @ORM\Column(name="nb_items", type="integer")
     */
    protected $nbItems = 0;

    /**
     * @Gedmo\Slug(fields={"name"}, updatable=false, unique=true)
     * @ORM\Column(name="slug", type="string", length=191)
     */
    protected $slug;

    /**
     * @ORM\Column(name="is_private", type="boolean")
     */
    protected $isPrivate = false;

    /**
     * @ORM\Column(name="sort_by", type="string")
     */
    protected $sortBy;

    /**
     * @ORM\Column(name="last_item_cached_at", type="datetime", nullable=true)
     */
    protected $lastItemCachedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="feed", cascade={"persist", "remove"})
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Log", mappedBy="feed", cascade={"persist", "remove"})
     */
    protected $logs;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set link.
     *
     * @param string $link
     *
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link.
     *
     * @return string $link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set createdAt.
     *
     * @param string|\DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return string|\DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param string|\DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return string|\DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string $slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set parser.
     *
     * @param string $parser
     *
     * @return self
     */
    public function setParser($parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Get parser.
     *
     * @return string $parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set formatter.
     *
     * @param string $formatter
     *
     * @return self
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * Get formatter.
     *
     * @return string $formatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Set host.
     *
     * @param string $host
     *
     * @return self
     */
    public function setHost($host)
    {
        $parsedHost = parse_url($host, PHP_URL_HOST);

        // be sure that host doesn't have http
        if ($parsedHost) {
            $host = $parsedHost;
        }

        $this->host = $host;

        return $this;
    }

    /**
     * Get host.
     *
     * @return string $host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set isPrivate.
     *
     * @param bool $isPrivate
     *
     * @return self
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * Get isPrivate.
     *
     * @return bool $isPrivate
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Set sortBy.
     *
     * @param string $sortBy
     *
     * @return self
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * Get sortBy.
     *
     * @return string $sortBy
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * Set lastItemCachedAt.
     *
     * @param \DateTime $lastItemCachedAt
     *
     * @return self
     */
    public function setLastItemCachedAt($lastItemCachedAt)
    {
        $this->lastItemCachedAt = $lastItemCachedAt;

        return $this;
    }

    /**
     * Get lastItemCachedAt.
     *
     * @return \DateTime $lastItemCachedAt
     */
    public function getLastItemCachedAt()
    {
        return $this->lastItemCachedAt;
    }

    /**
     * Set nbItems.
     *
     * @param int $nbItems
     *
     * @return self
     */
    public function setNbItems($nbItems)
    {
        $this->nbItems = $nbItems;

        return $this;
    }

    /**
     * Get nbItems.
     *
     * @return int $nbItems
     */
    public function getNbItems()
    {
        return $this->nbItems;
    }

    /**
     * Set logo.
     *
     * @param string $logo
     *
     * @return self
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo.
     *
     * @return string $logo
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set color.
     *
     * @param string $color
     *
     * @return self
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string $color
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function timestamps()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }

        $this->updatedAt = new \DateTime();
    }

    /**
     * Return items.
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Return logs.
     *
     * @return ArrayCollection
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
