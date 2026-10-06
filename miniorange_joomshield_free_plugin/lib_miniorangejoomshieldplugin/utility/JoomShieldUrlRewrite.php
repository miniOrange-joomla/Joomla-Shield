<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class JoomShieldUrlRewrite
{
	public const MAX_LOCATIONS = 50;
	public const MAX_LINE_LENGTH = 255;

	public static function rewriteFrontend()
	{
		if (JoomShieldCompat::isCli() || JoomShieldCompat::isAdministrator())
		{
			return;
		}

		$app = Factory::getApplication();

		if (method_exists($app, 'isClient') && !$app->isClient('site'))
		{
			return;
		}

		$document = method_exists($app, 'getDocument') ? $app->getDocument() : null;

		if ($document && method_exists($document, 'getType') && $document->getType() !== 'html')
		{
			return;
		}

		$config = JoomShieldSiteProtection::getConfig();

		if ((int) ($config['link_migration_live'] ?? 0) !== 1)
		{
			return;
		}

		$oldLocations = self::parseLocations((string) ($config['link_migration_old_hosts'] ?? ''));
		$newLocation = self::currentLocation();

		if (empty($oldLocations) || $newLocation === '')
		{
			return;
		}

		$oldLocations = array_values(
			array_filter(
				$oldLocations,
				static function ($location) use ($newLocation) {
					return $location !== $newLocation;
				}
			)
		);

		if (empty($oldLocations) || !method_exists($app, 'getBody') || !method_exists($app, 'setBody'))
		{
			return;
		}

		$body = (string) $app->getBody();

		if ($body === '' || !self::bodyLooksHtml($body) || !self::bodyMentionsLocations($body, $oldLocations))
		{
			return;
		}

		$rewritten = self::rewriteHtml($body, $oldLocations, $newLocation);

		if ($rewritten !== $body)
		{
			$app->setBody($rewritten);
		}
	}

	public static function sanitizeList($raw)
	{
		return implode("\n", self::parseLocations($raw));
	}

	public static function parseLocations($raw)
	{
		$raw = str_replace("\r", '', (string) $raw);
		$out = [];

		foreach (explode("\n", $raw) as $line)
		{
			$normalized = self::normalizeLocation($line);

			if ($normalized === '' || in_array($normalized, $out, true))
			{
				continue;
			}

			$out[] = $normalized;

			if (count($out) >= self::MAX_LOCATIONS)
			{
				break;
			}
		}

		return $out;
	}

	public static function currentLocation()
	{
		return self::normalizeLocation(Uri::base());
	}

	public static function normalizeLocation($value)
	{
		$value = trim((string) $value);

		if ($value === '' || strlen($value) > self::MAX_LINE_LENGTH)
		{
			return '';
		}

		if (preg_match('/[\s\\\\*@]/', $value))
		{
			return '';
		}

		$value = preg_replace('#^(https?:)?//#i', '', $value);
		$value = trim((string) $value, '/');

		if ($value === '')
		{
			return '';
		}

		$parts = explode('/', $value, 2);
		$hostPort = $parts[0];
		$path = $parts[1] ?? '';

		if (strpos($path, '..') !== false || strpos($path, '?') !== false || strpos($path, '#') !== false)
		{
			return '';
		}

		$host = $hostPort;
		$port = '';

		if ($hostPort !== '' && $hostPort[0] === '[')
		{
			$close = strpos($hostPort, ']');

			if ($close === false)
			{
				return '';
			}

			$host = substr($hostPort, 1, $close - 1);
			$rest = substr($hostPort, $close + 1);

			if ($rest !== '' && $rest[0] === ':')
			{
				$port = substr($rest, 1);
			}
			elseif ($rest !== '')
			{
				return '';
			}
		}
		elseif (substr_count($hostPort, ':') === 1)
		{
			[$host, $port] = explode(':', $hostPort, 2);
		}

		$host = strtolower($host);

		if (function_exists('idn_to_ascii') && preg_match('/[^\x00-\x7F]/', $host))
		{
			$ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

			if ($ascii === false)
			{
				return '';
			}

			$host = strtolower($ascii);
		}

		$isIp = filter_var($host, FILTER_VALIDATE_IP) !== false;
		$isName = (bool) preg_match(
			'/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/',
			$host
		);

		if (!$isIp && !$isName)
		{
			return '';
		}

		if ($port !== '' && (!preg_match('/^\d{1,5}$/', $port) || (int) $port < 1 || (int) $port > 65535))
		{
			return '';
		}

		if ($path !== '')
		{
			$path = trim($path, '/');

			if ($path === '' || !preg_match('#^[A-Za-z0-9._~!$&\'()*+,;=:@%/-]+$#', $path))
			{
				return '';
			}
		}

		$isIpv6 = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
		$location = $isIpv6 ? '[' . $host . ']' : $host;

		if ($port !== '')
		{
			$location .= ':' . $port;
		}

		if ($path !== '')
		{
			$location .= '/' . $path;
		}

		return $location;
	}

	public static function rewriteHtml($html, $oldLocations, $newLocation)
	{
		if ($html === '' || empty($oldLocations) || $newLocation === '')
		{
			return $html;
		}

		$attrs = 'href|src|poster|action|cite|formaction|longdesc|data-src|data-href|data-lazy-src';
		$updated = preg_replace_callback(
			'/(?<![\w-])(' . $attrs . ')\s*=\s*(["\'])(.*?)\2/is',
			static function ($match) use ($oldLocations, $newLocation) {
				return $match[1] . '=' . $match[2] . self::rewriteUrl($match[3], $oldLocations, $newLocation) . $match[2];
			},
			$html
		);

		if (is_string($updated))
		{
			$html = $updated;
		}

		$updated = preg_replace_callback(
			'/(?<![\w-])(srcset|data-srcset)\s*=\s*(["\'])(.*?)\2/is',
			static function ($match) use ($oldLocations, $newLocation) {
				return $match[1] . '=' . $match[2] . self::rewriteSrcset($match[3], $oldLocations, $newLocation) . $match[2];
			},
			$html
		);

		if (is_string($updated))
		{
			$html = $updated;
		}

		$updated = preg_replace_callback(
			'/(?<![\w-])style\s*=\s*(["\'])(.*?)\1/is',
			static function ($match) use ($oldLocations, $newLocation) {
				return 'style=' . $match[1] . self::rewriteCssUrls($match[2], $oldLocations, $newLocation) . $match[1];
			},
			$html
		);

		if (is_string($updated))
		{
			$html = $updated;
		}

		$updated = preg_replace_callback(
			'/(<style\b[^>]*>)(.*?)(<\/style>)/is',
			static function ($match) use ($oldLocations, $newLocation) {
				return $match[1] . self::rewriteCssUrls($match[2], $oldLocations, $newLocation) . $match[3];
			},
			$html
		);

		return is_string($updated) ? $updated : $html;
	}

	public static function rewriteUrl($url, $oldLocations, $newLocation)
	{
		$url = trim((string) $url);

		if ($url === '')
		{
			return $url;
		}

		$lower = strtolower($url);

		foreach (['javascript:', 'data:', 'mailto:', 'tel:', 'sms:', 'blob:'] as $scheme)
		{
			if (strpos($lower, $scheme) === 0)
			{
				return $url;
			}
		}

		foreach ($oldLocations as $oldLocation)
		{
			$replaced = self::replaceLocationPrefix($url, $oldLocation, $newLocation);

			if ($replaced !== $url)
			{
				return $replaced;
			}
		}

		return $url;
	}

	private static function rewriteSrcset($value, $oldLocations, $newLocation)
	{
		$parts = explode(',', (string) $value);
		$out = [];

		foreach ($parts as $part)
		{
			$part = trim($part);

			if ($part === '')
			{
				continue;
			}

			if (!preg_match('/^(\S+)(.*)$/', $part, $match))
			{
				$out[] = $part;
				continue;
			}

			$out[] = self::rewriteUrl($match[1], $oldLocations, $newLocation) . $match[2];
		}

		return implode(', ', $out);
	}

	private static function rewriteCssUrls($css, $oldLocations, $newLocation)
	{
		$updated = preg_replace_callback(
			'/url\(\s*([\'"]?)(.*?)\1\s*\)/i',
			static function ($match) use ($oldLocations, $newLocation) {
				$quote = $match[1];
				$rewritten = self::rewriteUrl($match[2], $oldLocations, $newLocation);

				return 'url(' . $quote . $rewritten . $quote . ')';
			},
			(string) $css
		);

		return is_string($updated) ? $updated : $css;
	}

	private static function replaceLocationPrefix($url, $oldLocation, $newLocation)
	{
		$candidates = [
			['prefix' => 'https://' . $oldLocation, 'replacement' => 'https://' . $newLocation],
			['prefix' => 'http://' . $oldLocation, 'replacement' => 'http://' . $newLocation],
			['prefix' => '//' . $oldLocation, 'replacement' => '//' . $newLocation],
			['prefix' => $oldLocation, 'replacement' => $newLocation],
		];

		foreach ($candidates as $pair)
		{
			$length = strlen($pair['prefix']);

			if ($length === 0 || strlen($url) < $length)
			{
				continue;
			}

			if (strncasecmp($url, $pair['prefix'], $length) !== 0)
			{
				continue;
			}

			$next = $url[$length] ?? '';

			if ($next !== '' && !in_array($next, ['/', '?', '#', ':'], true))
			{
				continue;
			}

			return $pair['replacement'] . substr($url, $length);
		}

		return $url;
	}

	private static function bodyLooksHtml($body)
	{
		$trimmed = ltrim($body);
		$first = $trimmed[0] ?? '';

		return $first === '<';
	}

	private static function bodyMentionsLocations($body, $oldLocations)
	{
		$haystack = strtolower($body);

		foreach ($oldLocations as $location)
		{
			if (strpos($haystack, strtolower($location)) !== false)
			{
				return true;
			}
		}

		return false;
	}
}
