<?php
/* $Id$ */
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);
unlink('package.xml');

$releaseVersion = '0.2.1';
$apiVersion = $releaseVersion;
$changelog = '
  - Moved set_error_handler to parseResponse method from sendRequest.
  - Added try catch in parseRequest method, and After catched Services_YouTube_Exception, set restore_error_handler.
  - Fixed many bugs in userXMLRPC method.
  - Changed the arguments when calling sendRequest. Split the prefix and method.
  - Fixed Bug #9917, and added the test Bug9917.php
  - Added more Unit Tests for xmlrpc driver.
  ';
$notes = $changelog;
$packagexml = new PEAR_PackageFileManager2();
$packagexml->setOptions(array('filelistgenerator' => 'file',
      'packagefile' => 'package2.xml',
      'packagedirectory' => dirname(__FILE__),
      'baseinstalldir' => 'Services',
      'ignore' => array('makepackage.php', 'Documentation/', 'ver/', 'CVS/'),
      'simpleoutput' => true,
      'changelogoldtonew' => true,
      'changelognotes' => $changelog,
      'exceptions' => array('ChangeLog' => 'doc'),
      'dir_roles' => array('examples' => 'doc', 'docs' => 'doc', 'tests' => 'test')));
$packagexml->setPackageType('php');
$packagexml->addRelease();
$packagexml->setChannel('pear.php.net');
$packagexml->setPackage('Services_YouTube');
$packagexml->setReleaseVersion($releaseVersion);
$packagexml->setAPIVersion($apiVersion);
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setSummary('PHP Client for YouTube API');
$packagexml->setDescription('Services_YouTube is a client for YouTube Developer APIs.

    YouTube is a place for people to engage in new ways with video by sharing, commenting on, and viewing videos. YouTube Developer APIs currently allow read-only access to key parts of the YouTube video respository and user community.

    Using Services_YouTube, you can configure
    A: REST or XML-RPC approach to use YouTube Developer APIs.
    B: caching the response of the YouTube Developer APIs.
    C: SimpleXMLElement or array for the response of the YouTube Developer APIs.

    About the YouTube Developer APIs:
    http://www.youtube.com/dev');
$packagexml->setNotes($notes);
$packagexml->setPhpDep('5.1.0');
$packagexml->setPearinstallerDep('1.4.0a12');
$packagexml->addExtensionDep('required', 'simplexml');
$packagexml->addExtensionDep('required', 'curl');
$packagexml->addPackageDepWithChannel('optional', 'Cache_Lite', 'pear.php.net');
$packagexml->addPackageDepWithChannel('optional', 'XML_RPC2', 'pear.php.net');
$packagexml->addMaintainer('lead', 'shin', 'Shin Ohno', 'ganchiku@gmail.com');
$packagexml->setLicense('PHP License', 'http://www.php.net/license');
$packagexml->addReplacement('YouTube.php', 'package-info', '@package_version@', 'version');
$packagexml->addReplacement('YouTube/Exception.php', 'package-info', '@package_version@', 'version');
$packagexml->generateContents();
$pkg = &$packagexml->exportCompatiblePackageFile1();
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $pkg->writePackageFile();
    $packagexml->writePackageFile();
} else {
    $pkg->debugPackageFile();
    $packagexml->debugPackageFile();
}
?>
