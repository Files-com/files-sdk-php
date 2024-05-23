# Message

## Example Message Object

```
{
  "id": 1,
  "subject": "Files.com Account Upgrade",
  "body": "We should upgrade our Files.com account!",
  "comments": [
    {
      "id": 1,
      "body": "What a great idea, thank you!",
      "reactions": [
        {
          "id": 1,
          "emoji": "👍"
        }
      ]
    }
  ]
}
```

* `id` (int64): Message ID
* `subject` (string): Message subject.
* `body` (string): Message body.
* `comments` (array): Comments.
* `user_id` (int64): User ID.  Provide a value of `0` to operate the current session's user.
* `project_id` (int64): Project to which the message should be attached.

---

## List Messages

```
$message = new \Files\Model\Message();
$message->list(, [
  'user_id' => 1,
  'per_page' => 1,
  'project_id' => 1,
]);
```


### Parameters

* `user_id` (int64): User ID.  Provide a value of `0` to operate the current session's user.
* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `project_id` (int64): Required - Project for which to return messages.

---

## Show Message

```
$message = new \Files\Model\Message();
$message->find($id);
```


### Parameters

* `id` (int64): Required - Message ID.

---

## Create Message

```
$message = new \Files\Model\Message();
$message->create(, [
  'user_id' => 1,
  'project_id' => 1,
  'subject' => "Files.com Account Upgrade",
  'body' => "We should upgrade our Files.com account!",
]);
```


### Parameters

* `user_id` (int64): User ID.  Provide a value of `0` to operate the current session's user.
* `project_id` (int64): Required - Project to which the message should be attached.
* `subject` (string): Required - Message subject.
* `body` (string): Required - Message body.

---

## Update Message

```
$message = \Files\Model\Message::find($id);

$message->update([
  'project_id' => 1,
  'subject' => "Files.com Account Upgrade",
  'body' => "We should upgrade our Files.com account!",
]);
```

### Parameters

* `id` (int64): Required - Message ID.
* `project_id` (int64): Required - Project to which the message should be attached.
* `subject` (string): Required - Message subject.
* `body` (string): Required - Message body.

### Example Response

```json
{
  "id": 1,
  "subject": "Files.com Account Upgrade",
  "body": "We should upgrade our Files.com account!",
  "comments": [
    {
      "id": 1,
      "body": "What a great idea, thank you!",
      "reactions": [
        {
          "id": 1,
          "emoji": "👍"
        }
      ]
    }
  ]
}
```

---

## Delete Message

```
$message = \Files\Model\Message::find($id);

$message->delete();
```

### Parameters

* `id` (int64): Required - Message ID.

