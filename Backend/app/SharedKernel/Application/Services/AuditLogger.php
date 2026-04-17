<?php

declare(strict_types=1);

namespace App\SharedKernel\Application\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final class AuditLogger
{
    public function log(string $action, string $entity, string $entityId, array $details = []): void
    {
        $userId = 'system';
        try {
            if (Auth::check()) {
                $userId = (string) Auth::id();
            }
        } catch (\Throwable $e) {
        }

        Log::channel('audit')->info('AUDIT', [
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'user_id' => $userId,
            'details' => $details,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function created(string $entity, string $entityId, array $data = []): void
    {
        $this->log('CREATED', $entity, $entityId, $data);
    }

    public function updated(string $entity, string $entityId, array $changes = []): void
    {
        $this->log('UPDATED', $entity, $entityId, $changes);
    }

    public function deleted(string $entity, string $entityId): void
    {
        $this->log('DELETED', $entity, $entityId);
    }

    public function payment(string $loanId, array $paymentData): void
    {
        $this->log('PAYMENT', 'loan', $loanId, $paymentData);
    }
}
