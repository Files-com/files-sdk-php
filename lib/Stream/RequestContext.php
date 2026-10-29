<?php

namespace Files\Stream;

use Files\Exception\ConfigurationException;
use Files\Files;

/** @internal Pins the API identity of an open native file or directory handle. */
class RequestContext
{
    private $options;
    private $baseUrl;
    private $endpointPrefix;
    private $requiresNoGlobalSession;

    public function __construct(array $options)
    {
        $this->baseUrl = Files::getBaseUrl();
        $this->endpointPrefix = Files::getEndpointPrefix();
        $session = !empty($options['session_id']) ? $options['session_id'] : Files::getSessionId();
        $this->requiresNoGlobalSession = !$session;
        if ($session) {
            $options['session_id'] = $session;
        } else {
            $options['api_key'] = !empty($options['api_key']) ? $options['api_key'] : Files::getApiKey();
        }
        if (!array_key_exists('workspace_id', $options)) {
            $options['workspace_id'] = Files::getWorkspaceId();
        }
        if (!array_key_exists('region_relocation', $options) && method_exists(Files::class, 'getRegionRelocation')) {
            $options['region_relocation'] = Files::getRegionRelocation();
        }
        $this->options = $options;
    }

    public function options()
    {
        if ($this->baseUrl !== Files::getBaseUrl() || $this->endpointPrefix !== Files::getEndpointPrefix()) {
            throw new ConfigurationException('The SDK site or endpoint changed while a Files stream was open; close and reopen the handle');
        }
        // The existing API gives any global session priority over an API key, even an explicit per-call key.
        if ($this->requiresNoGlobalSession && Files::getSessionId()) {
            throw new ConfigurationException('A global session now overrides this Files stream\'s API key; close and reopen the handle');
        }
        return $this->options;
    }
}
