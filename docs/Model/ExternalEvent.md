# ExternalEvent

## Example ExternalEvent Object

```
{
  "id": 1,
  "event_type": "example",
  "status": "example",
  "body": "example",
  "created_at": "2000-01-01T01:00:00Z",
  "body_url": "example",
  "folder_behavior_id": 1,
  "siem_http_destination_id": 1,
  "successful_files": 1,
  "errored_files": 1,
  "bytes_synced": 1,
  "compared_files": 1,
  "compared_folders": 1,
  "remote_server_type": "example"
}
```

* `id` (int64): Event ID
* `event_type` (string): Type of event being recorded.
* `status` (string): Status of event.
* `body` (string): Event body
* `created_at` (date-time): External event create date/time
* `body_url` (string): Link to log file.
* `folder_behavior_id` (int64): Folder Behavior ID
* `siem_http_destination_id` (int64): SIEM HTTP Destination ID.
* `successful_files` (int64): For sync events, the number of files handled successfully.
* `errored_files` (int64): For sync events, the number of files that encountered errors.
* `bytes_synced` (int64): For sync events, the total number of bytes synced.
* `compared_files` (int64): For sync events, the number of files considered for the sync.
* `compared_folders` (int64): For sync events, the number of folders listed and considered for the sync.
* `remote_server_type` (string): Associated Remote Server type, if any

---

## List External Events

```
$external_event = new \Files\Model\ExternalEvent();
$external_event->list
```


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `siem_http_destination_id`, `created_at`, `event_type`, `status` or `folder_behavior_id`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `event_type`, `remote_server_type`, `status`, `folder_behavior_id` or `siem_http_destination_id`. Valid field combinations are `[ event_type, created_at ]`, `[ remote_server_type, created_at ]`, `[ status, created_at ]`, `[ folder_behavior_id, created_at ]`, `[ event_type, status ]`, `[ remote_server_type, status ]`, `[ folder_behavior_id, status ]`, `[ event_type, status, created_at ]`, `[ remote_server_type, status, created_at ]` or `[ folder_behavior_id, status, created_at ]`.
* `filter_gt` (object): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
* `filter_gteq` (object): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
* `filter_lt` (object): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
* `filter_lteq` (object): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.

---

## Show External Event

```
$external_event = new \Files\Model\ExternalEvent();
$external_event->find($id);
```


### Parameters

* `id` (int64): Required - External Event ID.

---

## Create External Event

```
$external_event = new \Files\Model\ExternalEvent();
$external_event->create(, [
  'status' => "example",
  'body' => "example",
]);
```


### Parameters

* `status` (string): Required - Status of event.
* `body` (string): Required - Event body
