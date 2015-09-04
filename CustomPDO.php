<?php
class CustomPDO
{
  private $debug;
  private $pdo;

  public function __construct($dbDSN, $dbUsername, $dbPassword, $debug = false)
  {
    $this->debug = $debug;
    try {
      $this->pdo = new PDO($dbDSN, $dbUsername, $dbPassword);
    } catch (PDOException $e) {
      $this->throwException($e);
    }
  }

  public function Query($sql = '')
  {
    $results = false;
    try {

      if (strlen($sql) > 0) {
        $sth = $this->pdo->query($sql, PDO::FETCH_ASSOC);
        if ($sth) {
          $results = $sth->fetchAll();
        }
      }

    } catch (PDOException $e) {
      $this->throwException($e);
    }
    return $results;
  }

  public function create($sql = '')
  {
    if (strlen($sql) > 0) {
      $sth = $this->pdo->query($sql);
      if ($sth) {
        return $this->pdo->lastInsertId();
      }
    }
    return false;
  }

  public function throwException(PDOException $exception)
  {
    if ($this->debug) {
      echo '<pre>';
      print_r(array(
        $exception->getMessage( ) , $exception->getCode( )
      ));
      echo '</pre>';
      die();
    }
  }

}