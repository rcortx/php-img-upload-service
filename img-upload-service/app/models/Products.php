<?php

class Products extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=20, nullable=false)
     */
    protected $id;

    /**
     *
     * @var string
     * @Column(type="string", length=50, nullable=false)
     */
    protected $name;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=true)
     */
    protected $image;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=true)
     */
    protected $image_256;

    /**
     *
     * @var string
     * @Column(type="string", length=150, nullable=true)
     */
    protected $image_512;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    protected $price;

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Method to set the value of field image
     *
     * @param string $image
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Method to set the value of field image_256
     *
     * @param string $image_256
     * @return $this
     */
    public function setImage256($image_256)
    {
        $this->image_256 = $image_256;

        return $this;
    }

    /**
     * Method to set the value of field image_512
     *
     * @param string $image_512
     * @return $this
     */
    public function setImage512($image_512)
    {
        $this->image_512 = $image_512;

        return $this;
    }

    /**
     * Method to set the value of field price
     *
     * @param double $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the value of field image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Returns the value of field image_256
     *
     * @return string
     */
    public function getImage256()
    {
        return $this->image_256;
    }

    /**
     * Returns the value of field image_512
     *
     * @return string
     */
    public function getImage512()
    {
        return $this->image_512;
    }

    /**
     * Returns the value of field price
     *
     * @return double
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("test");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'products';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Products[]|Products
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Products
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
