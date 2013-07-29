<?php
/**
 * Thesis Planet - Digital Education Platform
 *
 * LICENSE
 *
 * This source file is subject to the licensing terms found at http://www.thesisplanet.com/platform/tos
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to sales@thesisplanet.com so we can send you a copy immediately.
 *
 * @category  ThesisPlanet
 * @copyright  Copyright (c) 2009-2012 Thesis Planet, LLC. All Rights Reserved. (http://www.thesisplanet.com)
 * @license   http://www.thesisplanet.com/platform/tos   ** DUAL LICENSED **  #1 - Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. #2 - Thesis Planet Commercial Use EULA.
 */
namespace App\Acl;

/**
 *
 *
 *
 *
 *
 *
 *
 *
 *
 * list of resources to have ACL'd
 * Format: strtolower(MODULE_CONTROLLER)
 *
 * @author Jack.Peterson
 *
 */
class Resources
{
    // PUBLIC STUFF -- EVERYTHING within the DEFAULT controller.
    const PUBLICPAGE = 'public';

    const HELP = 'help_index';
    // ADMINISTRATION
    const ADMIN_CUSTOMER = 'admin_customer';

    const ADMIN_CHANNEL = 'admin_channel';

    const ADMIN_DNS = 'admin_dns';

    const ADMIN_INDEX = 'admin_index';

    const ADMIN_MONITORING = 'admin_monitoring';

    const ADMIN_SERVER = 'admin_server';

    const ADMIN_USER = 'admin_user';

    // MY ACCOUNT
    const MY_INDEX = 'my_index';

    const MY_ACCOUNT = 'my_account';

    const MY_PRIVACY = 'my_privacy';

    const MY_NOTIFICATION = 'my_notification';

    const MY_PROFILE = 'my_profile';

    const MY_PASSWORD = 'my_password';

    const MY_SUBSCRIPTION = 'my_subscription';
    // PROVIDER STUFF
    const CMS_INDEX = 'cms_index';

    const CMS_CATEGORY = 'cms_category';

    const CMS_CHANNEL = 'cms_channel';

    const CMS_AUDIO = 'cms_audio';

    const CMS_FILE = 'cms_file';

    const CMS_VIDEO = 'cms_video';

    const CMS_SUBSCRIBER = 'cms_subscriber';
    // Main site
    const SITE_CATEGORY = 'site_category';

    const SITE_COURSE = 'site_course';

    const SITE_ASSESSMENT = 'site_assessment';

    const SITE_AUDIO = 'site_audio';

    const SITE_ERROR = 'site_error';

    const SITE_FILE = 'site_file';

    const SITE_UPGRADE = 'site_upgrade';

    const SITE_INDEX = 'site_index';

    const SITE_VIDEO = 'site_video';

    const SITE_EVENT = 'site_event';

    const SITE_DASHBOARD = 'site_dashboard';

    const INITIALIZATION_INDEX = 'initialization_index';

    const INITIALIZATION_AWS = 'initialization_aws';

    const INITIALIZATION_ZENCODER = 'initialization_zencoder';

    const INITIALIZATION_EMAIL = 'initialization_email';

    const INITIALIZATION_SERVER = 'initialization_server';

    const INITIALIZATION_ADMIN = 'initialization_admin';

    const INITIALIZATION_USER = 'initialization_user';

    const INITIALIZATION_TEST = 'initialization_test';

    const INITIALIZATION_BACKUP = 'initialization_backup';
}