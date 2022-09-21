# SftpHostKey

## Example SftpHostKey Object

```
{
  "id": 1,
  "name": "",
  "fingerprint_md5": "",
  "fingerprint_sha256": ""
}
```

* `id` (int64): Sftp Host Key ID
* `name` (string): The friendly name of this SFTP Host Key.
* `fingerprint_md5` (string): MD5 Fingerpint of the public key
* `fingerprint_sha256` (string): SHA256 Fingerpint of the public key
* `private_key` (string): The private key data.

---

## List Sftp Host Keys

```
$sftp_host_key = new \Files\Model\SftpHostKey();
$sftp_host_key->list(, [
  'per_page' => 1,
]);
```


### Parameters

* `cursor` (string): Used for pagination.  Send a cursor value to resume an existing list from the point at which you left off.  Get a cursor from an existing list via either the X-Files-Cursor-Next header or the X-Files-Cursor-Prev header.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).

---

## Show Sftp Host Key

```
$sftp_host_key = new \Files\Model\SftpHostKey();
$sftp_host_key->find($id);
```


### Parameters

* `id` (int64): Required - Sftp Host Key ID.

---

## Create Sftp Host Key

```
$sftp_host_key = new \Files\Model\SftpHostKey();
$sftp_host_key->create(, [
  'name' => "",
  'private_key' => "",
]);
```


### Parameters

* `name` (string): The friendly name of this SFTP Host Key.
* `private_key` (string): The private key data.

---

## Update Sftp Host Key

```
$sftp_host_key = current(\Files\Model\SftpHostKey::list());

$sftp_host_key->update([
  'name' => "",
  'private_key' => "",
]);
```

### Parameters

* `id` (int64): Required - Sftp Host Key ID.
* `name` (string): The friendly name of this SFTP Host Key.
* `private_key` (string): The private key data.

### Example Response

```json
{
  "id": 1,
  "name": "",
  "fingerprint_md5": "",
  "fingerprint_sha256": ""
}
```

---

## Delete Sftp Host Key

```
$sftp_host_key = current(\Files\Model\SftpHostKey::list());

$sftp_host_key->delete();
```

### Parameters

* `id` (int64): Required - Sftp Host Key ID.
