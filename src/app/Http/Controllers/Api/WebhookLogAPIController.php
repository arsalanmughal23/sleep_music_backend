<?php

namespace App\Http\Controllers\Api;

use App\Models\WebhookLog;
use App\Traits\RequestCacheable;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class WebhookLogAPIController extends AppBaseController
{
    use RequestCacheable;

    public $reqcSuffix = "webhook_log";

    public function index(Request $request)
    {
        $limit = $request->get('limit', \config('constants.limit'));
        $params = $request->only('platform', 'user_id','transactionId','originalTransactionId','notificationType','subType','productId','expiresDate','type','offerDiscountType');
        $orderByColumn = $request->orderByColumn ?? 'created_at';
        $orderBy = $request->orderBy ?? 'asc';

        $webhookLogs = WebhookLog::where($params);

        $webhookLogs = $webhookLogs->orderBy($orderByColumn, $orderBy)
                            ->paginate($limit);

        if($request->get('visible_data')){
            $webhookLogs = $webhookLogs->makeVisible('data');
        }
        if($request->get('visible_logs')){
            $webhookLogs = $webhookLogs->makeVisible('logs');
        }
        return $webhookLogs;
    }

    public function store(Request $request)
    {
        $data = $request->only('data', 'logs', 'signedPayload', 'platform', 'error', 'user_id','transactionId','originalTransactionId','notificationType','subType','productId','expiresDate','type','offerDiscountType');
        $webhookLogs = WebhookLog::create($data);

        return $this->sendResponse($webhookLogs->toArray(), 'WebhookLog saved successfully');
    }

    public function show($id)
    {
        /** @var WebhookLog $webhookLog */
        $webhookLog = WebhookLog::findOrFail($id);
        return $this->sendResponse($webhookLog->toArray(), 'WebhookLog retrieved successfully');
    }

    public function update($id, Request $request)
    {
        $webhookLog = WebhookLog::findOrFail($id);

        $data = $request->only('data', 'logs', 'signedPayload', 'platform', 'error', 'user_id','transactionId','originalTransactionId','notificationType','subType','productId','expiresDate','type','offerDiscountType');
        $webhookLog->update($data);

        return $this->sendResponse($webhookLog->toArray(), 'WebhookLog updated successfully');
    }

    public function destroy($id)
    {
        /** @var WebhookLog $webhookLog */
        $webhookLog = WebhookLog::findOrFail($id);
        $webhookLog->delete();

        return $this->sendResponse($id, 'WebhookLog deleted successfully');
    }
}
