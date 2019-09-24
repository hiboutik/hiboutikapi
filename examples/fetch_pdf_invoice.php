hp
/**
 *
 *
 *  @author:    Hiboutik
 *  @email      contact[at]hiboutik.com
 *
 ***********************************************************************************************************************
 *  @licence    GPLv3 as in "https://gnu.org/licenses/gpl.html"
 ***********************************************************************************************************************
 *
 *   Ce script permet de récupérer une facture au format PDF
 *   Il faut au préalable installer et configurer l'apllication "Documents PDF". C'est cette application qui interroge l'API et génère le document PDF.
 *   Token : cf app "Documents PDF" tout en bas de la page
 *   Template : l'intitulé de votre modèle de facture cf app "Documents PDF"
 *   sale_no : le numéro de la vente dont vous souhaitez récupérer le facture
 *   Account : votre compte hiboutik 
 *
 */

$token = "IqKSpo8B2etlO6xxxxxxxxxxxxxxc4JsiHgb";
$template = "ticket";
$sale_no = 243549;
$account = "moncompte";


/// POST data
$data = [
  'token'    => $token,
  'template' => $template,
  'sale_no'  => $sale_no,
];
// Setup cURL
$ch = curl_init("https://pdf.hiboutik.net/pdf/?account=$account");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => $data,
]);
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch) === 0 && curl_getinfo($ch, CURLINFO_RESPONSE_CODE) === 200) {
  // Stream file
  header("Content-Description: File Transfer");
  header("Content-Type: application/octet-stream");
  header("Content-Disposition:attachment;filename=\"facture_$sale_no.pdf\"");
  header("Expires: 0");
  header("Cache-Control: must-revalidate");
  header("Pragma: public");
  header("Content-Length: '.strlen($response)");
  print $response;
} else {
  // Show errors
  print 'CURL error: '.curl_error($ch)."\n";
  print 'HTTP status '.curl_getinfo($ch, CURLINFO_RESPONSE_CODE)."\n";
  print_r(json_decode($response, true));
}
curl_close($ch);
