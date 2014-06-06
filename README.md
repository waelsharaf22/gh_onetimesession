# EXT:gh_onetimesession - Onetime Frontend Sessions

## What does it do?

This extension provides a onetime frontend user session with a limited feature set. It does not need to create dummy fe_users in your Database.

A user with a onetime session can access pages and content elements which are restricted to frontend groups. The  frontend groups can be configured system wide in the extension settings.


## Installation

* git clone from github
* activate the extension using the extension manager
* Configure fe_groups which should be assigned to the user

## Integration

gh_onetimesession ships an authentication service which is connected to the TYPO3 service API by default.

It also provides an API, which allows you to start and terminate a user session:

```
\Gebruederheitz\GhOnetimesession\Session\OnetimeSession::start()
\Gebruederheitz\GhOnetimesession\Session\OnetimeSession::terminate()
```

You can add this API to any event you like, for example to a SignalSlot in powermail:

```
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('gh_onetimesession')) {
	/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
	$signalSlotDispatcher->connect(
		'Tx_Powermail_Controller_FormsController',
		'createActionAfterSubmitView',
		'Gebruederheitz\\GhOnetimesession\\Session\\OnetimeSession',
		'start'
	);
}
```


## Configuration

### Extension configuration

There is one global configuration option to set the frontend groups which will be assigned to the user:

```
# Comma separated list of frontend groups to assign to the user (default: 0)
feGroups = 
```

## Known issues

See the issue tracker at github.

## Changelog

See the issue tracker at github.

