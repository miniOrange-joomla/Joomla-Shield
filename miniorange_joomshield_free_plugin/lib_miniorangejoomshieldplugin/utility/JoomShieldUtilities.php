<?php

/**
 * @package    Joomla.Plugin
 * @subpackage lib_miniorangejoomshieldplugin
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Environment\Browser;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomshield' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_networksecurity_utility.php';

class JoomShieldUtilities
{
	public static function isNetworkRegistered($tableName)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName($tableName));
		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}


	public static function getClientIp()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return $_SERVER['REMOTE_ADDR'];
	}

	public static function getLoginRequestUrl()
	{
		$uri = Uri::getInstance();

		return $uri->toString();
	}

	public static function getLoginReportSiteUrl($record)
	{
		$url = trim($record['url'] ?? '');

		if (!empty($url))
		{
			return $url;
		}

		$root = rtrim(Uri::root(), '/');

		if (($record['isadmin_user'] ?? '') === 'Admin Login Page')
		{
			return $root . '/administrator/index.php';
		}

		return $root . '/index.php';
	}

	public static function addTransactionDetails($ipAddress, $username, $type, $status, $url = null)
	{
		$url = is_null($url) ? '' : $url;

		$currentTime = time();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('ip_address') . ' = ' . $db->quote($ipAddress),
			$db->quoteName('username') . ' = ' . $db->quote($username),
			$db->quoteName('type') . ' = ' . $db->quote($type),
			$db->quoteName('status') . ' = ' . $db->quote($status),
			$db->quoteName('created_timestamp') . ' = ' . $db->quote($currentTime),
			$db->quoteName('url') . ' = ' . $db->quote($url),
		);

		$query->insert($db->quoteName('#__miniorange_login_transactions'))->set($fields);
		$db->setQuery($query);
		$db->execute();
	}

	public static function updateTransactionTable($ipAddress)
	{
		$status = "pastfailed";
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('ip_address') . ' = ' . $db->quote($ipAddress),
			$db->quoteName('status') . ' = ' . $db->quote($status),
		);

		$query->update($db->quoteName('#__miniorange_login_transactions'))->set($fields)->where($db->quoteName('ip_address') . ' = ' . $db->quote($ipAddress) && ($db->quoteName('status') . '=failed'));
		$db->setQuery($query);
		$db->execute();

	}

	public static function getUserCredentials($username)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id,password')
			->from($db->quoteName('#__users'))
			->where($db->quoteName('username') . ' = ' . $db->quote($username));
		$db->setQuery($query);

		return $db->loadObject();
	}

	public static function getUserEmail($username)
	{
		if (empty($username))
		{
			return '';
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('email')
			->from($db->quoteName('#__users'))
			->where($db->quoteName('username') . ' = ' . $db->quote($username));
		$db->setQuery($query);
		$email = $db->loadResult();

		return $email ?? '';
	}

	public static function getLoginSecurityConfig()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'));
		$query->where($db->quoteName('id') . " = 1");
		$db->setQuery($query);
		$config = $db->loadAssoc();

		return $config;
	}

	public static function getRegisterSecurityConfig()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_jnsp_registersecurity_setup'));
		$query->where($db->quoteName('id') . " = 1");
		$db->setQuery($query);
		$config = $db->loadAssoc();

		return $config;
	}

	public static function clearIplookup()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('mo_ip_lookup_values') . ' = ' . $db->quote(''),
		);

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . ' = 1'
		);

		$query->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();

	}

	public static function getAllAttemptsCount()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_login_transactions'));
		$db->setQuery($query);
		$config = $db->loadAssocList();

		return $config;
	}

	public static function customRedirect($message, $status)
	{
		$app = Factory::getApplication();
		$app->enqueueMessage($message, $status);
		$app->redirect(Route::_('index.php?option=com_joomshield&tab=ip_blocking'));
	}

	public static function customRedirectUrl($url, $message = null, $status = null)
	{
		$app = Factory::getApplication();
		$app->enqueueMessage($message, $status);
		$app->redirect($url);
	}

	public static function isValidEmail($emailId)
	{
		$extraDomains = self::getValuesFromDb();

		$extraDomains = $extraDomains['mo_email_domains'] ?? '';
		$extraDomains = explode(';', $extraDomains);
		$uEmail = explode("@", $emailId);

		$domains = array('0-mail.com', '20email.eu', '0815.ru', '0815.su', '0clickemail.com', '0sg.net', '0wnd.net', '0wnd.org', '10mail.org', '10minutemail.cf', '10minutemail.com', '10minutemail.de', '10minutemail.ga', '10minutemail.gq', '10minutemail.ml', '123-m.com', '12hourmail.com', '12minutemail.com', '1ce.us', '1chuan.com', '1mail.ml', '1pad.de', '1zhuan.com', '20mail.in', '20mail.it', '20minutemail.com', '21cn.com', '24hourmail.com', '2prong.com', '30minutemail.com', '30minutesmail.com', '3126.com', '33mail.com', '3d-painting.com', '3mail.ga', '4mail.cf', '4mail.ga', '4warding.com', '4warding.net', '4warding.org', '50e.info', '5mail.cf', '5mail.ga', '60minutemail.com', '675hosting.com', '675hosting.net', '675hosting.org', '6ip.us', '6mail.cf', '6mail.ga', '6mail.ml', '6paq.com', '6url.com', '75hosting.com', '75hosting.net', '75hosting.org', '7days-printing.com', '7mail.ga', '7mail.ml', '7tags.com', '8mail.cf', '8mail.ga', '8mail.ml', '99experts.com', '9mail.cf', '9ox.net', 'BeefMilk.com', 'DingBone.com', 'FudgeRub.com', 'LookUgly.com', 'MailScrap.com', 'SmellFear.com', 'TempEmail.net', 'a-bc.net', 'a45.in', 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk.com', 'abusemail.de', 'abwesend.de', 'abyssmail.com', 'ac20mail.in', 'acentri.com', 'addcom.de', 'advantimo.com', 'afrobacon.com', 'ag.us.to', 'agedmail.com', 'agnitumhost.net', 'ahk.jp', 'ajaxapp.net', 'alivance.com', 'alpenjodel.de', 'alphafrau.de', 'amail.com', 'amilegit.com', 'amiri.net', 'amiriindustries.com', 'amorki.pl', 'anappthat.com', 'ano-mail.net', 'anonbox.net', 'anonymail.dk', 'anonymbox.com', 'antichef.com', 'antichef.net', 'antispam.de', 'antispam24.de', 'appixie.com', 'armyspy.com', 'asdasd.nl', 'autosfromus.com', 'aver.com', 'azmeil.tk', 'baldmama.de', 'baldpapa.de', 'ballyfinance.com', 'baxomale.ht.cx', 'beddly.com', 'beefmilk.com', 'betriebsdirektor.de', 'big1.us', 'bigmir.net', 'bigprofessor.so', 'bigstring.com', 'bin-wieder-da.de', 'binkmail.com', 'bio-muesli.info', 'bio-muesli.net', 'bladesmail.net', 'bleib-bei-mir.de', 'blockfilter.com', 'blogmyway.org', 'bluebottle.com', 'bobmail.info', 'bodhi.lawlita.com', 'bofthew.com', 'bonbon.net', 'bootybay.de', 'boun.cr', 'bouncr.com', 'boxformail.in', 'boxtemp.com.br', 'brefmail.com', 'brennendesreich.de', 'briefemail.com', 'broadbandninja.com', 'brokenvalve.com', 'brokenvalve.org', 'bsnow.net', 'bspamfree.org', 'bu.mintemail.com', 'buerotiger.de', 'buffemail.com', 'bugmenot.com', 'bumpymail.com', 'bund.us', 'bundes-li.ga', 'burnthespam.info', 'burstmail.info', 'buy-24h.net.ru', 'buyusedlibrarybooks.org', 'c2.hu', 'cachedot.net', 'cashette.com', 'casualdx.com', 'cbair.com', 'ce.mintemail.com', 'cellurl.com', 'center-mail.de', 'centermail.at', 'centermail.ch', 'centermail.com', 'centermail.de', 'centermail.info', 'centermail.net', 'cghost.s-a-d.de', 'chammy.info', 'cheatmail.de', 'chogmail.com', 'choicemail1.com', 'chong-mail.com', 'chong-mail.net', 'chong-mail.org', 'chongsoft.org', 'clixser.com', 'cmail.com', 'cmail.net', 'cmail.org', 'coldemail.info', 'consumerriot.com', 'cool.fr.nf', 'coole-files.de', 'correo.blogos.net', 'cosmorph.com', 'courriel.fr.nf', 'courrieltemporaire.com', 'crapmail.org', 'crazespaces.pw', 'crazymailing.com', 'cubiclink.com', 'curryworld.de', 'cust.in', 'cuvox.de', 'cyber-matrix.com', 'dacoolest.com', 'daintly.com', 'dandikmail.com', 'dating4best.net', 'dayrep.com', 'dbunker.com', 'dcemail.com', 'deadaddress.com', 'deadchildren.org', 'deadfake.cf', 'deadfake.ga', 'deadfake.ml', 'deadfake.tk', 'deadspam.com', 'deagot.com', 'dealja.com', 'despam.it', 'despammed.com', 'devnullmail.com', 'dfgh.net', 'dharmatel.net', 'die-besten-bilder.de', 'die-genossen.de', 'die-optimisten.de', 'die-optimisten.net', 'dieMailbox.de', 'digital-filestore.de', 'digitalsanctuary.com', 'dingbone.com', 'directbox.com', 'discard.cf', 'discard.email', 'discard.ga', 'discard.gq', 'discard.ml', 'discard.tk', 'discardmail.*', 'discardmail.com', 'discardmail.de', 'discartmail.com', 'disposable-email.ml', 'disposable.cf', 'disposable.ga', 'disposable.ml', 'disposableaddress.com', 'disposableemailaddresses.com', 'disposableemailaddresses.emailmiser.com', 'disposableinbox.com', 'dispose.it', 'disposeamail.com', 'disposemail.com', 'dispostable.com', 'divermail.com', 'dm.w3internet.co.uk', 'example.com', 'docmail.cz', 'dodgeit.com', 'dodgit.com', 'dodgit.org', 'dogit.com', 'doiea.com', 'domozmail.com', 'donemail.ru', 'dontreg.com', 'dontsendmespam.de', 'dontsentmespam.de', 'dotmsg.com', 'download-privat.de', 'drdrb.com', 'drdrb.net', 'droplar.com', 'dropmail.me', 'duam.net', 'dudmail.com', 'dump-email.info', 'dumpandjunk.com', 'dumpmail.com', 'dumpmail.de', 'dumpyemail.com', 'duskmail.com', 'dyndns.org', 'e-mail.com', 'e-mail.org', 'e4ward.com', 'easytrashmail.com', 'ee2.pl', 'eelmail.com', 'einrot.com', 'einrot.de', 'eintagsmail.de', 'email-fake.cf', 'email-fake.ga', 'email-fake.gq', 'email-fake.ml', 'email-fake.tk', 'email.org', 'email4u.info', 'email60.com', 'emailage.cf', 'emailage.ga', 'emailage.gq', 'emailage.ml', 'emailage.tk', 'emaildienst.de', 'emailgo.de', 'emailias.com', 'emailigo.de', 'emailinfive.com', 'emailisvalid.com', 'emaillime.com', 'emailmiser.com', 'emailproxsy.com', 'emails.ga', 'emailsensei.com', 'emailspam.cf', 'emailspam.ga', 'emailspam.gq', 'emailspam.ml', 'emailspam.tk', 'emailtaxi.de', 'emailtemporanea.net', 'emailtemporar.ro', 'emailtemporario.com.br', 'emailthe.net', 'emailtmp.com', 'emailto.de', 'emailwarden.com', 'emailx.at.hm', 'emailxfer.com', 'emailz.cf', 'emailz.ga', 'emailz.gq', 'emailz.ml', 'emeil.in', 'emeil.ir', 'emil.com', 'emkei.cf', 'emkei.ga', 'emkei.gq', 'emkei.ml', 'emkei.tk', 'emz.net', 'enterto.com', 'ephemail.net', 'etranquil.com', 'etranquil.net', 'etranquil.org', 'evopo.com', 'example.com', 'explodemail.com', 'eyepaste.com', 'facebook-email.cf', 'facebook-email.ga', 'facebook-email.ml', 'facebookmail.gq', 'facebookmail.ml', 'fahr-zur-hoelle.org', 'fake-mail.cf', 'fake-mail.ga', 'fake-mail.ml', 'fakeinbox.cf', 'fakeinbox.com', 'fakeinbox.ga', 'fakeinbox.ml', 'fakeinbox.tk', 'fakeinformation.com', 'fakemail.fr', 'fakemailgenerator.com', 'fakemailz.com', 'falseaddress.com', 'fammix.com', 'fansworldwide.de', 'fantasymail.de', 'farifluset.mailexpire.com', 'fastacura.com', 'fastchevy.com', 'fastchrysler.com', 'fastkawasaki.com', 'fastmazda.com', 'fastmitsubishi.com', 'fastnissan.com', 'fastsubaru.com', 'fastsuzuki.com', 'fasttoyota.com', 'fastyamaha.com', 'fatflap.com', 'fdfdsfds.com', 'feinripptraeger.de', 'fettabernett.de', 'fightallspam.com', 'fiifke.de', 'filzmail.com', 'fishfuse.com', 'fixmail.tk', 'fizmail.com', 'fleckens.hu', 'flurred.com', 'flyspam.com', 'footard.com', 'forgetmail.com', 'fornow.eu', 'fr33mail.info', 'frapmail.com', 'free-email.cf', 'free-email.ga', 'freemail.ms', 'freemails.cf', 'freemails.ga', 'freemails.ml', 'freemeilaadressforall.net', 'freudenkinder.de', 'freundin.ru', 'friendlymail.co.uk', 'fromru.com', 'front14.org', 'fuckingduh.com', 'fudgerub.com', 'fux0ringduh.com', 'garliclife.com', 'gawab.com', 'gelitik.in', 'gentlemansclub.de', 'get-mail.cf', 'get-mail.ga', 'get-mail.ml', 'get-mail.tk', 'get1mail.com', 'get2mail.fr', 'getairmail.cf', 'getairmail.com', 'getairmail.ga', 'getairmail.gq', 'getairmail.ml', 'getairmail.tk', 'getmails.eu', 'getonemail.com', 'getonemail.net', 'ghosttexter.de', 'girlsundertheinfluence.com', 'gishpuppy.com', 'goemailgo.com', 'gold-profits.info', 'goldtoolbox.com', 'golfilla.info', 'gorillaswithdirtyarmpits.com', 'gotmail.com', 'gotmail.net', 'gotmail.org', 'gotti.otherinbox.com', 'gowikibooks.com', 'gowikicampus.com', 'gowikicars.com', 'gowikifilms.com', 'gowikigames.com', 'gowikimusic.com', 'gowikinetwork.com', 'gowikitravel.com', 'gowikitv.com', 'grandmamail.com', 'grandmasmail.com', 'great-host.in', 'greensloth.com', 'grr.la', 'gsrv.co.uk', 'guerillamail.biz', 'guerillamail.com', 'guerillamail.net', 'guerillamail.org', 'guerrillamail.biz', 'guerrillamail.com', 'guerrillamail.de', 'guerrillamail.info', 'guerrillamail.net', 'guerrillamail.org', 'guerrillamailblock.com', 'gustr.com', 'h.mintemail.com', 'h8s.org', 'hab-verschlafen.de', 'habmalnefrage.de', 'hacccc.com', 'haltospam.com', 'harakirimail.com', 'hartbot.de', 'hatespam.org', 'hellodream.mobi', 'herp.in', 'herr-der-mails.de', 'hidemail.de', 'hidzz.com', 'hmamail.com', 'hochsitze.com', 'home.de', 'hopemail.biz', 'hot-mail.cf', 'hot-mail.ga', 'hot-mail.gq', 'hot-mail.ml', 'hot-mail.tk', 'hotpop.com', 'hulapla.de', 'humn.ws.gy', 'hush.com', 'hushmail.com', 'ich-bin-verrueckt-nach-dir.de', 'ich-will-net.de', 'ieatspam.eu', 'ieatspam.info', 'ieh-mail.de', 'ihateyoualot.info', 'iheartspam.org', 'ikbenspamvrij.nl', 'imails.info', 'imgof.com', 'imstations.com', 'inbax.tk', 'inbox.si', 'inbox2.info', 'inboxalias.com', 'inboxclean.com', 'inboxclean.org', 'inboxproxy.com', 'incognitomail.com', 'incognitomail.net', 'incognitomail.org', 'inerted.com', 'inmail24.com', 'insorg-mail.info', 'instant-mail.de', 'instantemailaddress.com', 'ipoo.org', 'irish2me.com', 'iroid.com', 'ist-allein.info', 'ist-einmalig.de', 'ist-ganz-allein.de', 'ist-willig.de', 'iwi.net', 'izmail.net', 'jetable.com', 'jetable.de', 'jetable.fr.nf', 'jetable.net', 'jetable.org', 'jetfix.ee', 'jetzt-bin-ich-dran.com', 'jn-club.de', 'jnxjn.com', 'jobbikszimpatizans.hu', 'jourrapide.com', 'jsrsolutions.com', 'junk1e.com', 'junkmail.com', 'junkmail.ga', 'junkmail.gq', 'kaffeeschluerfer.com', 'kaffeeschluerfer.de', 'kasmail.com', 'kaspop.com', 'keepmymail.com', 'killmail.com', 'killmail.net', 'kimsdisk.com', 'kinglibrary.net', 'kingsq.ga', 'kir.ch.tc', 'klassmaster.com', 'klassmaster.net', 'klzlk.com', 'kommespaeter.de', 'kook.ml', 'koszmail.pl', 'krim.ws', 'kuh.mu', 'kulturbetrieb.info', 'kurzepost.de', 'l33r.eu', 'labetteraverouge.at', 'lackmail.net', 'lags.us', 'landmail.co', 'lass-es-geschehen.de', 'lastmail.co', 'lastmail.com', 'lazyinbox.com', 'letthemeatspam.com', 'lhsdv.com', 'liebt-dich.info', 'lifebyfood.com', 'link2mail.net', 'listomail.com', 'litedrop.com', 'loadby.us', 'login-email.cf', 'login-email.ga', 'login-email.ml', 'login-email.tk', 'lol.ovpn.to', 'lookugly.com', 'lopl.co.cc', 'lortemail.dk', 'lovemeleaveme.com', 'loveyouforever.de', 'lr7.us', 'lr78.com', 'lroid.com', 'luv2.us', 'm4ilweb.info', 'maboard.com', 'maennerversteherin.com', 'maennerversteherin.de', 'mail-filter.com', 'mail-temporaire.fr', 'mail.by', 'mail.htl22.at', 'mail.mezimages.net', 'mail.misterpinball.de', 'mail.svenz.eu', 'mail114.net', 'mail15.com', 'mail2rss.org', 'mail333.com', 'mail4days.com', 'mail4trash.com', 'mail4u.info', 'mailbidon.com', 'mailblocks.com', 'mailbucket.org', 'mailcat.biz', 'mailcatch.*', 'mailcatch.com', 'maildrop.cc', 'maildrop.cf', 'maildrop.ga', 'maildrop.gq', 'maildrop.ml', 'maildx.com', 'maileater.com', 'mailexpire.com', 'mailfa.tk', 'mailforspam.com', 'mailfree.ga', 'mailfree.gq', 'mailfree.ml', 'mailfreeonline.com', 'mailfs.com', 'mailguard.me', 'mailimate.com', 'mailin8r.com', 'mailinater.com', 'mailinator.com', 'mailinator.gq', 'mailinator.net', 'mailinator.org', 'mailinator.us', 'mailinator2.com', 'mailinblack.com', 'mailincubator.com', 'mailismagic.com', 'mailjunk.cf', 'mailjunk.ga', 'mailjunk.gq', 'mailjunk.ml', 'mailjunk.tk', 'mailmate.com', 'mailme.gq', 'mailme.ir', 'mailme.lv', 'mailme24.com', 'mailmetrash.com', 'mailmoat.com', 'mailnator.com', 'mailnesia.com', 'mailnull.com', 'mailpick.biz', 'mailproxsy.com', 'mailquack.com', 'mailrock.biz', 'mailsac.com', 'mailscrap.com', 'mailseal.de', 'mailshell.com', 'mailsiphon.com', 'mailslapping.com', 'mailslite.com', 'mailtemp.info', 'mailtothis.com', 'mailtrash.net', 'mailueberfall.de', 'mailzilla.com', 'mailzilla.org', 'mailzilla.orgmbx.cc', 'makemetheking.com', 'mamber.net', 'manifestgenerator.com', 'manybrain.com', 'mbx.cc', 'mciek.com', 'mega.zik.dj', 'meine-dateien.info', 'meine-diashow.de', 'meine-fotos.info', 'meine-urlaubsfotos.de', 'meinspamschutz.de', 'meltmail.com', 'messagebeamer.de', 'metaping.com', 'mezimages.net', 'mfsa.ru', 'mierdamail.com', 'migumail.com', 'mintemail.com', 'mjukglass.nu', 'mns.ru', 'moakt.com', 'mobi.web.id', 'mobileninja.co.uk', 'moburl.com', 'mohmal.com', 'moncourrier.fr.nf', 'monemail.fr.nf', 'monmail.fr.nf', 'monumentmail.com', 'ms9.mailslite.com', 'msa.minsmail.com', 'msh.mailslite.com', 'mt2009.com', 'mt2014.com', 'mufmail.com', 'muskelshirt.de', 'mx0.wwwnew.eu', 'my-mail.ch', 'my10minutemail.com', 'myadult.info', 'mycleaninbox.net', 'myemailboxy.com', 'mymail-in.net', 'mymailoasis.com', 'mynetstore.de', 'mypacks.net', 'mypartyclip.de', 'myphantomemail.com', 'myspaceinc.com', 'myspaceinc.net', 'myspaceinc.org', 'myspacepimpedup.com', 'myspamless.com', 'mytemp.email', 'mytempemail.com', 'mytop-in.net', 'mytrashmail.com', 'mytrashmail.compookmail.com', 'neomailbox.com', 'nepwk.com', 'nervmich.net', 'nervtmich.net', 'netmails.com', 'netmails.net', 'netterchef.de', 'netzidiot.de', 'neue-dateien.de', 'neverbox.com', 'nice-4u.com', 'nmail.cf', 'no-spam.ws', 'nobulk.com', 'noclickemail.com', 'nogmailspam.info', 'nomail.xl.cx', 'nomail2me.com', 'nomorespamemails.com', 'nonspam.eu', 'nonspammer.de', 'noref.in', 'nospam.wins.com.br', 'nospam.ze.tc', 'nospam4.us', 'nospamfor.us', 'nospammail.net', 'nospamthanks.info', 'notmailinator.com', 'notsharingmy.info', 'nowhere.org', 'nowmymail.com', 'ntlhelp.net', 'nullbox.info', 'nur-fuer-spam.de', 'nurfuerspam.de', 'nus.edu.sg', 'nwldx.com', 'nybella.com', 'objectmail.com', 'obobbo.com', 'odaymail.com', 'office-dateien.de', 'oikrach.com', 'one-time.email', 'oneoffemail.com', 'oneoffmail.com', 'onewaymail.com', 'online.ms', 'oopi.org', 'opayq.com', 'orangatango.com', 'ordinaryamerican.net', 'otherinbox.com', 'ourklips.com', 'outlawspam.com', 'ovpn.to', 'owlpic.com', 'pancakemail.com', 'paplease.com', 'partybombe.de', 'partyheld.de', 'pcusers.otherinbox.com', 'pepbot.com', 'pfui.ru', 'phreaker.net', 'pimpedupmyspace.com', 'pisem.net', 'pjjkp.com', 'pleasedontsendmespam.de', 'plexolan.de', 'poczta.onet.pl', 'politikerclub.de', 'polizisten-duzer.de', 'poofy.org', 'pookmail.com', 'pornobilder-mal-gratis.com', 'portsaid.cc', 'postacin.com', 'postfach.cc', 'privacy.net', 'privy-mail.com', 'privymail.de', 'proxymail.eu', 'prtnx.com', 'prtz.eu', 'prydirect.info', 'pryworld.info', 'public-files.de', 'punkass.com', 'put2.net', 'putthisinyourspamdatabase.com', 'pwrby.com', 'qasti.com', 'qisdo.com', 'qisoa.com', 'qq.com', 'quantentunnel.de', 'quickinbox.com', 'quickmail.nl', 'qv7.info', 'radiku.ye.vc', 'ralib.com', 'raubtierbaendiger.de', 'rcpt.at', 'reallymymail.com', 'receiveee.chickenkiller.com', 'receiveee.com', 'recode.me', 'reconmail.com', 'record.me', 'recursor.net', 'recyclemail.dk', 'regbypass.com', 'regbypass.comsafe-mail.net', 'rejectmail.com', 'remail.cf', 'remail.ga', 'rhyta.com', 'rk9.chickenkiller.com', 'rklips.com', 'rmqkr.net', 'rootprompt.org', 'royal.net', 'rppkn.com', 'rtrtr.com', 'ruffrey.com', 's0ny.net', 'saeuferleber.de', 'safe-mail.net', 'safersignup.de', 'safetymail.info', 'safetypost.de', 'sags-per-mail.de', 'sandelf.de', 'satka.net', 'saynotospams.com', 'scatmail.com', 'schafmail.de', 'schmusemail.de', 'schreib-doch-mal-wieder.de', 'selfdestructingmail.com', 'selfdestructingmail.org', 'sendspamhere.com', 'senseless-entertainment.com', 'shared-files.de', 'sharedmailbox.org', 'sharklasers.com', 'shieldedmail.com', 'shiftmail.com', 'shinedyoureyes.com', 'shitmail.me', 'shitmail.org', 'shitware.nl', 'shortmail.net', 'showslow.de', 'sibmail.com', 'sinnlos-mail.de', 'siria.cc', 'siteposter.net', 'skeefmail.com', 'skeefmail.net', 'slaskpost.se', 'slave-auctions.net', 'slopsbox.com', 'slushmail.com', 'smashmail.de', 'smellfear.com', 'smellrear.com', 'sms.at', 'snakemail.com', 'sneakemail.com', 'snkmail.com', 'sofimail.com', 'sofort-mail.de', 'sofortmail.de', 'softpls.asia', 'sogetthis.com', 'sohu.com', 'soisz.com', 'solvemail.info', 'sonnenkinder.org', 'soodomail.com', 'soodonims.com', 'spam-be-gone.com', 'spam.la', 'spam.su', 'spam4.me', 'spamavert.com', 'spambob.com', 'spambob.net', 'spambob.org', 'spambog.*', 'spambog.com', 'spambog.de', 'spambog.net', 'spambog.ru', 'spambooger.com', 'spambox.info', 'spambox.irishspringrealty.com', 'spambox.us', 'spamcannon.com', 'spamcannon.net', 'spamcero.com', 'spamcon.org', 'spamcorptastic.com', 'spamcowboy.com', 'spamcowboy.net', 'spamcowboy.org', 'spamday.com', 'spamdecoy.net', 'spameater.com', 'spameater.org', 'spamex.com', 'spamfighter.cf', 'spamfighter.ga', 'spamfighter.gq', 'spamfighter.ml', 'spamfighter.tk', 'spamfree.eu', 'spamfree24.com', 'spamfree24.de', 'spamfree24.eu', 'spamfree24.info', 'spamfree24.net', 'spamfree24.org', 'spamgoes.in', 'spamgourmet.com', 'spamgourmet.net', 'spamgourmet.org', 'spamgrube.net', 'spamherelots.com', 'spamhereplease.com', 'spamhole.com', 'spamify.com', 'spaminator.de', 'spamkill.info', 'spaml.com', 'spaml.de', 'spammote.com', 'spammotel.com', 'spammuffel.de', 'spamobox.com', 'spamoff.de', 'spamreturn.com', 'spamsalad.in', 'spamslicer.com', 'spamspot.com', 'spamstack.net', 'spamthis.co.uk', 'spamthisplease.com', 'spamtrail.com', 'spamtroll.net', 'speed.1s.fr', 'sperke.net', 'spikio.com', 'spoofmail.de', 'squizzy.de', 'sriaus.com', 'ssoia.com', 'startkeys.com', 'stinkefinger.net', 'stop-my-spam.cf', 'stop-my-spam.com', 'stop-my-spam.ga', 'stop-my-spam.ml', 'stop-my-spam.tk', 'streber24.de', 'streetwisemail.com', 'stuffmail.de', 'super-auswahl.de', 'supergreatmail.com', 'supermailer.jp', 'superrito.com', 'superstachel.de', 'suremail.info', 'svk.jp', 'sweetville.net', 'sweetxxx.de', 'tafmail.com', 'tagesmail.eu', 'tagyourself.com', 'talkinator.com', 'tapchicuoihoi.com', 'teewars.org', 'teleworm.com', 'teleworm.us', 'temp-mail.com', 'temp-mail.org', 'temp.emeraldwebmail.com', 'temp.headstrong.de', 'tempail.com', 'tempalias.com', 'tempe-mail.com', 'tempemail.biz', 'tempemail.co.za', 'tempemail.com', 'tempemail.net', 'tempinbox.co.uk', 'tempinbox.com', 'tempmail.it', 'tempmail.com', 'tempmail2.com', 'tempmaildemo.com', 'tempmailer.com', 'tempomail.fr', 'temporarily.de', 'temporarioemail.com.br', 'temporaryemail.net', 'temporaryemail.us', 'temporaryforwarding.com', 'temporaryinbox.com', 'tempsky.com', 'tempthe.net', 'tempymail.com', 'terminverpennt.de', 'test.com', 'test.de', 'thanksnospam.info', 'thankyou2010.com', 'thecloudindex.com', 'thepryam.info', 'thisisnotmyrealemail.com', 'throam.com', 'throwawayemailaddress.com', 'throwawaymail.com', 'tilien.com', 'tittbit.in', 'tmail.ws', 'tmailinator.com', 'toiea.com', 'toomail.biz', 'topmail-files.de', 'tortenboxer.de', 'totalmail.de', 'tradermail.info', 'trash-amil.com', 'trash-mail.at', 'trash-mail.cf', 'trash-mail.com', 'trash-mail.de', 'trash-mail.ga', 'trash-mail.gq', 'trash-mail.ml', 'trash-mail.tk', 'trash2009.com', 'trash2010.com', 'trash2011.com', 'trashbox.eu', 'trashdevil.com', 'trashdevil.de', 'trashemail.de', 'trashmail.at', 'trashmail.com', 'trashmail.de', 'trashmail.me', 'trashmail.net', 'trashmail.org', 'trashmail.ws', 'trashmailer.com', 'trashymail.com', 'trashymail.net', 'trayna.com', 'trbvm.com', 'trickmail.net', 'trillianpro.com', 'trimix.cn', 'tryalert.com', 'turboprinz.de', 'turboprinzessin.de', 'turual.com', 'twinmail.de', 'twoweirdtricks.com', 'tyldd.com', 'ubismail.net', 'uggsrock.com', 'uk2.net', 'ukr.net', 'umail.net', 'unmail.ru', 'unterderbruecke.de', 'upliftnow.com', 'uplipht.com', 'uroid.com', 'username.e4ward.com', 'valemail.net', 'venompen.com', 'verlass-mich-nicht.de', 'veryrealemail.com', 'vidchart.com', 'viditag.com', 'viewcastmedia.com', 'viewcastmedia.net', 'viewcastmedia.org', 'vinbazar.com', 'vollbio.de', 'volloeko.de', 'vomoto.com', 'vorsicht-bissig.de', 'vorsicht-scharf.de', 'vubby.com', 'walala.org', 'walkmail.net', 'war-im-urlaub.de', 'wbb3.de', 'webemail.me', 'webm4il.info', 'webmail4u.eu', 'webuser.in', 'wee.my', 'weg-werf-email.de', 'wegwerf-email-addressen.de', 'wegwerf-emails.de', 'wegwerfadresse.de', 'wegwerfemail.com', 'wegwerfemail.de', 'wegwerfmail.de', 'wegwerfmail.info', 'wegwerfmail.net', 'wegwerfmail.org', 'wegwerpmailadres.nl', 'weibsvolk.de', 'weibsvolk.org', 'weinenvorglueck.de', 'wetrainbayarea.com', 'wetrainbayarea.org', 'wh4f.org', 'whatiaas.com', 'whatpaas.com', 'whatsaas.com', 'whopy.com', 'whtjddn.33mail.com', 'whyspam.me', 'wickmail.net', 'wilemail.com', 'will-hier-weg.de', 'willhackforfood.biz', 'willselfdestruct.com', 'winemaven.info', 'wir-haben-nachwuchs.de', 'wir-sind-cool.org', 'wirsindcool.de', 'wmail.cf', 'wolke7.net', 'wollan.info', 'women-at-work.org', 'wormseo.cn', 'wronghead.com', 'wuzup.net', 'wuzupmail.net', 'www.e4ward.com', 'www.gishpuppy.com', 'www.mailinator.com', 'wwwnew.eu', 'xagloo.com', 'xemaps.com', 'xents.com', 'xmail.com', 'xmaily.com', 'xoxox.cc', 'xoxy.net', 'xsecurity.org', 'xyzfree.net', 'yapped.net', 'yeah.net', 'yep.it', 'yert.ye.vc', 'yesey.net', 'yogamaven.com', 'yomail.info', 'yopmail.com', 'yopmail.fr', 'yopmail.gq', 'yopmail.net', 'yopweb.com', 'youmail.ga', 'youmailr.com', 'ypmail.webarnak.fr.eu.org', 'ystea.org', 'yuurok.com', 'yzbid.com', 'za.com', 'zehnminutenmail.de', 'zetmail.com', 'zippymail.info', 'zoaxe.com', 'zoemail.com', 'zoemail.net', 'zoemail.org', 'zomg.info', 'zweb.in', 'zxcv.com', 'zxcvbnm.com', 'zzz.com');

		if (count($uEmail) === 2)
		{
			if ($extraDomains != '' || !empty($extraDomains))
			{
				if (in_array(trim($uEmail[1]), $extraDomains))
				{
					return true;
				}
				elseif (in_array(trim($uEmail[1]), $domains))
				{
					return true;
				}
			}
		}

		return false;
	}

	public static function getValuesFromDb()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('mo_email_domains');
		$query->from($db->quoteName('#__miniorange_jnsp_registersecurity_setup'));
		$db->setQuery($query);
		$config = $db->loadAssoc();

		return $config;
	}

	public static function getAllLoginAttemptsCount()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from($db->quoteName('#__miniorange_login_transactions_reports'));
		$query->where($db->quoteName('username') . ' != ' . $db->quote(''));
		$query->where($db->quoteName('ip_address') . ' != ' . $db->quote(''));
		$db->setQuery($query);
		$config = $db->loadResult();

		return $config;
	}

	public static function getLoginTransactionReports()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_login_transactions_reports'));
		$query->where($db->quoteName('username') . ' != ' . $db->quote(''));
		$query->where($db->quoteName('ip_address') . ' != ' . $db->quote(''));
		$query->order($db->quoteName('created_timestamp') . ' DESC');
		$db->setQuery($query);
		$config = $db->loadAssocList();

		return $config;
	}

	public static function getLoginAttemptsCount($limit, $offset, $order = "down")
	{
		$db = Factory::getDbo();
		$temp = [];
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_login_transactions_reports'));
		$query->where($db->quoteName('username') . ' != ' . $db->quote(''));
		$query->where($db->quoteName('ip_address') . ' != ' . $db->quote(''));

		if ($order === "up")
		{
			$query->order($db->quoteName('created_timestamp') . ' ASC');
		}
		else
		{
			$query->order($db->quoteName('created_timestamp') . ' DESC');
		}

		$query->setLimit($limit, $offset);
		$db->setQuery($query);
		$temp[] = $db->loadAssocList();

		return $temp;
	}

	public static function getLoginTransactionReportsVal()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_login_transactions_reports'));
		$db->setQuery($query);
		$attributes = $db->loadAssoc();

		return $attributes;
	}

	public static function addLoginTransactionDetails(
		$ipAddress,
		$username,
		$type,
		$status,
		$isadmin,
		$countryName,
		$browserName,
		$os,
		$url = null,
		$email = '',
		$failureReason = ''
	) {
		$url = is_null($url) ? '' : $url;
		$currentTime = time();

		if (empty($email) && !empty($username))
		{
			$email = self::getUserEmail($username);
		}

		$db = Factory::getDbo();

		$deleteQuery = $db->getQuery(true)
			->delete($db->quoteName('#__miniorange_login_transactions_reports'))
			->where(
				'(' . $db->quoteName('username') . ' = ' . $db->quote('')
				. ' OR ' . $db->quoteName('ip_address') . ' = ' . $db->quote('') . ')'
			);
		$db->setQuery($deleteQuery);
		$db->execute();

		$query = $db->getQuery(true);
		$columns = [
			'ip_address',
			'username',
			'type',
			'status',
			'created_timestamp',
			'url',
			'isadmin_user',
			'country_name',
			'browser_name',
			'operating_system',
			'email',
			'failure_reason',
		];
		$values = [
			$db->quote($ipAddress),
			$db->quote($username),
			$db->quote($type),
			$db->quote($status),
			$db->quote($currentTime),
			$db->quote($url),
			$db->quote($isadmin),
			$db->quote($countryName),
			$db->quote($browserName),
			$db->quote($os),
			$db->quote($email),
			$db->quote($failureReason),
		];

		$query->insert($db->quoteName('#__miniorange_login_transactions_reports'))
			->columns($db->quoteName($columns))
			->values(implode(',', $values));
		$db->setQuery($query);
		$db->execute();

		self::pruneLoginTransactionReports(20);
	}

	public static function pruneLoginTransactionReports($maxEntries = 20)
	{
		$db = Factory::getDbo();
		$countQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__miniorange_login_transactions_reports'))
			->where($db->quoteName('username') . ' != ' . $db->quote(''))
			->where($db->quoteName('ip_address') . ' != ' . $db->quote(''));
		$db->setQuery($countQuery);
		$totalEntries = (int) $db->loadResult();

		if ($totalEntries <= $maxEntries)
		{
			return;
		}

		$idsQuery = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__miniorange_login_transactions_reports'))
			->where($db->quoteName('username') . ' != ' . $db->quote(''))
			->where($db->quoteName('ip_address') . ' != ' . $db->quote(''))
			->order($db->quoteName('created_timestamp') . ' ASC')
			->setLimit($totalEntries - $maxEntries);
		$db->setQuery($idsQuery);
		$idsToDelete = $db->loadColumn();

		if (empty($idsToDelete))
		{
			return;
		}

		$deleteQuery = $db->getQuery(true)
			->delete($db->quoteName('#__miniorange_login_transactions_reports'))
			->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $idsToDelete)) . ')');
		$db->setQuery($deleteQuery);
		$db->execute();
	}

	public static function getCurrentUserBrowser()
	{
		$browser = Browser::getInstance();
		$browser = $browser->getBrowser();

		if ($browser == "edg")
		{
			$browser = "edge";
		}

		return $browser ?? 'something else';
	}

	public static function getCountryName($userIp)
	{
		$result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $userIp), true);

		try
		{
			$timeoffset = timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]), new DateTime('now'));
			$timeoffset = $timeoffset / 3600;
		}
		catch (Exception $e)
		{
			$result["geoplugin_timezone"] = "";
			$timeoffset = "";
		}

		if ($result['geoplugin_request'] == $userIp)
		{
			$ipLookup['Country'] = $result["geoplugin_countryName"];
		}

		$countryName = $ipLookup['Country'];

		return $countryName;
	}

	public static function getOsInfo()
	{

		if (isset($_SERVER))
		{
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		else
		{
			if (isset($GLOBALS['HTTP_SERVER_VARS']))
			{
				$userAgent = $GLOBALS['HTTP_SERVER_VARS']['HTTP_USER_AGENT'];
			}
			else
			{
				$userAgent = $GLOBALS['HTTP_USER_AGENT'] ?? '';
			}
		}

		$osArray = [
			'windows nt 10' => 'Windows 10',
			'windows nt 6.3' => 'Windows 8.1',
			'windows nt 6.2' => 'Windows 8',
			'windows nt 6.1|windows nt 7.0' => 'Windows 7',
			'windows nt 6.0' => 'Windows Vista',
			'windows nt 5.2' => 'Windows Server 2003/XP x64',
			'windows nt 5.1' => 'Windows XP',
			'windows xp' => 'Windows XP',
			'windows nt 5.0|windows nt5.1|windows 2000' => 'Windows 2000',
			'windows me' => 'Windows ME',
			'windows nt 4.0|winnt4.0' => 'Windows NT',
			'windows ce' => 'Windows CE',
			'windows 98|win98' => 'Windows 98',
			'windows 95|win95' => 'Windows 95',
			'win16' => 'Windows 3.11',
			'mac os x 10.1[^0-9]' => 'Mac OS X Puma',
			'macintosh|mac os x' => 'Mac OS X',
			'mac_powerpc' => 'Mac OS 9',
			'linux' => 'Linux',
			'ubuntu' => 'Linux - Ubuntu',
			'iphone' => 'iPhone',
			'ipod' => 'iPod',
			'ipad' => 'iPad',
			'android' => 'Android',
			'blackberry' => 'BlackBerry',
			'webos' => 'Mobile',

			'(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
			'(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
			'(win)([0-9]{2})' => 'Windows',
			'(windows)([0-9x]{2})' => 'Windows',

			/*
			 * Doesn't seem like these are necessary...not totally sure though..
			 * '(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
			 * '(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', fix by bg
			 */

			'Win 9x 4.90' => 'Windows ME',
			'(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
			'win32' => 'Windows',
			'(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
			'(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
			'dos x86' => 'DOS',
			'Mac OS X' => 'Mac OS X',
			'Mac_PowerPC' => 'Macintosh PowerPC',
			'(mac|Macintosh)' => 'Mac OS',
			'(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
			'(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
			'(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
			'unix' => 'Unix',
			'os/2' => 'OS/2',
			'freebsd' => 'FreeBSD',
			'openbsd' => 'OpenBSD',
			'netbsd' => 'NetBSD',
			'irix' => 'IRIX',
			'plan9' => 'Plan9',
			'osf' => 'OSF',
			'aix' => 'AIX',
			'GNU Hurd' => 'GNU Hurd',
			'(fedora)' => 'Linux - Fedora',
			'(kubuntu)' => 'Linux - Kubuntu',
			'(ubuntu)' => 'Linux - Ubuntu',
			'(debian)' => 'Linux - Debian',
			'(CentOS)' => 'Linux - CentOS',
			'(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
			'(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
			'(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
			'(ASPLinux)' => 'Linux - ASPLinux',
			'(Red Hat)' => 'Linux - Red Hat',
			/*
			 * Loads of Linux machines will be detected as unix.
			 * Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
			 * 'X11'=>'Unix',
			 */
			'(linux)' => 'Linux',
			'(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
			'amiga-aweb' => 'AmigaOS',
			'amiga' => 'Amiga',
			'AvantGo' => 'PalmOS',
			/*
			 * '(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
			 * '(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
			 * '(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
			 */
			'[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
			'(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
			'Dreamcast' => 'Dreamcast OS',
			'GetRight' => 'Windows',
			'go!zilla' => 'Windows',
			'gozilla' => 'Windows',
			'gulliver' => 'Windows',
			'ia archiver' => 'Windows',
			'NetPositive' => 'Windows',
			'mass downloader' => 'Windows',
			'microsoft' => 'Windows',
			'offline explorer' => 'Windows',
			'teleport' => 'Windows',
			'web downloader' => 'Windows',
			'webcapture' => 'Windows',
			'webcollage' => 'Windows',
			'webcopier' => 'Windows',
			'webstripper' => 'Windows',
			'webzip' => 'Windows',
			'wget' => 'Windows',
			'Java' => 'Unknown',
			'flashget' => 'Windows',

			/*
			 * Delete next line if the script show not the right OS
			 * '(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
			 */
			'MS FrontPage' => 'Windows',
			'(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
			'(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
			'libwww-perl' => 'Unix',
			'UP.Browser' => 'Windows CE',
			'NetAnts' => 'Windows',
		];

		$archRegex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
		$arch = preg_match($archRegex, $userAgent) ? '64' : '32';

		foreach ($osArray as $regex => $value)
		{
			if (preg_match('{\b(' . $regex . ')\b}i', $userAgent))
			{
				return $value . ' x' . $arch;
			}
		}

		return 'Unknown';
	}

	public static function getUserTimezoneInfo()
	{
		try
		{
			$input = Factory::getApplication()->input;
			$browserTimezone = rawurldecode(trim($input->cookie->getString('mojsp_browser_timezone', '')));
			$timezoneName = in_array($browserTimezone, \DateTimeZone::listIdentifiers(\DateTimeZone::ALL_WITH_BC), true) ? $browserTimezone : '';

			if (empty($timezoneName))
			{
				$timezoneName = Factory::getUser()->getTimezone()->getName();
			}

			$dateTimeZone = new \DateTimeZone($timezoneName);
			$displayTimezoneName = $timezoneName;
		}
		catch (\Throwable $exception)
		{
			$dateTimeZone = new \DateTimeZone('UTC');
			$displayTimezoneName = 'UTC';
		}

		$dateTime = new \DateTime('now', $dateTimeZone);
		$offset = $dateTimeZone->getOffset($dateTime);
		$hours = floor(abs($offset) / 3600);
		$minutes = floor((abs($offset) % 3600) / 60);
		$offsetText = sprintf('UTC %s%02d:%02d', $offset < 0 ? '-' : '+', $hours, $minutes);
		$location = $dateTimeZone->getLocation();
		$countryCode = $location['country_code'] ?? '';
		$countryName = '';

		if (($countryCode === '' || $countryCode === '??') && class_exists('\IntlTimeZone'))
		{
			$countryCode = \IntlTimeZone::getRegion($displayTimezoneName) ?: '';
		}

		if (!empty($countryCode) && $countryCode !== '??' && $countryCode !== '001' && class_exists('\Locale'))
		{
			$countryName = \Locale::getDisplayRegion('-' . $countryCode, 'en') ?: '';
		}

		return trim($displayTimezoneName . ' (' . $offsetText . (!empty($countryName) ? ', ' . $countryName : '') . ')');
	}

	public static function checkEmptyOrNull($value)
	{
		if (empty($value))
		{
			return true;
		}

		return false;
	}

	public static function getPluginVersion()
	{
		$db = Factory::getDbo();
		$dbQuery = $db->getQuery(true)
			->select('manifest_cache')
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . " = " . $db->quote('com_joomshield'));
		$db->setQuery($dbQuery);
		$manifest = json_decode($db->loadResult());

		return ($manifest->version);
	}

	public static function getFeedbackForm($post)
	{
		$radio = $post['deactivate_plugin'] ?? '';
		$data = $post['query_feedback'] ?? '';

		$currentUser = Factory::getUser();
		$adminEmailDefault = $currentUser->email ?? '';
		$formEmail = $post['query_email'] ?? $adminEmailDefault;
		$data1 = isset($post['miniorange_feedback_submit']) ? $radio . ' : ' . $data : 'Skipped Feedback';
		include_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomshield' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_networksecurity_customer_setup.php';

		self::submitFeedbackForm($formEmail, $adminEmailDefault, $data1);
		self::genDbUpdate('#__miniorange_networksecurity_customer', ['uninstall_feedback' => 1]);

		$extensionIds = $post['result'] ?? [];

		if (!is_array($extensionIds))
		{
			$extensionIds = [$extensionIds];
		}

		foreach ($extensionIds as $fbkey)
		{
			$result = self::getDbValuesUsingColumns('type', '#__extensions', $fbkey);
			$identifier = (int) $fbkey;
			$type = '';

			foreach ($result as $results)
			{
				$type = $results;
			}

			if (!empty($type) && $identifier > 0)
			{
				self::uninstallExtension($type, $identifier);
			}
		}
	}

	public static function submitFeedbackForm($email, $adminEmail, $query)
	{
		$url = MoNetworkSecurityUtility::getHostname() . '/moas/api/notify/send';
		$customerKey = MoNetworkSecurityUtility::getNotifyCustomerKey();
		$fromEmail = $email;
		$subject = "Joomla JoomShield Free Feedback - " . $fromEmail;
		$phpVersion = phpversion();
		$version = new Version;
		$jCmsVersion = $version->getShortVersion();
		$moPluginVersion = self::getPluginVersion();
		$osInfo = self::getOsInfo();
		$timezoneInfo = self::getUserTimezoneInfo();
		$systemInfo = "Joomla " . $jCmsVersion . " | PHP " . $phpVersion . " | Plugin " . $moPluginVersion . " | OS " . $osInfo . " | Timezone " . $timezoneInfo;

		$query1 = "miniOrange JoomShield [Free] Plugin";
		$content = '<div >Hello, <br><br>
                    <b>Company : </b> <a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                    <b>Email: </b><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                    <b>Admin Email: </b>' . $adminEmail . '<br><br>
                    <b>Plugin Deactivated: </b>' . $query1 . '<br><br>
                    <b>Reason: </b>' . $query . '<br><br>
                    <b>System info: </b>' . $systemInfo . '
                    </div>';

		$fields = array(
			'customerKey' => $customerKey,
			'sendEmail' => true,
			'email' => array(
				'customerKey' => $customerKey,
				'fromEmail' => $fromEmail,
				'fromName' => 'miniOrange Joomla',
				'toEmail' => 'joomlasupport@xecurify.com',
				'toName' => 'joomlasupport@xecurify.com',
				'subject' => $subject,
				'content' => $content
			),
		);

		return MoNetworkSecurityUtility::sendNotifyApiRequest($url, $fields);
	}

	public static function pluginEfficiencyCheck($email)
	{
		$cTime = date("Y-m-d", time());
		$baseUrl = Uri::root();
		$url = MoNetworkSecurityUtility::getHostname() . '/moas/api/notify/send';
		$customerKey = MoNetworkSecurityUtility::getNotifyCustomerKey();
		$fromEmail             = $email;
		$subject            = "miniOrange JoomShield [Free] for Efficiency";
		$phpVersion = phpversion();
		$version = new Version;
		$jCmsVersion = $version->getShortVersion();
		$osInfo = self::getOsInfo();
		$timezoneInfo = self::getUserTimezoneInfo();
		$systemInfo = "Joomla " . $jCmsVersion . " | PHP " . $phpVersion . " | OS " . $osInfo . " | Timezone " . $timezoneInfo;

		$query1 = "miniOrange JoomShield [Free] Plugin to improve efficiency";
		$content = '<div >Hello, <br><br>
                    <b>Company : </b><a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                    <b>Email :</b><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                    <b>Plugin Efficency Check: </b>' . $query1 . '<br><br>
                    <b>Website: </b>' . $baseUrl . '<br><br>
                    <b>Creation Date: </b>' . $cTime . '<br><br>
                    <b>System Info: </b>' . $systemInfo . '</div>';

		$fields = array(
			'customerKey'    => $customerKey,
			'sendEmail'     => true,
			'email'         => array(
				'customerKey'     => $customerKey,
				'fromEmail'       => 'joomlasupport@xecurify.com',
				'bccEmail'        => 'nikhil.bhot@xecurify.com',
				'fromName'        => 'miniOrange',
				'toEmail'         => 'nutan.barad@xecurify.com',
				'toName'          => 'nutan.barad@xecurify.com',
				'subject'         => $subject,
				'content'         => $content
			),
		);

		MoNetworkSecurityUtility::sendNotifyApiRequest($url, $fields);
	}

	public static function downloadReports()
	{
		$data = self::getLoginTransactionReports();
		$reports = "SR.NO.,USERNAME,EMAIL,SITE URL,IP ADDRESS,STATUS,FAILURE REASON,DATE & TIME\n";

		$i = 1;

		foreach ($data as $key => $value)
		{
			if (empty($value['username']) || empty($value['ip_address']))
			{
				continue;
			}

			$timestamp = $value['created_timestamp'];
			$date = date('d-m-Y H:i:s', $timestamp);
			$siteUrl = self::getLoginReportSiteUrl($value);

			$reports .= $i . ',' . $value['username'] . ',' . ($value['email'] ?? '') . ',' . $siteUrl . ','
				. $value['ip_address'] . ',' . $value['status'] . ',' . ($value['failure_reason'] ?? '') . ','
				. $date . "\n";
			$i++;
		}

		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename="reports.csv"');
		print_r($reports);
		exit();
	}

	public static function checkUrl($val)
	{
		$post = Factory::getApplication()->input->post->getArray();

		if (isset($post['option_change_password']) && $post['option_change_password'] === 'mo_jnsp_change_password')
		{
			return;
		}

		$root = Uri::root();

		$tables = Factory::getDbo()->getTableList();

		foreach ($tables as $table)
		{
			if (strpos($table, "miniorange_jnsp_loginsecurity_setup"))
			{
				$loginConfig = self::getLoginSecurityConfig();
			}
		}

		$isCustAdmnLgn = $loginConfig['enable_custom_admin_login'] ?? 0;

		$urlKey = $loginConfig['access_lgn_urlky'] ?? '';
		$requestedUri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$cstmLnk = $root . 'administrator/?' . $urlKey;

		$customDestination = $loginConfig['custom_failure_destination'] ?? '';
		$customErrMessage = $loginConfig['custom_message_after_fail'] ?? '';
		$failureResponse = $loginConfig['after_adm_failure_response'] ?? '';

		if (strpos($requestedUri, 'administrator') !== false)
		{
			$isAdmin = 1;
		}
		else
		{
			$isAdmin = 0;
		}

		$lenActualLnk = strlen($requestedUri);
		$lenRoot = strlen($root);

		if (!self::isUserLogin())
		{
			return;
		}

		if ($isCustAdmnLgn != 1)
		{
			return;
		}

		if (!$isAdmin)
		{
			return;
		}

		if ($lenRoot === $lenActualLnk)
		{
			return;
		}

		$check = self::clnk($requestedUri, $cstmLnk);

		if ($check)
		{
			return;
		}

		if ($isCustAdmnLgn == 1 && $val == 1)
		{
			self::customRedirectMessage($root, $failureResponse, $customDestination, $customErrMessage);
		}

		if (self::checkDoLogin())
		{
			return;
		}

		self::customRedirectMessage($root, $failureResponse, $customDestination, $customErrMessage);
	}

	public static function customRedirectMessage($root, $failureResponse, $customDestination, $customErrMessage)
	{
		if (!empty('redirect_homepage' == $failureResponse))
		{
			$app = Factory::getApplication();
			$app->redirect($root);
		}

		if ($failureResponse == 'custom_redirect_url')
		{
			if (empty($customDestination))
			{
				$customDestination = "https://www.google.com";
			}

			if (!str_contains($customDestination, "https://") && !str_contains($customDestination, "http://"))
			{
				$customDestination = "https://$customDestination";
			}

			$app = Factory::getApplication();
			$app->redirect($customDestination);
		}

		if (('404_custom_message' == $failureResponse))
		{
			if (empty($customErrMessage))
			{
				$customErrMessage = "Some error has been occured";
			}
			?>
			<html lang="">
			<h1>404 Page Not Found</h1>
			<hr>
			<?php echo $customErrMessage; ?>
			</html>
			<?php
			exit();
		}
	}

	public static function clnk($url1, $url2)
	{
		if ($url1 == $url2)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public static function checkDoLogin()
	{
		$post = Factory::getApplication()->input->post->getArray();

		return isset($post['task']) && 'login' === $post['task'];
	}

	public static function isUserLogin()
	{
		$user = Factory::getUser();

		return $user->get('guest');
	}

	public static function saveBrowserBlocking($post)
	{
		$browserBlEnable = $post['mo_enable_browser_blocking'] ?? 0;
		$medge = $post['mo_medge_blocking'] ?? 0;

		if ($browserBlEnable == 0 || ($medge == 1))
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$fields = array(
				$db->quoteName('mo_enable_browser_blocking') . ' = ' . $db->quote($browserBlEnable),
				$db->quoteName('mo_medge_blocking') . ' = ' . $db->quote($medge),
			);

			$conditions = array(
				$db->quoteName('id') . ' = 1'
			);
			$query->update($db->quoteName('#__miniorange_jnsp_advance_blocking'))->set($fields)->where($conditions);

			$db->setQuery($query);
			$db->execute();

			return 1;
		}
		else
		{
			return 0;
		}
	}

	public static function isIpBlocked($userIPAddress)
	{
		return MoNetworkSecurityUtility::isIpBlocked($userIPAddress);
	}

	public static function getAdvanceIp()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_jnsp_advance_blocking'));
		$db->setQuery($query);
		$config = $db->loadAssoc();

		return $config;
	}

	public static function showErrorMessage()
	{
		$root = rtrim(Uri::root(), '/') . '/';
		$bootHref = htmlspecialchars($root . 'administrator/components/com_joomshield/assets/css/miniorange_boot.css', ENT_QUOTES, 'UTF-8');
		$netHref = htmlspecialchars($root . 'administrator/components/com_joomshield/assets/css/miniorange_netsecurity.css', ENT_QUOTES, 'UTF-8');

		$mailfrom = Factory::getApplication()->get('mailfrom', '');
		$contactBtn = '';

		if (is_string($mailfrom) && filter_var($mailfrom, FILTER_VALIDATE_EMAIL))
		{
			$safeMail = htmlspecialchars($mailfrom, ENT_QUOTES, 'UTF-8');
			$contactBtn = '<a class="mo_boot_btn mo_boot_btn-secondary mo_js_blocked-contact mo_boot_mt-3" href="mailto:' . $safeMail . '">Contact administrator</a>';
		}

		echo '<!DOCTYPE html>
            <html lang="en">
            <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="color-scheme" content="light dark">
            <title>403 — Access denied</title>
            <link rel="stylesheet" href="' . $bootHref . '">
            <link rel="stylesheet" href="' . $netHref . '">
            </head>
            <body class="mo_js_blocked-page mo_boot_d-flex mo_boot_justify-content-center">
            <div class="mo_js_blocked-card mo_boot_shadow">
            <div class="mo_js_blocked-topbar"></div>
            <div class="mo_js_blocked-body">
            <span class="mo_js_blocked-badge">403 — Access denied</span>
            <div class="mo_boot_d-flex mo_js_align_items_center mo_js_justify-content-center mo_js_blocked-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            </div>
            <h1 class="mo_js_blocked-title">You have been blocked</h1>
            <p class="mo_js_blocked-lead">Sorry, access to this site has been restricted. Your connection is not permitted to view the requested page.</p>
            <hr class="mo_js_blocked-hr">
            <ul class="mo_js_blocked-list">
            <li><span class="mo_js_blocked-dot" aria-hidden="true"></span><span>If you believe this is a mistake, contact your site administrator to request access.</span></li>
            <li><span class="mo_js_blocked-dot" aria-hidden="true"></span><span>Restrictions may be temporary; you can try again later or use a different network if appropriate.</span></li>
            </ul>
            ' . $contactBtn . '
            </div>
            </div>
            </body>
            </html>';
		exit();
	}

	public static function checkPasswdStrength($passwd)
	{
		if ($passwd != null)
		{
			if (strlen($passwd) > 11 && preg_match("#[0-9]+#", $passwd) && preg_match("#[a-zA-Z]+#", $passwd) && preg_match('/[^a-zA-Z\d]/', $passwd) && preg_match('/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/', $passwd))
			{
				return "success";
			}
			else
			{
				return "false";
			}
		}
	}

	public static function enqueueStrongPasswordErrors()
	{
		$app = Factory::getApplication();

		$app->enqueueMessage('Please select a strong password.', 'error');
		$app->enqueueMessage('Password should contain at least one uppercase and one lowercase letter.', 'error');
		$app->enqueueMessage('Password should be a minimum of 12 characters.', 'error');
		$app->enqueueMessage('Password should contain at least one numeric character.', 'error');
		$app->enqueueMessage('Password should contain at least one special character (!, @, #, $, %, ^, &, *, ?, _, ~, -).', 'error');
	}

	public static function redirectWithErrorMessage($message)
	{
		$app = Factory::getApplication();
		$app->enqueueMessage($message, 'error');
		$app->redirect(Route::_('index.php/component/users/?view=registration&Itemid=101'));
	}

	public static function redirectWithStrongPasswordErrors($redirectUrl = null)
	{
		self::enqueueStrongPasswordErrors();

		$app = Factory::getApplication();
		$app->redirect($redirectUrl ?? Route::_('index.php/component/users/?view=registration&Itemid=101'));
	}

	public static function handleChangePassword($username, $newPasswd, $confirmPasswd, $returnUrl = '')
	{
		$app = Factory::getApplication();
		$root = Uri::root();
		$redirectUrl = self::sanitizeReturnUrl($returnUrl, $root);

		if ($newPasswd !== $confirmPasswd)
		{
			$app->enqueueMessage('Both Passwords do not match', 'error');
			$app->redirect($redirectUrl);

			return;
		}

		if (self::checkPasswdStrength($newPasswd) !== 'success')
		{
			self::enqueueStrongPasswordErrors();
			$app->redirect($redirectUrl);

			return;
		}

		$userId = UserHelper::getUserId($username);
		$salt = UserHelper::genRandomPassword(32);
		$passwordHash = md5($confirmPasswd . $salt) . ':' . $salt;

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$fields = [
			$db->quoteName('password') . ' = ' . $db->quote($passwordHash),
		];
		$conditions = [
			$db->quoteName('id') . ' = ' . $db->quote($userId),
		];
		$query->update($db->quoteName('#__users'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();

		$app->enqueueMessage('You have successfully updated your password', 'success');
		$app->redirect($redirectUrl);
	}

	private static function getExtensionInstaller()
	{
		// Joomla 4/5/6: prefer the DI container (database is injected automatically).
		if (method_exists(Factory::class, 'getContainer'))
		{
			try
			{
				$container = Factory::getContainer();

				if ($container && method_exists($container, 'get'))
				{
					$installer = $container->get(Installer::class);

					if ($installer instanceof Installer)
					{
						return $installer;
					}
				}
			}
			catch (\Throwable $e)
			{
				// Fall through to getInstance().
			}
		}

		// Joomla 3.1+: core singleton; J4+ getInstance() also injects the database.
		return Installer::getInstance();
	}

	private static function uninstallExtension($type, $extensionId)
	{
		try
		{
			$installer = self::getExtensionInstaller();
			$installer->uninstall($type, (int) $extensionId);
		}
		catch (\Throwable $e)
		{
			// Do not interrupt feedback flow if uninstall fails.
		}
	}

	private static function sanitizeReturnUrl($returnUrl, $root)
	{
		if (!empty($returnUrl) && str_starts_with($returnUrl, $root))
		{
			return $returnUrl;
		}

		return $root . 'index.php';
	}

	public static function getLastIp()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('Max(id)');
		$query->from($db->quoteName('#__miniorange_networksecurity_customer'));
		$db->setQuery($query);

		return $db->loadResult();
	}

	public static function getDbValuesUsingColumns($type, $table, $fbkey)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($type);
		$query->from($table);
		$query->where($db->quoteName('extension_id') . " = " . $db->quote($fbkey));
		$db->setQuery($query);
		$result = $db->loadColumn();

		return $result;
	}

	public static function getDbValuesArray($table)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('*'));
		$query->from($db->quoteName($table));
		$query->where($db->quoteName('id') . " = 1");
		$db->setQuery($query);
		$customerResult = $db->loadAssoc();

		return $customerResult;
	}

	public static function genDbUpdate($dbTable, $dbColumns)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		foreach ($dbColumns as $key => $value)
		{
			$databaseValues[] = $db->quoteName($key) . ' = ' . $db->quote($value);
		}

		$query->update($db->quoteName($dbTable))->set($databaseValues)->where($db->quoteName('id') . " = 1");
		$db->setQuery($query);
		$db->execute();
	}
}
