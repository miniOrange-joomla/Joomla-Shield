<?php
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange Joomla Network / Website Security plugin.
 *
 * miniOrange Joomla Network / Website Security plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * miniOrange Joomla Network / Website Security plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange Joomla Network / Website Security plugin.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die;

include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomla_networksecurity' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_networksecurity_utility.php');

class NetworkSecurityUtilities
{
    public static function is_customer_registered()
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_networksecurity_customer'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $result = $db->loadAssoc();

        $email = $result['email'];
        $customerKey = $result['customer_key'];
        if (!$email || !$customerKey || !is_numeric(trim($customerKey))) {
            return 0;
        } else {
            return 1;
        }
    }
    public static function is_network_registered($table_name)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName($table_name));
        $db->setQuery($query);
        $result = $db->loadAssoc();
        return $result;
    }


    public static function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    public static function addTransactionDetails($ipAddress, $username, $type, $status, $url = null)
    {
        $url = is_null($url) ? '' : $url;

        $current_time = time();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('ip_address') . ' = ' . $db->quote($ipAddress),
            $db->quoteName('username') . ' = ' . $db->quote($username),
            $db->quoteName('type') . ' = ' . $db->quote($type),
            $db->quoteName('status') . ' = ' . $db->quote($status),
            $db->quoteName('created_timestamp') . ' = ' . $db->quote($current_time),
            $db->quoteName('url') . ' = ' . $db->quote($url),
        );

        $query->insert($db->quoteName('#__miniorange_login_transactions'))->set($fields);
        $db->setQuery($query);
        $db->execute();
    }

    public static function update_transaction_table($ip_address)
    {
        $status = "pastfailed";
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('ip_address') . ' = ' . $db->quote($ip_address),
            $db->quoteName('status') . ' = ' . $db->quote($status),
        );

        $query->update($db->quoteName('#__miniorange_login_transactions'))->set($fields)->where($db->quoteName('ip_address') . ' = ' . $db->quote($ip_address) AND ($db->quoteName('status') . '=' . 'failed'));
        $db->setQuery($query);
        $db->execute();

    }

    public static function getUserCredentials($username){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('id,password')
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('username') . ' = ' . $db->quote($username));
        $db->setQuery($query);
        return $db->loadObject();
    }

    public static function getLoginSecurityConfig()
    {
        $db = JFactory::getDbo();
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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_jnsp_registersecurity_setup'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $config = $db->loadAssoc();
        return $config;
    }

    public static function _clear_iplookup()
    {
        $db = JFactory::getDbo();
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

    public static function get_all_attempts_count()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_login_transactions'));
        $db->setQuery($query);
        $config = $db->loadAssocList();
        return $config;
    }

    public static function _custom_redirect($message, $status)
    {
        $app = JFactory::getApplication();
        $app->enqueueMessage($message, $status);
        $app->redirect(JRoute::_('index.php?option=com_joomla_networksecurity&tab=ip_blocking'));
    }

    public static function _custom_redirect_url($url, $message = null, $status = null)
    {
        $app = JFactory::getApplication();
        $app->enqueueMessage($message, $status);
        $app->redirect($url);
    }

    public static function _is_valid_email($email_id)
    {
        $extra_domains = self::get_values_from_db();

        $extra_domains = $extra_domains['mo_email_domains'] ?? '';
        $extra_domains = explode(';', $extra_domains);
        $u_email = explode("@", $email_id);

        $domains = array('0-mail.com', '20email.eu', '0815.ru', '0815.su', '0clickemail.com', '0sg.net', '0wnd.net', '0wnd.org', '10mail.org', '10minutemail.cf', '10minutemail.com', '10minutemail.de', '10minutemail.ga', '10minutemail.gq', '10minutemail.ml', '123-m.com', '12hourmail.com', '12minutemail.com', '1ce.us', '1chuan.com', '1mail.ml', '1pad.de', '1zhuan.com', '20mail.in', '20mail.it', '20minutemail.com', '21cn.com', '24hourmail.com', '2prong.com', '30minutemail.com', '30minutesmail.com', '3126.com', '33mail.com', '3d-painting.com', '3mail.ga', '4mail.cf', '4mail.ga', '4warding.com', '4warding.net', '4warding.org', '50e.info', '5mail.cf', '5mail.ga', '60minutemail.com', '675hosting.com', '675hosting.net', '675hosting.org', '6ip.us', '6mail.cf', '6mail.ga', '6mail.ml', '6paq.com', '6url.com', '75hosting.com', '75hosting.net', '75hosting.org', '7days-printing.com', '7mail.ga', '7mail.ml', '7tags.com', '8mail.cf', '8mail.ga', '8mail.ml', '99experts.com', '9mail.cf', '9ox.net', 'BeefMilk.com', 'DingBone.com', 'FudgeRub.com', 'LookUgly.com', 'MailScrap.com', 'SmellFear.com', 'TempEmail.net', 'a-bc.net', 'a45.in', 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk.com', 'abusemail.de', 'abwesend.de', 'abyssmail.com', 'ac20mail.in', 'acentri.com', 'addcom.de', 'advantimo.com', 'afrobacon.com', 'ag.us.to', 'agedmail.com', 'agnitumhost.net', 'ahk.jp', 'ajaxapp.net', 'alivance.com', 'alpenjodel.de', 'alphafrau.de', 'amail.com', 'amilegit.com', 'amiri.net', 'amiriindustries.com', 'amorki.pl', 'anappthat.com', 'ano-mail.net', 'anonbox.net', 'anonymail.dk', 'anonymbox.com', 'antichef.com', 'antichef.net', 'antispam.de', 'antispam24.de', 'appixie.com', 'armyspy.com', 'asdasd.nl', 'autosfromus.com', 'aver.com', 'azmeil.tk', 'baldmama.de', 'baldpapa.de', 'ballyfinance.com', 'baxomale.ht.cx', 'beddly.com', 'beefmilk.com', 'betriebsdirektor.de', 'big1.us', 'bigmir.net', 'bigprofessor.so', 'bigstring.com', 'bin-wieder-da.de', 'binkmail.com', 'bio-muesli.info', 'bio-muesli.net', 'bladesmail.net', 'bleib-bei-mir.de', 'blockfilter.com', 'blogmyway.org', 'bluebottle.com', 'bobmail.info', 'bodhi.lawlita.com', 'bofthew.com', 'bonbon.net', 'bootybay.de', 'boun.cr', 'bouncr.com', 'boxformail.in', 'boxtemp.com.br', 'brefmail.com', 'brennendesreich.de', 'briefemail.com', 'broadbandninja.com', 'brokenvalve.com', 'brokenvalve.org', 'bsnow.net', 'bspamfree.org', 'bu.mintemail.com', 'buerotiger.de', 'buffemail.com', 'bugmenot.com', 'bumpymail.com', 'bund.us', 'bundes-li.ga', 'burnthespam.info', 'burstmail.info', 'buy-24h.net.ru', 'buyusedlibrarybooks.org', 'c2.hu', 'cachedot.net', 'cashette.com', 'casualdx.com', 'cbair.com', 'ce.mintemail.com', 'cellurl.com', 'center-mail.de', 'centermail.at', 'centermail.ch', 'centermail.com', 'centermail.de', 'centermail.info', 'centermail.net', 'cghost.s-a-d.de', 'chammy.info', 'cheatmail.de', 'chogmail.com', 'choicemail1.com', 'chong-mail.com', 'chong-mail.net', 'chong-mail.org', 'chongsoft.org', 'clixser.com', 'cmail.com', 'cmail.net', 'cmail.org', 'coldemail.info', 'consumerriot.com', 'cool.fr.nf', 'coole-files.de', 'correo.blogos.net', 'cosmorph.com', 'courriel.fr.nf', 'courrieltemporaire.com', 'crapmail.org', 'crazespaces.pw', 'crazymailing.com', 'cubiclink.com', 'curryworld.de', 'cust.in', 'cuvox.de', 'cyber-matrix.com', 'dacoolest.com', 'daintly.com', 'dandikmail.com', 'dating4best.net', 'dayrep.com', 'dbunker.com', 'dcemail.com', 'deadaddress.com', 'deadchildren.org', 'deadfake.cf', 'deadfake.ga', 'deadfake.ml', 'deadfake.tk', 'deadspam.com', 'deagot.com', 'dealja.com', 'despam.it', 'despammed.com', 'devnullmail.com', 'dfgh.net', 'dharmatel.net', 'die-besten-bilder.de', 'die-genossen.de', 'die-optimisten.de', 'die-optimisten.net', 'dieMailbox.de', 'digital-filestore.de', 'digitalsanctuary.com', 'dingbone.com', 'directbox.com', 'discard.cf', 'discard.email', 'discard.ga', 'discard.gq', 'discard.ml', 'discard.tk', 'discardmail.*', 'discardmail.com', 'discardmail.de', 'discartmail.com', 'disposable-email.ml', 'disposable.cf', 'disposable.ga', 'disposable.ml', 'disposableaddress.com', 'disposableemailaddresses.com', 'disposableemailaddresses.emailmiser.com', 'disposableinbox.com', 'dispose.it', 'disposeamail.com', 'disposemail.com', 'dispostable.com', 'divermail.com', 'dm.w3internet.co.uk', 'example.com', 'docmail.cz', 'dodgeit.com', 'dodgit.com', 'dodgit.org', 'dogit.com', 'doiea.com', 'domozmail.com', 'donemail.ru', 'dontreg.com', 'dontsendmespam.de', 'dontsentmespam.de', 'dotmsg.com', 'download-privat.de', 'drdrb.com', 'drdrb.net', 'droplar.com', 'dropmail.me', 'duam.net', 'dudmail.com', 'dump-email.info', 'dumpandjunk.com', 'dumpmail.com', 'dumpmail.de', 'dumpyemail.com', 'duskmail.com', 'dyndns.org', 'e-mail.com', 'e-mail.org', 'e4ward.com', 'easytrashmail.com', 'ee2.pl', 'eelmail.com', 'einrot.com', 'einrot.de', 'eintagsmail.de', 'email-fake.cf', 'email-fake.ga', 'email-fake.gq', 'email-fake.ml', 'email-fake.tk', 'email.org', 'email4u.info', 'email60.com', 'emailage.cf', 'emailage.ga', 'emailage.gq', 'emailage.ml', 'emailage.tk', 'emaildienst.de', 'emailgo.de', 'emailias.com', 'emailigo.de', 'emailinfive.com', 'emailisvalid.com', 'emaillime.com', 'emailmiser.com', 'emailproxsy.com', 'emails.ga', 'emailsensei.com', 'emailspam.cf', 'emailspam.ga', 'emailspam.gq', 'emailspam.ml', 'emailspam.tk', 'emailtaxi.de', 'emailtemporanea.net', 'emailtemporar.ro', 'emailtemporario.com.br', 'emailthe.net', 'emailtmp.com', 'emailto.de', 'emailwarden.com', 'emailx.at.hm', 'emailxfer.com', 'emailz.cf', 'emailz.ga', 'emailz.gq', 'emailz.ml', 'emeil.in', 'emeil.ir', 'emil.com', 'emkei.cf', 'emkei.ga', 'emkei.gq', 'emkei.ml', 'emkei.tk', 'emz.net', 'enterto.com', 'ephemail.net', 'etranquil.com', 'etranquil.net', 'etranquil.org', 'evopo.com', 'example.com', 'explodemail.com', 'eyepaste.com', 'facebook-email.cf', 'facebook-email.ga', 'facebook-email.ml', 'facebookmail.gq', 'facebookmail.ml', 'fahr-zur-hoelle.org', 'fake-mail.cf', 'fake-mail.ga', 'fake-mail.ml', 'fakeinbox.cf', 'fakeinbox.com', 'fakeinbox.ga', 'fakeinbox.ml', 'fakeinbox.tk', 'fakeinformation.com', 'fakemail.fr', 'fakemailgenerator.com', 'fakemailz.com', 'falseaddress.com', 'fammix.com', 'fansworldwide.de', 'fantasymail.de', 'farifluset.mailexpire.com', 'fastacura.com', 'fastchevy.com', 'fastchrysler.com', 'fastkawasaki.com', 'fastmazda.com', 'fastmitsubishi.com', 'fastnissan.com', 'fastsubaru.com', 'fastsuzuki.com', 'fasttoyota.com', 'fastyamaha.com', 'fatflap.com', 'fdfdsfds.com', 'feinripptraeger.de', 'fettabernett.de', 'fightallspam.com', 'fiifke.de', 'filzmail.com', 'fishfuse.com', 'fixmail.tk', 'fizmail.com', 'fleckens.hu', 'flurred.com', 'flyspam.com', 'footard.com', 'forgetmail.com', 'fornow.eu', 'fr33mail.info', 'frapmail.com', 'free-email.cf', 'free-email.ga', 'freemail.ms', 'freemails.cf', 'freemails.ga', 'freemails.ml', 'freemeilaadressforall.net', 'freudenkinder.de', 'freundin.ru', 'friendlymail.co.uk', 'fromru.com', 'front14.org', 'fuckingduh.com', 'fudgerub.com', 'fux0ringduh.com', 'garliclife.com', 'gawab.com', 'gelitik.in', 'gentlemansclub.de', 'get-mail.cf', 'get-mail.ga', 'get-mail.ml', 'get-mail.tk', 'get1mail.com', 'get2mail.fr', 'getairmail.cf', 'getairmail.com', 'getairmail.ga', 'getairmail.gq', 'getairmail.ml', 'getairmail.tk', 'getmails.eu', 'getonemail.com', 'getonemail.net', 'ghosttexter.de', 'girlsundertheinfluence.com', 'gishpuppy.com', 'goemailgo.com', 'gold-profits.info', 'goldtoolbox.com', 'golfilla.info', 'gorillaswithdirtyarmpits.com', 'gotmail.com', 'gotmail.net', 'gotmail.org', 'gotti.otherinbox.com', 'gowikibooks.com', 'gowikicampus.com', 'gowikicars.com', 'gowikifilms.com', 'gowikigames.com', 'gowikimusic.com', 'gowikinetwork.com', 'gowikitravel.com', 'gowikitv.com', 'grandmamail.com', 'grandmasmail.com', 'great-host.in', 'greensloth.com', 'grr.la', 'gsrv.co.uk', 'guerillamail.biz', 'guerillamail.com', 'guerillamail.net', 'guerillamail.org', 'guerrillamail.biz', 'guerrillamail.com', 'guerrillamail.de', 'guerrillamail.info', 'guerrillamail.net', 'guerrillamail.org', 'guerrillamailblock.com', 'gustr.com', 'h.mintemail.com', 'h8s.org', 'hab-verschlafen.de', 'habmalnefrage.de', 'hacccc.com', 'haltospam.com', 'harakirimail.com', 'hartbot.de', 'hatespam.org', 'hellodream.mobi', 'herp.in', 'herr-der-mails.de', 'hidemail.de', 'hidzz.com', 'hmamail.com', 'hochsitze.com', 'home.de', 'hopemail.biz', 'hot-mail.cf', 'hot-mail.ga', 'hot-mail.gq', 'hot-mail.ml', 'hot-mail.tk', 'hotpop.com', 'hulapla.de', 'humn.ws.gy', 'hush.com', 'hushmail.com', 'ich-bin-verrueckt-nach-dir.de', 'ich-will-net.de', 'ieatspam.eu', 'ieatspam.info', 'ieh-mail.de', 'ihateyoualot.info', 'iheartspam.org', 'ikbenspamvrij.nl', 'imails.info', 'imgof.com', 'imstations.com', 'inbax.tk', 'inbox.si', 'inbox2.info', 'inboxalias.com', 'inboxclean.com', 'inboxclean.org', 'inboxproxy.com', 'incognitomail.com', 'incognitomail.net', 'incognitomail.org', 'inerted.com', 'inmail24.com', 'insorg-mail.info', 'instant-mail.de', 'instantemailaddress.com', 'ipoo.org', 'irish2me.com', 'iroid.com', 'ist-allein.info', 'ist-einmalig.de', 'ist-ganz-allein.de', 'ist-willig.de', 'iwi.net', 'izmail.net', 'jetable.com', 'jetable.de', 'jetable.fr.nf', 'jetable.net', 'jetable.org', 'jetfix.ee', 'jetzt-bin-ich-dran.com', 'jn-club.de', 'jnxjn.com', 'jobbikszimpatizans.hu', 'jourrapide.com', 'jsrsolutions.com', 'junk1e.com', 'junkmail.com', 'junkmail.ga', 'junkmail.gq', 'kaffeeschluerfer.com', 'kaffeeschluerfer.de', 'kasmail.com', 'kaspop.com', 'keepmymail.com', 'killmail.com', 'killmail.net', 'kimsdisk.com', 'kinglibrary.net', 'kingsq.ga', 'kir.ch.tc', 'klassmaster.com', 'klassmaster.net', 'klzlk.com', 'kommespaeter.de', 'kook.ml', 'koszmail.pl', 'krim.ws', 'kuh.mu', 'kulturbetrieb.info', 'kurzepost.de', 'l33r.eu', 'labetteraverouge.at', 'lackmail.net', 'lags.us', 'landmail.co', 'lass-es-geschehen.de', 'lastmail.co', 'lastmail.com', 'lazyinbox.com', 'letthemeatspam.com', 'lhsdv.com', 'liebt-dich.info', 'lifebyfood.com', 'link2mail.net', 'listomail.com', 'litedrop.com', 'loadby.us', 'login-email.cf', 'login-email.ga', 'login-email.ml', 'login-email.tk', 'lol.ovpn.to', 'lookugly.com', 'lopl.co.cc', 'lortemail.dk', 'lovemeleaveme.com', 'loveyouforever.de', 'lr7.us', 'lr78.com', 'lroid.com', 'luv2.us', 'm4ilweb.info', 'maboard.com', 'maennerversteherin.com', 'maennerversteherin.de', 'mail-filter.com', 'mail-temporaire.fr', 'mail.by', 'mail.htl22.at', 'mail.mezimages.net', 'mail.misterpinball.de', 'mail.svenz.eu', 'mail114.net', 'mail15.com', 'mail2rss.org', 'mail333.com', 'mail4days.com', 'mail4trash.com', 'mail4u.info', 'mailbidon.com', 'mailblocks.com', 'mailbucket.org', 'mailcat.biz', 'mailcatch.*', 'mailcatch.com', 'maildrop.cc', 'maildrop.cf', 'maildrop.ga', 'maildrop.gq', 'maildrop.ml', 'maildx.com', 'maileater.com', 'mailexpire.com', 'mailfa.tk', 'mailforspam.com', 'mailfree.ga', 'mailfree.gq', 'mailfree.ml', 'mailfreeonline.com', 'mailfs.com', 'mailguard.me', 'mailimate.com', 'mailin8r.com', 'mailinater.com', 'mailinator.com', 'mailinator.gq', 'mailinator.net', 'mailinator.org', 'mailinator.us', 'mailinator2.com', 'mailinblack.com', 'mailincubator.com', 'mailismagic.com', 'mailjunk.cf', 'mailjunk.ga', 'mailjunk.gq', 'mailjunk.ml', 'mailjunk.tk', 'mailmate.com', 'mailme.gq', 'mailme.ir', 'mailme.lv', 'mailme24.com', 'mailmetrash.com', 'mailmoat.com', 'mailnator.com', 'mailnesia.com', 'mailnull.com', 'mailpick.biz', 'mailproxsy.com', 'mailquack.com', 'mailrock.biz', 'mailsac.com', 'mailscrap.com', 'mailseal.de', 'mailshell.com', 'mailsiphon.com', 'mailslapping.com', 'mailslite.com', 'mailtemp.info', 'mailtothis.com', 'mailtrash.net', 'mailueberfall.de', 'mailzilla.com', 'mailzilla.org', 'mailzilla.orgmbx.cc', 'makemetheking.com', 'mamber.net', 'manifestgenerator.com', 'manybrain.com', 'mbx.cc', 'mciek.com', 'mega.zik.dj', 'meine-dateien.info', 'meine-diashow.de', 'meine-fotos.info', 'meine-urlaubsfotos.de', 'meinspamschutz.de', 'meltmail.com', 'messagebeamer.de', 'metaping.com', 'mezimages.net', 'mfsa.ru', 'mierdamail.com', 'migumail.com', 'mintemail.com', 'mjukglass.nu', 'mns.ru', 'moakt.com', 'mobi.web.id', 'mobileninja.co.uk', 'moburl.com', 'mohmal.com', 'moncourrier.fr.nf', 'monemail.fr.nf', 'monmail.fr.nf', 'monumentmail.com', 'ms9.mailslite.com', 'msa.minsmail.com', 'msh.mailslite.com', 'mt2009.com', 'mt2014.com', 'mufmail.com', 'muskelshirt.de', 'mx0.wwwnew.eu', 'my-mail.ch', 'my10minutemail.com', 'myadult.info', 'mycleaninbox.net', 'myemailboxy.com', 'mymail-in.net', 'mymailoasis.com', 'mynetstore.de', 'mypacks.net', 'mypartyclip.de', 'myphantomemail.com', 'myspaceinc.com', 'myspaceinc.net', 'myspaceinc.org', 'myspacepimpedup.com', 'myspamless.com', 'mytemp.email', 'mytempemail.com', 'mytop-in.net', 'mytrashmail.com', 'mytrashmail.compookmail.com', 'neomailbox.com', 'nepwk.com', 'nervmich.net', 'nervtmich.net', 'netmails.com', 'netmails.net', 'netterchef.de', 'netzidiot.de', 'neue-dateien.de', 'neverbox.com', 'nice-4u.com', 'nmail.cf', 'no-spam.ws', 'nobulk.com', 'noclickemail.com', 'nogmailspam.info', 'nomail.xl.cx', 'nomail2me.com', 'nomorespamemails.com', 'nonspam.eu', 'nonspammer.de', 'noref.in', 'nospam.wins.com.br', 'nospam.ze.tc', 'nospam4.us', 'nospamfor.us', 'nospammail.net', 'nospamthanks.info', 'notmailinator.com', 'notsharingmy.info', 'nowhere.org', 'nowmymail.com', 'ntlhelp.net', 'nullbox.info', 'nur-fuer-spam.de', 'nurfuerspam.de', 'nus.edu.sg', 'nwldx.com', 'nybella.com', 'objectmail.com', 'obobbo.com', 'odaymail.com', 'office-dateien.de', 'oikrach.com', 'one-time.email', 'oneoffemail.com', 'oneoffmail.com', 'onewaymail.com', 'online.ms', 'oopi.org', 'opayq.com', 'orangatango.com', 'ordinaryamerican.net', 'otherinbox.com', 'ourklips.com', 'outlawspam.com', 'ovpn.to', 'owlpic.com', 'pancakemail.com', 'paplease.com', 'partybombe.de', 'partyheld.de', 'pcusers.otherinbox.com', 'pepbot.com', 'pfui.ru', 'phreaker.net', 'pimpedupmyspace.com', 'pisem.net', 'pjjkp.com', 'pleasedontsendmespam.de', 'plexolan.de', 'poczta.onet.pl', 'politikerclub.de', 'polizisten-duzer.de', 'poofy.org', 'pookmail.com', 'pornobilder-mal-gratis.com', 'portsaid.cc', 'postacin.com', 'postfach.cc', 'privacy.net', 'privy-mail.com', 'privymail.de', 'proxymail.eu', 'prtnx.com', 'prtz.eu', 'prydirect.info', 'pryworld.info', 'public-files.de', 'punkass.com', 'put2.net', 'putthisinyourspamdatabase.com', 'pwrby.com', 'qasti.com', 'qisdo.com', 'qisoa.com', 'qq.com', 'quantentunnel.de', 'quickinbox.com', 'quickmail.nl', 'qv7.info', 'radiku.ye.vc', 'ralib.com', 'raubtierbaendiger.de', 'rcpt.at', 'reallymymail.com', 'receiveee.chickenkiller.com', 'receiveee.com', 'recode.me', 'reconmail.com', 'record.me', 'recursor.net', 'recyclemail.dk', 'regbypass.com', 'regbypass.comsafe-mail.net', 'rejectmail.com', 'remail.cf', 'remail.ga', 'rhyta.com', 'rk9.chickenkiller.com', 'rklips.com', 'rmqkr.net', 'rootprompt.org', 'royal.net', 'rppkn.com', 'rtrtr.com', 'ruffrey.com', 's0ny.net', 'saeuferleber.de', 'safe-mail.net', 'safersignup.de', 'safetymail.info', 'safetypost.de', 'sags-per-mail.de', 'sandelf.de', 'satka.net', 'saynotospams.com', 'scatmail.com', 'schafmail.de', 'schmusemail.de', 'schreib-doch-mal-wieder.de', 'selfdestructingmail.com', 'selfdestructingmail.org', 'sendspamhere.com', 'senseless-entertainment.com', 'shared-files.de', 'sharedmailbox.org', 'sharklasers.com', 'shieldedmail.com', 'shiftmail.com', 'shinedyoureyes.com', 'shitmail.me', 'shitmail.org', 'shitware.nl', 'shortmail.net', 'showslow.de', 'sibmail.com', 'sinnlos-mail.de', 'siria.cc', 'siteposter.net', 'skeefmail.com', 'skeefmail.net', 'slaskpost.se', 'slave-auctions.net', 'slopsbox.com', 'slushmail.com', 'smashmail.de', 'smellfear.com', 'smellrear.com', 'sms.at', 'snakemail.com', 'sneakemail.com', 'snkmail.com', 'sofimail.com', 'sofort-mail.de', 'sofortmail.de', 'softpls.asia', 'sogetthis.com', 'sohu.com', 'soisz.com', 'solvemail.info', 'sonnenkinder.org', 'soodomail.com', 'soodonims.com', 'spam-be-gone.com', 'spam.la', 'spam.su', 'spam4.me', 'spamavert.com', 'spambob.com', 'spambob.net', 'spambob.org', 'spambog.*', 'spambog.com', 'spambog.de', 'spambog.net', 'spambog.ru', 'spambooger.com', 'spambox.info', 'spambox.irishspringrealty.com', 'spambox.us', 'spamcannon.com', 'spamcannon.net', 'spamcero.com', 'spamcon.org', 'spamcorptastic.com', 'spamcowboy.com', 'spamcowboy.net', 'spamcowboy.org', 'spamday.com', 'spamdecoy.net', 'spameater.com', 'spameater.org', 'spamex.com', 'spamfighter.cf', 'spamfighter.ga', 'spamfighter.gq', 'spamfighter.ml', 'spamfighter.tk', 'spamfree.eu', 'spamfree24.com', 'spamfree24.de', 'spamfree24.eu', 'spamfree24.info', 'spamfree24.net', 'spamfree24.org', 'spamgoes.in', 'spamgourmet.com', 'spamgourmet.net', 'spamgourmet.org', 'spamgrube.net', 'spamherelots.com', 'spamhereplease.com', 'spamhole.com', 'spamify.com', 'spaminator.de', 'spamkill.info', 'spaml.com', 'spaml.de', 'spammote.com', 'spammotel.com', 'spammuffel.de', 'spamobox.com', 'spamoff.de', 'spamreturn.com', 'spamsalad.in', 'spamslicer.com', 'spamspot.com', 'spamstack.net', 'spamthis.co.uk', 'spamthisplease.com', 'spamtrail.com', 'spamtroll.net', 'speed.1s.fr', 'sperke.net', 'spikio.com', 'spoofmail.de', 'squizzy.de', 'sriaus.com', 'ssoia.com', 'startkeys.com', 'stinkefinger.net', 'stop-my-spam.cf', 'stop-my-spam.com', 'stop-my-spam.ga', 'stop-my-spam.ml', 'stop-my-spam.tk', 'streber24.de', 'streetwisemail.com', 'stuffmail.de', 'super-auswahl.de', 'supergreatmail.com', 'supermailer.jp', 'superrito.com', 'superstachel.de', 'suremail.info', 'svk.jp', 'sweetville.net', 'sweetxxx.de', 'tafmail.com', 'tagesmail.eu', 'tagyourself.com', 'talkinator.com', 'tapchicuoihoi.com', 'teewars.org', 'teleworm.com', 'teleworm.us', 'temp-mail.com', 'temp-mail.org', 'temp.emeraldwebmail.com', 'temp.headstrong.de', 'tempail.com', 'tempalias.com', 'tempe-mail.com', 'tempemail.biz', 'tempemail.co.za', 'tempemail.com', 'tempemail.net', 'tempinbox.co.uk', 'tempinbox.com', 'tempmail.it', 'tempmail.com', 'tempmail2.com', 'tempmaildemo.com', 'tempmailer.com', 'tempomail.fr', 'temporarily.de', 'temporarioemail.com.br', 'temporaryemail.net', 'temporaryemail.us', 'temporaryforwarding.com', 'temporaryinbox.com', 'tempsky.com', 'tempthe.net', 'tempymail.com', 'terminverpennt.de', 'test.com', 'test.de', 'thanksnospam.info', 'thankyou2010.com', 'thecloudindex.com', 'thepryam.info', 'thisisnotmyrealemail.com', 'throam.com', 'throwawayemailaddress.com', 'throwawaymail.com', 'tilien.com', 'tittbit.in', 'tmail.ws', 'tmailinator.com', 'toiea.com', 'toomail.biz', 'topmail-files.de', 'tortenboxer.de', 'totalmail.de', 'tradermail.info', 'trash-amil.com', 'trash-mail.at', 'trash-mail.cf', 'trash-mail.com', 'trash-mail.de', 'trash-mail.ga', 'trash-mail.gq', 'trash-mail.ml', 'trash-mail.tk', 'trash2009.com', 'trash2010.com', 'trash2011.com', 'trashbox.eu', 'trashdevil.com', 'trashdevil.de', 'trashemail.de', 'trashmail.at', 'trashmail.com', 'trashmail.de', 'trashmail.me', 'trashmail.net', 'trashmail.org', 'trashmail.ws', 'trashmailer.com', 'trashymail.com', 'trashymail.net', 'trayna.com', 'trbvm.com', 'trickmail.net', 'trillianpro.com', 'trimix.cn', 'tryalert.com', 'turboprinz.de', 'turboprinzessin.de', 'turual.com', 'twinmail.de', 'twoweirdtricks.com', 'tyldd.com', 'ubismail.net', 'uggsrock.com', 'uk2.net', 'ukr.net', 'umail.net', 'unmail.ru', 'unterderbruecke.de', 'upliftnow.com', 'uplipht.com', 'uroid.com', 'username.e4ward.com', 'valemail.net', 'venompen.com', 'verlass-mich-nicht.de', 'veryrealemail.com', 'vidchart.com', 'viditag.com', 'viewcastmedia.com', 'viewcastmedia.net', 'viewcastmedia.org', 'vinbazar.com', 'vollbio.de', 'volloeko.de', 'vomoto.com', 'vorsicht-bissig.de', 'vorsicht-scharf.de', 'vubby.com', 'walala.org', 'walkmail.net', 'war-im-urlaub.de', 'wbb3.de', 'webemail.me', 'webm4il.info', 'webmail4u.eu', 'webuser.in', 'wee.my', 'weg-werf-email.de', 'wegwerf-email-addressen.de', 'wegwerf-emails.de', 'wegwerfadresse.de', 'wegwerfemail.com', 'wegwerfemail.de', 'wegwerfmail.de', 'wegwerfmail.info', 'wegwerfmail.net', 'wegwerfmail.org', 'wegwerpmailadres.nl', 'weibsvolk.de', 'weibsvolk.org', 'weinenvorglueck.de', 'wetrainbayarea.com', 'wetrainbayarea.org', 'wh4f.org', 'whatiaas.com', 'whatpaas.com', 'whatsaas.com', 'whopy.com', 'whtjddn.33mail.com', 'whyspam.me', 'wickmail.net', 'wilemail.com', 'will-hier-weg.de', 'willhackforfood.biz', 'willselfdestruct.com', 'winemaven.info', 'wir-haben-nachwuchs.de', 'wir-sind-cool.org', 'wirsindcool.de', 'wmail.cf', 'wolke7.net', 'wollan.info', 'women-at-work.org', 'wormseo.cn', 'wronghead.com', 'wuzup.net', 'wuzupmail.net', 'www.e4ward.com', 'www.gishpuppy.com', 'www.mailinator.com', 'wwwnew.eu', 'xagloo.com', 'xemaps.com', 'xents.com', 'xmail.com', 'xmaily.com', 'xoxox.cc', 'xoxy.net', 'xsecurity.org', 'xyzfree.net', 'yapped.net', 'yeah.net', 'yep.it', 'yert.ye.vc', 'yesey.net', 'yogamaven.com', 'yomail.info', 'yopmail.com', 'yopmail.fr', 'yopmail.gq', 'yopmail.net', 'yopweb.com', 'youmail.ga', 'youmailr.com', 'ypmail.webarnak.fr.eu.org', 'ystea.org', 'yuurok.com', 'yzbid.com', 'za.com', 'zehnminutenmail.de', 'zetmail.com', 'zippymail.info', 'zoaxe.com', 'zoemail.com', 'zoemail.net', 'zoemail.org', 'zomg.info', 'zweb.in', 'zxcv.com', 'zxcvbnm.com', 'zzz.com');

        if (sizeof($u_email) == 2) {
            if ($extra_domains != '' || !empty($extra_domains)) {
                if (in_array(trim($u_email[1]), $extra_domains)){
                    return true;
                }elseif (in_array(trim($u_email[1]), $domains)){
                    return true;
                }
            }
        }
        return false;
    }

    public static function get_values_from_db()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('mo_email_domains');
        $query->from($db->quoteName('#__miniorange_jnsp_registersecurity_setup'));
        $db->setQuery($query);
        $config = $db->loadAssoc();
        return $config;
    }

    public static function _get_all_login_attempts_count(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->quoteName('#__miniorange_login_transactions_reports'));
        $db->setQuery($query);
        $config = $db->loadResult();
        return $config;
    }

    public static function _get_login_transaction_reports()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_login_transactions_reports'));
        $db->setQuery($query);
        $config = $db->loadAssocList();
        return $config;
    }

    public static function _get_login_attempts_count($limit, $offset, $order="down")
    {
        $db = JFactory::getDbo();
        $temp = array();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_login_transactions_reports'));
        if($order=="down")
            $query->order('created_timestamp DESC');
        $query->setLimit($limit, $offset);
        $db->setQuery($query);
        $temp[] = $db->loadAssocList();
        return $temp;
    }

    public static function _get_login_transaction_reports_val()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_login_transactions_reports'));
        $db->setQuery($query);
        $attributes = $db->loadAssoc();
        return $attributes;
    }

    public static function _add_login_transaction_details($ipAddress, $username, $type, $status, $isadmin, $country_name, $browser_name, $os, $url = null)
    {
        $total_entries = NetworkSecurityUtilities::_get_all_login_attempts_count();
        $url = is_null($url) ? '' : $url;
        $current_time = time();

        $is_update = false;
        $login_reports = self::_get_login_transaction_reports_val();

        if(isset($login_reports['id']))
        {
            if($login_reports['id'] == 1)
            {
                $db_ip_val   = $login_reports['ip_address'] ?? '';
                $db_username = $login_reports['username'] ?? '';
                $db_status   = $login_reports['status'] ?? '';
            }
        }

        if ($total_entries == 0)
        {
            $id = 1;
        }
        else if($total_entries == 1 && empty($db_ip_val) && empty($db_username) && empty($db_status))
        {
            $id = 1;
            $is_update = true;
        }
        else
        {
            $results = self::_get_login_transaction_reports();
            if(!empty($results))
            {
                foreach ($results as $key => $value)
                {
                    $id = $value['id'];
                }
                $id += 1;
            }
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('ip_address')        . ' = ' . $db->quote($ipAddress),
            $db->quoteName('username')          . ' = ' . $db->quote($username),
            $db->quoteName('type')              . ' = ' . $db->quote($type),
            $db->quoteName('status')            . ' = ' . $db->quote($status),
            $db->quoteName('created_timestamp') . ' = ' . $db->quote($current_time),
            $db->quoteName('url')               . ' = ' . $db->quote($url),
            $db->quoteName('isadmin_user')      . ' = ' . $db->quote($isadmin),
            $db->quoteName('country_name')      . ' = ' . $db->quote($country_name),
            $db->quoteName('browser_name')      . ' = ' . $db->quote($browser_name),
            $db->quoteName('operating_system')  . ' = ' . $db->quote($os),
        );
        if ($is_update){
            $query->update($db->quoteName('#__miniorange_login_transactions_reports'))->set($fields);
        }else{
            $query->insert($db->quoteName('#__miniorange_login_transactions_reports'))->set($fields);
        }

        $db->setQuery($query);
        $db->execute();
    }

    public static function _get_current_user_browser()
    {
        $browser = JBrowser::getInstance();
        $browser = $browser->getBrowser();

        if ($browser == "edg")
            $browser = "edge";

        return $browser ?? 'something else';
    }

    public static function _get_country_name($userIp)
    {
        $result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $userIp), true);

        try {
            $timeoffset = timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]), new DateTime('now'));
            $timeoffset = $timeoffset / 3600;
        } catch (Exception $e) {
            $result["geoplugin_timezone"] = "";
            $timeoffset = "";
        }

        if ($result['geoplugin_request'] == $userIp) {
            $ipLookup['Country'] = $result["geoplugin_countryName"];
        }

        $country_name = $ipLookup['Country'];
        return $country_name;
    }

    public static function _get_os_info()
    {

        if (isset($_SERVER)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        } else {
            global $HTTP_SERVER_VARS;
            if (isset($HTTP_SERVER_VARS)) {
                $user_agent = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
            } else {
                global $HTTP_USER_AGENT;
                $user_agent = $HTTP_USER_AGENT;
            }
        }

        $os_array = [
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

            // Doesn't seem like these are necessary...not totally sure though..
            //'(winnt)([0-9]{1,2}\.[0-9]{1,2}){0,1}'=>'Windows NT',
            //'(windows nt)(([0-9]{1,2}\.[0-9]{1,2}){0,1})'=>'Windows NT', // fix by bg

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
            // Loads of Linux machines will be detected as unix.
            // Actually, all of the linux machines I've checked have the 'X11' in the User Agent.
            //'X11'=>'Unix',
            '(linux)' => 'Linux',
            '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
            'amiga-aweb' => 'AmigaOS',
            'amiga' => 'Amiga',
            'AvantGo' => 'PalmOS',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1}-([0-9]{1,2}) i([0-9]{1})86){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1} i([0-9]{1}86)){1}'=>'Linux',
            //'(Linux)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3}(rel\.[0-9]{1,2}){0,1})'=>'Linux',
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

            // delete next line if the script show not the right OS
            //'(PHP)/([0-9]{1,2}.[0-9]{1,2})'=>'PHP',
            'MS FrontPage' => 'Windows',
            '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
            'libwww-perl' => 'Unix',
            'UP.Browser' => 'Windows CE',
            'NetAnts' => 'Windows',
        ];

        $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
        $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

        foreach ($os_array as $regex => $value) {
            if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
                return $value . ' x' . $arch;
            }
        }

        return 'Unknown';
    }

    public static function check_empty_or_null($value)
    {
        if (empty($value)) {
            return true;
        }
        return false;
    }

    public static function GetPluginVersion()
    {
        $db = JFactory::getDbo();
        $dbQuery = $db->getQuery(true)
            ->select('manifest_cache')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . " = " . $db->quote('com_joomla_networksecurity'));
        $db->setQuery($dbQuery);
        $manifest = json_decode($db->loadResult());
        return ($manifest->version);
    }

    function _get_feedback_form($post)
    {
        $db_table = '#__miniorange_networksecurity_customer';
        $db_coloums = array('uninstall_feedback' => 1,);

        self::__genDBUpdate($db_table, $db_coloums);
        $customerResult = self::__getDBValuesArray('#__miniorange_networksecurity_customer');

        if (!isset($post['skip_feedback'])) {
            $radio = $post['deactivate_plugin'] ?? '';
            $data = $post['query_feedback'] ?? '';
            
            $current_user = JFactory::getUser();
            $admin_email_default = $current_user->email ?? '';

            $admin_email = $post['query_email'] ?? '';
            $admin_phone = $customerResult['admin_phone'] ?? '';
            $data1 = $radio . ' : ' . $data;

            require_once JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomla_networksecurity' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_networksecurity_customer_setup.php';
            self::submit_feedback_form($admin_email,$admin_email_default, $admin_phone, $data1);
        }
        require_once JPATH_SITE . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Installer' . DIRECTORY_SEPARATOR . 'Installer.php';

        if (isset($post['result'])) {
            
            foreach ($post['result'] as $fbkey) {
                echo "<script>alert('".$fbkey."');</script>";
                $result = self::__getDBValuesUsingColumns('type', '#__extensions', $fbkey);
                $identifier = $fbkey;
                $type = 0;

                foreach ($result as $results) {
                    $type = $results;
                }
                if ($type) {
                    $cid = 0;
                    $installer = new JInstaller();
                    $installer->uninstall($type, $identifier, $cid);
                }
            }
        }
    }

    public static function submit_feedback_form($email, $admin_email, $phone, $query)
    {

        $url = 'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);
        $customerKey = "16555";
        $apiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
        $currentTimeInMillis = round(microtime(true) * 1000);
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader = "Authorization: " . $hashValue;
        $fromEmail = $email;
        $subject = "MiniOrange Joomla Feedback for Web Security Lite";
        $phpVersion = phpversion();
        $jVersion = new JVersion();
        $jCmsVersion = $jVersion->getShortVersion();
        $moPluginVersion = self::GetPluginVersion();
        $os_info = self::_get_os_info();
        $system_info = "Joomla ".$jCmsVersion." | PHP ".$phpVersion." | Plugin ".$moPluginVersion." | OS ".$os_info;

        $query1 = "Joomla Web Security Lite Plugin";
        $content = '<div >Hello, <br><br>Company :<a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                    <b>Phone Number: </b>' . $phone . '<br><br>
                    <b>Admin Email: </b>' . $admin_email . '<br><br>
                    <b>Email: </b><a href="mailto:' . $fromEmail . '" target="_blank">' . $fromEmail . '</a><br><br>
                    <b>Plugin Deactivated: </b>' . $query1 . '<br><br>
                    <b>Reason: </b>' . $query . '<br><br>
                    <b>System info: </b>'.$system_info.'           
                    </div>';


        $fields = array(
            'customerKey' => $customerKey,
            'sendEmail' => true,
            'email' => array(
                'customerKey' => $customerKey,
                'fromEmail' => $fromEmail,
                'fromName' => 'miniOrange',
                'toEmail' => 'shubham.pokharna@xecurify.com',
                'toName' => 'shubham.pokharna@xecurify.com',
                'subject' => $subject,
                'content' => $content
            ),
        );
        $field_string = json_encode($fields);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls

        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            return json_encode(array("status" => 'ERROR', 'statusMessage' => curl_error($ch)));
        }
        curl_close($ch);

        return ($content);
    }

    public static function plugin_efficiency_check($email)
    {
        $c_time =date("Y-m-d",time());
        $base_url = JURI::root();
        $url =  'https://login.xecurify.com/moas/api/notify/send';
        $ch = curl_init($url);


        $customerKey = base64_decode("MTY1NTU=");
        $apiKey = base64_decode("ZkZkMlhjdlRHRGVtWnZidzFiY1Vlc05KV0VxS2JiVXE=");

        $currentTimeInMillis= round(microtime(true) * 1000);
        $stringToHash 		= $customerKey .  number_format($currentTimeInMillis, 0, '', '') . $apiKey;
        $hashValue 			= hash("sha512", $stringToHash);
        $customerKeyHeader 	= "Customer-Key: " . $customerKey;
        $timestampHeader 	= "Timestamp: " .  number_format($currentTimeInMillis, 0, '', '');
        $authorizationHeader= "Authorization: " . $hashValue;
        $fromEmail 			= $email;
        $subject            = "Joomla Web Security Lite Plugin [Free] efficiency Check ";
        $phpVersion = phpversion();
        $jVersion = new JVersion();
        $jCmsVersion = $jVersion->getShortVersion();
        $os_info = self::_get_os_info();
        $system_info = "Joomla ".$jCmsVersion." | PHP ".$phpVersion." | OS ".$os_info;

        $query1 =" miniOrange Joomla Web Security Lite [Free] Plugin to improve efficiency ";
        $content='<div >Hello, <br><br>
                    <strong>Company :</strong><a href="'.$_SERVER['SERVER_NAME'].'" target="_blank" >'.$_SERVER['SERVER_NAME'].'</a><br><br>
                    <strong>Email :</strong><a href="mailto:'.$fromEmail.'" target="_blank">'.$fromEmail.'</a><br><br>
                    <strong>Plugin Efficency Check: </strong>'.$query1.'<br><br>
                    <strong>Website: </strong>'.$base_url.'<br><br>
                    <strong>Creation Date: </strong>'.$c_time.'<br><br>
                    <strong>System Info: </strong>'.$system_info.'</div>';

        $fields = array(
            'customerKey'	=> $customerKey,
            'sendEmail' 	=> true,
            'email' 		=> array(
                'customerKey' 	=> $customerKey,
                'fromEmail' 	=> $fromEmail,
                'bccEmail'      => 'shubham.pokharna@xecurify.com',
                'fromName' 		=> 'miniOrange',
                'toEmail' 		=> 'rohit.tejani@xecurify.com',
                'toName' 		=> 'rohit.tejani@xecurify.com',
                'subject' 		=> $subject,
                'content' 		=> $content
            ),
        );
        $field_string = json_encode($fields);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING, "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls

        curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", $customerKeyHeader,
            $timestampHeader, $authorizationHeader));
        curl_setopt( $ch, CURLOPT_POST, true);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $field_string);
        $content = curl_exec($ch);
        if(curl_errno($ch)){

            return;
        }
        curl_close($ch);
        return;
    }

    public static function _download_reports()
    {
        $data = self::_get_login_transaction_reports();
        $reports = "SR.NO.,IP ADDRESS,END USER/ADMIN,USERNAME,STATUS,DATE & TIME,COUNTRY,BROWSER,OPERATING SYSTEM\n";

        $i = 1;
        foreach ($data as $key => $value) {

            $timestamp = $value['created_timestamp'];
            $date = date('d-m-Y H:i:s', $timestamp);
            $reports .= $i . ',' . $value['ip_address'] . ',' . $value['isadmin_user'] . ','
                . $value['username'] . ',' . $value['status'] . ',' . $date . ',' . $value['country_name'] .','
                . $value['browser_name'] .',' . $value['operating_system'] ."\n";
            $i++;
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="reports.csv"');
        print_r($reports);
        exit();
    }

    public static function _check_url($val)
    {
        $root = JURI::root();

        $tables = JFactory::getDbo()->getTableList();

        foreach ($tables as $table) {
            if (strpos($table, "miniorange_jnsp_loginsecurity_setup")){
                $login_config = NetworkSecurityUtilities::getLoginSecurityConfig();
            }
        }

        $is_cust_admn_lgn = $login_config['enable_custom_admin_login'] ?? 0;

        $url_key = $login_config['access_lgn_urlky'] ?? '';
        $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $cstm_lnk = $root . 'administrator/?' . $url_key;

        $custom_destination = $login_config['custom_failure_destination'] ?? '';
        $custom_err_message = $login_config['custom_message_after_fail'] ?? '';
        $failure_response = $login_config['after_adm_failure_response'] ?? '';

        if (strpos($requested_uri, 'administrator') !== false) {
            $is_admin = 1;
        } else {
            $is_admin = 0;
        }

        $len_actual_lnk = strlen($requested_uri);
        $len_root = strlen($root);

        if (!self::isUserLogin()) {
            return;
        }

        if ($is_cust_admn_lgn != 1) {
            return;
        }

        if (!$is_admin) {
            return;
        }

        if ($len_root === $len_actual_lnk) {
            return;
        }

        $check = self::clnk($requested_uri, $cstm_lnk);

        if ($check) {
            return;
        }

        if ($is_cust_admn_lgn == 1 && $val == 1)
        {
            self::_custom_redirect_message($root, $failure_response, $custom_destination, $custom_err_message);
        }

        if (self::checkDoLogin()) {
            return;
        }

        self::_custom_redirect_message($root, $failure_response, $custom_destination, $custom_err_message);
    }

    public static function _custom_redirect_message($root, $failure_response, $custom_destination, $custom_err_message)
    {
        if (!empty('redirect_homepage' == $failure_response)) {
            $app = JFactory::getApplication();
            $app->redirect($root);
        }

        if ($failure_response == 'custom_redirect_url') {
            if (empty($custom_destination)) {
                $custom_destination = "https://www.google.com";
            }

            if (!str_contains($custom_destination, "https://") && !str_contains($custom_destination, "http://")) {
                $custom_destination = "https://$custom_destination";
            }

            $app = JFactory::getApplication();
            $app->redirect($custom_destination);
        }

        if (('404_custom_message' == $failure_response)) {
            if (empty($custom_err_message)) {
                $custom_err_message = "Some error has been occured";
            }
            ?>
            <html lang="">
            <h1>404 Page Not Found</h1>
            <hr>
            <?php echo $custom_err_message; ?>
            </html>
            <?php
            exit();
        }
    }

    public static function clnk($url1, $url2)
    {
        if ($url1 == $url2) {
            return 1;
        } else {
            return 0;
        }
    }

    public static function checkDoLogin()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        return isset($post['task']) && 'login' === $post['task'];
    }

    public static function isUserLogin()
    {
        $user = JFactory::getUser();
        return $user->get('guest');
    }

    public static function __save_browser_blocking($post)
    {
        $browser_bl_enable = $post['mo_enable_browser_blocking'] ?? 0;
        $medge = $post['mo_medge_blocking'] ?? 0;

        if($browser_bl_enable == 0 || ($medge == 1)){
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('mo_enable_browser_blocking') . ' = ' . $db->quote($browser_bl_enable),
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
        else{
            return 0;
        }
    }

    public static function _is_ip_blocked($userIPAddress)
    {
        return MoNetworkSecurityUtility::_is_ip_blocked($userIPAddress);
    }

    public static function _get_advance_ip()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__miniorange_jnsp_advance_blocking'));
        $db->setQuery($query);
        $config = $db->loadAssoc();
        return $config;
    }

    public static function _show_error_message()
    {

        echo "
           <div style='margin-top: 22px;background-color: #ffffff;border-radius: 4px;box-shadow: 0 5px 15px rgba(0,0,0,.5); width:908px;height:412px; align-self: center; margin: 0 auto; ' >
               
                    <b><p style='margin-left:30px; font-size:25px ;margin-top: 7%;padding-top: 22px;'>You have been Blocked!</p></b>
                       <p style='margin-left:30px; font-size:large;margin-top: 0; '> Sorry, access to the requested page has been revoked. </p>                                                  
                       </p>
                       <p style='padding-left:30px;font-size:large;margin-top: 0; margin-bottom: 10px'> Please contact your site Administrator for unblocking. </p>                      
                       <p style='margin-left:30px; font-size:1.5em; text-align:center;color:red'>403 Error!</p>    
                </div>";
        exit();
    }

    public static function _check_passwd_strength($passwd)
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

    public static function _redirect_with_error_message($message)
    {
        $app = JFactory::getApplication();
        $app->enqueueMessage($message, 'error');
        $app->redirect(JRoute::_('index.php/component/users/?view=registration&Itemid=101'));
    }

    public static function handle_change_password($username,$new_passwd,$confirm_passwd)
    {
        if($new_passwd != $confirm_passwd)
        {
            $message = 'Both Passwords do not match';
            self::_redirect_with_error_message($message);
        } else {

            $userId = JUserHelper::getUserId($username);
            $salt = JUserHelper::genRandomPassword(32);

            $password_hash = md5($confirm_passwd . $salt) . ":" . $salt;

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('password') . ' = ' . $db->quote($password_hash),
            );
            $conditions = array(
                $db->quoteName('id') . ' = ' . $db->quote($userId),
            );
            $query->update($db->quoteName('#__users'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();

            $app = JFactory::getApplication();
            $app->enqueueMessage("You have successfully updated your password", 'success');
            $app->redirect(JRoute::_('index.php'));
        }
    }
    public static function get_last_ip(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('Max(id)');
        $query->from($db->quoteName('#__miniorange_networksecurity_customer'));
        $db->setQuery($query);
        return $db->loadResult();
    }

    public static function __getDBValuesUsingColumns($type, $table, $fbkey)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($type);
        $query->from($table);
        $query->where($db->quoteName('extension_id') . " = " . $db->quote($fbkey));
        $db->setQuery($query);
        $result = $db->loadColumn();
        return $result;
    }

    public static function __getDBValuesArray($table)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select(array('*'));
        $query->from($db->quoteName($table));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $customerResult = $db->loadAssoc();
        return $customerResult;
    }

    public static function __genDBUpdate($db_table, $db_columns)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        foreach ($db_columns as $key => $value) {
            $database_values[] = $db->quoteName($key) . ' = ' . $db->quote($value);
        }

        $query->update($db->quoteName($db_table))->set($database_values)->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $db->execute();
    }
}