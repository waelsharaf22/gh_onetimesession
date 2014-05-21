<?php
namespace Gebruederheitz\GhOnetimesession\Session;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 support@gebruederheitz.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Onetime Session allows to start and terminate onetime sessions
 */
class OnetimeSession {

	const COOKIE_NAME = 'tx_ghonetimesession_token';

	/**
	 * Starts the session by setting a session cookie
	 *
	 * @return void
	 * @throws \TYPO3\CMS\Core\Exception
	 */
	public function start() {
		if ($_COOKIE[self::COOKIE_NAME]) {
			return;
		}

		$cookieDomain = (!empty($GLOBALS['TYPO3_CONF_VARS']['FE']['cookieDomain'])) ? $GLOBALS['TYPO3_CONF_VARS']['FE']['cookieDomain'] : $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieDomain'];
		$cookiePath = ($cookieDomain) ? '/' : GeneralUtility::getIndpEnv('TYPO3_SITE_PATH');
		$cookieSecure = (bool) $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieSecure'] && GeneralUtility::getIndpEnv('TYPO3_SSL');
		$cookieHttpOnly = (bool) $GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieHttpOnly'];
		$cookieValue = $this->generateToken();

		if ((int)$GLOBALS['TYPO3_CONF_VARS']['SYS']['cookieSecure'] !== 1 || GeneralUtility::getIndpEnv('TYPO3_SSL')) {
			setcookie(self::COOKIE_NAME, $cookieValue, 0, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
		} else {
			throw new \TYPO3\CMS\Core\Exception('Cookie was not set since HTTPS was forced in $TYPO3_CONF_VARS[SYS][cookieSecure].', 1254325546);
		}
	}

	/**
	 * Terminates a session by unsetting the session cookie
	 *
	 * @return void
	 */
	public function terminate() {
		if ($_COOKIE[self::COOKIE_NAME]) {
			setcookie(self::COOKIE_NAME, '', 1);
		}
	}

	/**
	 * Validate the cookie
	 *
	 * @return bool
	 */
	public function validateCookie() {
		$cookie = $this->getCookie();
		$hmac = $this->generateToken();

		if ($hmac === $cookie) {
			$result = TRUE;
		} else {
			$result = FALSE;
		}

		return $result;
	}

	/**
	 * generateCookieValue
	 *
	 * @return string
	 */
	protected function generateToken() {
		return GeneralUtility::hmac(implode('|', array(GeneralUtility::getIndpEnv('REMOTE_ADDR'), GeneralUtility::getIndpEnv('HTTP_USER_AGENT'))));
	}

	/**
	 * Get the value of a specified cookie.
	 *
	 * Mostly a copy of \TYPO3\CMS\Core\Authentication\AbstractUserAuthentication::getCookie()
	 *
	 * @return string The value stored in the cookie
	 */
	protected function getCookie() {
		$cookieValue = '';
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = GeneralUtility::trimExplode(';', $_SERVER['HTTP_COOKIE']);
			foreach ($cookies as $cookie) {
				list($name, $value) = GeneralUtility::trimExplode('=', $cookie);
				if (trim($name) === self::COOKIE_NAME) {
					// Use the last one
					$cookieValue = urldecode($value);
				}
			}
		} else {
			// Fallback if there is no HTTP_COOKIE, use original method:
			$cookieValue = isset($_COOKIE[self::COOKIE_NAME]) ? stripslashes($_COOKIE[self::COOKIE_NAME]) : '';
		}

		return $cookieValue;
	}
}
