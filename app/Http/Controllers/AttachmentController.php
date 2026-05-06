<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\RepairRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(auth()->user()->can('attachment.upload'), 403);

        $validated = $request->validate([
            'module' => ['required', 'string', 'max:100'],
            'attachable_type' => ['required', 'string'],
            'attachable_id' => ['required', 'integer'],
            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
            ],
        ]);

        $model = $this->resolveAttachable(
            $validated['attachable_type'],
            $validated['attachable_id']
        );

        $this->authorizeAttachable($model, 'upload');

        $file = $request->file('file');

        $fileName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            'attachments/' . $validated['module'],
            $fileName,
            'local'
        );

        $model->attachments()->create([
            'module' => $validated['module'],
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'อัปโหลดไฟล์เรียบร้อยแล้ว');
    }

    public function download(Attachment $attachment)
    {
        abort_unless(auth()->user()->can('attachment.download'), 403);
        $this->authorizeAttachable($attachment->attachable, 'download');

        if (! Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'ไม่พบไฟล์');
        }

        return Storage::disk('local')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    public function destroy(Attachment $attachment)
    {
        abort_unless(auth()->user()->can('attachment.delete'), 403);
        $this->authorizeAttachable($attachment->attachable, 'delete');

        if (Storage::disk('local')->exists($attachment->file_path)) {
            Storage::disk('local')->delete($attachment->file_path);
        }

        $attachment->delete();

        return back()->with('success', 'ลบไฟล์แนบเรียบร้อยแล้ว');
    }

    private function resolveAttachable(string $type, int $id): Model
    {
        return match ($type) {
            'employee' => Employee::findOrFail($id),
            'asset' => Asset::findOrFail($id),
            'repair_request' => RepairRequest::findOrFail($id),
            'leave_request' => LeaveRequest::findOrFail($id),
            default => abort(422, 'ประเภทไฟล์แนบไม่ถูกต้อง'),
        };
    }

    private function authorizeAttachable(?Model $model, string $action): void
    {
        abort_if($model === null, 404);

        $permission = match (true) {
            $model instanceof Employee => $this->employeeAttachmentPermission($action),
            $model instanceof Asset => $this->assetAttachmentPermission($action),
            $model instanceof RepairRequest => $this->repairRequestAttachmentPermission($action),
            $model instanceof LeaveRequest => $this->leaveRequestAttachmentPermission($action),
            default => null,
        };

        abort_unless($permission !== null && auth()->user()->can($permission), 403);
    }

    private function employeeAttachmentPermission(string $action): string
    {
        return match ($action) {
            'upload' => 'employee.update',
            'delete' => 'employee.delete',
            default => 'employee.view',
        };
    }

    private function assetAttachmentPermission(string $action): string
    {
        return match ($action) {
            'upload' => 'asset.update',
            'delete' => 'asset.delete',
            default => 'asset.view',
        };
    }

    private function repairRequestAttachmentPermission(string $action): string
    {
        return match ($action) {
            'upload', 'delete' => 'repair.update',
            default => 'repair.view',
        };
    }

    private function leaveRequestAttachmentPermission(string $action): string
    {
        return match ($action) {
            'upload', 'delete' => 'leave.update',
            default => 'leave.view',
        };
    }
}
