<?php
namespace Gebruederheitz\GhOnetimesession\Authentication;

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
use TYPO3\CMS\Sv\AbstractAuthenticationService;

/**
 * Authentication Service to provide onetime session without the need of real fe_users
 */
class OnetimeAuthentication extends AbstractAuthenticationService {

	const USER_TYPE = 'tx_ghonetimesession';
	const AUTH_CONTINUE = 100;
	const AUTH_SUCCESS = 200;

	/**
	 * OnetimeSession
	 *
	 * @var \Gebruederheitz\GhOnetimesession\Session\OnetimeSession
	 */
	protected $onetimeSession = NULL;

	/**
	 * Pseudo constructor
	 *
	 * @param string $subType
	 * @param array $loginData
	 * @param array $authInfo
	 * @param object $pObj
	 * @return void
	 */
	public function initAuth($subType, $loginData, $authInfo, $pObj) {
		parent::initAuth($subType, $loginData, $authInfo, $pObj);
		$this->onetimeSession = GeneralUtility::makeInstance('Gebruederheitz\\GhOnetimesession\\Session\\OnetimeSession');
	}

	/**
	 * Authenticate a onetime user.
	 * This function is called by the "getAuth" authentication service of TYPO3
	 *
	 * @param $user array The user record
	 * @return int 200 (SUCCESS) if the given $user is a onetime user - otherwise 100 (CONTINUE) 
	 */
	public function authUser($user) {
		if (!empty($user) && $user['tx_extbase_type'] === self::USER_TYPE) {
			return self::AUTH_SUCCESS;
		}

		return self::AUTH_CONTINUE;
	}

	/**
	 * Generate a onetime user.
	 * This function is called by the "getUser" authentication service of TYPO3
	 *
	 * @return array Frontend user data
	 */
	public function getUser() {
		$user = array();

		// Return empty user if the user does not have a valid onetime authentication cookie
		if (!$this->onetimeSession->validateCookie()) {
			return $user;
		}

		$settings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['gh_onetimesession']);
		$userGroups = $settings['fe_groups'];

		$randomString = GeneralUtility::getRandomHexString(32);
		$now = time();
		$user = array(
			'uid' => '',
			'pid' => 0,
			'usergroup' => $userGroups,
			'username' => $randomString,
			'password' => $randomString,
			'tstamp' => $now,
			'crdate' => $now,
			'tx_extbase_type' => self::USER_TYPE,
		);

		return $user;
	}

}
