<?php

/**
 * Class CustomPDO
 */
class CustomPDO
{
  /**
   * @var bool
   */
  private $debug;
  /**
   * @var PDO
   */
  private $pdo;

  /**
   * @param $dbDSN
   * @param $dbUsername
   * @param $dbPassword
   * @param bool $debug
   */
  public function __construct($dbDSN, $dbUsername, $dbPassword, $debug = false)
  {
    $this->debug = $debug;
    try {
      $this->pdo = new PDO($dbDSN, $dbUsername, $dbPassword);
    } catch (PDOException $e) {
      $this->throwException($e);
    }
  }

  /**
   * @param string $sql
   * @return array|bool
   */
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

  /**
   * @param string $sql
   * @return bool|string
   */
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

  /**
   * @param PDOException $exception
   */
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