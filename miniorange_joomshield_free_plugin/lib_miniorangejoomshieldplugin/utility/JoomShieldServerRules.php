<?php

use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class JoomShieldServerRules
{
	public const BEGIN_MARKER = '# BEGIN miniOrange JoomShield';
	public const END_MARKER = '# END miniOrange JoomShield';

	public static function htaccessPath()
	{
		return JPATH_ROOT . DIRECTORY_SEPARATOR . '.htaccess';
	}

	public static function passwdPath()
	{
		return JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . '.joomshield_passwd';
	}

	public static function backupDir()
	{
		return JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR
			. 'com_joomshield' . DIRECTORY_SEPARATOR . 'backups';
	}

	public static function nginxSnippetPath()
	{
		return JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR
			. 'com_joomshield' . DIRECTORY_SEPARATOR . 'nginx' . DIRECTORY_SEPARATOR . 'joomshield.conf';
	}

	public static function nginxIncludeDirective()
	{
		$path = str_replace('\\', '/', self::nginxSnippetPath());

		return 'include "' . str_replace('"', '\\"', $path) . '";';
	}

	public static function detectedServerKind()
	{
		$software = strtolower((string) ($_SERVER['SERVER_SOFTWARE'] ?? ''));

		if ($software === '')
		{
			return 'unknown';
		}

		if (strpos($software, 'nginx') !== false)
		{
			return 'nginx';
		}

		if (strpos($software, 'apache') !== false || strpos($software, 'litespeed') !== false)
		{
			return 'apache';
		}

		return 'unknown';
	}

	public static function detectedServerSoftware()
	{
		$software = trim((string) ($_SERVER['SERVER_SOFTWARE'] ?? ''));

		return $software !== '' ? $software : 'unknown';
	}

	public static function applyFromConfig()
	{
		$config = JoomShieldSiteProtection::getConfig();
		$nginxResult = self::writeGeneratedNginxFile();
		$authEnabled = ((int) ($config['admin_http_auth'] ?? 0) === 1);

		if ((int) ($config['server_rules_apache'] ?? 0) !== 1)
		{
			$removed = self::removeMarkedBlock();
			$warning = trim((string) ($nginxResult['warning'] ?? ''));

			if (!$authEnabled && $removed)
			{
				self::removeApachePassword();
			}
			elseif (!$removed && self::markedBlockPresent())
			{
				$warning = trim($warning . ' The .htaccess file could not be updated. Previously written JoomShield rules may still be active.');
			}

			return [
				'ok' => true,
				'warning' => $warning,
			];
		}

		$passwdWarning = self::writePasswdFile($config);
		$htaccessResult = self::writeHtaccessBlock($config);

		if (!$authEnabled && !empty($htaccessResult['ok']))
		{
			self::removeApachePassword();
		}

		$warning = trim($passwdWarning . ' ' . ($htaccessResult['warning'] ?? '') . ' ' . ($nginxResult['warning'] ?? ''));

		return [
			'ok' => (bool) ($htaccessResult['ok'] ?? false),
			'warning' => $warning,
		];
	}

	public static function writeGeneratedNginxFile()
	{
		$path = self::nginxSnippetPath();
		$dir = dirname($path);

		if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir))
		{
			return [
				'ok' => false,
				'warning' => 'The Nginx snippet file could not be updated automatically. Copy the current rules from the Nginx Rules tab.',
			];
		}

		$htaccessDeny = $dir . DIRECTORY_SEPARATOR . '.htaccess';

		if (!is_file($htaccessDeny))
		{
			@file_put_contents($htaccessDeny, "Require all denied\n");
		}

		$indexPath = $dir . DIRECTORY_SEPARATOR . 'index.html';

		if (!is_file($indexPath))
		{
			@file_put_contents($indexPath, '');
		}

		$written = @file_put_contents($path, self::getNginxSnippet());

		if ($written === false)
		{
			return [
				'ok' => false,
				'warning' => 'The Nginx snippet file could not be updated automatically. Copy the current rules from the Nginx Rules tab.',
			];
		}

		return [
			'ok' => true,
			'warning' => '',
		];
	}

	public static function writePasswdFile($config)
	{
		if ((int) ($config['admin_http_auth'] ?? 0) !== 1)
		{
			return '';
		}

		$path = self::passwdPath();

		if (is_file($path))
		{
			return '';
		}

		return 'Create the Apache password file by saving Administrator Access Protection with a password. PHP protection is still active.';
	}

	public static function writeApachePassword($username, $plainPassword)
	{
		$sha = '{SHA}' . base64_encode(sha1($plainPassword, true));
		$path = self::passwdPath();
		$line = $username . ':' . $sha . "\n";

		if (@file_put_contents($path, $line) === false)
		{
			return false;
		}

		return true;
	}

	public static function writeHtaccessBlock($config)
	{
		$path = self::htaccessPath();
		$block = self::buildApacheBlock($config);

		if (!is_file($path))
		{
			$written = @file_put_contents($path, $block . "\n");

			return [
				'ok' => $written !== false,
				'warning' => $written === false ? 'The .htaccess file could not be created. PHP protection is still active.' : '',
			];
		}

		if (!is_readable($path) || !is_writable($path))
		{
			return [
				'ok' => false,
				'warning' => 'The .htaccess file is not writable. PHP protection is still active.',
			];
		}

		$contents = (string) file_get_contents($path);
		self::backupHtaccess($contents);
		$updated = self::replaceMarkedBlock($contents, $block);

		if (@file_put_contents($path, $updated) === false)
		{
			return [
				'ok' => false,
				'warning' => 'The .htaccess file could not be updated. PHP protection is still active.',
			];
		}

		return [
			'ok' => true,
			'warning' => '',
		];
	}

	public static function removeApachePassword()
	{
		$path = self::passwdPath();

		if (is_file($path))
		{
			@unlink($path);
		}
	}

	public static function removeMarkedBlock()
	{
		$path = self::htaccessPath();

		if (!is_file($path))
		{
			return true;
		}

		if (!is_readable($path) || !is_writable($path))
		{
			return false;
		}

		$contents = (string) file_get_contents($path);
		$updated = self::replaceMarkedBlock($contents, '');

		if ($updated === $contents)
		{
			return true;
		}

		self::backupHtaccess($contents);

		return @file_put_contents($path, $updated) !== false;
	}

	public static function getNginxSnippet()
	{
		$config = JoomShieldSiteProtection::getConfig();
		$offlineIps = JoomShieldCompat::parseIpList($config['offline_whitelist_ips'] ?? '');
		$authIps = JoomShieldCompat::parseIpList($config['admin_http_whitelist_ips'] ?? '');
		$passwd = str_replace('\\', '/', self::passwdPath());
		$basePath = rtrim((string) Uri::root(true), '/');
		$adminPath = $basePath . '/administrator';
		$adminPattern = preg_quote($adminPath, '#');
		$offlineEnabled = ((int) ($config['emergency_offline'] ?? 0) === 1);
		$authEnabled = ((int) ($config['admin_http_auth'] ?? 0) === 1);
		$lines = [];
		$lines[] = self::BEGIN_MARKER;
		$lines[] = '# Paste this snippet into the Nginx server { } block that serves this site,';
		$lines[] = '# or include the generated file from the Nginx Rules tab, then reload Nginx.';
		$lines[] = '# PHP protection stays active even if these rules are not applied.';
		$lines[] = '# After you disable a JoomShield restriction feature, replace any previously';
		$lines[] = '# pasted JoomShield Nginx rules with this snippet (or include this file) and reload Nginx.';
		$lines[] = '';

		if (!$offlineEnabled && !$authEnabled)
		{
			$lines[] = '# No extra JoomShield Nginx restriction rules are required for the current settings.';
			$lines[] = '# If you previously pasted JoomShield rules into your Nginx config, delete those lines and reload Nginx.';
			$lines[] = self::END_MARKER;

			return implode("\n", $lines) . "\n";
		}

		if ($authEnabled)
		{
			$lines[] = '# Add the following directives inside your existing Nginx location that handles ' . $adminPath . '.';
			$lines[] = '# Keeping the existing location preserves its PHP/FastCGI configuration.';
			$lines[] = '# PHP protection works on Nginx when the Authorization header is forwarded:';
			$lines[] = '# fastcgi_param HTTP_AUTHORIZATION $http_authorization;';
			$lines[] = '# Optional extra web-server prompt (in addition to PHP):';
			$lines[] = '# auth_basic "JoomShield Administrator Access Protection";';
			$lines[] = '# auth_basic_user_file "' . str_replace('"', '\\"', $passwd) . '";';

			if (!empty($authIps))
			{
				$lines[] = '# satisfy any;';

				foreach ($authIps as $ip)
				{
					$lines[] = '# allow ' . $ip . ';';
				}

				$lines[] = '# deny all;';
			}

			$lines[] = '';
		}

		if ($offlineEnabled)
		{
			$offlineBody = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Site temporarily unavailable</title><style>body{margin:0;background:#f4f6f8;color:#263238;font-family:Arial,sans-serif}.wrap{max-width:640px;margin:12vh auto;padding:32px}.card{background:#fff;border:1px solid #d8dce0;border-radius:8px;padding:32px;box-shadow:0 8px 24px rgba(0,0,0,.08)}h1{font-size:26px;margin:0 0 12px}p{line-height:1.6;color:#52606d}</style></head><body><main class="wrap"><section class="card"><h1>Site temporarily unavailable</h1><p>Maintenance is currently in progress. Please try again later.</p></section></main></body></html>';
			$lines[] = 'set $joomshield_offline 1;';
			$lines[] = 'if ($request_uri ~* "^' . $adminPattern . '(?:/|$)") { set $joomshield_offline 0; }';

			foreach ($offlineIps as $ip)
			{
				$lines[] = 'if ($remote_addr = ' . $ip . ') { set $joomshield_offline 0; }';
			}

			$lines[] = 'if ($joomshield_offline = 1) { return 503; }';
			$lines[] = 'error_page 503 =503 @joomshield_offline;';
			$lines[] = 'location @joomshield_offline {';
			$lines[] = '    default_type text/html;';
			$lines[] = '    add_header Retry-After 3600 always;';
			$lines[] = "    return 503 '" . $offlineBody . "';";
			$lines[] = '}';
		}

		$lines[] = self::END_MARKER;

		return implode("\n", $lines) . "\n";
	}

	private static function buildApacheBlock($config)
	{
		$lines = [
			self::BEGIN_MARKER,
			'<IfModule mod_rewrite.c>',
			'RewriteEngine On',
			'RewriteCond %{HTTP:Authorization} .',
			'RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]',
		];

		if ((int) ($config['emergency_offline'] ?? 0) === 1)
		{
			$whitelist = JoomShieldCompat::parseIpList($config['offline_whitelist_ips'] ?? '');
			$adminPath = rtrim((string) Uri::root(true), '/') . '/administrator';
			$lines[] = 'RewriteCond %{REQUEST_URI} !^' . preg_quote($adminPath, '#') . '(?:/|$)';

			foreach ($whitelist as $ip)
			{
				$escaped = str_replace('.', '\.', $ip);
				$lines[] = 'RewriteCond %{REMOTE_ADDR} !^' . $escaped . '$';
			}

			$lines[] = 'RewriteRule .* - [R=503,L]';
		}

		$lines[] = '</IfModule>';

		if ((int) ($config['admin_http_auth'] ?? 0) === 1 && is_file(self::passwdPath()))
		{
			$authFile = str_replace('\\', '/', self::passwdPath());
			$lines[] = '<If "%{REQUEST_URI} =~ m#administrator#">';
			$lines[] = 'CGIPassAuth On';
			$lines[] = 'AuthType Basic';
			$lines[] = 'AuthName "JoomShield Administrator Access Protection"';
			$lines[] = 'AuthUserFile "' . $authFile . '"';
			$lines[] = 'Require valid-user';

			$authIps = JoomShieldCompat::parseIpList($config['admin_http_whitelist_ips'] ?? '');

			if (!empty($authIps))
			{
				$lines[] = 'Require ip ' . implode(' ', $authIps);
			}

			$lines[] = '</If>';
		}

		$lines[] = self::END_MARKER;

		return implode("\n", $lines);
	}

	private static function markedBlockPresent()
	{
		$path = self::htaccessPath();

		if (!is_file($path) || !is_readable($path))
		{
			return false;
		}

		$contents = (string) file_get_contents($path);

		return strpos($contents, self::BEGIN_MARKER) !== false;
	}

	private static function replaceMarkedBlock($contents, $block)
	{
		$pattern = '/' . preg_quote(self::BEGIN_MARKER, '/') . '.*?' . preg_quote(self::END_MARKER, '/') . '\s*/s';

		if (preg_match($pattern, $contents))
		{
			$replacement = $block === '' ? '' : $block . "\n";

			return preg_replace($pattern, $replacement, $contents, 1);
		}

		if ($block === '')
		{
			return $contents;
		}

		return rtrim($contents) . "\n\n" . $block . "\n";
	}

	private static function backupHtaccess($contents)
	{
		$dir = self::backupDir();

		if (!is_dir($dir))
		{
			@mkdir($dir, 0755, true);
		}

		$target = $dir . DIRECTORY_SEPARATOR . 'htaccess-' . date('Ymd-His') . '.bak';
		@file_put_contents($target, $contents);

		$files = glob($dir . DIRECTORY_SEPARATOR . 'htaccess-*.bak');

		if (is_array($files) && count($files) > 5)
		{
			sort($files);

			foreach (array_slice($files, 0, count($files) - 5) as $old)
			{
				@unlink($old);
			}
		}
	}
}
