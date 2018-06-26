<!--

This document provides sample PHP code on how to connect to the Anthill CRM platform using the various methods available. 

You will require a specific username and password for the installation you are working with. 

For any questions, please contact support@anthill.co.uk

--!>

<html>
<head>
<title>Anthill CRM PHP API Example</title>
</head>
<body>
<?php
//define('ANTHILL_INSTALLATION', 'https://<<YOUR ANTHILL DOMAIN>>.anthillcrm.com/');
//define('ANTHILL_WSDL','api/v1.asmx?wsdl');

//define('ANTHILL_USERNAME', '<<YOUR API USERNAME>>');
//define('ANTHILL_KEY', '<<YOUR API PASSWORD>>');

define('ANTHILL_INSTALLATION', 'http://localhost:5441/');
define('ANTHILL_WSDL','api/v1.asmx?wsdl');

define('ANTHILL_USERNAME', 'testapi');
define('ANTHILL_KEY', 'testapi');

class Anthill {
  public static function CreateAuthHeader() {
    return new SoapHeader('http://www.anthill.co.uk/', 'AuthHeader',
      array(
        'Username' => ANTHILL_USERNAME,
        'Password' => ANTHILL_KEY
      )
    );
  }


  // test communication with Anthill endpoint - should return "Pong"
  public static function Ping(){
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $result = $client->__soapCall('Ping', array());
    return $result->PingResult;
  }

