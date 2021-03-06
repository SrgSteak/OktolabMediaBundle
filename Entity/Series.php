<?php

namespace Oktolab\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

interface SeriesMergerInterface
{
    public function merge(Series $series);
}

/**
 * Series
 *
 * @ORM\Table()
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 * @UniqueEntity("webtitle")
 */
class Series implements SeriesMergerInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\ReadOnly
     */
    private $id;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"search", "oktolab"})
     * @Assert\Length(max=255, min=1)
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\Groups({"oktolab"})
    * @Assert\Length(max=255, min=1)
    * @ORM\Column(name="webtitle", type="string", length=255, unique=true)
    */
    private $webtitle;

    /**
     * @var string
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Groups({"search", "oktolab"})
     * @Assert\Length(max=650)
     * @ORM\Column(name="description", type="string", length=700)
     */
    private $description;

    /**
     * @var boolean
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\Groups({"search", "oktolab"})
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @JMS\Expose
     * @JMS\Type("DateTime")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("uniqID")
     * @JMS\Groups({"search", "oktolab"})
     * @var string
     * @ORM\Column(name="uniqID", type="string", length=13, unique=true)
     */
    private $uniqID;

    /**
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\Groups({"oktolab", "series_posterframe"})
    * @ORM\OneToOne(targetEntity="Bprs\AssetBundle\Entity\AssetInterface", fetch="EAGER", cascade={"remove"})
    * @ORM\JoinColumn(name="posterframe_id", referencedColumnName="id")
    * @Assert\NotNull()
    */
    private $posterframe;

    /**
     * @ORM\ManyToOne(targetEntity="Bprs\AppLinkBundle\Entity\Keychain")
     * @ORM\JoinColumn(name="keychain_id", referencedColumnName="id", nullable=true)
     */
    protected $keychain;

    public function __construct() {
        $this->uniqID = uniqid();
        $this->isActive = true;
    }

    public function __toString() {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Series
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Series
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Series
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Series
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Series
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set uniqID
     *
     * @param string $uniqID
     * @return Episode
     */
    public function setUniqID($uniqID)
    {
        $this->uniqID = $uniqID;

        return $this;
    }

    /**
     * Get uniqID
     *
     * @return string
     */
    public function getUniqID()
    {
        return $this->uniqID;
    }

    /**
     * Set webtitle
     *
     * @param string $webtitle
     * @return Series
     */
    public function setWebtitle($webtitle)
    {
        $this->webtitle = $webtitle;

        return $this;
    }

    /**
     * Get webtitle
     *
     * @return string
     */
    public function getWebtitle()
    {
        return $this->webtitle;
    }

    /**
     * Set posterframe
     *
     * @param \Oktolab\MediaBundle\Entity\Asset $posterframe
     * @return Episode
     */
    public function setPosterframe($posterframe = null)
    {
        $this->posterframe = $posterframe;

        return $this;
    }

    /**
     * Get posterframe
     *
     * @return \Oktolab\MediaBundle\Entity\Asset
     */
    public function getPosterframe()
    {
        return $this->posterframe;
    }

    public function getKeychain()
    {
        return $this->keychain;
    }

    public function setKeychain($keychain)
    {
        $this->keychain = $keychain;
        return $this;
    }

    public function merge(Series $series)
    {
        $this->name = $series->getName();
        $this->description = $series->getDescription();
        $this->uniqID = $series->getUniqID();
        $this->webtitle = $series->getWebtitle();
        $this->isActive = $series->getIsActive();
    }
}
