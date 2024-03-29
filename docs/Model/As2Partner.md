# As2Partner

## Example As2Partner Object

```
{
  "id": 1,
  "as2_station_id": 1,
  "name": "AS2 Partner Name",
  "uri": "example",
  "server_certificate": "require_match",
  "enable_dedicated_ips": true,
  "hex_public_certificate_serial": "A5:EB:C1:95:DC:D8:2B:E7",
  "public_certificate_md5": "example",
  "public_certificate_subject": "example",
  "public_certificate_issuer": "example",
  "public_certificate_serial": "example",
  "public_certificate_not_before": "example",
  "public_certificate_not_after": "example"
}
```

* `id` (int64): Id of the AS2 Partner.
* `as2_station_id` (int64): Id of the AS2 Station associated with this partner.
* `name` (string): The partner's formal AS2 name.
* `uri` (string): Public URI for sending AS2 message to.
* `server_certificate` (string): Remote server certificate security setting
* `enable_dedicated_ips` (boolean): `true` if remote server only accepts connections from dedicated IPs
* `hex_public_certificate_serial` (string): Serial of public certificate used for message security in hex format.
* `public_certificate_md5` (string): MD5 hash of public certificate used for message security.
* `public_certificate_subject` (string): Subject of public certificate used for message security.
* `public_certificate_issuer` (string): Issuer of public certificate used for message security.
* `public_certificate_serial` (string): Serial of public certificate used for message security.
* `public_certificate_not_before` (string): Not before value of public certificate used for message security.
* `public_certificate_not_after` (string): Not after value of public certificate used for message security.
* `public_certificate` (string): 

---

## List As2 Partners

```
$as2_partner = new \Files\Model\As2Partner();
$as2_partner->list(, [
  'per_page' => 1,
]);
```


### Parameters

* `cursor` (string): Used for pagination.  When a list request has more records available, cursors are provided in the response headers `X-Files-Cursor-Next` and `X-Files-Cursor-Prev`.  Send one of those cursor value here to resume an existing list from the next available record.  Note: many of our SDKs have iterator methods that will automatically handle cursor-based pagination.
* `per_page` (int64): Number of records to show per page.  (Max: 10,000, 1,000 or less is recommended).

---

## Show As2 Partner

```
$as2_partner = new \Files\Model\As2Partner();
$as2_partner->find($id);
```


### Parameters

* `id` (int64): Required - As2 Partner ID.

---

## Create As2 Partner

```
$as2_partner = new \Files\Model\As2Partner();
$as2_partner->create(, [
  'name' => "name",
  'uri' => "uri",
  'public_certificate' => "public_certificate",
  'as2_station_id' => 1,
  'server_certificate' => "require_match",
  'enable_dedicated_ips' => true,
]);
```


### Parameters

* `name` (string): Required - AS2 Name
* `uri` (string): Required - URL base for AS2 responses
* `public_certificate` (string): Required - 
* `as2_station_id` (int64): Required - Id of As2Station for this partner
* `server_certificate` (string): Remote server certificate security setting
* `enable_dedicated_ips` (boolean): 

---

## Update As2 Partner

```
$as2_partner = current(\Files\Model\As2Partner::all());

$as2_partner->update([
  'name' => "AS2 Partner Name",
  'uri' => "example",
  'server_certificate' => "require_match",
  'enable_dedicated_ips' => true,
]);
```

### Parameters

* `id` (int64): Required - As2 Partner ID.
* `name` (string): AS2 Name
* `uri` (string): URL base for AS2 responses
* `server_certificate` (string): Remote server certificate security setting
* `public_certificate` (string): 
* `enable_dedicated_ips` (boolean): 

### Example Response

```json
{
  "id": 1,
  "as2_station_id": 1,
  "name": "AS2 Partner Name",
  "uri": "example",
  "server_certificate": "require_match",
  "enable_dedicated_ips": true,
  "hex_public_certificate_serial": "A5:EB:C1:95:DC:D8:2B:E7",
  "public_certificate_md5": "example",
  "public_certificate_subject": "example",
  "public_certificate_issuer": "example",
  "public_certificate_serial": "example",
  "public_certificate_not_before": "example",
  "public_certificate_not_after": "example"
}
```

---

## Delete As2 Partner

```
$as2_partner = current(\Files\Model\As2Partner::all());

$as2_partner->delete();
```

### Parameters

* `id` (int64): Required - As2 Partner ID.

