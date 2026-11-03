<?php

namespace Files\Model;

use Files\Api;
use Files\Logger;

require_once __DIR__ . '/../Files.php';

/**
 * AutomationExecutionNode API model.
 *
 * @property string|null $node_id Node ID from the pinned Automation definition.
 * @property string|null $node_type Node type.
 * @property string|null $status Node status.
 * @property string|null $run_stage Current node execution stage.
 * @property bool|null $reused Whether this node reused persisted output from an earlier run.
 * @property int|null $successful_operations Count of successful operations in this node.
 * @property int|null $failed_operations Count of failed operations in this node.
 * @property string|null $started_at When this node started.
 * @property string|null $completed_at When this node completed.
 * @property int|null $duration_ms Node runtime in milliseconds.
 * @property object[]|null $inputs Ordered inbound edge references.
 * @property object|null $outputs Output counts, item kinds, and typed-error summaries by outlet.
 * @property object|null $input_items Materialized input items currently available, grouped by inlet.
 * @property object|null $output_items Materialized output items grouped by outlet.
 * @package Files
 */
class AutomationExecutionNode
{
    private $attributes = [];
    private $options = [];
    private static $static_mapped_functions = [
        'list' => 'all',
    ];

    /**
     * Build a model from API attributes and retain its request options.
     *
     * @param array $attributes Initial model attributes, keyed by property name.
     * @param array $options Request options used by this model's operations.
     */
    public function __construct($attributes = [], $options = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[str_replace('?', '', $key)] = $value;
        }

        $this->options = $options;
    }

    /**
     * Set a model attribute locally; call save() to persist supported changes.
     *
     * @param string $name Attribute name.
     * @param mixed $value Attribute value.
     * @return void
     */
    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Read a model attribute.
     *
     * @param string $name Attribute name.
     * @return mixed The attribute value, or null when it is not set.
     */
    public function __get($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Dispatch a legacy static alias, including list() to all().
     *
     * @param string $name Alias name.
     * @param array $arguments Arguments passed to the canonical method.
     * @return mixed The canonical method's result, or null for an unknown alias.
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, array_keys(self::$static_mapped_functions))) {
            $method = self::$static_mapped_functions[$name];
            if (method_exists(__CLASS__, $method)) {
                return @self::$method(...$arguments);
            }
        }
    }

    /**
     * Whether this model has a nonempty primary key.
     *
     * @return bool
     */
    public function isLoaded()
    {
        return !!@$this->attributes['id'];
    }
    /**
     * Node ID from the pinned Automation definition.
     *
     * @return string|null
     */
    public function getNodeId()
    {
        return isset($this->attributes['node_id']) ? $this->attributes['node_id'] : null;
    }
    /**
     * Node type.
     *
     * @return string|null
     */
    public function getNodeType()
    {
        return isset($this->attributes['node_type']) ? $this->attributes['node_type'] : null;
    }
    /**
     * Node status.
     *
     * @return string|null
     */
    public function getStatus()
    {
        return isset($this->attributes['status']) ? $this->attributes['status'] : null;
    }
    /**
     * Current node execution stage.
     *
     * @return string|null
     */
    public function getRunStage()
    {
        return isset($this->attributes['run_stage']) ? $this->attributes['run_stage'] : null;
    }
    /**
     * Whether this node reused persisted output from an earlier run.
     *
     * @return bool|null
     */
    public function getReused()
    {
        return isset($this->attributes['reused']) ? $this->attributes['reused'] : null;
    }
    /**
     * Count of successful operations in this node.
     *
     * @return int|null
     */
    public function getSuccessfulOperations()
    {
        return isset($this->attributes['successful_operations']) ? $this->attributes['successful_operations'] : null;
    }
    /**
     * Count of failed operations in this node.
     *
     * @return int|null
     */
    public function getFailedOperations()
    {
        return isset($this->attributes['failed_operations']) ? $this->attributes['failed_operations'] : null;
    }
    /**
     * When this node started.
     *
     * @return string|null
     */
    public function getStartedAt()
    {
        return isset($this->attributes['started_at']) ? $this->attributes['started_at'] : null;
    }
    /**
     * When this node completed.
     *
     * @return string|null
     */
    public function getCompletedAt()
    {
        return isset($this->attributes['completed_at']) ? $this->attributes['completed_at'] : null;
    }
    /**
     * Node runtime in milliseconds.
     *
     * @return int|null
     */
    public function getDurationMs()
    {
        return isset($this->attributes['duration_ms']) ? $this->attributes['duration_ms'] : null;
    }
    /**
     * Ordered inbound edge references.
     *
     * @return object[]|null
     */
    public function getInputs()
    {
        return isset($this->attributes['inputs']) ? $this->attributes['inputs'] : null;
    }
    /**
     * Output counts, item kinds, and typed-error summaries by outlet.
     *
     * @return object|null
     */
    public function getOutputs()
    {
        return isset($this->attributes['outputs']) ? $this->attributes['outputs'] : null;
    }
    /**
     * Materialized input items currently available, grouped by inlet.
     *
     * @return object|null
     */
    public function getInputItems()
    {
        return isset($this->attributes['input_items']) ? $this->attributes['input_items'] : null;
    }
    /**
     * Materialized output items grouped by outlet.
     *
     * @return object|null
     */
    public function getOutputItems()
    {
        return isset($this->attributes['output_items']) ? $this->attributes['output_items'] : null;
    }
}
