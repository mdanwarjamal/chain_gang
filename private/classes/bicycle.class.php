<?php

class Bicycle {
  //START: Active Database Design Pattern
  protected static $database;
  protected static $db_columns = ['id','brand', 'model', 'year', 'category', 'color', 'description', 'gender', 'price', 'weight_kg', 'condition_id'];
  public $errors = [];
  public static function set_database($database){
    self::$database = $database;
  }
  public static function find_by_sql($sql){
    $result = self::$database->query($sql);
    $object_array = [];
    while($record = $result->fetch_assoc()){
      $object = self::instantiate($record);
      $object_array[] = $object;
    }
    $result->free();
    //$object = self::instantiate($result);
    //$object_array[] = $object;
    return $object_array;
  }
  public static function find_all(){
    $sql = "SELECT * FROM bicycles";
    return self::find_by_sql($sql);
  }
  public static function find_by_id($id){
    $sql = "SELECT * FROM bicycles ";
    $sql .= "WHERE id='" . self::$database->escape_string($id) . "'";
    $object_array = self::find_by_sql($sql);
    if(!empty($object_array)){
      return array_shift($object_array);
    }else{
      return false;
    }
  }
  protected static function instantiate($record){
    $object = new self;
    foreach ($record as $property => $value) {
      if(property_exists($object,$property)){
        $object->$property = $value;
      }
    }
    return $object;
  }
  public function validate(){
    $this->errors = [];
    if(is_blank($this->brand)) {
      $this->errors[] = "Brand cannot be blank";
    }
    if(is_blank($this->model)) {
      $this->errors[] = "Model cannot be blank";
    }

    return $this->errors;
  }
  public function create(){
    /*$sql = "INSERT INTO bicycles (";
    $sql .= "brand, model, year, category, color, description, gender, price, weight_kg, condition_id";
    $sql .= ") VALUES (";
      $sql .= "'" . self::$database->escape_string($this->brand) . "', ";
      $sql .= "'" . self::$database->escape_string($this->model) . "', ";
      $sql .= "'" . self::$database->escape_string($this->year) . "', ";
      $sql .= "'" . self::$database->escape_string($this->category) . "', ";
      $sql .= "'" . self::$database->escape_string($this->color) . "', ";
      $sql .= "'" . self::$database->escape_string($this->description) . "', ";
      $sql .= "'" . self::$database->escape_string($this->gender) . "', ";
      $sql .= "'" . self::$database->escape_string($this->price) . "', ";
      $sql .= "'" . self::$database->escape_string($this->weight_kg) . "', ";
      $sql .= "'" . self::$database->escape_string($this->condition_id) . "'";
    $sql .= ")";*/

    //version 2
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $sql = "INSERT INTO bicycles (";
    $sql .= join(', ', array_keys($attributes));
    $sql .= ") VALUES ('";
    $sql .= join("', '", array_values($attributes));
    $sql .= "')";

    $result = self::$database->query($sql);

    if($result){
      $this->id = self::$database->insert_id;
    }
    return  $result;
  }
  public function update(){
    $this->validate();
    if(!empty($this->errors)) { return false; }

    $attributes = $this->sanitized_attributes();
    $attribute_pairs = [];
    foreach ($attributes as $key => $value) {
      $attribute_pairs[] = "{$key}='{$value}'";
    }
    $sql = "UPDATE bicycles SET ";
    $sql .= join(', ',$attribute_pairs);
    $sql .= " WHERE id='" . self::$database->escape_string($this->id) . "' ";
    $sql .= "LIMIT 1";

    $result = self::$database->query($sql);
    return $result;
  }
  public function save(){
    if(!isset($this->id)){
      return $this->create();
    }else{
      return $this->update();
    }
  }
  public function delete(){
    $sql = "DELETE FROM bicycles ";
    $sql .= "WHERE id='" . self::$database->escape_string($this->id)  . "' ";
    $sql .= "LIMIT 1";
    $result = self::$database->query($sql);
    return $result;
  }
  public function merge_attributes($args){
    foreach ($args as $key => $value) {
      if(property_exists($this,$key) && !is_null($value)){
        $this->$key = $value;
      }
    }
  }
  public function attributes(){
    $attributes = [];
    foreach (self::$db_columns as $column) {
      if($column == "id") { continue; }
      $attributes[$column] = $this->$column;
    }
    return $attributes;
  }
  protected function sanitized_attributes(){
    $sanitized =[];
    foreach ($this->attributes() as $key => $value) {
      $sanitized[$key] = self::$database->escape_string($value);
    }
    return $sanitized;
  }
  //END: Active Database Design Pattern
  public $id;
  public $brand;
  public $model;
  public $year;
  public $category;
  public $color;
  public $description;
  public $gender;
  public $price;
  public $weight_kg;
  public $condition_id;

  public const CATEGORIES = ['Road', 'Mountain', 'Hybrid', 'Cruiser', 'City', 'BMX'];

  public const GENDERS = ['Mens', 'Womens', 'Unisex'];

  public const CONDITION_OPTIONS = [
    1 => 'Beat up',
    2 => 'Decent',
    3 => 'Good',
    4 => 'Great',
    5 => 'Like New'
  ];

  public function __construct($args=[]) {
    //$this->brand = isset($args['brand']) ? $args['brand'] : '';
    $this->brand = $args['brand'] ?? '';
    $this->model = $args['model'] ?? '';
    $this->year = $args['year'] ?? '';
    $this->category = $args['category'] ?? '';
    $this->color = $args['color'] ?? '';
    $this->description = $args['description'] ?? '';
    $this->gender = $args['gender'] ?? '';
    $this->price = $args['price'] ?? 0;
    $this->weight_kg = $args['weight_kg'] ?? 0.0;
    $this->condition_id = $args['condition_id'] ?? 3;

    // Caution: allows private/protected properties to be set
    // foreach($args as $k => $v) {
    //   if(property_exists($this, $k)) {
    //     $this->$k = $v;
    //   }
    // }
  }
  public function name() {
    return "{$this->brand} {$this->model} {$this->year}";
  }
  public function weight_kg() {
    return number_format($this->weight_kg, 2) . ' kg';
  }

  public function set_weight_kg($value) {
    $this->weight_kg = floatval($value);
  }

  public function weight_lbs() {
    $weight_lbs = floatval($this->weight_kg) * 2.2046226218;
    return number_format($weight_lbs, 2) . ' lbs';
  }

  public function set_weight_lbs($value) {
    $this->weight_kg = floatval($value) / 2.2046226218;
  }

  public function condition() {
    if($this->condition_id > 0) {
      return self::CONDITION_OPTIONS[$this->condition_id];
    } else {
      return "Unknown";
    }
  }

}

?>
