<?php
include 'excel_reader/excel_reader.php';
include 'PHPMailer/PHPMailerAutoload.php';
function readExcel($conf_fp){
	$excel = new PhpExcelReader;
	$excel->read($conf_fp);
	return $excel->sheets;
}

function sendEmail($address, $subject, $message) {
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->SMTPDebug = 0; //2 for both client and server side response
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;

    $mail->addAddress($address, '');//receiver's information
    $mail->Subject = $subject;//subject of the email
    $html_Message = nl2br($message);
    $mail->msgHTML($html_Message);
    $mail->AltBody = $message;
    //$mail->addAttachment('images/logo.png');//some attachment

    $mail->Username = "INPUT EMAIL ACCOUNT";//sender's gmail address
    $mail->Password = "INPUT EMAIL PASSWORD";//sender's password
    $mail->setFrom($mail->Username, '');//sender's incormation
    //$mail->addReplyTo('myanotheremail@gmail.com', 'Barack Obama');//if alternative reply to address is being used

    if (!$mail->send()) {
    	echo 'Message could not be sent.';
    	echo 'Mailer Error: ' . $mail->ErrorInfo;
        return false; //not sent
    } else {
    	echo sprintf("Email sent to %s\n", $address);
        return true; //sent
    }
}

class ConsoleQuestion
{
    function readline()
    {
        return strtolower(rtrim(fgets(STDIN)));
    }
}


parse_str(implode('&', array_slice($argv, 1)), $_GET);
$len_argv = count($_GET);
$GOOD_TO_SEND = false;
if (array_key_exists('template', $_GET) && array_key_exists('conf', $_GET)){
	$template_fp = $_GET['template'];
	$conf_fp = $_GET['conf'];
	if (!file_exists($template_fp)){
		echo sprintf("File %s not exists\n", $template_fp);
		exit();		
	}
	if (!file_exists($conf_fp)){
		echo sprintf("File %s not exists\n", $conf_fp);
		exit();
	}
		
	$template = file_get_contents($template_fp);
	$excelContent = readExcel($conf_fp)[0];
	$firstRow = reset($excelContent['cells']);
	$firstRowLineNum = key($excelContent['cells']);
	if (!in_array('Address', $firstRow)){
		echo "Please have Address in the first column";
		exit();
	}
	if (!in_array('Subject', $firstRow)){
		echo "Please have Subject in the second column";
		exit();
	}

	foreach ($excelContent['cells'] as $rowIndex => $row){
		if ($rowIndex === $firstRowLineNum){
			continue;
		}
		$message = $template;
		$address = "";
		$subject = "";

		foreach ($row as $cellIndex => $cell){
			$para = $excelContent['cells'][$firstRowLineNum][$cellIndex];
			$val = $excelContent['cells'][$rowIndex][$cellIndex];
			if (strpos($para, "#") === 0){
				$message = str_replace($para, $val, $message);
			}else{
				if (strcmp($excelContent['cells'][$firstRowLineNum][$cellIndex], "Address") === 0){
					$address = $excelContent['cells'][$rowIndex][$cellIndex];
				}else if (strcmp($excelContent['cells'][$firstRowLineNum][$cellIndex], "Subject") === 0){
					$subject = $excelContent['cells'][$rowIndex][$cellIndex];
				}					
			}
		}
		if(!$GOOD_TO_SEND){
			$line = new ConsoleQuestion();
			echo sprintf("Is the email good to go? (Y/N)\n%s\n", $message);
			$answer = $line->readline();
			if (strcmp($answer, "y") === 0){
				$GOOD_TO_SEND = true;
			}else{
				exit();
			}
		}

		$message = wordwrap($message, 70, "\r\n");
		sendEmail($address, $subject, $message);
		
	}
}else{
	echo "php -f script.php template=\"template-file-path\" conf=\"onfiguration-file-path\"\n";
}
exit();

?>