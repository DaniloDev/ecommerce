<?php

namespace DDev;

use Rain\Tpl; 

class Mailer{

	const USERNAME 	= "";
	const PASSWORD 	= "";
	const NAME_FROM =  "Curso PHP 7 Estudos";


	private $mail;

	public function __construct($toAddress, $toName, $subject, $tplName, $data = array()){

      

		$config = array(         //'$_SERVER["DOCUMENT_ROOT"]', TRAZ A PASTA QUE P SERVIDOR ESTA CONFIGURADO//
				"tpl_dir"       =>$_SERVER["DOCUMENT_ROOT"] ."/views/email/",
				"cache_dir"     =>$_SERVER["DOCUMENT_ROOT"] ."/views-cache/",
				"debug"         => false // trás alguns comentarios e etc
				   );

		Tpl::configure( $config );


		$tpl = new Tpl;


		foreach ($data as $key => $value) {
			
			$tpl->assign($key , $value);
		}


		$html = $tpl->draw($tplName, true);

		//Import PHPMailer classes into the global namespace
//use PHPMailer\PHPMailer\PHPMailer;

//Create a new PHPMailer instance
$this->mail = new \PHPMailer;

//Tell PHPMailer to use SMTP
$this->mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$this->mail->SMTPDebug = 0;

//Set the hostname of the mail server
$this->mail->Host = 'smtp.gmail.com';
// use
// $this->mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$this->mail->Port = 587;

/////////////Bloco add/////////////////////// 20/07/2018
/*$this->mail->isSMTP();
$this->mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

*/
/////////////////////////////////////////////////////////////////////////
//Set the encryption system to use - ssl (deprecated) or tls
$this->mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$this->mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
$this->mail->Username = Mailer::USERNAME;

//Password to use for SMTP authentication
$this->mail->Password = Mailer::PASSWORD;

//Set who the message is to be sent from
$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

//Set an alternative reply-to address
//$this->mail->addReplyTo('', 'First Last');

//Set who the message is to be sent to
$this->mail->addAddress($toAddress, $toName);

//Set the subject line
$this->mail->Subject = $subject;

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body 'contents.html', não existe e precisa ser criada//
$this->mail->msgHTML($html);

//Replace the plain text body with one created manually, se o html não funcionar, faz aqui as mensagens e etc
$this->mail->AltBody = 'HTML NÂO FUNCIONOU';

//Attach an image file. se quiser adicionar anexos
//$this->mail->addAttachment('images/phpmailer_mini.png');

	}

	public function send(){

 		return $this->mail->send();


	}

}

?>
