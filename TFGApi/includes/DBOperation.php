<?php


$errores="";
class DbOperation
{
    //Database connection link
    private $con;


    //Class constructor
    function __construct()
    {
        //Getting the DbConnect.php file
        require_once dirname(__FILE__) . '/DbConnect.php';

        //Creating a DbConnect object to connect to the database
        $db = new DbConnect();

        //Initializing our connection link of this class
        //by calling the method connect of DbConnect class
        $this->con = $db->connect();
    }


    function getErrores(){
      global $errores;
      return $errores;
    }

    // ------------------------------------------------------------------------------------------------------------------------------------------------
    // DATOS----------------------------------------------------------------------------------------------------------------------------------------
    // ------------------------------------------------------------------------------------------------------------------------------------------------

    /*
    * Operacion select de fijos
    * FIJOS
    */
    function cargaDatos($nombreTabla){
      try {
        $stmt = $this->con->prepare("SELECT nombre, ingredientes, precio FROM $nombreTabla");
        if($stmt){
          $stmt->execute();
          $stmt->bind_result($nombre, $ingrediente, $precio);
          $fijo = array();

          while($stmt->fetch()){
            $pedido = array();
            $pedido['nombre'] = $nombre;
            $pedido['ingredientes'] = $ingrediente;
            $pedido['precio'] = $precio;

            array_push($fijo, $pedido);
          }
          return $fijo;
        }
      }
      catch(Exception $e)
      {
        global $errores;
        $errores = array(
          "numError" => $stmt->errno,
          "descError" => $stmt->error);
        echo $e->errorMessage();
      }
    }

    // ------------------------------------------------------------------------------------------------------------------------------------------------
    // Ingredientes------------------------------------------------------------------------------------------------------------------------------------
    // ------------------------------------------------------------------------------------------------------------------------------------------------
    function cargaIngredientes(){
      try {
        $stmt = $this->con->prepare("select tipo, nombre, stock, precio from ingrediente;");
        if($stmt){
          $stmt->execute();
          $stmt->bind_result($tipo, $nombreIngrediente, $stockIngrediente, $precio);
          $fijo = array();

          while($stmt->fetch()){
            $pedido = array();
            $pedido['tipo'] = $tipo;
            $pedido['nombre'] = $nombreIngrediente;
            $pedido['stock'] = $stockIngrediente;
            $pedido['precio'] = $precio;

            array_push($fijo, $pedido);
          }
          return $fijo;
        }
      }
      catch(Exception $e)
      {
        global $errores;
        $errores = array(
          "numError" => $stmt->errno,
          "descError" => $stmt->error);
        echo $e->errorMessage();
      }
    }


// ------------------------------------------------------------------------------------------------------------------------------------------------
// CLIENTES----------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------------------------------

  /*
  * Crear clientes
  */
function createCliente($nombre, $apellido,$apellido2, $tlfno, $calle, $portal, $piso, $puerta, $urbanizacion, $usuario,$contrasenia,$codigoPostal)
{
    try {
      /*
      $sql = "insert into cliente (nombre, apellido1, apellido2, tlfno, calle, portal, piso, puerta, urbanizacion, usuario, contrasenia, codigoPostal) values  (?,?,?,?,?,?,?,?,?,?,?,?)";
      $stmt = $this->con->prepare($sql);
      if($stmt){
        $stmt->bind_param("ssssssssssss", $nombre, $apellido,$apellido2, $tlfno, $calle, $portal, $piso, $puerta,$urbanizacion, $usuario,$contrasenia,$codigoPostal);
        if($stmt->execute()){
          echo $stmt->error;
          $stmt -> close();
          return true;
        }
        global $errores;
        $errores = array(
          "numError" => $stmt->errno,
          "descError" => $stmt->error);
        $stmt -> close();
        return false;
      }
      */
      if (!$this->con->query("CALL crea_cliente('$nombre', '$apellido','$apellido2', '$tlfno', '$calle', '$portal', '$piso', '$puerta', '$urbanizacion', '$usuario', '$contrasenia', '$codigoPostal')"))
      {
        //echo "Falló CALL: (" . $this->con->errno . ") " . $this->con->error;
        global $errores;
        $errores = array(
          "numError" => $this->con->errno,
          "descError" => $this->con->error);
        return false;
      }
      return true;
    }
    catch(Exception $e)
    {
      global $errores;
      $errores = array(
        "numError" => $stmt->errno,
        "descError" => $stmt->error);
      echo $e->errorMessage();
    }
  }