  // retrieves the current locations list from Anthill
  public static function GetLocations(){  
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('GetLocations', array(), null, $header);
    return $result->GetLocationsResult->Location;
  }
  //creates a customer, deduplicating where possible
  public static function CreateCustomer() {
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateCustomer', array('parameters' => array(
      'locationId' => 2, // Default location id
      'source' => "Website",
      'customer' => Anthill::constructCustomerModel()
    )), null, $header);
    return $result->CreateCustomerResult;
  }

  // creates a customer, de-duplicating where possible, and an associated enquiry
  public static function CreateCustomerEnquiry() {
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateCustomerEnquiry', array('parameters' => array(
      'locationId' => 2, // Default location id
      'source' => "Website",
      'customer' => Anthill::constructCustomerModel(),
      'enquiry' => Anthill::constructEnquiryModel()
    )), null, $header);
    return $result->CreateCustomerEnquiryResult;
  }

  // creates a customer, de-duplicating where possible, and an associated lead
  public static function CreateCustomerLead() {
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateCustomerLead', array('parameters' => array(
      'locationId' => 2, // Default location id
      'source' => "Website",
      'customer' => Anthill::constructCustomerModel(),
      'lead' => Anthill::constructLeadModel()
    )), null, $header);
    return $result->CreateCustomerLeadResult;
  }


  // creates a customer, de-duplicating where possible, and an associated sale
  // where an existing customer with a single open lead is found, the lead is converted to the new sale
  public static function CreateCustomerSale() {
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateCustomerSale', array('parameters' => array(
      'locationId' => 2, // Default location id
      'source' => "Website",
      'customer' => Anthill::constructCustomerModel(),
      'sale' => Anthill::constructSaleModel()
    )), null, $header);
    return $result->CreateCustomerSaleResult;
  }

  // creates an enquiry, given a customerId
  public static function CreateEnquiry($customerId) {
	$client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateEnquiry', array('parameters' => array(
	  'customerId' => $customerId,
      'locationId' => 2, // Default location id
      'source' => "Website",
      'enquiry' => Anthill::constructEnquiryModel()
    )), null, $header);
    return $result->CreateEnquiryResult;
  }

  // creates a lead, given a customerId
  public static function CreateLead($customerId) {
	$client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateLead', array('parameters' => array(
	  'customerId' => $customerId,
      'locationId' => 2, // Default location id
      'source' => "Website",
      'lead' => Anthill::constructleadModel()
    )), null, $header);
    return $result->CreateLeadResult;
  }

  // creates a sale, given a customerId
  public static function CreateSale($customerId) {
	$client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('CreateSale', array('parameters' => array(
	  'customerId' => $customerId,
      'locationId' => 2, // Default location id
      'source' => "Website",
      'sale' => Anthill::constructSaleModel()
    )), null, $header);
    return $result->CreateLeadResult;
  }

  // returns a paged array of basic customer information given a set of searchCriteria
  public static function FindCustomers($lastName, $postcode, $pageNumber = 1, $pageSize = 50) {
      $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('FindCustomers', array('parameters' => array (
        'searchCriteria' => array(
            (object)array('FieldName' => 'CustomField(LastName)', 'Operation' => 'Is', 'Args' => $lastName),
            (object)array('FieldName' => 'Address(Postcode)', 'Operation' => 'StartsWith', 'Args' => $postcode)
        ),
        'pageNumber' => $pageNumber,
        'pageSize' => $pageSize
      )), null, $header);
      return $result->FindCustomersResult;        
  }

  // returns customer details and optionally include their recent activity, given a customerId
  public static function GetCustomerDetails($customerId, $includeActivity) {
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('GetCustomerDetails', array('parameters' => array(
	  'customerId' => $customerId,
      'includeActivity' => $includeActivity
    )), null, $header);
    return $result->GetCustomerDetailsResult;
  }

  // modifies a customer record, given a customerId and array of CustomFields that should be changed
  public static function EditCustomerDetails($customerId, $fieldsToEdit){
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $customFields = Anthill::constructEditCustomerDetailsModel($fieldsToEdit);
    $result = $client->__soapCall('EditCustomerDetails', array('parameters' => array(
	  'customerId' => $customerId,
      'customFields' => $customFields
    )), null, $header);    
  }
  // returns the location detail, given a locationId
  public static function GetLocationDetails($locationId){
    $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
    $header = Anthill::CreateAuthHeader();
    $result = $client->__soapCall('GetLocationDetails', array('parameters' => array(
	  'locationId' => $locationId
    )), null, $header);
    return $result->GetLocationDetailsResult;
  }

  public static function AttachFileToEnquiry($enquiryId, $pathToFile, $filename, $attachmentType){
      $handle = fopen($pathToFile, "r");
      $contents = fread($handle, filesize($pathToFile));
      $base64Contents = base64_encode($contents);
      $client =new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('AddEnquiryAttachment', array('parameters' =>array(
        'enquiryId' => $enquiryId,
        'attachmentTypeId' => $attachmentType, 
        'filename' => $filename, 
        'base64EncodedAttachment' => $base64Contents
      ))
      , null, $header);
  }

  public static function AttachFileToLead($leadId, $pathToFile, $filename, $attachmentType){
      $handle = fopen($pathToFile, "r");
      $contents = fread($handle, filesize($pathToFile));
      $base64Contents = base64_encode($contents);
      $client =new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('AddLeadAttachment', array('parameters' =>array(
        'leadId' => $leadId,
        'attachmentTypeId' => $attachmentType, 
        'filename' => $filename, 
        'base64EncodedAttachment' => $base64Contents
      ))
      , null, $header);
  }

  public static function AttachFileToSale($saleId, $pathToFile, $filename, $attachmentType){
      $handle = fopen($pathToFile, "r");
      $contents = fread($handle, filesize($pathToFile));
      $base64Contents = base64_encode($contents);
      $client =new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('AddSaleAttachment', array('parameters' =>array(
        'saleId' => $saleId,
        'attachmentTypeId' => $attachmentType, 
        'filename' => $filename, 
        'base64EncodedAttachment' => $base64Contents
      ))
      , null, $header);
  }

  public static function AttachFileToFulfilment($fulfilmentId, $pathToFile, $filename, $attachmentType){
      $handle = fopen($pathToFile, "r");
      $contents = fread($handle, filesize($pathToFile));
      $base64Contents = base64_encode($contents);
      $client =new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('AddFulfilmentAttachment', array('parameters' =>array(
        'fulfilmentId' => $fulfilmentId,
        'attachmentTypeId' => $attachmentType, 
        'filename' => $filename, 
        'base64EncodedAttachment' => $base64Contents
      ))
      , null, $header);
  }

  public static function EditLeadDetails($leadId, $customFields) {
      $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('EditLeadDetails', array('parameters' => array(
        'leadId' => $leadId,
        'customFields' => $customFields
      ))
      , null, header);
  }

  public static function EditSaleDetails($saleId, $customFields) {
      $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('EditSaleDetails', array('parameters' => array(
        'saleId' => $saleId,
        'customFields' => $customFields
      ))
      , null, header);
  }

  public static function EditFulfilmentDetails($fulfilmentId, $customFields) {
      $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('EditFulfilmentDetails', array('parameters' => array(
        'fulfilmentId' => $fulfilmentId,
        'customFields' => $customFields
      ))
      , null, header);
  }

  public static function GetLeadsByExternalReference($externalReference){
      $client = new SoapClient(ANTHILL_INSTALLATION . ANTHILL_WSDL);
      $header = Anthill::CreateAuthHeader();
      $result = $client->__soapCall('GetLeadsByExternalReference', array('parameters' => array(
        'externalReference' => $externalReference
      ))
      , null, header);
      return $result->GetLeadsByExternalReferenceResult;
  }


  // builds the customer model to be passed to Anthill
  // populate the appropriate custom fields from your form post
  private static function constructCustomerModel() {
    return array(
      'TypeId' => 1, // customer account type
      'MarketingConsentGiven' => true,
      'CustomFields' => array(
        Anthill::CustomField("First Name", "Ned"),
        Anthill::CustomField("Last Name", "Flanders"),
        Anthill::CustomField("Email", "ned@flanders.com"),
        Anthill::CustomField("Phone", "01234567890")
      ),
	  'Address' => array(
		'Address1' => 'Flat 40, Primrose Court',
		'Address2' => 'Azalea Drive',
		'City' => 'Rington',
		'County' => 'Ringshire',
		'Postcode' => 'RN1 7AT'		
	  )
    );
  }

  // builds the lead model to be passed to Anthill
  // populate the appropriate custom fields from your form post
  private static function constructEnquiryModel() {
    return array(
      'TypeId' => 1, // Demo enquiry type
      'CustomFields' => array(
        Anthill::CustomField("Demo Type", "Example Demo") // set value as required
      )
    );
  }

  // builds the lead model to be passed to Anthill
  // populate the appropriate custom fields from your form post
  private static function constructLeadModel() {
    return array(
      'TypeId' => 1, // Demo lead type
      'CustomFields' => array(
        Anthill::CustomField("Demo Type", "Example Demo") // set value as required
      )
    );
  }


  // builds the sale model to be passed to Anthill
  // populate the appropriate custom fields from your form post
  private static function constructSaleModel() {
    return array(
      'TypeId' => 1, //Live Account type
      'CustomFields' => array(
        Anthill::CustomField("Account Type", "Example Account Type") // set value as required
      )
    );
  }

  private static function constructEditCustomerDetailsModel($fieldsToEdit) {
      $customFields = array();
      foreach($fieldsToEdit as $k => $v){
          array_push($customFields, Anthill::CustomField($k, $v));
      }
     return $customFields;      
  }

  private static function CustomField($key, $value) {
    return (object)array('Key' => $key, 'Value' => $value);
  }
}

