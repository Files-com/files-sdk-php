
---

## retry Action Webhook Failure

```
$action_webhook_failure = new \Files\Model\ActionWebhookFailure();
$action_webhook_failure->path = $myFilePath;

$action_webhook_failure->retry();
```

### Parameters

* `id` (int64): Required - Action Webhook Failure ID.

