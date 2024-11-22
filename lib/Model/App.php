<?php

declare(strict_types=1);

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * Class App
 *
 * @package Files
 */
class App
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return @$this->attributes[$name];
    }

    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    // string # The type of the App
    public function getAppType()
    {
        return @$this->attributes['app_type'];
    }
    // object # Collection of named links to documentation
    public function getDocumentationLinks()
    {
        return @$this->attributes['documentation_links'];
    }
    // string # Long description for the in-App landing page
    public function getExtendedDescription()
    {
        return @$this->attributes['extended_description'];
    }
    // string # Long form description of the App
    public function getExtendedDescriptionForMarketingSite()
    {
        return @$this->attributes['extended_description_for_marketing_site'];
    }
    // string # Link to external homepage
    public function getExternalHomepageUrl()
    {
        return @$this->attributes['external_homepage_url'];
    }
    // boolean # Is featured on the App listing?
    public function getFeatured()
    {
        return @$this->attributes['featured'];
    }
    // string # Associated Folder Behavior type, if any
    public function getFolderBehaviorType()
    {
        return @$this->attributes['folder_behavior_type'];
    }
    // string # App icon
    public function getIconUrl()
    {
        return @$this->attributes['icon_url'];
    }
    // string # Logo thumbnail for the App
    public function getLogoThumbnailUrl()
    {
        return @$this->attributes['logo_thumbnail_url'];
    }
    // string # Full size logo for the App
    public function getLogoUrl()
    {
        return @$this->attributes['logo_url'];
    }
    // string # Marketing introdution of the App
    public function getMarketingIntro()
    {
        return @$this->attributes['marketing_intro'];
    }
    // string # Marketing video page
    public function getMarketingYoutubeUrl()
    {
        return @$this->attributes['marketing_youtube_url'];
    }
    // string # Name of the App
    public function getName()
    {
        return @$this->attributes['name'];
    }
    // string # Package manager install command
    public function getPackageManagerInstallCommand()
    {
        return @$this->attributes['package_manager_install_command'];
    }
    // string # Associated Remote Server type, if any
    public function getRemoteServerType()
    {
        return @$this->attributes['remote_server_type'];
    }
    // array(string) # Screenshots of the App
    public function getScreenshotListUrls()
    {
        return @$this->attributes['screenshot_list_urls'];
    }
    // string # Link to SDK installation instructions
    public function getSdkInstallationInstructionsLink()
    {
        return @$this->attributes['sdk_installation_instructions_link'];
    }
    // string # Short description of the App
    public function getShortDescription()
    {
        return @$this->attributes['short_description'];
    }
    // string # Associated SSO Strategy type, if any
    public function getSsoStrategyType()
    {
        return @$this->attributes['sso_strategy_type'];
    }
    // string # Associated SIEM type, if any
    public function getSiemType()
    {
        return @$this->attributes['siem_type'];
    }
    // string # Tutorial video page
    public function getTutorialYoutubeUrl()
    {
        return @$this->attributes['tutorial_youtube_url'];
    }

    // Parameters:
    //   cursor - string - Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
    //   per_page - int64 - Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
    //   sort_by - object - If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `name` and `app_type`.
    //   filter - object - If set, return records where the specified field is equal to the supplied value. Valid fields are `name` and `app_type`. Valid field combinations are `[ name, app_type ]`.
    //   filter_prefix - object - If set, return records where the specified field is prefixed by the supplied value. Valid fields are `name`.
    public static function all($params = [], $options = [])
    {
        if (@$params['cursor'] && !is_string(@$params['cursor'])) {
            throw new \Files\Exception\InvalidParameterException('$cursor must be of type string; received ' . gettype(@$params['cursor']));
        }

        if (@$params['per_page'] && !is_int(@$params['per_page'])) {
            throw new \Files\Exception\InvalidParameterException('$per_page must be of type int; received ' . gettype(@$params['per_page']));
        }

        $response = Api::sendRequest('/apps', 'GET', $params, $options);

        $return_array = [];

        foreach ($response->data as $obj) {
            $return_array[] = new App((array) $obj, $options);
        }

        return $return_array;
    }
}
