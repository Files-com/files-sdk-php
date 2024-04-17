# SyncLog

## Example SyncLog Object

```
{
  "timestamp": "2000-01-01T01:00:00Z",
  "sync_id": 1,
  "external_event_id": 1,
  "error_type": "example",
  "message": "example",
  "operation": "example",
  "path": "example",
  "status": "example",
  "type": "example"
}
```

* `timestamp` (date-time): Start Time of Action
* `sync_id` (int64): Sync ID
* `external_event_id` (int64): External Event ID
* `error_type` (string): Error type, if applicable
* `message` (string): Message
* `operation` (string): Operation type
* `path` (string): File path This must be slash-delimited, but it must neither start nor end with a slash. Maximum of 5000 characters.
* `status` (string): Status
* `type` (string): Operation type

---

## List Sync Logs

```
$sync_log = new \Files\Model\SyncLog();
$sync_log->list(, [
  'per_page' => 1,
]);
```


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `start_date`, `end_date`, `external_event_id`, `operation`, `status`, `sync_id` or `type`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ type ]`, `[ start_date, end_date ]`, `[ start_date, external_event_id ]`, `[ start_date, operation ]`, `[ start_date, status ]`, `[ start_date, sync_id ]`, `[ start_date, type ]`, `[ end_date, external_event_id ]`, `[ end_date, operation ]`, `[ end_date, status ]`, `[ end_date, sync_id ]`, `[ end_date, type ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, type ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, type ]`, `[ status, sync_id ]`, `[ status, type ]`, `[ sync_id, type ]`, `[ start_date, end_date, external_event_id ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, sync_id ]`, `[ start_date, end_date, type ]`, `[ start_date, external_event_id, operation ]`, `[ start_date, external_event_id, status ]`, `[ start_date, external_event_id, sync_id ]`, `[ start_date, external_event_id, type ]`, `[ start_date, operation, status ]`, `[ start_date, operation, sync_id ]`, `[ start_date, operation, type ]`, `[ start_date, status, sync_id ]`, `[ start_date, status, type ]`, `[ start_date, sync_id, type ]`, `[ end_date, external_event_id, operation ]`, `[ end_date, external_event_id, status ]`, `[ end_date, external_event_id, sync_id ]`, `[ end_date, external_event_id, type ]`, `[ end_date, operation, status ]`, `[ end_date, operation, sync_id ]`, `[ end_date, operation, type ]`, `[ end_date, status, sync_id ]`, `[ end_date, status, type ]`, `[ end_date, sync_id, type ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, type ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, type ]`, `[ external_event_id, sync_id, type ]`, `[ operation, status, sync_id ]`, `[ operation, status, type ]`, `[ operation, sync_id, type ]`, `[ status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation ]`, `[ start_date, end_date, external_event_id, status ]`, `[ start_date, end_date, external_event_id, sync_id ]`, `[ start_date, end_date, external_event_id, type ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, operation, sync_id ]`, `[ start_date, end_date, operation, type ]`, `[ start_date, end_date, status, sync_id ]`, `[ start_date, end_date, status, type ]`, `[ start_date, end_date, sync_id, type ]`, `[ start_date, external_event_id, operation, status ]`, `[ start_date, external_event_id, operation, sync_id ]`, `[ start_date, external_event_id, operation, type ]`, `[ start_date, external_event_id, status, sync_id ]`, `[ start_date, external_event_id, status, type ]`, `[ start_date, external_event_id, sync_id, type ]`, `[ start_date, operation, status, sync_id ]`, `[ start_date, operation, status, type ]`, `[ start_date, operation, sync_id, type ]`, `[ start_date, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status ]`, `[ end_date, external_event_id, operation, sync_id ]`, `[ end_date, external_event_id, operation, type ]`, `[ end_date, external_event_id, status, sync_id ]`, `[ end_date, external_event_id, status, type ]`, `[ end_date, external_event_id, sync_id, type ]`, `[ end_date, operation, status, sync_id ]`, `[ end_date, operation, status, type ]`, `[ end_date, operation, sync_id, type ]`, `[ end_date, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, type ]`, `[ external_event_id, operation, sync_id, type ]`, `[ external_event_id, status, sync_id, type ]`, `[ operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status ]`, `[ start_date, end_date, external_event_id, operation, sync_id ]`, `[ start_date, end_date, external_event_id, operation, type ]`, `[ start_date, end_date, external_event_id, status, sync_id ]`, `[ start_date, end_date, external_event_id, status, type ]`, `[ start_date, end_date, external_event_id, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id ]`, `[ start_date, end_date, operation, status, type ]`, `[ start_date, end_date, operation, sync_id, type ]`, `[ start_date, end_date, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id ]`, `[ start_date, external_event_id, operation, status, type ]`, `[ start_date, external_event_id, operation, sync_id, type ]`, `[ start_date, external_event_id, status, sync_id, type ]`, `[ start_date, operation, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status, sync_id ]`, `[ end_date, external_event_id, operation, status, type ]`, `[ end_date, external_event_id, operation, sync_id, type ]`, `[ end_date, external_event_id, status, sync_id, type ]`, `[ end_date, operation, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status, sync_id ]`, `[ start_date, end_date, external_event_id, operation, status, type ]`, `[ start_date, end_date, external_event_id, operation, sync_id, type ]`, `[ start_date, end_date, external_event_id, status, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id, type ]` or `[ end_date, external_event_id, operation, status, sync_id, type ]`.
* `filter_prefix` (object): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `operation`, `status` or `type`. Valid field combinations are `[ start_date ]`, `[ end_date ]`, `[ external_event_id ]`, `[ operation ]`, `[ status ]`, `[ sync_id ]`, `[ type ]`, `[ start_date, end_date ]`, `[ start_date, external_event_id ]`, `[ start_date, operation ]`, `[ start_date, status ]`, `[ start_date, sync_id ]`, `[ start_date, type ]`, `[ end_date, external_event_id ]`, `[ end_date, operation ]`, `[ end_date, status ]`, `[ end_date, sync_id ]`, `[ end_date, type ]`, `[ external_event_id, operation ]`, `[ external_event_id, status ]`, `[ external_event_id, sync_id ]`, `[ external_event_id, type ]`, `[ operation, status ]`, `[ operation, sync_id ]`, `[ operation, type ]`, `[ status, sync_id ]`, `[ status, type ]`, `[ sync_id, type ]`, `[ start_date, end_date, external_event_id ]`, `[ start_date, end_date, operation ]`, `[ start_date, end_date, status ]`, `[ start_date, end_date, sync_id ]`, `[ start_date, end_date, type ]`, `[ start_date, external_event_id, operation ]`, `[ start_date, external_event_id, status ]`, `[ start_date, external_event_id, sync_id ]`, `[ start_date, external_event_id, type ]`, `[ start_date, operation, status ]`, `[ start_date, operation, sync_id ]`, `[ start_date, operation, type ]`, `[ start_date, status, sync_id ]`, `[ start_date, status, type ]`, `[ start_date, sync_id, type ]`, `[ end_date, external_event_id, operation ]`, `[ end_date, external_event_id, status ]`, `[ end_date, external_event_id, sync_id ]`, `[ end_date, external_event_id, type ]`, `[ end_date, operation, status ]`, `[ end_date, operation, sync_id ]`, `[ end_date, operation, type ]`, `[ end_date, status, sync_id ]`, `[ end_date, status, type ]`, `[ end_date, sync_id, type ]`, `[ external_event_id, operation, status ]`, `[ external_event_id, operation, sync_id ]`, `[ external_event_id, operation, type ]`, `[ external_event_id, status, sync_id ]`, `[ external_event_id, status, type ]`, `[ external_event_id, sync_id, type ]`, `[ operation, status, sync_id ]`, `[ operation, status, type ]`, `[ operation, sync_id, type ]`, `[ status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation ]`, `[ start_date, end_date, external_event_id, status ]`, `[ start_date, end_date, external_event_id, sync_id ]`, `[ start_date, end_date, external_event_id, type ]`, `[ start_date, end_date, operation, status ]`, `[ start_date, end_date, operation, sync_id ]`, `[ start_date, end_date, operation, type ]`, `[ start_date, end_date, status, sync_id ]`, `[ start_date, end_date, status, type ]`, `[ start_date, end_date, sync_id, type ]`, `[ start_date, external_event_id, operation, status ]`, `[ start_date, external_event_id, operation, sync_id ]`, `[ start_date, external_event_id, operation, type ]`, `[ start_date, external_event_id, status, sync_id ]`, `[ start_date, external_event_id, status, type ]`, `[ start_date, external_event_id, sync_id, type ]`, `[ start_date, operation, status, sync_id ]`, `[ start_date, operation, status, type ]`, `[ start_date, operation, sync_id, type ]`, `[ start_date, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status ]`, `[ end_date, external_event_id, operation, sync_id ]`, `[ end_date, external_event_id, operation, type ]`, `[ end_date, external_event_id, status, sync_id ]`, `[ end_date, external_event_id, status, type ]`, `[ end_date, external_event_id, sync_id, type ]`, `[ end_date, operation, status, sync_id ]`, `[ end_date, operation, status, type ]`, `[ end_date, operation, sync_id, type ]`, `[ end_date, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id ]`, `[ external_event_id, operation, status, type ]`, `[ external_event_id, operation, sync_id, type ]`, `[ external_event_id, status, sync_id, type ]`, `[ operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status ]`, `[ start_date, end_date, external_event_id, operation, sync_id ]`, `[ start_date, end_date, external_event_id, operation, type ]`, `[ start_date, end_date, external_event_id, status, sync_id ]`, `[ start_date, end_date, external_event_id, status, type ]`, `[ start_date, end_date, external_event_id, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id ]`, `[ start_date, end_date, operation, status, type ]`, `[ start_date, end_date, operation, sync_id, type ]`, `[ start_date, end_date, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id ]`, `[ start_date, external_event_id, operation, status, type ]`, `[ start_date, external_event_id, operation, sync_id, type ]`, `[ start_date, external_event_id, status, sync_id, type ]`, `[ start_date, operation, status, sync_id, type ]`, `[ end_date, external_event_id, operation, status, sync_id ]`, `[ end_date, external_event_id, operation, status, type ]`, `[ end_date, external_event_id, operation, sync_id, type ]`, `[ end_date, external_event_id, status, sync_id, type ]`, `[ end_date, operation, status, sync_id, type ]`, `[ external_event_id, operation, status, sync_id, type ]`, `[ start_date, end_date, external_event_id, operation, status, sync_id ]`, `[ start_date, end_date, external_event_id, operation, status, type ]`, `[ start_date, end_date, external_event_id, operation, sync_id, type ]`, `[ start_date, end_date, external_event_id, status, sync_id, type ]`, `[ start_date, end_date, operation, status, sync_id, type ]`, `[ start_date, external_event_id, operation, status, sync_id, type ]` or `[ end_date, external_event_id, operation, status, sync_id, type ]`.