<?php

namespace XM\SecurityBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @UniqueEntity("username", message="xm_security.validation.user.email_unique", groups={"Registration", "Profile", "UserAdmin"}, errorPath="email")
 */
class User extends BaseUser
{
    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Registration", "Profile", "UserAdmin"})
     * @Assert\Length(min=2, max=180, groups={"Registration", "Profile", "UserAdmin"})
     * @Assert\Email(groups={"Registration", "Profile", "UserAdmin"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"Registration", "Profile", "UserAdmin"})
     * @Assert\Length(min=2, max=255, groups={"Registration", "Profile", "UserAdmin"})
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"Registration", "Profile", "UserAdmin"})
     * @Assert\Length(min=2, max=255, groups={"Registration", "Profile", "UserAdmin"})
     */
    protected $lastName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    protected $locked;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(name="registration_date", type="datetime", nullable=true)
     */
    protected $registrationDate;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    protected $loginCount = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AuthLog", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"datetime" = "DESC"})
     */
    protected $authLogs;


    public function __construct()
    {
        parent::__construct();

        $this->locked = false;

        $this->authLogs = new ArrayCollection();
    }

    /**
     * Returns true if the user is enabled and not locked.
     *
     * @return boolean
     */
    public function isActive()
    {
        if ($this->isEnabled() && !$this->isLocked()) {
            return true;
        }

        return false;
    }

    /**
     * Set email
     * Also sets the username to the email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $email = is_null($email) ? '' : $email;
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get the combination of the first and last names.
     * If both first & last are empty, an empty string will be returned.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getFirstName().' '.$this->getLastName();

        return trim($name);
    }

    /**
     * Returns the name if it's not empty.
     * Otherwise, returns the email address.
     *
     * @return string
     */
    public function displayName()
    {
        $name = $this->getName();

        if (!empty($name)) {
            return $name;
        } else {
            return $this->getEmail();
        }
    }

    /**
     * Get locked
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = (bool) $locked;

        return $this;
    }

    /**
     * Lock the user
     *
     * @return User
     */
    public function lock()
    {
        return $this->setLocked(true);
    }

    /**
     * Unlock the user
     *
     * @return User
     */
    public function unlock()
    {
        return $this->setLocked(false);
    }

    /**
     * True if the account is unlocked.
     * False if the account is locked.
     *
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return !$this->isLocked();
    }

    /**
     * Set registrationDate
     *
     * @param \DateTimeInterface $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTimeInterface
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set loginCount
     *
     * @param integer $loginCount
     * @return User
     */
    public function setLoginCount($loginCount)
    {
        $this->loginCount = $loginCount;

        return $this;
    }

    /**
     * Get loginCount
     *
     * @return integer
     */
    public function getLoginCount()
    {
        return $this->loginCount;
    }

    /**
     * Increment the login count
     *
     * @return integer
     */
    public function incrementLoginCount()
    {
        ++ $this->loginCount;

        return $this->loginCount;
    }

    /**
     * Get auth logs.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthLogs()
    {
        return $this->authLogs;
    }
}
