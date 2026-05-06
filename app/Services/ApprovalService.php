<?php

namespace App\Services;

use App\Models\ApprovalRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApprovalService
{
    public static function create(
        string $module,
        string $title,
        User $requestedBy,
        ?User $approver = null,
        ?string $description = null,
        ?Model $requestable = null,
        ?array $data = null
    ): ApprovalRequest {
        $approval = ApprovalRequest::create([
            'module' => $module,
            'title' => $title,
            'description' => $description,
            'requestable_type' => $requestable ? get_class($requestable) : null,
            'requestable_id' => $requestable?->getKey(),
            'requested_by' => $requestedBy->id,
            'approver_id' => $approver?->id,
            'status' => 'pending',
            'data' => $data,
        ]);

        $approval->actions()->create([
            'user_id' => $requestedBy->id,
            'action' => 'created',
            'old_status' => null,
            'new_status' => 'pending',
            'comment' => 'สร้างรายการขออนุมัติ',
        ]);

        if ($approver && class_exists(AppNotificationService::class)) {
            AppNotificationService::sendToUser(
                user: $approver,
                title: 'มีรายการรออนุมัติ',
                message: $title,
                linkUrl: route('approvals.index'),
                type: 'approval',
                data: [
                    'approval_request_id' => $approval->id,
                    'module' => $module,
                ]
            );
        }

        return $approval;
    }

    public static function approve(
        ApprovalRequest $approval,
        User $user,
        ?string $comment = null
    ): ApprovalRequest {
        return DB::transaction(function () use ($approval, $user, $comment) {
            $oldStatus = $approval->status;

            $approval->update([
                'status' => 'approved',
                'approved_at' => now(),
                'remark' => $comment,
            ]);

            $approval->actions()->create([
                'user_id' => $user->id,
                'action' => 'approved',
                'old_status' => $oldStatus,
                'new_status' => 'approved',
                'comment' => $comment,
            ]);

            if (class_exists(AppNotificationService::class)) {
                AppNotificationService::sendToUser(
                    user: $approval->requester,
                    title: 'รายการของคุณได้รับการอนุมัติ',
                    message: $approval->title,
                    linkUrl: route('approvals.index'),
                    type: 'approval',
                    data: [
                        'approval_request_id' => $approval->id,
                        'status' => 'approved',
                    ]
                );
            }

            return $approval;
        });
    }

    public static function reject(
        ApprovalRequest $approval,
        User $user,
        ?string $comment = null
    ): ApprovalRequest {
        return DB::transaction(function () use ($approval, $user, $comment) {
            $oldStatus = $approval->status;

            $approval->update([
                'status' => 'rejected',
                'rejected_at' => now(),
                'remark' => $comment,
            ]);

            $approval->actions()->create([
                'user_id' => $user->id,
                'action' => 'rejected',
                'old_status' => $oldStatus,
                'new_status' => 'rejected',
                'comment' => $comment,
            ]);

            if (class_exists(AppNotificationService::class)) {
                AppNotificationService::sendToUser(
                    user: $approval->requester,
                    title: 'รายการของคุณไม่ผ่านการอนุมัติ',
                    message: $approval->title,
                    linkUrl: route('approvals.index'),
                    type: 'approval',
                    data: [
                        'approval_request_id' => $approval->id,
                        'status' => 'rejected',
                    ]
                );
            }

            return $approval;
        });
    }
}