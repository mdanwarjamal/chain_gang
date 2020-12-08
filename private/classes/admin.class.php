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
    protected $hashed_password;
    public $password;
    public $confirm_password;
    //public $password;

    public function __construct($args=[]) {
      $this->first_name = $args['first_name'] ?? '';
      $this->last_name = $args['last_name'] ?? '';
      $this->email = $args['email'] ?? '';
      $this->username = $args['username'] ?? '';
      $this->password = $args['password'] ?? '';
      $this->confirm_password = $args['confirm_password'] ?? '';
    }

    public function name() {
      return "{$this->first_name} {$this->last_name}";
    }

    protected function set_hashed_password(){
      $this->hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    protected function create(){
      $this->set_hashed_password();
      return parent::create();
    }

    protected function update(){
      $this->set_hashed_password();
      return parent::update();
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
