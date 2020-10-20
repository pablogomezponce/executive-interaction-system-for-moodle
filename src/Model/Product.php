<?php


namespace SallePW\Model;


class Product
{
    private $title;
    private $id;
    private $description;
    private $price;
    private $product_image_dir;
    private $category;
    private $isActive;

    /**
     * @param mixed $product_image_dir
     */
    public function setProductImageDir($product_image_dir): void
    {
        $this->product_image_dir = $product_image_dir;
    }


    /**
     * Product constructor.
     * @param $title
     * @param $description
     * @param $price
     * @param $product_image_dir
     * @param $category
     * @param $isActive
     */
    public function __construct($title, $description, $price, $product_image_dir, $category, $isActive)
    {
        $this->title = $title;
        $this->description = $description;
        $this->price = $price;
        $this->product_image_dir = $product_image_dir;
        $this->category = $category;
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getProductImageDir()
    {
        return $this->product_image_dir;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }




}