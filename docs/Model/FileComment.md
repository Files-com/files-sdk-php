# FileComment

## Example FileComment Object

```
{
  "id": 1,
  "body": "What a great file!",
  "reactions": [
    {
      "id": 1,
      "emoji": "👍"
    }
  ]
}
```

* `id` (int64): File Comment ID
* `body` (string): Comment body.
* `reactions` (array(object)): Reactions to this comment.
* `path` (string): File path.

---

## List File Comments by Path

```
$file_comment = new \Files\Model\FileComment();
$file_comment->listFor($path);
```


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).
* `path` (string): Required - Path to operate on.

---

## Create File Comment

```
$file_comment = new \Files\Model\FileComment();
$file_comment->create(, [
  'body' => "body",
  'path' => "path",
]);
```


### Parameters

* `body` (string): Required - Comment body.
* `path` (string): Required - File path.

---

## Update File Comment

```
$file_comment = current(\Files\Model\FileComment::all());

$file_comment->update([
  'body' => "body",
]);
```

### Parameters

* `id` (int64): Required - File Comment ID.
* `body` (string): Required - Comment body.

### Example Response

```json
{
  "id": 1,
  "body": "What a great file!",
  "reactions": [
    {
      "id": 1,
      "emoji": "👍"
    }
  ]
}
```

---

## Delete File Comment

```
$file_comment = current(\Files\Model\FileComment::all());

$file_comment->delete();
```

### Parameters

* `id` (int64): Required - File Comment ID.

