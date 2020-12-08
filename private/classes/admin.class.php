<?php
  class Admin extends DatabaseObject {
    //START: Active Database Design Pattern
    protected static $db_columns = ['id','first_name', 'last_name', 'email', 'username', 'hashed_password'];
    protected static $table_name = 'admins';
    //END: Active Database Design Pattern
    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $username;
    public $hashed_password;
    //public $password;

    public function __construct($args=[]) {
      //$this->brand = isset($args['brand']) ? $args['brand'] : '';
      $this->first_name = $args['first_name'] ?? '';
      $this->last_name = $args['last_name'] ?? '';
      $this->email = $args['email'] ?? '';
      $this->username = $args['username'] ?? '';
      $this->hashed_password = $args['hashed_password'] ?? '';
      //$this->password = $args['password'] ?? '';
    }

    public function name() {
      return "{$this->first_name} {$this->last_name}";
    }

    public function validate(){
      $this->errors = [];
      if(is_blank($this->first_name)) {
        $this->errors[] = "First Name cannot be blank";
      }
      if(is_blank($this->last_name)) {
        $this->errors[] = "Last Name cannot be blank";
      }
      if(is_blank($this->username)) {
        $this->errors[] = "Username cannot be blank";
      }
      // if(is_blank($this->hashed_password)) {
      //   $this->errors[] = "Password cannot be blank";
      // }

      return $this->errors;
    }

  }
?>
