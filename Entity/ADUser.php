<?php declare(strict_types = 1);
/*
 * This file is part of the GublerADSearchBundle
 *
 * (c) Daryl Gubler <daryl@dev88.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gubler\ADSearchBundle\Entity;

/**
 * Class ADUser
 */
class ADUser
{
    /**
     * Username - usually `samaccountname`
     *
     * @var string
     **/
    protected $username;

    /**
     * User's domain
     *
     * @var string
     **/
    protected $domain;

    /**
     * User's full name - usually `cn` (common name)
     *
     * @var string
     **/
    protected $name;

    /**
     * Job title - usually `title`
     *
     * @var string
     **/
    protected $title;

    /**
     * Office Location - usually `physicaldeliveryofficename`
     *
     * @var string
     **/
    protected $office;

    /**
     * Phone number - usually `telephonenumber`
     *
     * @var string
     **/
    protected $phone;

    /**
     * Email address - usually `mail`
     *
     * @var string
     **/
    protected $email;

    /**
     * Does AD User have account in App, defaults to false
     *
     * @var bool
     **/
    protected $account;

    /**
     * Is AD User account in App active, defaults to false
     *
     * @var bool
     **/
    protected $active;

    /**
     * Sets initial properties
     *
     * @param string $username
     */
    public function __construct($username)
    {
        $this->username = $username;
        $this->account = false;
        $this->active = false;
    }

    /**
     * Get username
     *
     * @return string
     **/
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param  string $username
     *
     * @return ADUser
     **/
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get domain
     *
     * @return string
     **/
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set domain
     *
     * @param  string $domain
     *
     * @return ADUser
     **/
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     **/
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param  string $name
     *
     * @return ADUser
     **/
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     **/
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param  string $title
     *
     * @return ADUser
     **/
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get office
     *
     * @return string
     **/
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set office
     *
     * @param  string $office
     *
     * @return ADUser
     **/
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     **/
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set phone
     *
     * @param  string $phone
     *
     * @return ADUser
     **/
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     **/
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param  string $email
     *
     * @return ADUser
     **/
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get hasAccount
     *
     * @return bool
     **/
    public function hasAccount()
    {
        return $this->account;
    }

    /**
     * Set hasAccount
     *
     * @param  bool $account
     *
     * @return ADUser
     **/
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     **/
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Set isActive
     *
     * @param  bool $active
     *
     * @return ADUser
     **/
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Return properties as array for JSON conversion
     *
     * @return array
     **/
    public function asArray()
    {
        $return = array(
            'username' => $this->username,
            'domain' => $this->domain,
            'name' => $this->name,
            'title' => $this->title,
            'office' => $this->office,
            'phone' => $this->phone,
            'email' => $this->email,
            'hasAccount' => $this->account,
            'isActive' => $this->active,
        );

        return $return;
    }
}
