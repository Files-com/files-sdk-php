# EmailIncomingMessage

## Example EmailIncomingMessage Object

```
{
  "id": 1,
  "inbox_id": 1,
  "sender": "example",
  "sender_name": "example",
  "status": "success",
  "body": "example",
  "message": "example",
  "created_at": "2000-01-01T01:00:00Z",
  "inbox_title": "Inbox Title"
}
```

* `id` (int64): Id of the Email Incoming Message
* `inbox_id` (int64): Id of the Inbox associated with this message
* `sender` (string): Sender of the email
* `sender_name` (string): Sender name
* `status` (string): Status of the message
* `body` (string): Body of the email
* `message` (string): Message describing the failure
* `created_at` (date-time): Message creation date/time
* `inbox_title` (string): Title of the Inbox associated with this message

---

## List Email Incoming Messages

```
$email_incoming_message = new \Files\Model\EmailIncomingMessage();
$email_incoming_message->list
```


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `sort_by` (object): If set, sort records by the specified field in either `asc` or `desc` direction. Valid fields are `created_at`, `sender`, `status` or `inbox_id`.
* `filter` (object): If set, return records where the specified field is equal to the supplied value. Valid fields are `created_at`, `inbox_id`, `sender` or `status`. Valid field combinations are `[ inbox_id, created_at ]`, `[ sender, created_at ]`, `[ status, created_at ]`, `[ inbox_id, status ]`, `[ status, sender ]`, `[ inbox_id, status, created_at ]`, `[ status, sender, created_at ]`, `[ inbox_id, status, sender ]` or `[ inbox_id, status, sender, created_at ]`.
* `filter_gt` (object): If set, return records where the specified field is greater than the supplied value. Valid fields are `created_at`.
* `filter_gteq` (object): If set, return records where the specified field is greater than or equal the supplied value. Valid fields are `created_at`.
* `filter_prefix` (object): If set, return records where the specified field is prefixed by the supplied value. Valid fields are `sender`.
* `filter_lt` (object): If set, return records where the specified field is less than the supplied value. Valid fields are `created_at`.
* `filter_lteq` (object): If set, return records where the specified field is less than or equal the supplied value. Valid fields are `created_at`.