  /*
  * Operacion select de clientes
  * LOGIN
  */
  function login($usuario, $contrasenia){
    try {
      $stmt = $this->con->prepare("SELECT id, nombre, apellido1, tlfno, calle, portal, piso, puerta, urbanizacion, codigoPostal FROM cliente WHERE usuario = ? AND contrasenia = SHA2(?, 384)");
      if($stmt){
        $stmt->bind_param("ss", $usuario, $contrasenia);
        $stmt->execute();
        /*$stmt->store_result();
        $res = $stmt->num_rows;*/
        $stmt->bind_result($id, $nombre, $apellido, $tlfno, $calle, $portal, $piso, $puerta, $urbanizacion,$codigoPostal);
        $fijo = array();

        while($stmt->fetch()){
          $pedido = array();
          $pedido['id'] = $id;
          $pedido['nombre'] = $nombre;
          $pedido['apellido'] = $apellido;
          $pedido['tlfno'] = $tlfno;
          $pedido['calle'] = $calle;
          $pedido['portal'] = $portal;
          $pedido['piso'] = $piso;
          $pedido['puerta'] = $puerta;
          $pedido['urbanizacion'] = $urbanizacion;
          $pedido['codigoPostal'] = $codigoPostal;
          array_push($fijo, $pedido);
        }
        return $fijo;
      }
    }
    catch(PDOException $e)
    {
      echo "Error: " . $e->getMessage();
    }
  }


  /*
  * Update cliente
  *
  */
  function updateCliente($nombreParametro, $valorParametro, $id){
    try {
      $stmt = $this->con->prepare("UPDATE cliente SET $nombreParametro = ? WHERE id = ?");
      if($stmt){
        $stmt->bind_param("si", $valorParametro, $id);
        if($stmt->execute())
          return true;
        return false;
      }
    }
    catch(PDOException $e)
    {
      echo "Error: " . $e->getMessage();
    }
  }

// ------------------------------------------------------------------------------------------------------------------------------------------------
// PEDIDOS-----------------------------------------------------------------------------------------------------------------------------------------
// ------------------------------------------------------------------------------------------------------------------------------------------------

/*
* Crear pedido
*/
function createPedido($refCliente, $numPedido, $fechaPedido, $extra_domicilio, $extra_local, $extra_recoger, $subtotal, $impuesto, $total){
$stmt = $this->con->prepare("INSERT INTO pedido (numPedido, fechaPedido, extra_domicilio, extra_local, extra_recoger,
subtotal, impuesto, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iisssssss", $refCliente, $numPedido, $fechaPedido, $extra_domicilio, $extra_local, $extra_recoger, $subtotal, $impuesto, $total);
if($stmt->execute())
  return true;
return false;
}


 /*
 * Select pedidos por parametro
 */
  function getPedido($nombreParametro, $valorParametro){
    $stmt = $this->con->prepare("SELECT * FROM pedido where $nombreParametro = ?");
    if($stmt){
      $stmt->bind_param("i", $valorParametro);
      $stmt->execute();
      $stmt->bind_result($id, $refCliente, $numPedido, $fechaPedido, $extra_domicilio, $extra_local, $extra_recoger, $subtotal, $impuesto, $total);

      $pedidos = array();

      while($stmt->fetch()){
        $pedido = array();
        $pedido['id'] = $id;
        $pedido['refCliente'] = $refCliente;
        $pedido['numPedido'] = $numPedido;
        $pedido['fechaPedido'] = $fechaPedido;
        $pedido['extra_domicilio'] = $extra_domicilio;
        $pedido['extra_local'] = $extra_local;
        $pedido['extra_recoger'] = $extra_recoger;
        $pedido['subtotal'] = $subtotal;
        $pedido['impuesto'] = $impuesto;
        $pedido['total'] = $total;

        array_push($pedidos, $pedido);
      }
      return $pedidos;
    }
  }
  

 /*
 * The update operation
 * When this method is called the record with the given id is updated with the new given values
 */
 function updateHero($id, $name, $realname, $rating, $teamaffiliation){
 $stmt = $this->con->prepare("UPDATE heroes SET name = ?, realname = ?, rating = ?, teamaffiliation = ? WHERE id = ?");
 $stmt->bind_param("ssisi", $name, $realname, $rating, $teamaffiliation, $id);
 if($stmt->execute())
 return true;
 return false;
 }


 /*
 * The delete operation
 * When this method is called record is deleted for the given id
 */
 function deleteHero($id){
 $stmt = $this->con->prepare("DELETE FROM heroes WHERE id = ? ");
 $stmt->bind_param("i", $id);
 if($stmt->execute())
 return true;

 return false;
 }
}

?>
