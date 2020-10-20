<?php


namespace SallePW\Model;


class User
{


    private $id;
    private $username;
    private $lastname;
    private $email;
    private $password;
    private $name;
    private $birthdate;
    private $phone;
    private $image_dir;

    /**
     * @param mixed $image_dir
     */
    public function setImageDir($image_dir): void
    {
        $this->image_dir = $image_dir;
    }

    /**
     * User constructor.
     * @param $id
     * @param $username
     * @param $email
     * @param $password
     * @param $name
     * @param $birthdate
     * @param $phone
     * @param $image_dir
     */

    public function __construct($id, $name, $lastname, $email, $username, $password, $phone, $bday, $image_dir)
    {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->birthdate = $bday;
        $this->phone = $phone;
        $this->image_dir = $image_dir;
        $this->lastname =$lastname;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param mixed $birthdate
     */
    public function setBirthdate($birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getImageDir()
    {
        return $this->image_dir;
    }

    public function getAttributes(){
        return get_object_vars($this);
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }





}