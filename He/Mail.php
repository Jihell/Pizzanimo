<?php

/**
 * Description of Mail
 *
 * @author joseph lemoine - joseph.lemoine@fluedis.com
 */
namespace He;

class Mail
{
	/**
	 * Liste des adress où envoyer le mail en BCC
	 *
	 * @var array
	 */
	protected static $_adressListBCC = array();
	
	/**
	 * Liste des adress où envoyer le mail en CC
	 *
	 * @var array
	 */
	protected static $_adressListCC = array();
	
	/**
	 * Liste des adress où envoyer le mail
	 *
	 * @var array
	 */
	protected static $_adressList = array();
	
	/**
	 * Envoi un mail de confirmation
	 * Passez $addContact à vrai pour envoyer le mail a la personne qui 
	 * s'inscrit également.
	 * Le premier contact de $adressList est envoyé en normal, les autre en BCC
	 * $addContact prend la valeur du mail du contact
	 *
	 * @param string $templatePath
	 * @param array $varList
	 * @param boolean $addContact 
	 */	
	public static function send($templatePath, $varList = array(), $addContact = false)
	{
		if(file_exists($templatePath)) {
			\He\Trace::addTrace('Envoi du mail de confirmation', get_called_class(), 1);
			
			// Ajout de quelques variables globale pour le mail
			$varList['#SERVER_NAME#'] = SERVER_NAME;
			$varList['#SERVER_ADDRESS#'] = $_SERVER['SERVER_NAME']; // Aheum, choix judicieu pour le nom :p
			
			// Récupération du template et localisation
			$msg = file_get_contents($templatePath);
			$msg = str_replace(array_keys($varList), array_values($varList), $msg);
			
			$title = EMAIL_TITLE;
			\Localise::run($msg);
			\Localise::run($title);
			
			$mail = new \PHPMailer();
			$mail->From = EMAIL_FROM;
			$mail->FromName = EMAIL_SENDER_NAME;
			$mail->IsMail();
			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';
			$mail->Subject = $title;
			$mail->Body = $msg;
			
			// Ajout des adresses en bcc
			if(count(static::$_adressListBCC)) {
				foreach(static::$_adressListBCC AS $address) {
					$mail->AddBCC($address);
				}
			}
			
			// ajout des adresses en CC
			if(count(static::$_adressListCC)) {
				foreach(static::$_adressListCC AS $address) {
					$mail->AddCC($address);
				}
			}
			
			// Ajoute des adresses
			if(count(static::$_adressList)) {
				foreach(static::$_adressList AS $address) {
					$mail->AddAddress($address);
				}
			}
			
			// Ajout du contact le cas échéant
			if($addContact) {
				$mail->AddAddress($addContact);
			}
			
			return $mail->Send();
		} else {
			throw new Exception('Le template mail '.$templatePath.' est introuvable !');
		}
	}
	
	/**
	 * Ajoute une adresse en BCC
	 *
	 * @param string $address 
	 */
	public static function addBCC($address)
	{
		if(preg_match(REGEX_MAIL, $address)) {
			static::$_adressListBCC[] = $address;
		} else {
			throw new Exception('Adresse mail invalide : '.$address);
		}
	}
	
	/**
	 * Ajoute une adresse en CC
	 *
	 * @param string $address 
	 */
	public static function addCC($address)
	{
		if(preg_match(REGEX_MAIL, $address)) {
			static::$_adressListCC[] = $address;
		} else {
			throw new Exception('Adresse mail invalide : '.$address);
		}
	}
	
	/**
	 * Ajoute une adresse
	 *
	 * @param string $address 
	 */
	public static function addAddress($address)
	{
		if(preg_match(REGEX_MAIL, $address)) {
			static::$_adressList[] = $address;
		} else {
			throw new Exception('Adresse mail invalide : '.$address);
		}
	}
}