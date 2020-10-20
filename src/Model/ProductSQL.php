<?php


namespace SallePW\Model;


use PDO;

class ProductSQL implements ProductRepository
{
    private $address;
    private $dbname;
    private $userNameDB;
    private $passwordDB;

    /**
     * ProfileSQL constructor.
     */
    public function __construct($settings)
    {
        $this->address = $settings['address'];
        $this->dbname = $settings['dbname'];
        $this->userNameDB = $settings['userNameDB'];
        $this->passwordDB = $settings['passwordDB'];

    }


    /**
     * This function stores a Product in the table and returns it's id.
     * @param Product $product
     * @return string
     */
    public function save(Product $product)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "INSERT INTO Product (title,description,price,product_image_dir,category,isActive) VALUES (?,?,?,?,?,?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$product->getTitle(),$product->getDescription(),$product->getPrice(),$product->getProductImageDir(),$product->getCategory(),true]);

        $id = $db->lastInsertId();
        $name = $id ."/". $product->getProductImageDir();
        $sql = "UPDATE Product SET product_image_dir = '$name' WHERE id = $id";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        return $id;
    }

    /**
     * This function gets all the products' ID posted by a user
     * @param int $id
     * @return array
     */
    public function getAllProductsBy(int $id)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM UserProductOwn WHERE owner = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $variables  = $stmt->fetchAll();

        foreach ($variables as $variable)
        {
            $variable['owner'] = $id;
        }

        return $variables;
    }

    /**
     * This function returns all products posted by a user from it's ID
     * @param $id
     * @return array
     */
    public function getAllProductsByEmail($id)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM User WHERE email LIKE ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        $id = ($stmt->fetch())['id'];

        $sql = "SELECT product FROM UserProductOwn WHERE owner LIKE ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        $owned = $stmt->fetchAll();

        $products = [];

        foreach ($owned as $product)
        {
            $sql = "SELECT * FROM Product WHERE id = ? AND isActive = 1";
            $stmt = $db->prepare($sql);

            $stmt->execute([$product['product']]);
            $var = $stmt->fetch();
            if(!empty($var)){
            array_push($var, $_SESSION['profile']['email']);
            $var['owner'] = $_SESSION['profile']['email'];
            array_push($products, $var);
            }
        }

        return $products;
    }

    /**
     * This function gets the product associated with an id
     * @param int $id
     * @return mixed
     */
    public function get(int $id)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM Product WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $variables  = $stmt->fetchAll();
        if (!empty($_SESSION['profile'])){
        foreach ($variables as $variable)
        {
            array_push($variable, $_SESSION['profile']['id']);
            $variable['owner'] = $_SESSION['profile']['id'];

        }

        }

        return $variables[0];

    }

    /**
     * This function stores the ownership from a user to a product
     * @param int $productId
     * @param int $userId
     */
    public function associate(int $productId, int $userId)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "INSERT INTO UserProductOwn(owner, product,buyed) VALUES (?,?,false)";

        $stmt = $db->prepare($sql);
        $stmt->execute([$userId, $productId]);
    }

    public function getFavourites($id)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM Product WHERE id IN
                    (SELECT product FROM Favorites WHERE user = ?)
                    AND isActive = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }


    /**
     * This function shows if a user owns a product or not
     * @param $product
     * @param $user
     * @return bool
     */
    public function isOwner($product, $user)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM UserProductOwn WHERE ? LIKE product AND owner LIKE ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$product, $user]);

        $exists = $stmt->fetch();

        if ($exists)
        {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function changes the product information
     * @param Product $product
     * @return bool
     */
    public function updateProduct(Product $product)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "UPDATE Product 
                SET title=?,
                    description=?,
                    price=?,
                    category=?
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $status = $stmt->execute([$product->getTitle(), $product->getDescription(), $product->getPrice(), $product->getCategory(), $product->getId()]);
        return $status;
    }

    /**
     * This function sets inactive a product by it's ID
     * @param $prodID
     */
    public function removeProduct($prodID)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "UPDATE Product
                SET isActive = 0
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$prodID]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getProductByID($id)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM Product WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    /**
     * Get products for Home
     * @return array
     */
    public function getAllProducts(){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $stmt = null;
        if (isset($_SESSION['profile']['id'])){
            $sql = "SELECT * FROM Product WHERE 
                id NOT IN (SELECT product FROM UserProductOwn WHERE owner LIKE ?)
                AND isActive = true
            ORDER BY id DESC 
            LIMIT 5";
            $stmt = $db->prepare($sql);
            $stmt->execute([$_SESSION['profile']['id']]);

        } else {
            $sql = "SELECT * FROM Product WHERE isActive = 1 ORDER BY id DESC LIMIT 5";
            $stmt = $db->prepare($sql);
            $stmt->execute();
        }

        $products  = $stmt->fetchAll();
        return $products;
    }

    /**
     * Check if there is a like given
     * @param int $idProducte
     * @param string $idUser
     * @return array
     */
    public function isLike(int $idProducte ,string $idUser){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "SELECT COUNT(*) FROM Favorites WHERE Favorites.product LIKE $idProducte AND Favorites.user LIKE $idUser";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $count  = $stmt->fetchAll();
        return $count;
    }


    /**
     * Erase Like from table
     * @param int $idProducte
     * @param string $idUser
     */
    public function deleteLike(int $idProducte ,string $idUser) {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "DELETE FROM Favorites WHERE Favorites.user='$idUser' AND product = $idProducte";

        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    /**
     * Store like to a product (idProducte) by a user (idUser)
     * @param int $idProducte
     * @param string $idUser
     */
    public function addLike(int $idProducte ,string $idUser) {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "INSERT INTO Favorites(user,product)VALUES ('$idUser',$idProducte)";
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }



    /**
     * Get the products by title introduced in the search
     * @param string $nameProduct
     * @return array
     */
    public function getProductsSearch(string $nameProduct){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "SELECT * FROM Product WHERE title LIKE '$nameProduct' LIMIT 5";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $productsSearch  = $stmt->fetchAll();
        return $productsSearch;
    }



}