echo Anthill::Ping();

////// Example - GetLocations - retrieves the current locations list from Anthill
/*
echo "<pre>";
$locations = Anthill::GetLocations();
print_r($locations);
echo "</pre>";
*/

/*
echo "<pre>";
$customers = Anthill::FindCustomers('Flanders', 'LS1');
print_r($customers);
echo "</pre>";
*/



// Example - CreateCustomerLead - creates a customer and lead in Anthill 

//echo "<pre>";
//$createdIds = Anthill::CreateCustomerLead();
//print_r($createdIds);
//echo "</pre>";

// Example = CreateCustomerSale - creates a customer and sale in Anthill
//try {
//    echo "<pre>";
//    $createdIds = Anthill::CreateCustomerSale();
//    print_r($createdIds);
//    echo "</pre>";

//} catch (Exception $e) {
//    echo 'Caught exception: ',  $e->getMessage(), "\n";
//}

// Example - GetCustomerDetails
//echo "<pre>";
//$customerDetails = Anthill::GetCustomerDetails(12345, FALSE);
//print_r($customerDetails);
//echo("</pre>");

// Example - GetLocationDetails
//echo "<pre>";
//$location = Anthill::GetLocationDetails(3);
//print_r($location);
//echo "</pre>";

// Example - EditCustomerDetails
//echo "<pre>";
//$fieldsToEdit = array(
//    "Ref" => "ABC12345",
//    "First Name" => "Jan"
//);
//print_r($fieldsToEdit);
//try {
//    Anthill::EditCustomerDetails(12345, $fieldsToEdit);
//    echo "Done\n";
//}
//catch(Exception $e){
//    echo 'Caught exception: ',  $e->getMessage(), "\n";
//}
//echo "</pre>";


// Example - Attach a file to a new lead
//echo "<pre>";
//try {
//    $ids = Anthill::CreateCustomerLead();
//    $leadId = $ids->int[1];    // leadId is 2nd item in returned array
//    Anthill::AttachFileToLead($leadId, "C:\\temp\\test.txt", "test.txt", 1);
//    
//print_r($leadId);
//}
//catch(Exception $e) {
//    echo 'Caught exception: ',  $e->getMessage(), "\n";
//}
//echo "</pre>";

try {
    $extRef = "12345678";
    $leads = Anthill::GetLeadsByExternalReference($extRef);
    print_r($leads);
}
catch(Exception $e){
    echo 'Caught exception: ', $e->getMessage(), "\n";
}

?>
</body>
</html>