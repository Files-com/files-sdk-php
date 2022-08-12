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
* `reactions` (array): Reactions to this comment.
* `path` (string): File path.

---

## List File Comments by path

```
$file_comment = new \Files\Model\FileComment();
$file_comment->listFor($path, [
  'per_page' => 1,
]);
```


### Parameters

* `cursor` (string): Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
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
$file_comment = current(\Files\Model\FileComment::list());

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
$file_comment = current(\Files\Model\FileComment::list());

$file_comment->delete();
```

### Parameters

* `id` (int64): Required - File Comment ID.

