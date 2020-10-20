<?php


namespace SallePW\Model;


use PDO;
use PDOException;
use function PHPSTORM_META\elementType;
use Psr\Container\ContainerInterface;

class ProfileSQL implements ProfileRepository
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
     * Create user
     * @param User $user
     */
    public function save(User $user)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "INSERT INTO User (username, email, password, name, birthdate, phone, image_dir) VALUES (?,?,?,?,?,?,?)";


        $username = $user->getUsername();
        $email = $user->getEmail();
        $password = MD5($user->getPassword());
        $name = $user->getName();
        $birthdate = $user->getBirthdate();
        $phone = $user->getPhone();
        $image_dir = $user->getImageDir();
        $user = $this->defaultValues($user);

        $db->prepare($sql)->execute([$username,$email,$password, $name, $user->getBirthdate(), $phone,$user->getImageDir()]);
        var_dump($user);
    }

/* TODO:CHECK STATIS
  /**
     * Get information from a user
     * @param array $fields
     * @param string $table
     * @param string $conditions
     * @return array
 *
    public function get(array $fields, string $table, string $conditions)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $elements = "";

        foreach ($fields as $field) {
            $elements = $elements . $field . ",";
        }

        $elements = rtrim($elements,',');

        $sql = "SELECT ? FROM User";

        $stmt = $db->prepare($sql);
        $stmt->execute([$elements]);
        $variables  = $stmt->fetchAll();
        return $variables;
    }
    */


    /**
     * This function needs an email and it returns the same email if there is any coincidence
     * @param string $email
     * @return array
     */
    public function checkIfEmailExists(string $email){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "SELECT email FROM User WHERE email LIKE ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$email]);
        $variables  = $stmt->fetchAll();
        return $variables;

    }

    /**
     * This function returns an array with an email associated with a nickname if the given string already exists within the database
     * @param string $username
     * @return array
     */
    public function checkIfUsernameExists(string $username){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);
        $sql = "SELECT email FROM User WHERE username LIKE ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
        $variables  = $stmt->fetchAll();
        return $variables;

    }

    /**
     * This function returns all user info stored at the database if the user is active and the password is valid
     * @param string $password
     * @param string $id
     * @return array
     */
    public function login(string $password, string $id)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM User
                WHERE password LIKE MD5(" . ":password" . ") AND isActive = TRUE ";

        if (filter_var($id, FILTER_VALIDATE_EMAIL)){
            $sql = $sql . " AND email LIKE " . ":id" . "";
        } else {
            $sql = $sql . " AND username LIKE " . ":id" . "";
        }

        // select a particular user by id
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id, 'password'=>$password,]);
        $response = $stmt->fetchAll();

        if (sizeof($response) == 0){
            $sql = "SELECT username, email, isActive FROM User WHERE ? LIKE ";
            if (filter_var($id, FILTER_VALIDATE_EMAIL)){
                $sql = $sql."email";
            } else {
                $sql =$sql."username";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            $response = $stmt->fetchAll();

            return $response;

        }

        return $response;

    }

    /**
     * return all User information from an email
     * @param string $id
     * @return array
     */
    public function getUserDetails(string $id){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM User
                WHERE email LIKE " . ":id" . " ";


        // select a particular user by id
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $response = $stmt->fetchAll();

        if (sizeof($response) == 0){
            $sql = "SELECT username, email FROM User WHERE ? LIKE ";
            if (filter_var($id, FILTER_VALIDATE_EMAIL)){
                $sql = $sql."email";
            } else {
                $sql =$sql."username";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            $response = $stmt->fetchAll();


            return $response;

        }
        return $response;
    }

    /**
     * Returns all user info using an ID (By a cookie given)
     * @param string $id
     * @return array
     */
    public function getUserbyId(string $id){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "SELECT * FROM User
                WHERE id LIKE " . ":id" . " ";


        // select a particular user by id
        $stmt = $db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $response = $stmt->fetchAll();

        if (sizeof($response) == 0){
            $sql = "SELECT username, email FROM User WHERE ? LIKE ";
            if (filter_var($id, FILTER_VALIDATE_EMAIL)){
                $sql = $sql."email";
            } else {
                $sql =$sql."username";
            }

            $stmt = $db->prepare($sql);
            $stmt->execute([$id]);
            $response = $stmt->fetchAll();


            return $response;

        }
        return $response;
    }



    /**
     * Set all information related to a User as inactive
     * @param string $id
     */
    //TODO: CHECK IF LIKES SHOULD BE DELETED
    public function deleteAccount(string $id){
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        //GET USER ID
        $sql = "SELECT id FROM User WHERE email LIKE ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        $userId = ($stmt->fetch())['id'];

        //SET USER inactive
        $sql = "UPDATE User SET isActive = 0 WHERE email LIKE ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        //GET USER PRODUCTS && SET inactive
        $sql = "SELECT * FROM UserProductOwn WHERE owner = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);

        $products = $stmt->fetchAll();

        foreach ($products as $product)
        {
            $sql = "UPDATE Product SET isActive = 0 WHERE id = ?";

            $stmt = $db->prepare($sql);
            $stmt->execute([$product['product']]);
        }
    }

    /**
     * Update user infromation, if done propertly returns true
     * @param User $u
     * @return bool
     */
    public function update(User $u)
    {
        var_dump($u->getImageDir());
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);

        $sql = "UPDATE User SET email = ?, password = MD5(?), name = ?, birthdate = ?, phone = ?, image_dir = ? WHERE username = ?";

        $stmt = $db->prepare($sql);

        $u = $this->defaultValues($u);

        $val = $stmt->execute([$u->getEmail(), $u->getPassword(), $u->getName(), $u->getBirthdate(), $u->getPhone(), $u->getImageDir(), $u->getUsername()]);

        $sql = "SELECT * FROM User";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $vals = $stmt->fetchAll();

        return $val;
    }

    private function defaultValues(User $u)
    {
        if (empty($u->getImageDir())) $u->setImageDir("____");
        if (empty($u->getBirthdate())) $u->setBirthdate("2018-06-07");

        return $u;
    }

    /**
     * Returns information about a product owner
     * @param $productID
     * @return mixed
     */
    public function getOwner($productID)
    {
        $db = new PDO('mysql:host=' . $this->address . ';dbname=' . $this->dbname . ';', $this->userNameDB, $this->passwordDB);


        $sql = "SELECT * FROM UserProductOwn WHERE product = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$productID]);
        $info = $stmt->fetch();

        $sql = "SELECT * FROM User WHERE id = ?";

        $stmt = $db->prepare($sql);
        $stmt->execute([$info['owner']]);
        return $stmt->fetch();
    }
}