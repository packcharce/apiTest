<?php

 //getting the dboperation class
 require_once '../includes/DbOperation.php';

 //function validating all the paramters are available
 //we will pass the required parameters to this function
 function isTheseParametersAvailable($params){
 //assuming all parameters are available
 $available = true;
 $missingparams = "";

 foreach($params as $param){
 if(!isset($_POST[$param]) || strlen($_POST[$param])<=0){
 $available = false;
 $missingparams = $missingparams . ", " . $param;
 }
 }

 //if parameters are missing
 if(!$available){
 $response = array();
 $response['error'] = true;
 $response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';

 //displaying error
 echo json_encode($response);

 //stopping further execution
 die();
 }
 }

 //an array to display response
 $response = array();

 //if it is an api call
 //that means a get parameter named api call is set in the URL
 //and with this parameter we are concluding that it is an api call
 if(isset($_GET['apicall'])){

 switch($_GET['apicall']){

//---------------------------------------------------------------------------
// La operacion carga datos en el movil
case 'cargaDatos':
isTheseParametersAvailable(
  array('nombreTabla')
);

$db = new DbOperation();
$response['datos'] = $db->cargaDatos(
  $_POST['nombreTabla']
);
if($response){
$response['error'] = false;
$response['message'] = 'Datos cargados';
}else{
$response['error'] = true;
$aux = $db->getErrores();
$response['message'] = 'Some error occurred please try again ';
$response['numError'] = $db->getErrores()['numError'];
$response['descError'] = $db->getErrores()['descError'];
}
break;

case 'cargaIngredientes':

$db = new DbOperation();
$response['datos'] = $db->cargaIngredientes();
if($response){
$response['error'] = false;
$response['message'] = 'Ingredientes cargados';
}else{
$response['error'] = true;
$aux = $db->getErrores();
$response['message'] = 'Some error occurred please try again ';
$response['numError'] = $db->getErrores()['numError'];
$response['descError'] = $db->getErrores()['descError'];
}
break;

//---------------------------------------------------------------------------
 //the CREATE operation
 //if the api call value is 'createCliente'
 //we will create a record in the database
 case 'createCliente':
 isTheseParametersAvailable(
   array('nombre', 'apellido1', 'tlfno', 'calle',
    'portal', 'piso', 'puerta', 'usuario',
     'contrasenia', 'codigoPostal')
  );
 $db = new DbOperation();
 $result = $db->createCliente(
   $_POST['nombre'],
   $_POST['apellido1'],
   $_POST['apellido2'],
   $_POST['tlfno'],
   $_POST['calle'],
   $_POST['portal'],
   $_POST['piso'],
   $_POST['puerta'],
   $_POST['urbanizacion'],
   $_POST['usuario'],
   $_POST['contrasenia'],
   $_POST['codigoPostal']
 );

 if($result){
 $response['error'] = false;
 $response['message'] = 'Cliente añadido Correctamente';
 }else{
 $response['error'] = true;
 $aux = $db->getErrores();
 $response['message'] = 'Some error occurred please try again ';
 $response['numError'] = $db->getErrores()['numError'];
 $response['descError'] = $db->getErrores()['descError'];
 }
 break;

 //---------------------------------------------------------------------------
  //the READ operation
  case 'login':
  isTheseParametersAvailable(array('usuario', 'contrasenia'));
  $usuario = $_POST['usuario'];
  $contrasenia = $_POST['contrasenia'];
  $db = new DbOperation();
  $response['error'] = false;
  $response['message'] = 'Request successfully completed';
  $response['datos'] = $db->login(
    $usuario,
    $contrasenia
  );
  break;

//---------------------------------------------------------------------------
 //the READ operation
 case 'getPedido':
 isTheseParametersAvailable(array('nombrePar', 'valorPar'));
 $nombreParametro = $_POST['nombrePar'];
 $valorParametro = $_POST['valorPar'];
 $db = new DbOperation();
 $response['error'] = false;
 $response['message'] = 'Request successfully completed';
 $response['datos'] = $db->getPedido(
   $nombreParametro,
   $valorParametro
 );
 break;

//---------------------------------------------------------------------------
 //the UPDATE operation
 case 'updateCliente':
   isTheseParametersAvailable(array('nombrePar','valorPar','id'));
   $db = new DbOperation();
   $result = $db->updateCliente(
     $_POST['nombrePar'],
     $_POST['valorPar'],
     $_POST['id']
   );

   if($result){
   $response['error'] = false;
   $response['message'] = 'Cliente actualizado con exito';
   //$response['heroes'] = $db->getHeroes();
   }else{
   $response['error'] = true;
   $response['message'] = 'Some error occurred please try again';
   }
 break;


//---------------------------------------------------------------------------
 //the UPDATE operation
 case 'updatehero':
 isTheseParametersAvailable(array('id','name','realname','rating','teamaffiliation'));
 $db = new DbOperation();
 $result = $db->updateHero(
 $_POST['id'],
 $_POST['name'],
 $_POST['realname'],
 $_POST['rating'],
 $_POST['teamaffiliation']
 );

 if($result){
 $response['error'] = false;
 $response['message'] = 'Hero updated successfully';
 $response['heroes'] = $db->getHeroes();
 }else{
 $response['error'] = true;
 $response['message'] = 'Some error occurred please try again';
 }
 break;

//---------------------------------------------------------------------------
 //the delete operation
 case 'deletehero':

 //for the delete operation we are getting a GET parameter from the url having the id of the record to be deleted
 if(isset($_GET['id'])){
 $db = new DbOperation();
 if($db->deleteHero($_GET['id'])){
 $response['error'] = false;
 $response['message'] = 'Hero deleted successfully';
 $response['heroes'] = $db->getHeroes();
 }else{
 $response['error'] = true;
 $response['message'] = 'Some error occurred please try again';
 }
 }else{
 $response['error'] = true;
 $response['message'] = 'Nothing to delete, provide an id please';
 }
 break;
 }

 }else{
 //if it is not api call
 //pushing appropriate values to response array
 $response['error'] = true;
 $response['message'] = 'Invalid API Call';
 }

 //displaying the response in json structure
 echo json_encode($response);

 ?>
