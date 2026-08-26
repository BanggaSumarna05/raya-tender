<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity.
     *
     * @param  string       $action       e.g. CREATE_TENDER, UPDATE_TENDER, VIEW_FINANCIAL_DATA
     * @param  string       $module       e.g. Tender, Proposal, Auth
     * @param  int|null     $subjectId    primary key of the affected record
     * @param  string|null  $subjectType  fully-qualified class name of the model
     * @param  string|null  $description  human-readable summary
     * @param  array|null   $properties   arbitrary JSON — use 'before'/'after' keys for field diffs
     */
    public function log(
        string  $action,
        string  $module,
        ?int    $subjectId    = null,
        ?string $subjectType  = null,
        ?string $description  = null,
        ?array  $properties   = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => Auth::id(),
            'action'       => $action,
            'module'       => $module,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'properties'   => $properties,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'created_at'   => now(),
        ]);
    }

    /**
     * Capture a before/after diff for audited fields and log it.
     *
     * Usage in service:
     *   $this->activityLogService->logWithDiff(
     *       'UPDATE_TENDER', 'Tender', $tender, $oldValues, $newValues,
     *       "Memperbarui tender: {$tender->title}"
     *   );
     *
     * The diff only includes fields that actually changed, so the properties
     * array stays compact. Financial fields (estimated_value, bid_value) are
     * intentionally excluded — those are tracked only via VIEW_FINANCIAL_DATA.
     *
     * @param  string     $action
     * @param  string     $module
     * @param  Model      $subject     the Eloquent model instance
     * @param  array      $before      key→value snapshot BEFORE the change
     * @param  array      $after       key→value snapshot AFTER the change
     * @param  string|null $description
     * @param  array      $extra       any additional top-level properties to merge in
     */
    public function logWithDiff(
        string  $action,
        string  $module,
        Model   $subject,
        array   $before,
        array   $after,
        ?string $description = null,
        array   $extra       = []
    ): ActivityLog {
        // Build diff — only changed fields
        $diff = [];
        foreach ($after as $field => $newValue) {
            $oldValue = $before[$field] ?? null;
            // Normalize enums and Carbon instances for comparison
            $normalizedOld = $this->normalizeValue($oldValue);
            $normalizedNew = $this->normalizeValue($newValue);

            if ($normalizedOld !== $normalizedNew) {
                $diff[$field] = [
                    'before' => $normalizedOld,
                    'after'  => $normalizedNew,
                ];
            }
        }

        $properties = array_merge($extra, ['changes' => $diff]);

        return $this->log(
            $action,
            $module,
            $subject->getKey(),
            get_class($subject),
            $description,
            $properties
        );
    }

    /**
     * Normalize a value for comparison/storage.
     * Converts enums to their scalar value, Carbon to Y-m-d H:i:s string, etc.
     */
    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        if ($value instanceof \Carbon\Carbon || $value instanceof \Carbon\CarbonInterface) {
            return $value->toDateTimeString();
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        return $value;
    }

    /**
     * Helper: extract only the audited fields from an Eloquent model
     * as a plain array snapshot. Excludes financial fields.
     *
     * Audited fields for Tender:
     *   status, pic_id, backup_pic_id, client_id, category_id,
     *   submission_deadline, priority, title
     *
     * Audited fields for Proposal:
     *   status, pic_id, backup_pic_id, tender_id, deadline, title
     */
    public static function tenderSnapshot(array|Model $data): array
    {
        $fields = ['title', 'status', 'pic_id', 'backup_pic_id', 'client_id', 'category_id', 'submission_deadline', 'priority'];

        if ($data instanceof Model) {
            return array_intersect_key($data->toArray(), array_flip($fields));
        }

        return array_intersect_key($data, array_flip($fields));
    }

    public static function proposalSnapshot(array|Model $data): array
    {
        $fields = ['title', 'status', 'pic_id', 'backup_pic_id', 'tender_id', 'deadline'];

        if ($data instanceof Model) {
            return array_intersect_key($data->toArray(), array_flip($fields));
        }

        return array_intersect_key($data, array_flip($fields));
    }
}
