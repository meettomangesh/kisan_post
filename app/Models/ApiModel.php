<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PDO;
use Redis;

abstract class ApiModel extends Model
{

  /**
   * Object of PDO
   * @var object 
   */
  protected $pdo;

  /**
   * Object of Redis
   * @var object 
   */
  protected $redis;

  /**
   * All column names of customers table as an array
   * @var array 
   */
  public $fields = [];

  const PDO_FETCH_ASSOC = PDO::FETCH_ASSOC;

  /**
   * Create a new Model instance.
   *
   * @return void
   */
  public function __construct(PDO $pdo, Redis $redis)
  {
    parent::__construct();
    $this->pdo = $pdo;
    $this->redis = $redis;
    //$this->fields = $this->getAllTableFields(); //Get all table column names 
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function create($params)
  {
    return parent::create($params);
  }
  //  abstract function getAllTableFields();
}
