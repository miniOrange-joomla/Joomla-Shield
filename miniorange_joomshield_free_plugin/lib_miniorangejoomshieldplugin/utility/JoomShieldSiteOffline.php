<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class JoomShieldSiteOffline
{
	public static function enforce()
	{
		if (JoomShieldCompat::isCli())
		{
			return;
		}

		$config = JoomShieldSiteProtection::getConfig();

		if ((int) ($config['emergency_offline'] ?? 0) !== 1)
		{
			return;
		}

		if (JoomShieldCompat::isAdministrator())
		{
			return;
		}

		$user = JoomShieldCompat::getUser();

		if (JoomShieldCompat::isSuperUser($user) && empty($user->guest))
		{
			return;
		}

		$ip = JoomShieldUtilities::getClientIp();
		$whitelist = JoomShieldCompat::parseIpList($config['offline_whitelist_ips'] ?? '');

		if (JoomShieldCompat::ipInList($ip, $whitelist))
		{
			return;
		}

		self::renderOfflinePage();
	}

	public static function renderOfflinePage()
	{
		try
		{
			Factory::getLanguage()->load('com_joomshield', JPATH_ADMINISTRATOR);
		}
		catch (\Throwable $e)
		{
		}

		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 3600');
		header('Content-Type: text/html; charset=utf-8');

		$title = Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE');
		$message = Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_PUBLIC_MESSAGE');
		$brand = Text::_('COM_JOOMSHIELD_MANAGER_SHIELD');

		echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>';
		echo '<style>*{box-sizing:border-box}body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;background:#f4f6f8;color:#263238}';
		echo '.wrap{width:100%;max-width:680px}.card{overflow:hidden;background:#fff;border:1px solid #d8dce0;border-radius:8px;box-shadow:0 10px 32px rgba(0,0,0,.08)}';
		echo '.bar{height:6px;background:#3d618f}.content{padding:40px}.icon{display:flex;align-items:center;justify-content:center;width:52px;height:52px;margin-bottom:22px;border-radius:50%;font-size:28px;font-weight:700;color:#3d618f;background:#e8eef5}';
		echo 'h1{margin:0 0 12px;font-size:28px;line-height:1.25;color:#1f3047}p{margin:0 0 26px;font-size:16px;line-height:1.65;color:#52606d}';
		echo '.footer{display:flex;align-items:center;justify-content:space-between;gap:16px;padding-top:20px;border-top:1px solid #e5e7eb}small{color:#6b7280}.retry{padding:10px 16px;border:1px solid #3d618f;border-radius:4px;color:#fff;background:#3d618f;font:inherit;font-weight:600;cursor:pointer}.retry:hover{background:#325173}@media(max-width:520px){.content{padding:28px}.footer{align-items:stretch;flex-direction:column}}</style></head><body>';
		echo '<main class="wrap"><section class="card" role="alert"><div class="bar"></div><div class="content"><div class="icon" aria-hidden="true">!</div>';
		echo '<h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';
		echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><div class="footer">';
		echo '<small>' . htmlspecialchars($brand, ENT_QUOTES, 'UTF-8') . '</small>';
		echo '<button class="retry" type="button" onclick="window.location.reload()">' . htmlspecialchars(Text::_('COM_JOOMSHIELD_TRY_AGAIN'), ENT_QUOTES, 'UTF-8') . '</button>';
		echo '</div></div></section></main></body></html>';
		exit;
	}

	public static function currentHost()
	{
		$host = parse_url(Uri::root(), PHP_URL_HOST);

		return is_string($host) ? $host : '';
	}
}
